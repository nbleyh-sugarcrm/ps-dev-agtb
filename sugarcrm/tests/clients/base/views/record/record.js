describe("Record View", function() {
    var moduleName = 'Cases',
        viewName = 'record',
        sinonSandbox, view;

    beforeEach(function() {
        SugarTest.testMetadata.init();
        SugarTest.loadHandlebarsTemplate('button', 'field', 'base', 'detail');
        SugarTest.loadHandlebarsTemplate('buttondropdown', 'field', 'base', 'detail');
        SugarTest.loadHandlebarsTemplate(viewName, 'view', 'base');
        SugarTest.loadComponent('base', 'field', 'button');
        SugarTest.loadComponent('base', 'field', 'buttondropdown');
        SugarTest.loadComponent('base', 'view', 'editable');
        SugarTest.loadComponent('base', 'view', viewName);
        SugarTest.testMetadata.addViewDefinition(viewName, {
            "buttons": [{
                "type":"button",
                "name":"cancel_button",
                "label":"LBL_CANCEL_BUTTON_LABEL",
                "css_class":"btn-invisible btn-link",
                "showOn":"edit"
            }, {
                "type":"buttondropdown",
                "name":"main_dropdown",
                "buttons":[{
                    "name":"edit_button",
                    "label":"LBL_EDIT_BUTTON_LABEL",
                    "primary":true,
                    "showOn":"view"
                }, {
                    "name":"save_button",
                    "label":"LBL_SAVE_BUTTON_LABEL",
                    "primary":true,
                    "showOn":"edit"
                }, {
                    "name":"delete_button",
                    "label":"LBL_DELETE_BUTTON_LABEL"
                }, {
                    "name":"duplicate_button",
                    "label":"LBL_DUPLICATE_BUTTON_LABEL",
                    "showOn":"view"
                }]
            }],
            "panels": [{
                "name": "panel_header",
                "header": true,
                "fields": ["name"]
            }, {
                "name": "panel_body",
                "label": "LBL_PANEL_2",
                "columns": 1,
                "labels": true,
                "labelsOnTop": false,
                "placeholders":true,
                "fields": ["description","case_number","type"]
            }, {
                "name": "panel_hidden",
                "hide": true,
                "labelsOnTop": false,
                "placeholders": true,
                "fields": ["created_by","date_entered","date_modified","modified_user_id"]
            }]
        }, moduleName);
        SugarTest.testMetadata.set();
        SugarTest.app.data.declareModels();

        sinonSandbox = sinon.sandbox.create();

        view = SugarTest.createView("base", moduleName, "record", null, null);
    });

    afterEach(function() {
        SugarTest.testMetadata.dispose();
        SugarTest.app.view.reset();
        sinonSandbox.restore();
        view = null;
    });

    describe('Render', function() {
        it("Should not render any fields if model is empty", function() {
            view.render();

            expect(_.size(view.fields)).toBe(0);
        });

        it("Should render 8 editable fields and 5 buttons", function() {
            var fields = 0,
                buttons = 0;

            view.render();
            view.model.set({
                name: 'Name',
                case_number: 123,
                description: 'Description'
            });

            _.each(view.fields, function(field) {
                if (field.type === 'button') {
                    buttons++;
                } else if (field.type !== 'buttondropdown') {
                    fields++;
                }
            });

            expect(fields).toBe(8);
            expect(buttons).toBe(5);
        });

        it("Should hide 4 editable fields", function() {
            var hiddenFields = 0;

            view.render();
            view.model.set({
                name: 'Name',
                case_number: 123,
                description: 'Description'
            });
            _.each(view.fields, function(field) {
                if ((field.type !== 'button') && (field.type !== 'buttondropdown') && (field.$el.closest('.hide').length === 1)) {
                    hiddenFields++;
                }
            });

            expect(hiddenFields).toBe(4);
        });

        it("Should place name field in the header", function() {
            view.render();
            view.model.set({
                name: 'Name',
                case_number: 123,
                description: 'Description'
            });

            expect(view.getField('name').$el.closest('.headerpane').length === 1).toBe(true);
        });

        it("Should not render any fields when a user doesn't have access to the data", function() {
            sinonSandbox.stub(SugarTest.app.acl, 'hasAccessToModel', function() {
                return false;
            });
            sinonSandbox.stub(SugarTest.app.error, 'handleRenderError', $.noop());

            view.render();
            view.model.set({
                name: 'Name',
                case_number: 123,
                description: 'Description'
            });

            expect(_.size(view.fields)).toBe(0);
        });

        it("Should display all 8 editable fields when more link is clicked", function() {
            var hiddenFields = 0,
                visibleFields = 0;

            view.render();
            view.model.set({
                name: 'Name',
                case_number: 123,
                description: 'Description'
            });

            view.$('.more').click();
            _.each(view.fields, function(field) {
                if ((field.type !== 'button') && (field.type !== 'buttondropdown')) {
                    if (field.$el.closest('.hide').length === 1) {
                        hiddenFields++;
                    } else {
                        visibleFields++;
                    }
                }
            });

            expect(hiddenFields).toBe(0);
            expect(visibleFields).toBe(8);
        });
    });

    describe('Edit', function() {
        it("Should toggle to an edit mode when a user clicks on the inline edit icon", function() {
            view.render();
            view.model.set({
                name: 'Name',
                case_number: 123,
                description: 'Description'
            });

            expect(view.getField('name').options.viewName).toBe(view.action);

            view.getField('name').$el.closest('.record-cell').find('a.record-edit-link').click();

            expect(view.getField('name').options.viewName).toBe('edit');
        });

        it("Should toggle all editable fields to edit modes when a user clicks on the edit button", function() {
            view.render();
            view.model.set({
                name: 'Name',
                case_number: 123,
                description: 'Description'
            });

            _.each(view.editableFields, function(field) {
                expect(field.options.viewName).toBe(view.action);
            });

            view.context.trigger('button:edit_button:click');

            waitsFor(function() {
                return (_.last(view.editableFields)).options.viewName == 'edit';
            }, 'it took too long to wait switching view', 1000);

            runs(function() {
                _.each(view.editableFields, function(field) {
                    expect(field.options.viewName).toBe('edit');
                });
            });
        });

        it("Should show save and cancel buttons and hide edit button when data changes", function() {

            view.model.set({
                name: 'Name',
                case_number: 123,
                description: 'Description'
            });
            view.render();
            view.editMode = true;
            view.model.set({
                name: 'Foo',
                case_number: 123,
                description: 'Description'
            });

            expect(view.getField('save_button').getFieldElement().css('display')).toBe('none');
            expect(view.getField('cancel_button').getFieldElement().css('display')).toBe('none');
            expect(view.getField('edit_button').getFieldElement().css('display')).not.toBe('none');

            view.context.trigger('button:edit_button:click');
            view.model.set({
                name: 'Bar'
            });

            expect(view.getField('save_button').getFieldElement().css('display')).not.toBe('none');
            expect(view.getField('cancel_button').getFieldElement().css('display')).not.toBe('none');
            expect(view.getField('edit_button').getFieldElement().css('display')).toBe('none');
        });

        it("Should revert data back to the old value when the cancel button is clicked after data has been changed", function() {
            view.render();
            view.model.set({
                name: 'Foo',
                case_number: 123,
                description: 'Description'
            });
            view.context.trigger('button:edit_button:click');
            view.model.set({
                name: 'Bar'
            });

            expect(view.model.get('name')).toBe('Bar');
            view.$('a[name=cancel_button]').click();
            expect(view.model.get('name')).toBe('Foo');
        });
    });

    describe('_renderPanels with 1 column', function () {
        it("Should create panel grid with all fields on separate rows", function () {
            var results,
                panelDefs = [{
                    "name":         "panel_body",
                    "label":        "LBL_PANEL_2",
                    "columns":      1,
                    "labels":       true,
                    "labelsOnTop":  true,
                    "placeholders": true,
                    "fields":       ["description", "case_number", "type"]
                }];

            view._renderPanels(panelDefs);
            results = panelDefs[0].grid;

            expect(results.length).toBe(3);
            expect(results[0].length).toBe(1);
            expect(results[1].length).toBe(1);
            expect(results[2].length).toBe(1);
        });
    });

    describe('_renderPanels with 2 columns', function () {
        it("Should create panel grid with last row containing one empty column", function () {
            var results,
                panelDefs = [{
                    "name":         "panel_body",
                    "label":        "LBL_PANEL_2",
                    "columns":      2,
                    "labels":       true,
                    "labelsOnTop":  true,
                    "placeholders": true,
                    "fields":       ["description", "case_number", "type"]
                }];

            view._renderPanels(panelDefs);
            results = panelDefs[0].grid;

            expect(results.length).toBe(2);
            expect(results[0].length).toBe(2);
            expect(results[1].length).toBe(1);
        });

        it("Should create panel grid with second field on its own row where second field's span causes overflow", function () {
            var results,
                panelDefs = [{
                    "name":         "panel_body",
                    "label":        "LBL_PANEL_2",
                    "columns":      2,
                    "labels":       true,
                    "labelsOnTop":  true,
                    "placeholders": true,
                    "fields":       [
                        "case_number",
                        {
                            'name': 'description',
                            'span': 12
                        },
                        "type"
                    ]
                }];

            view._renderPanels(panelDefs);
            results = panelDefs[0].grid;

            expect(results.length).toBe(3);
            expect(results[0].length).toBe(1);
            expect(results[1].length).toBe(1);
            expect(results[2].length).toBe(1);
        });

        it("Should create panel grid with first field on its own row where the first field's span fills the row", function () {
            var results,
                panelDefs = [{
                    "name":         "panel_body",
                    "label":        "LBL_PANEL_2",
                    "columns":      2,
                    "labels":       true,
                    "labelsOnTop":  true,
                    "placeholders": true,
                    "fields":       [
                        {
                            'name': 'description',
                            'span': 12
                        },
                        "case_number",
                        "type"
                    ]
                }];

            view._renderPanels(panelDefs);
            results = panelDefs[0].grid;

            expect(results.length).toBe(2);
            expect(results[0].length).toBe(1);
            expect(results[1].length).toBe(2);
        });

        it("Should create panel grid with all fields fitting within the maximum allowable span when the panel def specifies a field whose span is out of range", function () {
            var results,
                panelDefs = [{
                                 "name":         "panel_body",
                                 "label":        "LBL_PANEL_2",
                                 "columns":      2,
                                 "labels":       true,
                                 "labelsOnTop":  false,
                                 "placeholders": true,
                                 "fields":       [
                                     {
                                         'name': 'description',
                                         'span': 12 // out of range for a panel with inline labels
                                     },
                                     "case_number",
                                     "type"
                                 ]
                             }];

            view._renderPanels(panelDefs);
            results = panelDefs[0].grid;

            expect(results.length).toBe(2);
            expect(results[0].length).toBe(1);
            expect(results[1].length).toBe(2);
            expect(results[0][0].span).toBe(8); // the description field's span should have been reset to 8 since 12 won't fit
            expect(results[1][0].span).toBe(4); // verifying that the field span is calculated correctly when labels are inline
            expect(results[1][1].span).toBe(4); // verifying that the field span is calculated correctly when labels are inline
        });
    });

    describe('_renderPanels with 3 columns', function () {
        it("Should create panel grid with last field on its own row where the last field's span causes overflow", function () {
            var results,
                panelDefs = [{
                    "name":         "panel_body",
                    "label":        "LBL_PANEL_2",
                    "columns":      3,
                    "labels":       true,
                    "labelsOnTop":  true,
                    "placeholders": true,
                    "fields":       [
                        "case_number",
                        {
                            'name': 'description',
                            'span': 6
                        },
                        "type"
                    ]
                }];

            view._renderPanels(panelDefs);
            results = panelDefs[0].grid;

            expect(results.length).toBe(2);
            expect(results[0].length).toBe(2);
            expect(results[1].length).toBe(1);
        });

        it("Should create panel grid with first field on its own row where the first field's span fills the row", function () {
            var results,
                panelDefs = [{
                    "name":         "panel_body",
                    "label":        "LBL_PANEL_2",
                    "columns":      3,
                    "labels":       true,
                    "labelsOnTop":  true,
                    "placeholders": true,
                    "fields":       [
                        {
                            'name': 'description',
                            'span': 12
                        },
                        "case_number",
                        "type"
                    ]
                }];

            view._renderPanels(panelDefs);
            results = panelDefs[0].grid;

            expect(results.length).toBe(2);
            expect(results[0].length).toBe(1);
            expect(results[1].length).toBe(2);
        });

        it("Should create panel grid with all fields on their own row when the span of the second of three fields causes fills a row", function () {
            var results,
                panelDefs = [{
                    "name":         "panel_body",
                    "label":        "LBL_PANEL_2",
                    "columns":      3,
                    "labels":       true,
                    "labelsOnTop":  true,
                    "placeholders": true,
                    "fields":       [
                        "case_number",
                        {
                            'name': 'description',
                            'span': 10 // this field won't fit on the row with case_number
                        },
                        "type" // this field won't fit on the row with description because description's span was too large
                    ]
                }];

            view._renderPanels(panelDefs);
            results = panelDefs[0].grid;

            expect(results.length).toBe(3);
            expect(results[0].length).toBe(1);
            expect(results[1].length).toBe(1);
            expect(results[2].length).toBe(1);
        });
    });
});
