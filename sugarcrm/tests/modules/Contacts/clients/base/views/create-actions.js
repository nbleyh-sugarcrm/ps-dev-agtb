//FILE SUGARCRM flav=ent ONLY
describe("Contacts Create View", function() {
    var moduleName = 'Contacts',
        app,
        viewName = 'create-actions',
        view,
        stub_serverInfo;

    beforeEach(function() {
        SugarTest.testMetadata.init();
        SugarTest.loadComponent('base', 'view', 'record', moduleName);
        SugarTest.loadComponent('base', 'view', viewName, moduleName);
        SugarTest.testMetadata.addViewDefinition(viewName, {
            "panels": [
                {
                    "name": "panel_header",
                    "header": true,
                    "fields": ["name"]
                },
                {
                    "name": "panel_body",
                    "label": "LBL_PANEL_2",
                    "columns": 1,
                    "labels": true,
                    "labelsOnTop": false,
                    "placeholders": true,
                    //Portal specific fields
                    "fields": ["portal_name", "portal_active"]
                },
                {
                    "name": "panel_hidden",
                    "hide": true,
                    "labelsOnTop": false,
                    "placeholders": true,
                    "fields": ["created_by", "date_entered", "date_modified", "modified_user_id"]
                }
            ]
        }, moduleName);
        SugarTest.testMetadata.set();
        SugarTest.app.data.declareModels();
        app = SugarTest.app;

        //Fake portal is inactive
        stub_serverInfo = sinon.stub(app.metadata, "getServerInfo", function() {
            var fakeInfo = {
                portal_active: false
            };
            return fakeInfo;
        });
        view = SugarTest.createView("base", moduleName, viewName, null, null);
    });

    afterEach(function() {
        view.dispose();
        view = null;
        stub_serverInfo.restore();
    });

    describe('Render', function() {
        it("Should not render portal fields if portal is disabled", function() {
            expect(_.size(view.meta.panels[0].fields)).toEqual(1);
            expect(_.size(view.meta.panels[1].fields)).toEqual(0);
            expect(_.size(view.meta.panels[2].fields)).toEqual(4);
        });
    });
});
