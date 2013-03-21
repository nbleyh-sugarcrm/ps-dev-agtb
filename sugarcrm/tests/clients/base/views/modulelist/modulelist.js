describe("Module List", function() {
    var moduleName = 'Cases',
        viewName = 'modulelist';

    beforeEach(function() {
        SugarTest.testMetadata.init();
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base');
        SugarTest.loadComponent('base', 'view', viewName);
        SugarTest.testMetadata.set();
    });

    afterEach(function() {
        SugarTest.testMetadata.dispose();
    });

    describe('Render', function() {
        var view, isAuthenticatedStub, getModuleNamesStub, modStrings;

        beforeEach(function() {
            view = SugarTest.createView("base", moduleName, "modulelist", null, null);
            isAuthenticatedStub = sinon.stub(SugarTest.app.api, 'isAuthenticated', function() {
                return true;
            });
            getModuleNamesStub = sinon.stub(SugarTest.app.metadata, 'getModuleNames', function() {
                return {
                    Accounts: 'Accounts',
                    Bugs: 'Bugs',
                    Calendar: 'Calendar',
                    Calls: 'Calls',
                    Campaigns: 'Campaigns',
                    Cases: 'Cases',
                    Contacts: 'Contacts',
                    Forecasts: 'Forecasts',
                    Home: 'Home',
                    Opportunities: 'Opportunities',
                    Prospects: 'Prospects',
                    Reports: 'Reports',
                    Tasks: 'Tasks'
                }
            });
            modStrings = sinon.stub(SugarTest.app.metadata, 'getStrings', function() {
                return {
                    Accounts: {}
                }
            });
        });

        afterEach(function() {
            view.dispose();
            modStrings.restore();
            isAuthenticatedStub.restore();
            getModuleNamesStub.restore();
        });

        it("Should display all the modules in the module list metadata", function() {
            var modules = SugarTest.app.metadata.getModuleNames();

            view.render();

            _.each(modules, function(module, key) {
                expect(view.$el.find("[data-module='" + module+"']").length).not.toBe(0);
            });
        });

        it("Should select Cases module to be currently active module", function() {
            var getModuleStub = sinon.stub(SugarTest.app.controller.context, 'get', function() {
                    return moduleName;
                });

            view.activeModule._moduleList = view;
            view.render();

            expect(view.activeModule.isActive(view.$el.find("[data-module='" + moduleName+"']"))).toBe(true);

            getModuleStub.restore();
        });

        it("Should know that Contacts module is next to the Cases module", function() {
            var getModuleStub = sinon.stub(SugarTest.app.controller.context, 'get', function() {
                return moduleName;
            });

            view.activeModule._moduleList = view;
            view.render();

            expect(view.activeModule.isNext(view.$el.find("[data-module='Contacts']"))).toBe(true);

            getModuleStub.restore();
        });
        it("Should populate favorites and call favorite populate callback", function() {
            var cbMock = sinon.mock();
            var module = 'Accounts';
            var beanCreateMock = sinon.stub(SugarTest.app.data,'createBeanCollection', function() {
               var collection = new Backbone.Collection();
                collection.url = "/test/url";
                return collection;
            });
            // Workaround because router not defined yet
            var oRouter = SugarTest.app.router;
            SugarTest.app.router = {buildRoute: function(){}};
            sinon.stub(SugarTest.app.router,'buildRoute',function(){
                        return 'testRouteString';
                    }
            );

            view.activeModule._moduleList = view;
            view.render();

            SugarTest.seedFakeServer();
            SugarTest.server.respondWith("GET", /.*\/test\/url.*/,
                [200, {  "Content-Type": "application/json"},
                    JSON.stringify( [
                            new Backbone.Model({
                                id:'model1',
                                name:'model1'
                            }),
                            new Backbone.Model({
                                id:'model2',
                                name:'model2'
                            })
                        ]
                    )]);

            view.populateFavorites(module, cbMock);
            SugarTest.server.respond();
            expect(cbMock).toHaveBeenCalled();
            expect(view.$el.find("[data-module='Accounts']").find('.favoritesContainer').find('li').length).toEqual(2);
            beanCreateMock.restore();
            SugarTest.app.router = oRouter;
        });
        it("Should populate Recents and call recents populate callback", function() {
            var cbMock = sinon.mock();
            var module = 'Accounts';
            var beanCreateMock = sinon.stub(SugarTest.app.data,'createBeanCollection', function(module, models) {
                var collection = new Backbone.Collection(models);
                return collection;
            });
            // Workaround because router not defined yet
            var oRouter = SugarTest.app.router;
            SugarTest.app.router = {buildRoute: function(){}};
            sinon.stub(SugarTest.app.router,'buildRoute',function(){
                    return 'testRouteString';
                }
            );

            view.activeModule._moduleList = view;
            view.render();

            SugarTest.seedFakeServer();
            SugarTest.server.respondWith("POST", /.*\/Accounts.*/,
                [200, {  "Content-Type": "application/json"},
                    JSON.stringify( {
                        records: [
                            new Backbone.Model({
                                id:'model1',
                                name:'model1'
                            }),
                            new Backbone.Model({
                                id:'model2',
                                name:'model2'
                            })
                        ]
                    }
                    )]);

            view.populateRecents(module, cbMock);
            SugarTest.server.respond();
            expect(cbMock).toHaveBeenCalled();
            expect(view.$el.find("[data-module='Accounts']").find('.recentContainer').find('li').length).toEqual(2);
            beanCreateMock.restore();
            SugarTest.app.router = oRouter;
        });
        it("Should be able to filter menu items by acl", function() {
            sinon.stub(SugarTest.app.acl, 'hasAccess', function(action,module) {
                if (module == 'noAccess' || action =='edit') {
                    return false;
                } else {
                    return true;
                }
            });
            var meta = [
                {
                    label: 'blah',
                    acl_action: 'edit',
                    module:'test'
                },
                {
                    label: 'blah',
                    acl_action: 'edit',
                    module:'noAccess'
                },
                {
                    label: 'blah',
                    acl_action: 'read',
                    module:'testModule'
                }
            ];
            var result = view.filterAvailableMenuActions(meta);
            meta.shift();
            meta.shift();
            expect(result).toEqual(meta);
            SugarTest.app.acl.hasAccess.restore();
        });
        it("should trigger data event on click of action links", function() {
            var cbspy = sinon.spy();

            SugarTest.app.events.register("sugar:app:testEvent", view);
            SugarTest.app.events.on("sugar:app:testEvent", cbspy, view);
            var testEl = $('<li class="dropdown" data-module="testModule"><div><div><div><div data-event="sugar:app:testEvent"></div></div></div></div></li>');
                view.$el.append(testEl);
            var event = {
                currentTarget:testEl.find('[data-event=\'sugar:app:testEvent\']')
            };
            view.handleMenuEvent(event);
            expect(cbspy).toHaveBeenCalled();
            expect(cbspy).toHaveBeenCalledWith("testModule", event);
            SugarTest.app.events.unregister(view,"sugar:app:testEvent");
        });
    });

    describe("handle data route events", function() {
        var view,
            loadUrlStub,
            navigateStub,
            oRouter;

        beforeEach(function() {
            // Workaround because router not defined yet
            oRouter              = SugarTest.app.router;
            SugarTest.app.router = {navigate: function() {}};

            view         = SugarTest.createView("base", moduleName, "modulelist", null, null);
            loadUrlStub  = sinon.stub(Backbone.history, "loadUrl");
            navigateStub = sinon.stub(SugarTest.app.router, "navigate");
        });

        afterEach(function() {
            SugarTest.app.router = oRouter;
            view.dispose();
            loadUrlStub.restore();
            navigateStub.restore();
        });

        it("should not call navigate or loadUrl when data-route is empty", function() {
            var link = $('<a href="#Contacts" data-route="">Contacts</a>');

            view.$el.append(link);
            link.click();

            expect(loadUrlStub).not.toHaveBeenCalled();
            expect(navigateStub).not.toHaveBeenCalled();
        });

        it("should call loadUrl when data-route matches the current route", function() {
            var link              = $('<a href="#Contacts" data-route="#Contacts">Contacts</a>'),
                getFragmentStub = sinon.stub(Backbone.history, "getFragment", function() {
                    return "Contacts";
                });

            view.$el.append(link);
            link.click();

            expect(loadUrlStub).toHaveBeenCalled();
            expect(navigateStub).not.toHaveBeenCalled();

            getFragmentStub.restore();
        });

        it("should call navigate when data-route is a new route", function() {
            var link            = $('<a href="#Contacts" data-route="#Contacts">Contacts</a>'),
                getFragmentStub = sinon.stub(Backbone.history, "getFragment", function() {
                return "Accounts";
            });

            view.$el.append(link);
            link.click();

            expect(loadUrlStub).not.toHaveBeenCalled();
            expect(navigateStub).toHaveBeenCalled();

            getFragmentStub.restore();
        });
    });
});
