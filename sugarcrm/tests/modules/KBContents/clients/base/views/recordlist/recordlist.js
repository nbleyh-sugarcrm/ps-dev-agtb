describe('KBContents.Base.Views.RecordList', function() {

    var app, view, sandbox, layout, context, moduleName = 'KBContents';

    beforeEach(function() {
        app = SugarTest.app;
        sandbox = sinon.sandbox.create();
        context = app.context.getContext({
            module: moduleName
        });
        context.set('model', app.data.createBean(moduleName));
        context.parent = new Backbone.Model();
        context.parent.set('module', moduleName);
        SugarTest.loadPlugin('KBContent');
        SugarTest.loadComponent('base', 'view', 'list');
        SugarTest.loadComponent('base', 'view', 'flex-list');
        SugarTest.loadComponent('base', 'view', 'recordlist');
        SugarTest.loadComponent('base', 'view', 'recordlist', moduleName);
        SugarTest.loadHandlebarsTemplate('flex-list', 'view', 'base');
        layout = SugarTest.createLayout(
            'base',
            moduleName,
            'list',
            null,
            context.parent
        );
        view = SugarTest.createView(
            'base',
            moduleName,
            'recordlist',
            null,
            context,
            moduleName,
            layout
        );
    });

    afterEach(function() {
        sandbox.restore();
        layout.dispose();
        view.dispose();
        SugarTest.testMetadata.dispose();
        app.cache.cutAll();
        app.view.reset();
        Handlebars.templates = {};
        delete app.plugins.plugins['view']['KBContent'];
        view = null;
        layout = null;
    });

    describe('initialize()', function() {
        var superStub, hasAccessToModelStub, contextSetStub;

        beforeEach(function() {
            superStub = sandbox.stub(view, '_super');
            contextSetStub = sandbox.stub(view.context, 'set');
        });

        it('should call parent method when initialize', function() {
            view.initialize({});
            expect(superStub).toHaveBeenCalled();
        });

        it('should check acl edit and when not allowed set context noedit', function() {
            hasAccessToModelStub = sandbox.stub(app.acl, 'hasAccess', function() {
                return false;
            });
            view.initialize({});
            expect(contextSetStub).toHaveBeenCalledWith('requiredFilter', 'records-noedit');
        });

        it('should check acl edit and when allowed then not set context', function() {
            hasAccessToModelStub = sandbox.stub(app.acl, 'hasAccess', function() {
                return true;
            });
            view.initialize({});
            expect(contextSetStub).not.toHaveBeenCalled();
        });
    });

    describe('parseFieldMetadata()', function() {
        var superStub, hasAccessToModelStub, contextSetStub, data;

        beforeEach(function() {
            data = {
                module: moduleName,
                meta: {
                    panels: [
                        {
                            fields: [
                                {
                                    name: 'test1'
                                },
                                {
                                    name: 'test2'
                                },
                                {
                                    name: 'status'
                                }
                            ]
                        }
                    ]
                }
            };
            superStub = sandbox.stub(view, '_super', function(func, args) {
                return _.clone(args[0]);
            });
            contextSetStub = sandbox.stub(view.context, 'set');
        });

        it('should check acl edit and do not filter data when allowed', function() {
            hasAccessToModelStub = sandbox.stub(app.acl, 'hasAccess', function() {
                return true;
            });
            var result = view.parseFieldMetadata(data);

            expect(superStub).toHaveBeenCalled();
            expect(hasAccessToModelStub).toHaveBeenCalled();
            expect(result.meta.panels[0].fields).toContain({
                name: 'status'
            });
        });

        it('should check acl edit and remove secure fields when not allowed', function() {
            hasAccessToModelStub = sandbox.stub(app.acl, 'hasAccess', function() {
                return false;
            });
            var result = view.parseFieldMetadata(data);

            expect(superStub).toHaveBeenCalled();
            expect(hasAccessToModelStub).toHaveBeenCalled();
            expect(result.meta.panels[0].fields).not.toContain({
                name: 'status'
            });
        });
    });

    describe('Additional validation', function() {

        it('Expiration date cannot be lower than publishing.', function() {
            var validateSpy = sandbox.spy(function(mod, field, errors) {
            });
            sandbox.stub(view, 'getField', function(name) {
                var obj = {};
                obj[name] = true;
                return obj;
            });
            view.model.set('status', 'draft');
            view.model.set('exp_date', '2010-10-10');
            view.model.set('active_date', '2011-10-10');

            view._doValidateExpDateField(view.model, [], [], validateSpy);

            expect(validateSpy.args[0][2].exp_date.expDateLow).toBeTruthy();
        });

        it('Approved requires publishing date and the field on view.', function() {
            var validateSpy = sandbox.spy(function(mod, field, errors) {
            });
            sandbox.stub(view, 'getField', function(name) {
                if (name == 'active_date') {
                    return {name: 'active_date'};
                }
            });

            view.model.set('status', 'approved');
            view.model.set('active_date', null);

            view._doValidateActiveDateField(view.model, [], [], validateSpy);

            expect(validateSpy.args[0][2].active_date.activeDateApproveRequired).toBeTruthy();
        });

        it('Approved requires publishing date and the view not on view.', function() {
            var validateSpy = sandbox.spy(function(mod, view, errors) {
            });
            sandbox.stub(view, 'getField', function(name) {
                if (name == 'active_date') {
                    return undefined;
                }
            });
            view.model.set('status', 'approved');
            view.model.set('active_date', null);

            view._doValidateActiveDateField(view.model, [], [], validateSpy);

            // The validation decorator should be on the status view.
            expect(validateSpy.args[0][2].status.activeDateApproveRequired).toBeTruthy();
        });

        it('Expiration changes own date to current.', function() {
            sandbox.stub(view.model, 'changedAttributes', function() {
                return {status: 'published'};
            });
            view.model.set('status', 'expired');
            view._validationComplete(view.model, true);

            expect(view.model.get('exp_date')).toEqual(app.date().formatServer(true));
        });

        it('Publishing changes own date to current.', function() {
            sandbox.stub(view.model, 'changedAttributes', function() {
                return {status: 'approved'};
            });
            view.model.set('status', 'published-in');
            view._validationComplete(view.model, true);

            expect(view.model.get('active_date')).toEqual(app.date().formatServer(true));
        });


        it('Switching from publishing to publishing should not change own date.', function() {
            sandbox.stub(view.model, 'changedAttributes', function() {
                return {status: 'published-in'};
            });
            var expectedDate = '2000-10-10';
            view.model.set('active_date', '2000-10-10');
            view.model.set('status', 'published-ex');
            view._validationComplete(view.model, true);

            expect(view.model.get('active_date')).toEqual(expectedDate);
        });

    });
});
