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
});