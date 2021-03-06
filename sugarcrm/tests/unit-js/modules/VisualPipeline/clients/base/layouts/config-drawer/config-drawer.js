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
describe('VisualPipeline.Layout.ConfigDrawer', function() {
    var app;
    var layout;
    var context;
    var options;
    var sandbox;
    var moduleMeta;

    beforeEach(function() {
        app = SugarTest.app;
        sandbox = sinon.sandbox.create();
        context = app.context.getContext();
        context.set({
            model: new Backbone.Model(),
            collection: app.data.createBeanCollection('VisualPipeline')
        });
        context.prepare();
        options = {
            context: context
        };

        moduleMeta = {
            priority: {
                audited: true,
                name: 'priority',
                type: 'enum',
                vname: 'LBL_PRIORITY'
            },
            fields: {
                name: {
                    type: 'name'
                },
                accept_status_name: {
                    massupdate: false,
                    name: 'accept_status_name',
                    type: 'enum',
                    source: 'non-db',
                    vname: 'LBL_LIST_ACCEPT_STATUS',
                    options: 'dom_meeting_accept_status'
                },
                priority: {
                    default: true,
                    enabled: true,
                    options: 'case_priority_dom',
                    vname: 'LBL_PRIORITY',
                    name: 'priority',
                    type: 'enum'
                },
                following: {
                    massupdate: false,
                    name: 'following',
                    vname: 'LBL_FOLLOWING',
                    type: 'bool',
                    source: 'non-db',
                    comment: 'Is user following this record',
                    studio: 'false',
                    link: 'following_link',
                    rname: 'id',
                    rname_exists: true
                },
                my_favorite: {
                    massupdate: false,
                    name: 'my_favorite',
                    vname: 'LBL_FAVORITE',
                    type: 'bool',
                    source: 'non-db',
                    comment: 'Favorite for the user',
                    studio: {
                        list: false,
                        recordview: false,
                        basic_search: false,
                        advanced_search: false,
                    }
                },
                comment_log: {
                    name: 'commentlog',
                    vname: 'LBL_COMMENTLOG',
                    type: 'collection',
                    studio: {
                        listview: false,
                        recordview: true
                    }
                }
            },
            isBwcEnabled: false
        };

        sinon.collection.stub(app.metadata, 'getModule').withArgs('Tasks').returns({})
            .withArgs('Cases').returns(moduleMeta)
            .withArgs('Opportunities').returns(moduleMeta);
        SugarTest.loadComponent('base', 'layout', 'config-drawer');
        layout = SugarTest.createLayout('base', 'VisualPipeline', 'config-drawer', {}, context, true);
        sinon.collection.stub(layout, 'checkAccess', function() {
            return true;
        });
    });

    afterEach(function() {
        sinon.collection.restore();
        layout = null;
        context = null;
        options = null;
    });

    describe('initialize', function() {
        beforeEach(function() {
            sinon.collection.stub(layout, 'setAllowedModules', function() {});
            sinon.collection.stub(layout, '_super');
            layout.initialize(options);
        });

        it('should call the _super method with initialize', function() {

            expect(layout._super).toHaveBeenCalledWith('initialize', [options]);
        });

        it('should call setAllowedModules', function() {

            expect(layout.setAllowedModules).toHaveBeenCalled();
        });
    });

    describe('bindDataChange', function() {
        beforeEach(function() {
            sinon.collection.stub(layout, 'addModelToCollection', function() {});
            sinon.collection.stub(layout, 'removeModelFromCollection', function() {});
            sinon.collection.stub(layout.context, 'on', function() {});
            layout.bindDataChange();
        });

        it('should call layput.context.on with pipeline:config:model:add', function() {

            expect(layout.context.on).toHaveBeenCalledWith('pipeline:config:model:add');
        });

        it('should call layput.context.on with pipeline:config:model:remove', function() {

            expect(layout.context.on).toHaveBeenCalledWith('pipeline:config:model:remove');
        });
    });

    describe('loadData', function() {
        beforeEach(function() {
            sinon.collection.stub(layout, 'addModelToCollection', function() {});
            sinon.collection.stub(layout, 'setActiveTabIndex', function() {});
            sinon.collection.stub(app.metadata, 'getModuleNames', function() {
                return ['Cases', 'Tasks', 'Accounts', 'Contacts', 'Leads'];
            });
            sinon.collection.spy(JSON, 'parse');
        });

        describe('when recordsPerColumn is not an object', function() {
            beforeEach(function() {
                sinon.collection.stub(layout.model, 'get')
                    .withArgs('enabled_modules').returns(['Cases'])
                    .withArgs('table_header').returns({Cases: 'status'})
                    .withArgs('tile_header').returns({Cases: 'name'})
                    .withArgs('tile_body_fields').returns({Cases: ['account_name', 'priority']})
                    .withArgs('records_per_column').returns('{"Cases": 10}')
                    .withArgs('hidden_values').returns({Cases: ['test']})
                    .withArgs('available_columns').returns({Cases: ['Assigned']});
                layout.loadData();
            });
            afterEach(function() {
                sinon.collection.restore();
            });

            it('should call JSON.parse to convert recordsPerColumn into object', function() {

                expect(JSON.parse).toHaveBeenCalledWith('{"Cases": 10}');
            });
        });

        it('should call layout.model.get with various arguments', function() {
            sinon.collection.stub(layout.model, 'get')
                .withArgs('enabled_modules').returns(['Cases'])
                .withArgs('table_header').returns({Cases: 'status'})
                .withArgs('tile_header').returns({Cases: 'name'})
                .withArgs('tile_body_fields').returns({Cases: ['account_name', 'priority']})
                .withArgs('records_per_column').returns({Cases: 10})
                .withArgs('hidden_values').returns({Cases: ['test']})
                .withArgs('available_columns').returns({Cases: ['Assigned']});
            layout.loadData();

            expect(layout.model.get).toHaveBeenCalledWith('enabled_modules');
            expect(layout.model.get).toHaveBeenCalledWith('table_header');
            expect(layout.model.get).toHaveBeenCalledWith('tile_header');
            expect(layout.model.get).toHaveBeenCalledWith('tile_body_fields');
            expect(layout.model.get).toHaveBeenCalledWith('records_per_column');
            expect(layout.model.get).toHaveBeenCalledWith('hidden_values');
            expect(layout.model.get).toHaveBeenCalledWith('available_columns');
        });

        it('should call layout.addModelToCollection with moduleName and data', function() {
            sinon.collection.stub(layout.model, 'get')
                .withArgs('enabled_modules').returns(['Cases'])
                .withArgs('table_header').returns({Cases: 'status'})
                .withArgs('tile_header').returns({Cases: 'name'})
                .withArgs('tile_body_fields').returns({Cases: ['account_name', 'priority']})
                .withArgs('records_per_column').returns({Cases: 10})
                .withArgs('hidden_values').returns({Cases: ['test']})
                .withArgs('available_columns').returns({Cases: ['Assigned']});
            layout.loadData();

            expect(layout.addModelToCollection).toHaveBeenCalledWith('Cases', {
                enabled: true,
                enabled_module: 'Cases',
                table_header: 'status',
                tile_header: 'name',
                tile_body_fields: ['account_name', 'priority'],
                records_per_column: 10,
                hidden_values: ['test'],
                available_columns: ['Assigned']
            });
        });

        it('should return the list of available and supported module names', function() {
            sinon.collection.stub(layout.model, 'get')
                .withArgs('enabled_modules').returns(['Cases', 'Opportunities', 'Tasks']);
            expect(layout.getAvailableModules()).toEqual(['Cases', 'Opportunities']);
        });

        it('should call layout.setActiveTabIndex with 0', function() {
            sinon.collection.stub(layout.model, 'get')
                .withArgs('enabled_modules').returns(['Cases'])
                .withArgs('table_header').returns({Cases: 'status'})
                .withArgs('tile_header').returns({Cases: 'name'})
                .withArgs('tile_body_fields').returns({Cases: ['account_name', 'priority']})
                .withArgs('records_per_column').returns({Cases: 10})
                .withArgs('hidden_values').returns({Cases: ['test']})
                .withArgs('available_columns').returns({Cases: ['Assigned']});
            layout.loadData();

            expect(layout.setActiveTabIndex).toHaveBeenCalledWith(0);
        });
    });

    describe('_checkModuleAccess', function() {
        beforeEach(function() {
            sandbox.stub(app.user, 'getAcls', function() {
                return {
                    'VisualPipeline': {
                        admin: true
                    }
                };
            });
            sinon.collection.spy(app.user, 'get');
            layout._checkModuleAccess();
        });

        it('should call app.user.getAcls method', function() {

            expect(app.user.getAcls).toHaveBeenCalled();
        });

        it('should call the app.user.get method with', function() {

            expect(app.user.get).toHaveBeenCalledWith('type');
        });
    });

    describe('setAllowedModules', function() {
        beforeEach(function() {
            layout.supportedModules = null;
            sinon.collection.stub(app.metadata, 'getModuleNames', function() {
                return ['Cases'];
            });
            sinon.collection.stub(app.lang, 'getAppListStrings', function() {return {Cases: 'Cases'};});
            sinon.collection.stub(layout.context, 'set', function() {});
            layout.setAllowedModules();
        });

        it('should call the app.metadata.getModulenames method', function() {

            expect(app.metadata.getModuleNames).toHaveBeenCalledWith({
                filter: 'display_tab',
                access: 'read'
            });
        });

        it('should call layout.context.on method with allowedModules and modules', function() {

            expect(layout.context.set).toHaveBeenCalledWith('allowedModules', {Cases: 'Cases'});
        });
    });

    describe('setActiveIndex', function() {
        var index;
        beforeEach(function() {
            sinon.collection.stub(layout.context, 'set', function() {});
        });

        describe('when collection.length is < 1 and index is undefined', function() {
            it('should not call layotu.context.set', function() {
                index = undefined;
                layout.collection = {
                    length: 0,
                    off: function() {}
                };
                layout.setActiveTabIndex(index);

                expect(layout.context.set).not.toHaveBeenCalled();
            });
        });

        describe('when collection.length is >= 1 or index is undefined', function() {
            it('should not call layout.context.set at length 1', function() {
                index = undefined;
                layout.collection = {
                    length: 1,
                    off: function() {}
                };
                layout.setActiveTabIndex(index);

                expect(layout.context.set).toHaveBeenCalledWith('activeTabIndex', 0);
            });

            it('should not call layout.context.set at index defined', function() {
                index = 2;
                layout.collection = {
                    length: 0,
                    off: function() {}
                };
                layout.setActiveTabIndex(index);

                expect(layout.context.set).toHaveBeenCalledWith('activeTabIndex', 2);
            });
        });
    });

    describe('removeModelFromCollection', function() {
        var module;
        var removeStub;
        beforeEach(function() {
            removeStub = sinon.collection.stub();
            sinon.collection.stub(layout, 'setActiveTabIndex', function() {});
        });

        afterEach(function() {
            removeStub = null;
        });

        describe('when module is not an enabled_module', function() {
            it('should not call layout.context.trigger', function() {
                module = 'Cases';
                layout.collection = {
                    models: [
                        {
                            get: function() {
                                return 'Tasks';
                            }
                        }
                    ],
                    off: function() {},
                    remove: removeStub
                };
                layout.removeModelFromCollection(module);

                expect(layout.context.trigger).not.toHaveBeenCalled();
            });
        });

        describe('when module is enabled and model is not empty', function() {
            beforeEach(function() {
                module = 'Tasks';
                layout.collection = {
                    models: [
                        {
                            get: function() {
                                return 'Tasks';
                            }
                        }
                    ],
                    off: function() {},
                    remove: removeStub
                };
                layout.removeModelFromCollection(module);
            });

            it('should call layout.collection.remove', function() {

                expect(layout.collection.remove).toHaveBeenCalledWith(layout.collection.models[0]);
            });

            it('should call layout.setActiveTabIndex', function() {

                expect(layout.setActiveTabIndex).toHaveBeenCalled();
            });
        });
    });

    describe('addModelToCollection', function() {
        var module;
        var data;
        var addStub;
        beforeEach(function() {
            module = 'Cases';
            data = {
                enabled: true,
                module: 'Cases',
                table_header: 'status',
                tile_header: 'name',
                tile_body_fields: ['account_name', 'priority'],
                records_per_column: '10'
            };

            addStub = sinon.collection.stub();
            sinon.collection.stub(layout.context, 'get', function() {
                return {Cases: 'Cases'};
            });
            sinon.collection.stub(layout, 'setActiveTabIndex', function() {});
            sinon.collection.stub(layout, 'getModuleFields', function() {});
            sinon.collection.stub(layout, 'addValidationTasks', function() {});
        });

        it('should call setActiveTabIndex method', function() {
            layout.collection = {
                models: [
                    {
                        get: function() {
                            return 'Tasks';
                        }
                    }
                ],
                off: function() {},
                add: addStub
            };
            layout.addModelToCollection(module, data);

            expect(layout.setActiveTabIndex).toHaveBeenCalled();
        });

        describe('when existingBean is not empty', function() {
            it('should not call layout.addValidationTasks method', function() {
                layout.collection = {
                    models: [
                        {
                            get: function() {
                                return 'Cases';
                            }
                        }
                    ],
                    off: function() {},
                    add: addStub
                };
                layout.addModelToCollection(module, data);

                expect(layout.addValidationTasks).not.toHaveBeenCalled();
            });
        });

        describe('when existingBean is empty', function() {
            beforeEach(function() {
                layout.collection = {
                    models: [
                        {
                            get: function() {
                                return 'Tasks';
                            }
                        }
                    ],
                    off: function() {},
                    add: addStub
                };
                layout.addModelToCollection(module, data);
            });

            it('should call layout.collection.add with bean', function() {

                expect(layout.collection.add).toHaveBeenCalled();
            });

            it('should call layout.getModuleFields with bean', function() {

                expect(layout.getModuleFields).toHaveBeenCalled();
            });

            it('should not layout.addValidationTasks method', function() {

                expect(layout.addValidationTasks).toHaveBeenCalled();
            });
        });
    });

    describe('getModuleFields', function() {
        var bean;
        beforeEach(function() {
            bean = app.data.createBean(layout.module, {
                enabled: true,
                enabled_module: 'Cases',
                table_header: 'status',
                tile_header: 'name',
                tile_body_fields: ['account_name', 'priority'],
                records_per_column: '10'
            });

            sinon.collection.stub(bean, 'set', function() {});
            sinon.collection.stub(bean, 'get', function() {
                return 'Cases';
            });

            sinon.collection.stub(app.lang, 'get').withArgs('LBL_PRIORITY', 'Cases').returns('LBL_PRIORITY')
                .withArgs('LBL_COMMENTLOG', 'Cases').returns('LBL_COMMENTLOG');
        });

        it('should call bean.get method', function() {
            layout.getModuleFields(bean);

            expect(bean.get).toHaveBeenCalledWith('enabled_module');
        });

        it('should call app.metadata.getView', function() {
            layout.getModuleFields(bean);

            expect(app.metadata.getModule).toHaveBeenCalledWith('Cases');
        });

        it('should call app.metadata.getModule', function() {
            layout.getModuleFields(bean);

            expect(app.metadata.getModule).toHaveBeenCalledWith('Cases');
        });

        it('should call bean.set with tabContent and content', function() {
            layout.getModuleFields(bean);

            expect(bean.set).toHaveBeenCalledWith('tabContent', {
                dropdownFields: {
                    priority: 'LBL_PRIORITY'
                },
                fields: {
                    priority: 'LBL_PRIORITY',
                    commentlog: 'LBL_COMMENTLOG'
                }
            });
        });
    });

    describe('isValidStudioField', function() {
        var testMeta;
        var res;
        it('should return true if studio is not false', function() {
            testMeta = {
                name: 'test',
                type: 'json',
                studio: true
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeTruthy();
        });

        it('should return false if studio is false', function() {
            testMeta = {
                name: 'test',
                studio: false
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeFalsy();
        });

        it('should return true if studio.recordview is not false or hidden', function() {
            testMeta = {
                name: 'test',
                studio: {
                    recordview: true
                }
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeTruthy();
        });

        it('should return false if studio.recordview is false or hidden', function() {
            testMeta = {
                name: 'test',
                studio: {
                    recordview: 'hidden'
                }
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeFalsy();
        });

        it('should return true if studio.visible is true', function() {
            testMeta = {
                name: 'test',
                studio: {
                    visible: true
                }
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeTruthy();
        });

        it('should return false if studio.visible is false', function() {
            testMeta = {
                name: 'test',
                studio: {
                    visible: false
                }
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeFalsy();
        });

        it('should return false if field type is json', function() {
            testMeta = {
                name: 'test',
                type: 'json'
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeFalsy();
        });

        it('should return true if name is email1', function() {
            testMeta = {
                name: 'email1',
                type: 'id',
                source: 'non-db'
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeTruthy();
        });

        it('should return true if name ends with _name', function() {
            testMeta = {
                name: 'random_test_name',
                dbType: 'id'
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeTruthy();
        });

        it('should return false if field type is id', function() {
            testMeta = {
                name: 'test',
                type: 'id'
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeFalsy();
        });

        it('should return false if field dbType is id', function() {
            testMeta = {
                name: 'test',
                type: 'id'
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeFalsy();
        });

        it('should return true if source is db, type is not id and name is not deleted', function() {
            testMeta = {
                name: 'test',
                source: 'db',
                type: 'int',
                dbType: 'int'
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeTruthy();
        });

        it('should return false if name is deleted', function() {
            testMeta = {
                name: 'deleted',
                source: 'db',
                type: 'int',
                dbType: 'int'
            };

            res = layout.isValidStudioField(testMeta);
            expect(res).toBeFalsy();
        });
    });

    describe('addValidationTasks', function() {
        var bean;
        beforeEach(function() {
            sinon.collection.stub(layout, '_validateTableHeader', function() {});
            sinon.collection.stub(layout, '_validateTileOptionsHeader', function() {});
            sinon.collection.stub(layout, '_validateTileOptionsBody', function() {});
            sinon.collection.stub(layout, '_validateRecordsDisplayed', function() {});
            sinon.collection.stub(layout, '_validateNbFieldsInTileOptions', function() {});
        });
        describe('when bean is not undefined', function() {
            beforeEach(function() {
                bean = app.data.createBean(layout.module, {
                    enabled: true,
                    enabled_module: 'Cases',
                    table_header: 'status',
                    tile_header: 'name',
                    tile_body_fields: ['account_name', 'priority'],
                    records_per_column: '10'
                });

                sinon.collection.stub(bean, 'addValidationTask', function() {});
            });

            it('should call bean.addValidationTask method', function() {
                layout.addValidationTasks(bean);

                expect(bean.addValidationTask).toHaveBeenCalledWith('check_table_header');
                expect(bean.addValidationTask).toHaveBeenCalledWith('check_tile_header');
                expect(bean.addValidationTask).toHaveBeenCalledWith('check_tile_body_fields');
                expect(bean.addValidationTask).toHaveBeenCalledWith('check_records_displayed');
            });

            describe('when layout.fieldsAllowedInTileBody is greater than 0', function() {
                it('should call bean.addValidationTask method with check_nb_fields_in_tile_body_fields', function() {
                    layout.fieldsAllowedInTileBody = 5;
                    layout.addValidationTasks(bean);

                    expect(bean.addValidationTask).toHaveBeenCalledWith('check_nb_fields_in_tile_body_fields');
                });
            });

            describe('when layout.fieldsAllowedInTileBody is not greater than 0', function() {
                it('should call bean.addValidationTask method with check_nb_fields_in_tile_body_fields', function() {
                    layout.fieldsAllowedInTileBody = 0;
                    layout.addValidationTasks(bean);

                    expect(bean.addValidationTask).not.toHaveBeenCalledWith('check_nb_fields_in_tile_body_fields');
                });
            });
        });

        describe('when bean is undefined', function() {
            var model;
            beforeEach(function() {
                bean = undefined;
                layout.collection = {
                    models: [
                        {
                            addValidationTask: sinon.collection.stub()
                        }
                    ],
                    off: function() {}
                };

                model = layout.collection.models[0];
            });

            it('should call bean.addValidationTask method', function() {
                layout.addValidationTasks(bean);

                expect(model.addValidationTask).toHaveBeenCalledWith('check_table_header');
                expect(model.addValidationTask).toHaveBeenCalledWith('check_tile_header');
                expect(model.addValidationTask).toHaveBeenCalledWith('check_tile_body_fields');
                expect(model.addValidationTask).toHaveBeenCalledWith('check_records_displayed');
            });

            describe('when layout.fieldsAllowedInTileBody is greater than 0', function() {
                it('should call bean.addValidationTask method with check_nb_fields_in_tile_body_fields', function() {
                    layout.fieldsAllowedInTileBody = 5;
                    layout.addValidationTasks(bean);

                    expect(model.addValidationTask).toHaveBeenCalledWith('check_nb_fields_in_tile_body_fields');
                });
            });

            describe('when layout.fieldsAllowedInTileBody is not greater than 0', function() {
                it('should call bean.addValidationTask method with check_nb_fields_in_tile_body_fields', function() {
                    layout.fieldsAllowedInTileBody = 0;
                    layout.addValidationTasks(bean);

                    expect(model.addValidationTask).not.toHaveBeenCalledWith('check_nb_fields_in_tile_body_fields');
                });
            });
        });
    });

    describe('_validateTableHeader', function() {
        var fields;
        var errors;
        var callback;
        beforeEach(function() {
            fields = {
                name: 'table_header',
                type: 'enum',
                vname: 'LBL_PIPELINE_TABLE_HEADER'
            };

            errors = {
                table_header: {
                    required: false
                }
            };

            callback = sinon.collection.stub();
        });

        describe('when table_header is empty', function() {
            it('should set table_header required as true', function() {
                layout.get = function() {return;};
                layout._validateTableHeader(fields, errors, callback);

                expect(errors.table_header.required).toBe(true);
            });
        });

        describe('when table_header is not empty', function() {
            it('should not set table_header required as true', function() {
                layout.get = function() {
                    return 'test';
                };
                layout._validateTableHeader(fields, errors, callback);

                expect(errors.table_header.required).toBe(false);
            });
        });

        it('should make the callback', function() {
            layout.get = function() {return;};
            layout._validateTableHeader(fields, errors, callback);

            expect(callback).toHaveBeenCalledWith(null, fields, errors);
        });
    });

    describe('_validateTileOptionsHeader', function() {
        var fields;
        var errors;
        var callback;
        beforeEach(function() {
            fields = {
                name: 'tile_header',
                type: 'enum',
                vname: 'LBL_PIPELINE_TILE_HEADER'
            };

            errors = {
                tile_header: {
                    required: false
                }
            };

            callback = sinon.collection.stub();
        });

        describe('when tile_header is empty', function() {
            it('should set tile_header required as true', function() {
                layout.get = function() {
                    return;
                };
                layout._validateTileOptionsHeader(fields, errors, callback);

                expect(errors.tile_header.required).toBe(true);
            });
        });

        describe('when tile_header is not empty', function() {
            it('should not set tile_header required as true', function() {
                layout.get = function() {
                    return 'test';
                };
                layout._validateTileOptionsHeader(fields, errors, callback);

                expect(errors.tile_header.required).toBe(false);
            });
        });

        it('should make the callback', function() {
            layout.get = function() {
            };
            layout._validateTileOptionsHeader(fields, errors, callback);

            expect(callback).toHaveBeenCalledWith(null, fields, errors);
        });
    });

    describe('_validateTileOptionsBody', function() {
        var fields;
        var errors;
        var callback;
        beforeEach(function() {
            fields = {
                name: 'tile_body_fields',
                type: 'enum',
                vname: 'LBL_PIPELINE_TILE_BODY'
            };

            errors = {
                tile_body_fields: {
                    required: false
                }
            };

            callback = sinon.collection.stub();
        });

        describe('when tile_body_fields is empty', function() {
            it('should set tile_body_fields required as true', function() {
                layout.get = function() {
                    return;
                };
                layout._validateTileOptionsBody(fields, errors, callback);

                expect(errors.tile_body_fields.required).toBe(true);
            });
        });

        describe('when tile_body_fields is not empty', function() {
            it('should not set tile_body_fields required as true', function() {
                layout.get = function() {
                    return 'test';
                };
                layout._validateTileOptionsBody(fields, errors, callback);

                expect(errors.tile_body_fields.required).toBe(false);
            });
        });

        it('should make the callback', function() {
            layout.get = function() {
            };
            layout._validateTileOptionsBody(fields, errors, callback);

            expect(callback).toHaveBeenCalledWith(null, fields, errors);
        });
    });

    describe('_validateNbFieldsInTileOptions', function() {
        var fields;
        var errors;
        var callback;
        beforeEach(function() {
            fields = {
                name: 'tile_body_fields',
                type: 'enum',
                vname: 'LBL_PIPELINE_TILE_BODY'
            };

            errors = {
                tile_body_fields: {
                    tooManyFields: false
                }
            };

            callback = sinon.collection.stub();
        });

        describe('when nbFields is more than allowed', function() {
            it('should set tile_body_fields required as true', function() {
                sinon.collection.stub(layout.model, 'get', function() {
                    return {
                        length: 5
                    };
                });
                layout.nbFieldsAllowed = 2;
                layout._validateNbFieldsInTileOptions(fields, errors, callback);

                expect(errors.tile_body_fields.tooManyFields).toBe(true);
            });
        });

        describe('when nbFields is less than or equal to allowed', function() {
            it('should not set tile_body_fields required as true', function() {
                sinon.collection.stub(layout.model, 'get', function() {
                    return {
                        length: 1
                    };
                });
                layout.nbFieldsAllowed = 2;
                layout._validateNbFieldsInTileOptions(fields, errors, callback);

                expect(errors.tile_body_fields.tooManyFields).toBe(false);
            });
        });

        it('should make the callback', function() {
            sinon.collection.stub(layout.model, 'get', function() {
                return {
                    length: 1
                };
            });
            layout.nbFieldsAllowed = 2;
            layout._validateNbFieldsInTileOptions(fields, errors, callback);

            expect(callback).toHaveBeenCalledWith(null, fields, errors);
        });
    });

    describe('_validateRecordsDisplayed', function() {
        var fields;
        var errors;
        var callback;
        beforeEach(function() {
            fields = {
                name: 'records_per_column',
                vname: 'TEST'
            };

            errors = {
                records_per_column: {
                    required: false
                }
            };

            callback = sinon.collection.stub();
        });

        describe('when records_per_column is empty', function() {
            it('should set records_per_column required as true', function() {
                layout.get = function() {
                    return;
                };
                layout._validateRecordsDisplayed(fields, errors, callback);

                expect(errors.records_per_column.required).toBe(true);
            });
        });

        describe('when records_per_column is not empty', function() {
            it('should not set records_per_column required as true', function() {
                layout.get = function() {
                    return 'test';
                };
                layout._validateRecordsDisplayed(fields, errors, callback);

                expect(errors.records_per_column.required).toBe(false);
            });
        });

        it('should make the callback', function() {
            layout.get = function() {
            };
            layout._validateRecordsDisplayed(fields, errors, callback);

            expect(callback).toHaveBeenCalledWith(null, fields, errors);
        });
    });

    describe('_dispose', function() {
        beforeEach(function() {
            sinon.collection.stub(layout.context, 'off', function() {});
            sinon.collection.stub(layout, '_super');
            layout._dispose();
        });

        it('should call layout.context.off method', function() {

            expect(layout.context.off).toHaveBeenCalledWith('pipeline:config:model:add');
            expect(layout.context.off).toHaveBeenCalledWith('pipeline:config:model:remove');
        });

        it('should call layout._super method', function() {

            expect(layout._super).toHaveBeenCalledWith('_dispose');
        });

    });
});
