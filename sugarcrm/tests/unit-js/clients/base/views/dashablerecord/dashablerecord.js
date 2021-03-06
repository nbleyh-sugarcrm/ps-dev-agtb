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
describe('Base.View.Dashablerecord', function() {
    var app;
    var context;
    var dashboard;
    var dashletToolbar;
    var dashletToolbarContext;
    var getComponentStub;
    var getComponentStubListBottomReturn;
    var layout;
    var layoutName = 'dashlet';
    var moduleName = 'Cases'; // important: visible is true in metadata.json for Cases, so we can select it as a module
    var recordMeta = {
        buttons: [
            {
                name: 'save_button',
                type: 'button',
            },
            {
                name: 'a_button_we_dont_want',
                type: 'button',
            },
            {
                name: 'cancel_button',
                type: 'button',
            },
            {
                name: 'dropdown',
                type: 'actiondropdown',
                buttons: [
                    {
                        name: 'action_we_dont_want',
                        type: 'rowaction',
                    },
                    {
                        name: 'edit_button',
                        type: 'rowaction',
                    }
                ],
            }
        ],
        panels: [
            {
                name: 'panel_header',
                fields: [
                    {
                        name: 'picture',
                        type: 'avatar',
                        size: 'large',
                    },
                    {
                        name: 'name',
                    },
                    {
                        name: 'favorite',
                        type: 'favorite',
                    },
                    {
                        name: 'follow',
                        type: 'follow',
                    }
                ],
                header: true,
            },
            {
                name: 'businesscard',
                fields: [
                    {
                        name: 'case_number',
                        readonly: true,
                    }
                ],
            }
        ]
    };
    var view;
    var viewName = 'dashablerecord';

    beforeEach(function() {
        app = SugarTest.app;

        SugarTest.loadComponent('base', 'view', 'record');
        SugarTest.loadComponent('base', 'view', viewName);
        SugarTest.loadComponent('base', 'layout', 'dashlet');
        SugarTest.loadComponent('base', 'layout', 'dashboard');

        SugarTest.testMetadata.init();
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base', 'dashablerecord');
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base', 'businesscard');
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base', 'headerpane');
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base', 'noaccess');
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base', 'tabs');
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base', 'record');
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base', 'records');
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base', 'dashlet-config');

        SugarTest.testMetadata.addViewDefinition(
            'record',
            recordMeta,
            moduleName
        );
        SugarTest.testMetadata.set();
        app.data.declareModels();
        SugarTest.loadPlugin('Dashlet');

        context = app.context.getContext();
        context.set({
            module: moduleName,
            layout: layoutName
        });
        context.parent = app.data.createBean('Home');
        context.parent.set('dashboard_module', moduleName);
        context.prepare();

        dashboard = app.view.createLayout({
            name: 'dashboard',
            type: 'dashboard',
            context: context.parent,
        });

        layout = app.view.createLayout({
            name: layoutName,
            context: context,
            meta: {index: 0},
            layout: dashboard,
        });

        view = SugarTest.createView(
            'base',
            moduleName,
            viewName,
            {module: moduleName},
            context,
            false,
            layout
        );

        dashletToolbarContext = new app.Context();
        dashletToolbar = {
            context: dashletToolbarContext,
            editClicked: sinon.collection.stub(),
            setButtonStates: sinon.collection.stub()
        };
        getComponentStub = sinon.collection.stub(layout, 'getComponent');
        getComponentStub.withArgs('dashlet-toolbar').returns(dashletToolbar);
        getComponentStubListBottomReturn = {hide: function() {}, show: function() {}};
        getComponentStub.withArgs('list-bottom').returns(getComponentStubListBottomReturn);
    });

    afterEach(function() {
        sinon.collection.restore();
        app.data.reset();
        view.dispose();
        layout.dispose();
        dashboard.dispose();
        SugarTest.testMetadata.dispose();
        app.cache.cutAll();
        app.view.reset();
        Handlebars.templates = {};
        view = null;
        layout = null;
        dashboard = null;
    });

    describe('Plugins', function() {
        it('should have most record view and list view plugins', function() {
            expect(view.plugins.sort()).toEqual([
                'Dashlet',
                'Editable',
                'ErrorDecoration',
                'GridBuilder',
                'Pagination',
                'SugarLogic',
                'ToggleMoreLess',
            ]);
        });
    });

    describe('initDashlet', function() {
        // commented out because we arne't supporting changing the base module right now
        // it('should update the module on settings change if in configuration mode', function() {
        //     view.initDashlet('config');
        //     view.settings.trigger('change:module', app.data.createBean('Home'), 'Cases');
        //     expect(view.dashModel.get('module')).toEqual('Cases');
        // });

        it('should show a warning if there are too many tabs in configuration mode', function() {
            view.initDashlet('config');
            view._tabLimit = {
                number: 2,
                label: 'LBL_TWO'
            };
            var alertStub = sinon.collection.stub(app.alert, 'show');
            var langStub = sinon.collection.stub(app.lang, 'get');
            var msg = 'TWO (2) tabs is the most! You chose too many!';
            langStub.withArgs('LBL_TWO').returns('TWO');
            langStub.withArgs(
                'TPL_DASHLET_RECORDVIEW_TOO_MANY_TABS',
                null,
                {num: 2, numWord: 'TWO'}
            ).returns(msg);
            sinon.collection.stub(dashletToolbarContext, 'trigger');
            var dummyDashletConfiguration = {trigger: sinon.collection.stub()};
            var closestComponentStub = sinon.collection.stub(view, 'closestComponent');
            closestComponentStub.withArgs('dashletconfiguration')
                .returns(dummyDashletConfiguration);
            var dummyConfigDashlet = {context: {trigger: sinon.collection.stub()}};
            getComponentStub.withArgs('dashlet').returns(dummyConfigDashlet);

            view.settings.trigger('change:tab_list', view.model, ['', 'accounts', 'contacts']);

            expect(alertStub).toHaveBeenCalledWith(
                'too_many_tabs',
                {
                    level: 'warning',
                    messages: msg
                }
            );
            expect(dummyConfigDashlet.context.trigger).toHaveBeenCalledWith('dashablerecord:config:tablist:change');
        });

        describe('pseudo dashlet', function() {
            describe('tab change', function() {
                it('should rerender when the tabs are changed', function() {
                    var renderStub = sinon.collection.stub(view, 'render');
                    view.meta.pseudo = true;
                    var tab = {
                        label: 'This Case',
                        link: 'Cases',
                        module: 'Cases',
                        relatedModule: 'This Case',
                        skipFetch: true
                    };

                    view.initDashlet('main');
                    view.meta.tabs = [tab];

                    expect(view.meta.tabs).toEqual([tab]);
                    expect(renderStub).toHaveBeenCalled();
                });
            });
        });
    });

    describe('initTabSugarLogic', function() {
        var initStub;
        var defaultCollection;

        beforeEach(function() {
            defaultCollection = view.collection;
            initStub = sinon.collection.stub(view, 'initSugarLogic').returns({
                model: view.model,
                collection: view.collection
            });
        });

        afterEach(function() {
            view.collection = defaultCollection;
        });

        it('should re-initialize the SugarLogic with a new model', function() {
            view.initTabSugarLogic();
            expect(initStub).toHaveBeenCalled();
            expect(view._slCtx.model.cid).toEqual(view.model.cid);
        });

        it('should not re-initialize the SugarLogic plugin with the same model', function() {
            view.model.hasSugarLogicEvents = true;
            view.initTabSugarLogic();
            expect(initStub).not.toHaveBeenCalled();
        });

        it('should create a standard collection for the plugin if there is none', function() {
            view.collection = undefined;
            view.initTabSugarLogic();
            expect(view.collection instanceof Backbone.Collection).toBe(true);
        });
    });

    describe('_getContextModel', function() {
        it('should clone context models before returning', function() {
            var cloneStub = sinon.collection.stub(view, '_cloneModel');
            view._getContextModel();
            expect(cloneStub).toHaveBeenCalled();
        });
    });

    describe('_loadContextModel', function() {
        it('should fetch the context model', function() {
            var contextModel = app.data.createBean(moduleName);
            var fetchStub = sinon.collection.stub(contextModel, 'fetch');
            sinon.collection.stub(view, '_getContextModel').returns(contextModel);
            view._loadContextModel();
            expect(fetchStub).toHaveBeenCalled();
        });
    });

    describe('_syncIdsToModels', function() {
        it('should set ids for related record view tab models', function() {
            var model = app.data.createBean(moduleName, {accounts: {name: 'asdf', id: 'id1'}});
            view.tabs = [{type: 'record', link: 'accounts', model: null}];
            sinon.collection.stub(view, '_getContextModel').returns(model);
            view._syncIdsToModels(model);
            expect(view.tabs[0].model.get('id')).toEqual('id1');
        });
    });

    describe('_setDataView', function() {
        it('should set view option on the model based on metadata existing', function() {
            var model = app.data.createBean(moduleName);
            view._setDataView(model);
            expect(model.getOption('view')).toEqual('record');
            sinon.collection.stub(app.metadata, 'getView').returns({}); // it returns something
            view._setDataView(model);
            expect(model.getOption('view')).toEqual('recorddashlet');
        });
    });

    describe('_getRelateFieldsForContextModel', function() {
        it('should return relate fields based off tab links', function() {
            var contextModel = app.data.createBean(moduleName);
            sinon.collection.stub(view, '_getContextModel').returns(contextModel);
            var tabs = [{
                type: 'record',
                link: 'accounts'
            }];
            view.settings.set('tabs', tabs);
            var fields = {
                account_name: {
                    name: 'account_name',
                    link: 'accounts',
                }
            };
            sinon.collection.stub(app.metadata, 'getModule').returns(fields);
            expect(view._getRelateFieldsForContextModel()).toEqual(['account_name']);
        });
    });

    describe('_cloneModel', function() {
        it('should copy all attributes including id to a new model', function() {
            var modelToClone = app.data.createBean(moduleName, {id: 'id1', name: 'case1'});
            var clonedModel = view._cloneModel(modelToClone);
            expect(clonedModel.get('id')).toEqual('id1');
            expect(clonedModel.get('name')).toEqual('case1');
        });
    });

    describe('bindDataChange', function() {
        var renderStub;
        var switchModelStub;
        var triggerStub;
        var model;

        var headerFields = [
            {
                name: 'picture',
                type: 'avatar',
                size: 'button', // test that we shrank the avatar for the smaller dashlet toolbar
                height: 28,
                width: 28, // default the size
            },
            {
                name: 'name',
                type: 'name',
                label: 'LBL_SUBJECT',
            },
        ];

        var headerButtons = [
            {
                name: 'dashlet_save_button',
                type: 'button'
            },
            {
                name: 'dashlet_cancel_button',
                type: 'button'
            },
            {
                name: 'dashlet_dropdown',
                type: 'actiondropdown',
                buttons: [
                    {
                        name: 'edit_button',
                        type: 'rowaction'
                    }
                ]
            }
        ];

        beforeEach(function() {
            switchModelStub = sinon.collection.stub(view, 'switchModel', function(model) { this.model = model; });
            renderStub = sinon.collection.stub(view, 'render');
            triggerStub = sinon.collection.stub(dashletToolbarContext, 'trigger');
            model = app.data.createBean(moduleName);
            view.meta = {};
            view.tabs = [{type: 'record'}];
            view.bindDataChange();
        });

        it('should update the model, trigger dashlet:toolbar:change, and then re-render on change:model', function() {
            view.context.trigger('change:model', view.context, model);

            expect(switchModelStub).toHaveBeenCalledWith(model);
            expect(triggerStub).toHaveBeenCalledWith(
                'dashlet:toolbar:change',
                headerFields,
                headerButtons,
                model
            );
            expect(renderStub).toHaveBeenCalled();
        });

        it('should not send any buttons if we lack ACL access', function() {
            sinon.collection.stub(app.acl, 'hasAccessToModel').withArgs('edit', model).returns(false);
            view.context.trigger('change:model', view.context, model);

            expect(switchModelStub).toHaveBeenCalledWith(model);
            expect(triggerStub).toHaveBeenCalledWith('dashlet:toolbar:change', headerFields, [], model);
        });
    });

    describe('editClicked', function() {
        it('should proxy to the dashlet toolbar', function() {
            view.editClicked();
            expect(dashletToolbar.editClicked).toHaveBeenCalled();
        });
    });

    describe('switchModel', function() {
        it('should abort any ongoing fetches, stop listening to the old model, and change to the new one', function() {
            var oldModel = view.model;
            var abortFetchRequestStub = sinon.collection.stub(oldModel, 'abortFetchRequest');
            var stopListeningStub = sinon.collection.stub(view, 'stopListening');

            var newModel = app.data.createBean(moduleName, {id: '12345'});
            view.switchModel(newModel);

            expect(abortFetchRequestStub).toHaveBeenCalledOnce();
            expect(stopListeningStub).toHaveBeenCalledWith(oldModel);
            expect(view.model).toBe(newModel);
        });
    });

    describe('rendering', function() {
        var unavailableModuleMsg = 'This module is not available';

        beforeEach(function() {
            var langStub = sinon.collection.stub(app.lang, 'get');
            langStub.withArgs('LBL_DASHLET_MODULE_UNAVAILABLE')
                .returns(unavailableModuleMsg);
        });

        it('should show the no access template if the module is not available', function() {
            view.moduleIsAvailable = false;

            view.render();

            expect(view.$('.block-footer').text().trim()).toEqual(unavailableModuleMsg);
        });

        it('should show the no access template if there is no model to show', function() {
            view.moduleIsAvailable = true;
            delete view.model;

            view.render();

            expect(view.$('.block-footer').text().trim()).toEqual(unavailableModuleMsg);
        });

        it('should set showTabs based on number of tabs', function() {
            sinon.collection.stub(view, '_getToolbar');
            view.tabs = [{}];
            view.render();
            expect(view.showTabs).toBeFalsy();
            view.tabs = [{}, {}];
            view.render();
            expect(view.showTabs).toBeTruthy();
        });

        describe('main mode', function() {
            beforeEach(function() {
                view.moduleIsAvailable = true;
                view.model = app.data.createBean(view.module, {name: 'Test'});
                view.model.dataFetched = true;
                view.settings.set('module', moduleName);
                view.settings.set('tabs', [moduleName]);
                view.settings.set('activeTab', {type: 'record'});
                view._initTabs();
            });

            it('should update the toolbar header', function() {
                sinon.collection.stub(view, 'adjustHeaderpane');
                view.initDashlet('main');
                var triggerStub = sinon.collection.stub(dashletToolbarContext, 'trigger');
                var tab = {
                    label: 'This Case',
                    link: 'Cases',
                    module: 'Cases',
                    relatedModule: 'This Case',
                    skipFetch: true,
                    type: 'record'
                };
                var getActiveTabStub = sinon.collection.stub(view, '_getActiveTab').returns(tab);
                view.model = app.data.createBean(moduleName);
                view.module = moduleName;
                view.render();
                // we test the precise field list elsewhere, no need to do it again here
                expect(triggerStub).toHaveBeenCalledWith('dashlet:toolbar:change');
            });

            it('should hide the list-bottom view', function() {
                view.moduleIsAvailable = false;
                var _showHideListBottomStub = sinon.collection.stub(view, '_showHideListBottom');
                view.render();

                expect(_showHideListBottomStub).toHaveBeenCalledWith(null, true);
            });
        });
    });

    describe('_initTabs', function() {
        var hasAccessStub;
        beforeEach(function() {
            sinon.collection.stub(view, '_getTabsFromSettings').returns(null);
            hasAccessStub = sinon.collection.stub(app.acl, 'hasAccess');
            hasAccessStub.withArgs('view', 'Cases').returns(true);
            hasAccessStub.withArgs('view', 'Accounts').returns(false);
            hasAccessStub.withArgs('view', 'Contacts').returns(true);
            sinon.collection.stub(view, '_cloneModel').returns(app.data.createBean(moduleName));
        });

        it('should create tabs from metadata', function() {
            var createCollectionStub = sinon.collection.stub(view, '_createCollection').returns({
                link: {name: 'contacts'}
            });
            var getColumnsStub = sinon.collection.stub(view, '_getColumnsForTab').returns('columns');
            view.meta.tabs = [
                {
                    link: '',
                    module: 'Cases',
                },
                {
                    link: 'contacts',
                    module: 'Contacts',
                    fields: [
                        'name',
                        'date_entered',
                    ],
                    order_by: {
                        field: 'date_entered',
                        direction: 'desc',
                    },
                    limit: 10,
                }
            ];
            view._baseModule = 'Cases';

            view._initTabs();

            expect(view.tabs.length).toEqual(2);
            expect(view.tabs[0].module).toBe('Cases');
            expect(view.tabs[0].link).toBe('');
            expect(view.tabs[0].collection).not.toBeDefined();
            expect(view.tabs[1].module).toBe('Contacts');
            expect(view.tabs[1].link).toBe('contacts');
            expect(view.tabs[1].relate).toBe(true);
            expect(view.tabs[1].collection).toBeDefined();
            expect(view.tabs[1].collection.display_columns).toBeDefined();
        });

        it('should set up the tab with template required properties', function() {
            view.meta.tabs = [
                {
                    link: '',
                    module: 'Cases'
                },
                {
                    link: 'Cases',
                    module: 'Cases'
                }
            ];

            view._initTabs();
            var tab = view.tabs[0];
            expect(tab.meta).not.toBeUndefined();
            expect(tab.type).toEqual('record');
            expect(tab.module).toEqual('Cases');

        });

        it('should set moduleIsAvailable based of ACLs', function() {
            // Has access
            view.meta.tabs = [
                {
                    module: 'Cases',
                    link: '',
                }
            ];
            view._initTabs();
            expect(view.moduleIsAvailable).toBeTruthy();

            // No access to any tab
            view.meta.tabs = [
                {
                    module: 'Accounts',
                    link: 'accounts',
                }
            ];
            view._initTabs();
            expect(view.moduleIsAvailable).toBeFalsy();

            // Access to at least 1 tab
            view.meta.tabs = [
                {
                    module: 'Cases',
                    link: '',
                },
                {
                    module: 'Accounts',
                    link: 'accounts',
                }
            ];
            view._initTabs();
            expect(view.moduleIsAvailable).toBeTruthy();
        });
    });

    describe('tabSwitcher', function() {
        var jDataStub;
        var loadDataForTabsStub;
        var tab1Collection;

        beforeEach(function() {
            jDataStub = sinon.collection.stub($.fn, 'data').returns(1);
            loadDataForTabsStub = sinon.collection.stub(view, '_loadDataForTabs');
            tab1Collection = app.data.createBeanCollection('Contacts');
            tab1Collection.dataFetched = false;
            tab1Collection.display_columns = {};
            view.tabs = [
                {},
                {
                    collection: tab1Collection
                }
            ];
        });

        it('should change tab', function() {
            view.tabSwitcher({currentTarget: '#tab1'});

            expect(view.settings.get('activeTabIndex')).toEqual(1);
            expect(view.collection).toBe(tab1Collection);
            expect(loadDataForTabsStub).toHaveBeenCalledWith([view.tabs[1]]);
        });

        it('should not switch if we are editing', function() {
            view.action = 'edit';
            expect(view.tabSwitcher()).toEqual(undefined); // undefined means the function returned nothing
        });

        describe('config mode', function() {
            var renderStub;

            beforeEach(function() {
                renderStub = sinon.collection.stub(view, 'render');
                view.initDashlet('config');

            });

            it('should not render if the active tab is the one that was clicked', function() {
                view.settings.set('activeTabIndex', 1);

                view.tabSwitcher({currentTarget: '#tab1'});

                expect(view.settings.get('activeTabIndex')).toEqual(1);
                expect(renderStub).not.toHaveBeenCalled();
            });

            it('should re-render if an inactive tab was clicked', function() {
                view.settings.set('activeTabIndex', 0);

                view.tabSwitcher({currentTarget: '#tab1'});

                expect(view.settings.get('activeTabIndex')).toEqual(1);
                expect(renderStub).toHaveBeenCalled();
            });
        });
    });

    describe('setOrderBy', function() {
        it('should change order by', function() {
            var jFindStub = sinon.collection.stub($.fn, 'find').returns({length: 0});
            var jDataStub = sinon.collection.stub($.fn, 'data').returns('name');
            var loadDataForTabsStub = sinon.collection.stub(view, '_loadDataForTabs');
            var accessStub = sinon.collection.stub(app.acl, 'hasAccess').returns(true);
            var collection = app.data.createBeanCollection('Contacts');
            var orderBy = {field: 'date_entered', direction: 'asc'};
            var tab = {
                collection: collection,
                order_by: orderBy
            };
            view.tabs = [tab];
            view.settings.set('activeTab', tab);
            view.setOrderBy({currentTarget: '#tab0'});
            expect(collection.orderBy).toEqual({field: 'name', direction: 'desc'});
            expect(loadDataForTabsStub).toHaveBeenCalledWith([view.tabs[0]]);
        });
    });

    describe('_createCollection', function() {
        it('should create collection for tab', function() {
            var getModuleStub = sinon.collection.stub(app.metadata, 'getModule').returns({
                fields: {contacts: {type: 'link'}}
            });
            var contextModel = app.data.createBean(moduleName);
            var contextModelStub = sinon.collection.stub(view, '_getContextModel').returns(contextModel);
            var createCollectionStub = sinon.collection.stub(app.data, 'createRelatedCollection');
            var tab = {link: 'contacts', module: 'Contacts'};
            view._createCollection(tab);
            expect(createCollectionStub.lastCall.args[0]).toEqual(contextModel);
            expect(createCollectionStub.lastCall.args[1]).toEqual('contacts');
        });
    });

    describe('getPaginationOptions', function() {
        it('should get the pagination options', function() {
            var tab  = {
                limit: 5,
                relate: true,
                order_by: {direction: 'asc', field: 'name'},
                include_child_items: false,
                fields: ['name']
            };
            view.tabs = [tab];
            view.settings.set('activeTab', tab);

            var result = view.getPaginationOptions();

            expect(result).toEqual({
                limit: 5,
                relate: true,
                params: {
                    order_by: 'name:asc',
                    include_child_items: null
                },
                fields: ['name']
            });
        });
    });

    describe('_loadDataForTabs', function() {
        var asyncParallelStub;

        beforeEach(function() {
            asyncParallelStub = sinon.collection.stub(async, 'parallel');
        });

        it('should load data for tabs', function() {
            var collection = app.data.createBeanCollection('Contacts');
            var tabs = [
                {collection: app.data.createBeanCollection('Contacts')},
                {collection: app.data.createBeanCollection('Tasks')}
            ];
            view._loadDataForTabs(tabs);
            expect(asyncParallelStub.lastCall.args[0].length).toEqual(2);
        });

        it('should not do anything on the pseudo config dashlet', function() {
            view.meta.pseudo = true;

            view._loadDataForTabs([{}]);

            expect(asyncParallelStub).not.toHaveBeenCalled();
        });
    });

    describe('_getColumnsForTab', function() {
        it('should return display columns for tab', function() {
            var getModuleStub = sinon.collection.stub(app.metadata, 'getModule').returns({
                fields: {subject: {}}
            });
            var fieldMetaStub = sinon.collection.stub(view, '_getFieldMetaForTab').returns(
                [{name: 'subject'}]
            );
            var tab = {fields: ['subject'], module: 'Tasks'};
            expect(view._getColumnsForTab(tab)).toEqual([{name: 'subject', sortable: true}]);
        });
    });

    describe('pseudo dashlet', function() {
        it('should be added on init in config mode', function() {
            var initComponentsStub = sinon.collection.stub(view.layout, 'initComponents');
            var dummyContext = {prepare: sinon.collection.stub()};
            sinon.collection.stub(view.context, 'getChildContext').returns(dummyContext);
            var dummyDashletConfiguration = {trigger: sinon.collection.stub()};
            var closestComponentStub = sinon.collection.stub(view, 'closestComponent');
            closestComponentStub.withArgs('dashletconfiguration')
                .returns(dummyDashletConfiguration);
            getComponentStub
                .withArgs('dashlet')
                .returns({context: {trigger: sinon.collection.stub()}});
            var _cachePseudoComponentStub = sinon.collection.stub(view, '_cachePseudoComponent');
            view.model = app.data.createBean(moduleName);
            view.initDashlet('config');
            view.settings.set('tab_list', ['Cases', 'accounts']);

            view.layout.trigger('init');

            expect(dummyContext.prepare).toHaveBeenCalled();
            expect(initComponentsStub).toHaveBeenCalledWith(
                [{
                    layout: {
                        type: 'dashlet',
                        css_class: 'dashlets',
                        config: false,
                        preview: false,
                        module: 'Cases',
                        context: view.context,
                        components: [
                            {
                                name: view.name,
                                type: view.type,
                                context: dummyContext,
                                preview: true,
                                module: 'Cases',
                                custom_toolbar: 'no',
                                view: {
                                    module: 'Cases',
                                    name: view.name,
                                    type: view.type,
                                    preview: true,
                                    context: dummyContext,
                                    custom_toolbar: 'no',
                                    tabs: view.settings.get('tabs'),
                                    pseudo: true,
                                }
                            }
                        ],
                        pseudo: true
                    }
                }],
                view.context
            );
        });
    });

    describe('dashletconfig:save event', function() {
        it('should only save a whitelisted list of settings', function() {
            var dummyDashletConfiguration = {trigger: sinon.collection.stub()};
            var closestComponentStub = sinon.collection.stub(view, 'closestComponent');
            closestComponentStub.withArgs('dashletconfiguration')
                .returns(dummyDashletConfiguration);
            view.settings.set('base_module', 'Cases');
            view.initDashlet('config');

            var tabs = [
                {
                    type: 'record',
                    label: 'TPL_DASHLET_RECORDVIEW_THIS_RECORD_TYPE',
                    module: 'Cases',
                    link: 'Cases'
                },
                {
                    type: 'record',
                    label: 'LBL_ACCOUNT',
                    module: 'Accounts',
                    link: 'accounts'
                }
            ];
            view.settings.set(
                {
                    activeTab: 1,
                    label: 'I am a record view dashlet',
                    tabs: ['Cases', 'accounts'],
                    I: 'should not be in the result'
                },
                {silent: true}
            );
            var settings = new app.Bean({
                tabs: tabs
            });
            view._pseudoDashlet = {
                settings: settings
            };
            view.layout.triggerBefore('dashletconfig:save');

            expect(view.settings.get('base_module')).toEqual(moduleName);
            expect(view.settings.get('label')).toEqual('I am a record view dashlet');
            expect(view.settings.get('tabs')).toEqual([
                {
                    type: 'record',
                    label: 'TPL_DASHLET_RECORDVIEW_THIS_RECORD_TYPE',
                    module: 'Cases',
                    link: 'Cases'
                },
                {
                    type: 'record',
                    label: 'LBL_ACCOUNT',
                    module: 'Accounts',
                    link: 'accounts'
                }
            ]);
            expect(view.settings.has('I')).toBeFalsy();
        });
    });

    describe('_updateViewToCurrentTab', function() {
        it('should set the view data', function()  {
            var model = app.data.createBean(moduleName);
            view._defaultBaseMeta = {oldMeta: 'somethingOld', panels: []};
            view.settings.set('activeTab', {
                model: model,
                link: '',
                module: moduleName,
                meta: {panels: [{fields: []}]},
                type: 'record',
            });

            view._updateViewToCurrentTab();
            expect(view.model).toEqual(model);
            expect(view.module).toEqual(moduleName);
            expect(view.meta.oldMeta).toEqual('somethingOld');
            expect(view.meta.panels).toEqual([
                {
                    fields: [],
                    labelsOnTop: true,
                    labels: true,
                    grid: []
                }
            ]);
            /*expect(view.meta).toEqual({
                oldMeta: 'somethingOld',
                panels: [{fields: [], labels: true, grid: []}]
            });*/
            expect(view.context.get('model')).toEqual(model);
            expect(view.action).toEqual('detail');
        });
        it('should', function() {
            view.settings.set('activeTab', {
                //model: model,
                link: 'cases',
                module: moduleName,
                meta: {panels: [{fields: []}]},
                type: 'list',
            });
            view._updateViewToCurrentTab();
            expect(view.action).toEqual('list');

        });
    });

    describe('_showHideListBottom', function() {
        it('should call show() when the tab is a list', function() {
            sinon.collection.spy(getComponentStubListBottomReturn, 'show');
            view._showHideListBottom({type: 'list'}, null);
            expect(getComponentStubListBottomReturn.show).toHaveBeenCalled();
        });

        it('should call hide() when the tab is a record', function() {
            sinon.collection.spy(getComponentStubListBottomReturn, 'hide');
            view._showHideListBottom({type: 'record'}, null);
            expect(getComponentStubListBottomReturn.hide).toHaveBeenCalled();
        });

        it('should call hide() when forceHide is true', function() {
            sinon.collection.spy(getComponentStubListBottomReturn, 'hide');
            view._showHideListBottom({}, true);
            expect(getComponentStubListBottomReturn.hide).toHaveBeenCalled();
        });
    });

    describe('setActiveTab', function() {
        it('should set activeTab and activeTabIndex settings', function() {
            var tab = {
                label: 'This Case',
                link: 'Cases',
                module: 'Cases',
                relatedModule: 'This Case',
                skipFetch: true
            };
            view.tabs = [tab];
            view.setActiveTab(0);
            expect(view.settings.get('activeTabIndex')).toEqual(0);
            expect(view.settings.get('activeTab')).toEqual(tab);
            view.setActiveTab(3);
            expect(view.settings.get('activeTabIndex')).toEqual(null);
            expect(view.settings.get('activeTab')).toEqual(null);
        });
    });

    describe('_shouldShowStudioText', function() {
        it('should determine if we should show the edit in studio message', function() {
            var tab = {
                type: 'record',
                module: moduleName,
            };
            view.meta.pseudo = true;
            sinon.collection.stub(app.acl, 'hasAccess').returns(true);
            sinon.collection.stub(app.metadata, 'getView').returns({});
            expect(view._shouldShowStudioText(tab)).toBeTruthy();
        });
    });

    describe('_getTabContentTemplate', function() {
        it('should get correct templates for pseudo dashlet', function() {
            view.meta.pseudo = true;
            expect(view._getTabContentTemplate('list')).toEqual(view._configListTemplate);
            expect(view._getTabContentTemplate('record')).toEqual(view._recordTemplate);
        });

        it('should get correct templates for main mode', function() {
            view._mode = 'main';
            expect(view._getTabContentTemplate('list')).toEqual(view._recordsTemplate);
            expect(view._getTabContentTemplate('record')).toEqual(view._recordTemplate);
        });
    });

    describe('_setRecordState', function() {
        it('should set the recordstate', function() {
            var activeTab = {
                type: 'record',
                model: app.data.createBean('Accounts'),
            };
            var contextModel = app.data.createBean(moduleName);
            sinon.collection.stub(view, '_getActiveTab').returns(activeTab);
            sinon.collection.stub(view, '_getContextModel').returns(contextModel);

            view._setRecordState();
            expect(view.recordState).toEqual('LOADING');

            activeTab.model.dataFetched = true;
            view._setRecordState();
            expect(view.recordState).toEqual('READY');

            activeTab.model.dataFetched = false;
            contextModel.dataFetched = true;
            view._setRecordState();
            expect(view.recordState).toEqual('NODATA');

            view.meta.pseudo = true;
            view._setRecordState();
            expect(view.recordState).toEqual('READY');
        });
    });

    describe('_patchTabsFromSettings', function() {
        it('should merge newTabs with saved tabs', function() {
            var savedTabs = [
                {
                    type: 'record',
                    link: 'Cases',
                    module: 'Cases'

                },
                {
                    type: 'list',
                    display_columns: ['name', 'case_number'],
                    link: 'bugs',
                    module: 'Bugs'
                }
            ];
            view.settings.set('tabs', savedTabs);
            var newTabs = [
                {
                    link: 'bugs',
                    module: 'Bugs'
                }
            ];

            var actual = view._patchTabsFromSettings(newTabs);
            expect(actual).toEqual([{
                type: 'list',
                display_columns: ['name', 'case_number'],
                link: 'bugs',
                module: 'Bugs'
            }]);
        });
    });

    describe('_getTabsToSave', function() {
        it('should whitelist only certain properties of tabs for saving', function() {
            var recordTab = {
                type: 'record',
                link: 'Cases',
                label: 'LBL_THIS_CASE',
                module: 'Cases',
                junk: 'junk'
            };
            var listModelAttrs = {
                fields: ['name', 'bug_number'],
                limit: 5,
                auto_refresh: 1
            };
            var listTab = {
                type: 'list',
                link: 'bugs',
                label: 'LBL_BUGS',
                module: 'Bugs',
                junk: 'junk',
                model: app.data.createBean('Bugs', listModelAttrs)
            };
            var expected = [
                {
                    type: 'record',
                    link: 'Cases',
                    label: 'LBL_THIS_CASE',
                    module: 'Cases',
                },
                {
                    type: 'list',
                    link: 'bugs',
                    label: 'LBL_BUGS',
                    module: 'Bugs',
                    fields: ['name', 'bug_number'],
                    limit: 5,
                    auto_refresh: 1
                }
            ];
            expect(view._getTabsToSave([recordTab, listTab])).toEqual(expected);
        });
    });

    describe('_getAvailableColumns', function() {
        it('should return columns for the listview', function() {
            var tab = {
                module: 'Bugs'
            };
            var expected = [
                {name: 'first_name', label: 'First Name'},
                {name: 'last_name', label: 'Last Name'},
                {name: 'email1', label: 'Email'},
                {name: 'phone_work', label: 'Phone'}
            ];
            expect(view._getFieldMetaForTab(tab)).toEqual(expected);
        });
    });

    describe('_getGridsFromPanelsMetadata', function() {
        using('Different panel settings and user preferences', [
                {
                    panelPlacement: 'field_on_top',
                    userPreference: 'field_on_top'
                },
                {
                    panelPlacement: 'field_on_top',
                    userPreference: 'field_on_side'
                },
                {
                    panelPlacement: 'field_on_side',
                    userPreference: 'field_on_top'
                },
                {
                    panelPlacement: 'field_on_side',
                    userPreference: 'field_on_side'
                },
            ], function(params) {
            it('should set panel label position to top regardless of user preference',
                function() {
                view.meta.panels = [{
                    labelsOnTop: params.panelPlacement,
                    fields: ['description']
                }];
                app.user.setPreference('field_name_placement', params.userPreference);
                view._buildGridsFromPanelsMetadata();
                _.each(view.meta.panels, function(panel) {
                    expect(panel.labelsOnTop).toEqual(true);
                });
            });
        });
    });

    // BEGIN SUGARCRM flav=ent ONLY
    describe('_addRelateFields', function() {
        it('should add relate fields', function() {
            var fields = ['assigned_user_name', 'amount'];
            var expected = ['assigned_user_name', 'amount', 'assigned_user_id', 'currency_id'];
            sinon.collection.stub(app.metadata, 'getModule').returns({
                'assigned_user_name': {
                    'type': 'relate',
                    'id_name': 'assigned_user_id'
                },
                'amount': {
                    'related_fields': [
                        'currency_id'
                    ]
                }
            });
            expect(view._addRelateFields('Opportunities', fields)).toEqual(expected);
        });

        it('should add relate fields for fields defined only in list views', function() {
            var fields = ['assigned_user_name', 'amount', 'list_view_field'];
            var expected = ['assigned_user_name', 'amount', 'list_view_field', 'assigned_user_id',
                'currency_id', 'list_view_related_1', 'list_view_related_2'];

            sinon.collection.stub(app.metadata, 'getModule').returns({
                'assigned_user_name': {
                    'type': 'relate',
                    'id_name': 'assigned_user_id'
                },
                'amount': {
                    'related_fields': [
                        'currency_id'
                    ]
                }
            });
            sinon.collection.stub(view, '_getFieldMetaForView').returns([
                {
                    'name': 'list_view_field',
                    'related_fields': [
                        'list_view_related_1',
                        'list_view_related_2'
                    ]
                }
            ]);

            expect(view._addRelateFields('Opportunities', fields)).toEqual(expected);
        });
    });
    // END SUGARCRM flav=ent ONLY
});
