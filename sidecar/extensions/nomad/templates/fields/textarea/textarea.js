(function(app) {

    app.view.fields.TextareaField = app.view.Field.extend({

        // TODO: Override base implementation
        // Check out Mango/sugarcrm/clients/base/fields/int/int.js

        fieldTag: "textarea"
    });

})(SUGAR.App);