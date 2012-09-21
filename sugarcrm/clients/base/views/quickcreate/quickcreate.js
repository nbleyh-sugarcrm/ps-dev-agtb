({
    initialize: function(options) {
        app.view.View.prototype.initialize.call(this, options);
        this.context.on('quickcreate:clear', this.clear, this);
        this.context.on('quickcreate:edit', this.editExisting, this);
        this.context.on('quickcreate:restore', this.restoreModel, this);
        this.context.on('quickcreate:validateModel', this.validateModel, this);
        this.context.on('quickcreate:highlightDuplicateFields', this);
        this.model.on("error:validation", this.handleValidationError, this);
    },

    render: function() {
        var totalFieldCount = 0;

        _.each(this.meta.panels, function(panel) {
            var columns = (panel.columns) || 2,
                count = 0,
                rows = [],
                row = [];

            _.each(panel.fields, function(field) {
                var maxSpan;

                if (_.isUndefined(panel.labels)) {
                    panel.labels = true;
                }
                //8 for span because we are using a 2/3 ratio between field span and label span with a max of 12
                maxSpan = (panel.labels) ? 8 : 12;

                if (_.isUndefined(field.span)) {
                    field.span = Math.floor(maxSpan / columns);
                }

                //4 for label span because we are using a 1/3 ratio between field span and label span with a max of 12
                if (_.isUndefined(field.labelSpan)) {
                    field.labelSpan = Math.floor(4 / columns);
                }

                totalFieldCount++;
                field.index = totalFieldCount;
                row.push(field);

                if (count % columns == columns - 1) {
                    rows.push(row);
                    row = [];
                }

                count++;
            }, this);

            panel.grid = rows;
        }, this);

        app.view.View.prototype.render.call(this);
    },

    handleValidationError:function (errors) {
        var self = this;

        _.each(errors, function (fieldErrors, fieldName) {
            //retrieve the field by name
            var field = self.getField(fieldName);
            var ftag = this.fieldTag || '';

            if (field) {
                var controlGroup = field.$el.parents('.control-group:first');

                if (controlGroup) {
                    controlGroup.addClass("error");
                    controlGroup.find('.add-on').remove();
                    controlGroup.find('.help-block').html("");

                    if (field.$el.parent().parent().find('.input-append').length > 0) {
                        field.$el.unwrap()
                    }
                    // Add error styling
                    field.$el.wrap('<div class="input-append  '+ftag+'">');

                    _.each(fieldErrors, function (errorContext, errorName) {
                        controlGroup.find('.help-block').append(self.app.error.getErrorString(errorName, errorContext));
                    });

                    $('<span class="add-on"><i class="icon-exclamation-sign"></i></span>').insertBefore(controlGroup.find('.help-block'));
                }
            }
        });
    },

    /**
     * Clears out field values
     */
    clear: function() {
        this.model.clear();
        this.model.set(this.model._defaults);
    },

    /**
     * Make the specified record as the data to be edited, and merge the existing data.
     * @param model
     */
    editExisting: function(model) {
        var newTitle = this.app.lang.get('LBL_EDIT_BUTTON', this.module) + '' + this.module,
            origAttributes = this.saveFormData();

        this.model.clear();
        this.model.set(this.extendModel(model, origAttributes));

        this.context.parent.trigger("modal:changetitle", newTitle);
    },

    /**
     * Merge the selected record with the data entered in the form
     * @param newModel
     * @param origAttributes
     * @return {*}
     */
    extendModel: function(newModel, origAttributes) {
        var modelAttributes = newModel.previousAttributes();

        _.each(modelAttributes, function(value, key, list) {
            if ( _.isUndefined(value)|| _.isEmpty(value)) {
                delete modelAttributes[key];
            }
        });

        return _.extend({}, origAttributes, modelAttributes);
    },

    /**
     * Restore to the original form state before edit selection
     */
    restoreModel: function() {
        var newTitle = this.app.lang.get('LBL_NEW_FORM_TITLE', this.module);
        this.context.parent.trigger('modal:changetitle', newTitle);

        this.context.trigger('quickcreate:resetDuplicateState');

        this.model.clear();
        if (this._origAttributes) {
            this.model.set(this._origAttributes);
        }
    },

    /**
     * Save the data entered in the form
     * @return {*}
     */
    saveFormData: function() {
        this._origAttributes = this.model.previousAttributes();
        return this._origAttributes;
    },

    /**
     * Check to make sure that all fields are valid
     * @param callback
     */
    validateModel: function(callback) {
        var isValid = this.model.isValid(this.getFields(this.module));
        callback(isValid);
        return isValid;
    }
})



