/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (“MSA”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */
describe('Resolve Conflicts List View', function() {
    var view, app,
        module = 'Contacts';

    beforeEach(function() {
        app = SugarTest.app;
        SugarTest.testMetadata.init();
        SugarTest.testMetadata.set();

        view = SugarTest.createView('base', module, 'resolve-conflicts-list');
    });

    afterEach(function() {
        view.dispose();

        SugarTest.testMetadata.dispose();
        app.cache.cutAll();
        app.view.reset();
        Handlebars.templates = {};
    });

    describe('_populateMissingDataFromDatabase', function() {
        it('should populate missing data from the database data', function() {
            var clientModel = app.data.createBean(module),
                databaseModel = app.data.createBean(module);

            databaseModel.set('foo', 'bar');

            view._populateMissingDataFromDatabase(clientModel, databaseModel);

            expect(clientModel.get('foo')).toBe('bar');
        });

        it('should not copy over from the database data if the value already exists in the client bean', function() {
            var clientModel = app.data.createBean(module),
                databaseModel = app.data.createBean(module);

            clientModel.set('one', 'foo');
            databaseModel.set('one', 'bar');

            view._populateMissingDataFromDatabase(clientModel, databaseModel);

            expect(clientModel.get('one')).toBe('foo');
        });
    });

    describe('_getFieldViewDefinition', function() {
        it('should get record view field view definition', function() {
            SugarTest.testMetadata.addViewDefinition('record', {
                "panels": [{
                    "fields":[{
                        "name":"first_name"
                    }, {
                        "name":"last_name"
                    }, {
                        "name":"phone"
                    }]
                }]
            }, module);

            var actual = view._getFieldViewDefinition(['first_name', 'phone']);

            expect(actual.length).toBe(2);
            expect(actual[0].name).toBe('first_name');
            expect(actual[1].name).toBe('phone');
        });

        it('should get record view field view definition in fieldsets', function() {
            SugarTest.testMetadata.addViewDefinition('record', {
                "panels": [{
                    "fields":[{
                        "fields":[{
                            "name":"first_name"
                        }, {
                            "name":"last_name"
                        }]
                    }, {
                        "name":"phone"
                    }]
                }]
            }, module);

            var actual = view._getFieldViewDefinition(['first_name', 'phone']);

            expect(actual.length).toBe(2);
            expect(actual[0].name).toBe('first_name');
            expect(actual[1].name).toBe('phone');
        });
    });

    describe('_buildFieldDefinitions', function() {
        it('should build columns based on record view field definitions', function() {
            var clientModel = app.data.createBean(module),
                databaseModel = app.data.createBean(module),
                getFieldViewDefinitionStub = sinon.stub(view, '_getFieldViewDefinition', function() {
                    return [{
                        name: 'first_name'
                    }, {
                        name: 'last_name'
                    }]
                });

            view._buildFieldDefinitions(clientModel, databaseModel);

            expect(view._fields.visible.length).toBe(3);
            expect(view._fields.available.length).toBe(0);
            expect(view._fields.default.length).toBe(3);
            expect(view._fields.options.length).toBe(3);

            getFieldViewDefinitionStub.restore();
        });

        it('should have modified by column as the first column', function() {
            var clientModel = app.data.createBean(module),
                databaseModel = app.data.createBean(module),
                getFieldViewDefinitionStub = sinon.stub(view, '_getFieldViewDefinition', function() {
                    return [{
                        name: 'first_name'
                    }, {
                        name: 'last_name'
                    }]
                });

            view._buildFieldDefinitions(clientModel, databaseModel);

            expect(view._fields.visible[0].name).toBe('_modified_by');
            expect(view._fields.default[0].name).toBe('_modified_by');
            expect(view._fields.options[0].name).toBe('_modified_by');

            getFieldViewDefinitionStub.restore();
        });

        it('should set all columns to not sort', function() {
            var clientModel = app.data.createBean(module),
                databaseModel = app.data.createBean(module),
                getFieldViewDefinitionStub = sinon.stub(view, '_getFieldViewDefinition', function() {
                    return [{
                        name: 'first_name'
                    }, {
                        name: 'last_name'
                    }]
                });

            view._buildFieldDefinitions(clientModel, databaseModel);

            expect(view._fields.visible[0].sortable).toBe(false);
            expect(view._fields.visible[1].sortable).toBe(false);
            expect(view._fields.visible[2].sortable).toBe(false);

            getFieldViewDefinitionStub.restore();
        });

        it('should hide date modified column', function() {
            var clientModel = app.data.createBean(module),
                databaseModel = app.data.createBean(module),
                getFieldViewDefinitionStub = sinon.stub(view, '_getFieldViewDefinition', function() {
                    return [{
                        name: 'first_name'
                    }, {
                        name: 'last_name'
                    }, {
                        name: 'date_modified'
                    }]
                });

            view._buildFieldDefinitions(clientModel, databaseModel);

            expect(view._fields.visible.length).toBe(3);
            expect(view._fields.available.length).toBe(1);
            expect(view._fields.default.length).toBe(4);
            expect(view._fields.options.length).toBe(4);

            expect(view._fields.available[0].selected).toBe(false);
            expect(view._fields.visible[0].selected).toBe(true);
            expect(view._fields.visible[1].selected).toBe(true);
            expect(view._fields.visible[2].selected).toBe(true);

            getFieldViewDefinitionStub.restore();
        });
    });

    describe('_buildList', function() {
        it('should have two rows of data', function() {
            app.data.declareModels();

            var clientModel = app.data.createBean(module, {
                    id: 1
                }),
                dataInDatabase = {
                    id: 1
                },
                buildFieldDefinitionsStub = sinon.stub(view, '_buildFieldDefinitions'),
                populateMissingDataFromDatabaseStub = sinon.stub(view, '_populateMissingDataFromDatabase');

            view.context.set('modelToSave', clientModel);
            view.context.set('dataInDb', dataInDatabase);

            view._buildList();

            expect(view.collection.length).toBe(2);

            buildFieldDefinitionsStub.restore();
            populateMissingDataFromDatabaseStub.restore();
        });

        it('should change the models to have different IDs', function() {
            app.data.declareModels();

            var clientModel = app.data.createBean(module, {
                    id: 1
                }),
                dataInDatabase = {
                    id: 1
                },
                buildFieldDefinitionsStub = sinon.stub(view, '_buildFieldDefinitions'),
                populateMissingDataFromDatabaseStub = sinon.stub(view, '_populateMissingDataFromDatabase');

            view.context.set('modelToSave', clientModel);
            view.context.set('dataInDb', dataInDatabase);

            view._buildList();

            expect(view.collection.at(0).get('id')).toBe('1-client');
            expect(view.collection.at(1).get('id')).toBe('1-database');

            buildFieldDefinitionsStub.restore();
            populateMissingDataFromDatabaseStub.restore();
        });

        it('should indicate which models are which', function() {
            app.data.declareModels();

            var clientModel = app.data.createBean(module, {
                    id: 1
                }),
                dataInDatabase = {
                    id: 1
                },
                buildFieldDefinitionsStub = sinon.stub(view, '_buildFieldDefinitions'),
                populateMissingDataFromDatabaseStub = sinon.stub(view, '_populateMissingDataFromDatabase');

            view.context.set('modelToSave', clientModel);
            view.context.set('dataInDb', dataInDatabase);

            view._buildList();

            expect(view.collection.at(0).get('_dataOrigin')).toBe('client');
            expect(view.collection.at(1).get('_dataOrigin')).toBe('database');

            buildFieldDefinitionsStub.restore();
            populateMissingDataFromDatabaseStub.restore();
        });

        it('should populate _modified_by column', function() {
            app.data.declareModels();

            var clientModel = app.data.createBean(module, {
                    id: 1
                }),
                dataInDatabase = {
                    id: 1,
                    modified_by_name: 'foo'
                },
                buildFieldDefinitionsStub = sinon.stub(view, '_buildFieldDefinitions'),
                populateMissingDataFromDatabaseStub = sinon.stub(view, '_populateMissingDataFromDatabase');

            view.context.set('modelToSave', clientModel);
            view.context.set('dataInDb', dataInDatabase);

            view._buildList();

            expect(view.collection.at(0).get('_modified_by')).toBe('LBL_YOU');
            expect(view.collection.at(1).get('_modified_by')).toBe('foo');

            buildFieldDefinitionsStub.restore();
            populateMissingDataFromDatabaseStub.restore();
        });

        it('should not use modelToSave in the collection but instead use a copy of it', function() {
            app.data.declareModels();

            var clientModel = app.data.createBean(module, {
                    id: 1
                }),
                dataInDatabase = {
                    id: 1
                },
                buildFieldDefinitionsStub = sinon.stub(view, '_buildFieldDefinitions'),
                populateMissingDataFromDatabaseStub = sinon.stub(view, '_populateMissingDataFromDatabase');

            view.context.set('modelToSave', clientModel);
            view.context.set('dataInDb', dataInDatabase);

            view._buildList();

            expect(view.collection.at(0)).not.toBe(clientModel);

            buildFieldDefinitionsStub.restore();
            populateMissingDataFromDatabaseStub.restore();
        });
    });
});
