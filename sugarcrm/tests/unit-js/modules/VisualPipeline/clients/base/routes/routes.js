// FILE SUGARCRM flav=ent ONLY
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
describe('VisualPipeline.Routes', function() {
    var app;
    var buildKeyStub;
    var getStub;
    var setStub;

    beforeEach(function() {
        app = SugarTest.app;
        app.controller.loadAdditionalComponents(app.config.additionalComponents);
        // FIXME: SC-4677, load additionalComponents in tests
        // "Before Route Show Wizard Check" dependency
        buildKeyStub = sinon.collection.stub(app.user.lastState, 'buildKey');
        getStub = sinon.collection.stub(app.user.lastState, 'get');
        setStub = sinon.collection.stub(app.user.lastState, 'set');

        SugarTest.loadFile('../modules/VisualPipeline/clients/base/routes', 'routes', 'js', function(d) {
            eval(d);
            app.routing.start();
        });
    });

    afterEach(function() {
        sinon.collection.restore();
        app.router.stop();
    });

    describe('Routes', function() {
        var mockKey = 'foo:key';
        var oldIsSynced;

        beforeEach(function() {
            oldIsSynced = app.isSynced;
            app.isSynced = true;
            sinon.collection.stub(app.router, 'redirect');
            app.drawer = {
                open: $.noop
            };
            sinon.collection.stub(app.drawer, 'open');

            sinon.collection.stub(app.router, 'hasAccessToModule').returns(true);
            sinon.collection.stub(app.api, 'isAuthenticated').returns(true);
            sinon.collection.stub(app, 'sync');
            buildKeyStub.returns(mockKey);
        });

        afterEach(function() {
            app.isSynced = oldIsSynced;
            delete app.drawer;
        });

        it('should redirect to Opportunities records view on VisualPipeline list', function() {
            app.router.navigate('VisualPipeline', {trigger: true});

            expect(app.router.redirect).toHaveBeenCalledWith('#Opportunities/pipeline');
        });

        it('should redirect to Opportunities records view on VisualPipeline create', function() {
            app.router.navigate('VisualPipeline/create', {trigger: true});

            expect(app.router.redirect).toHaveBeenCalledWith('#Opportunities/pipeline');
        });

        it('should redirect to Opportunities records view on VisualPipeline record', function() {
            app.router.navigate('VisualPipeline/test-hash', {trigger: true});

            expect(app.router.redirect).toHaveBeenCalledWith('#Opportunities/pipeline');
        });

        it('should redirect to VisualPipeline/config when :id is config', function() {
            app.router.navigate('VisualPipeline/config', {trigger: true});

            expect(app.drawer.open).toHaveBeenCalledWith({
                layout: 'config-drawer',
                context: {
                    module: 'VisualPipeline',
                    fromRouter: true
                }
            });
        });
    });
});
