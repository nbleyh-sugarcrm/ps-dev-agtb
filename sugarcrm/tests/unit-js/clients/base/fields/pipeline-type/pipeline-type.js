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

describe('Base.Fields.PipelineType', function() {
    var app;
    var sandbox;
    var context;
    var model;
    var moduleName;
    var field;
    var fieldDef;
    var config;
    var fields;
    var getStub;
    var getModStub;

    beforeEach(function() {
        app = SugarTest.app;
        sandbox = sinon.sandbox.create();
        moduleName = 'Tasks';
        model = app.data.createBean(moduleName, {
            id: '123test',
            name: 'Lórem ipsum dolor sit àmêt, ut úsu ómnés tatión imperdiet.'
        });
        context = new app.Context();
        context.set({model: model});

        fieldDef = {
            label: 'LBL_PIPELINE_TYPE',
            name: 'pipeline_type',
            type: 'pipeline-type'
        };

        config = {
            enabled_modules: ['Opportunities', 'Tasks'],
            is_setup: 1,
            records_per_column: 10,
            table_header: {Opportunities: 'sales_status', Tasks: 'status'},
            tile_header: {Opportunities: 'name', Tasks: 'name'}
        };
        fields = {
            status: {
                vname: 'LBL_LIST_ACCEPT_STATUS'
            }
        };

        sinon.collection.stub(app.metadata, 'getModule').withArgs('VisualPipeline').returns(config)
            .withArgs('Tasks').returns(fields);
        field = SugarTest.createField('base', 'pipeline-type', 'pipeline-type',
            'detail', fieldDef, 'Tasks', model, false);
    });

    afterEach(function() {
        sandbox.restore();
        sinon.collection.restore();
        getStub = null;
        getModStub = null;
        app = null;
        context = null;
        model = null;
        field = null;
        fieldDef = null;
        moduleName = null;
    });

    describe('initialize()', function() {
        it('should call getTabs method', function() {
            sinon.collection.stub(field, 'getTabs', function() {});
            field.initialize({context: context});

            expect(field.getTabs).toHaveBeenCalled();
        });
    });

    describe('getTabs()', function() {
        var metaObject;

        beforeEach(function() {
            metaObject = [
                {
                    'headerLabel': 'Status',
                    'moduleField': 'status',
                    'tabLabel': 'Test String Status'
                }
            ];
            getStub = sandbox.stub(app.lang, 'get');
            getModStub = sandbox.stub(app.lang, 'getModString');
        });

        afterEach(function() {
            metaObject = null;
        });

        it('should push metaObject into field.tabs and set pipelineHeaderLabel', function() {
            getStub.withArgs('LBL_PIPELINE_VIEW_TAB_NAME', field.module).returns('Test String ');
            getModStub.withArgs('LBL_LIST_ACCEPT_STATUS', field.module).returns('Status');
            field.getTabs();
            expect(field.tabs).toEqual(metaObject);
        });
    });
});
