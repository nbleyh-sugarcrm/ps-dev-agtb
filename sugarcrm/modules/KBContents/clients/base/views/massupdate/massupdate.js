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
({
    extendsFrom: 'MassupdateView',
    
    /**
     * @inheritdoc
     */
    initialize: function(options) {
        this.plugins = _.union(this.plugins || [], ['CommittedDeleteWarning', 'KBContent']);
        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    saveClicked: function(evt) {
        var massUpdateModels = this.getMassUpdateModel(this.module).models,
            fieldsToValidate = this._getFieldsToValidate(),
            emptyValues = [];

        this._restoreInitialState(massUpdateModels);

        this._doValidateMassUpdate(massUpdateModels, fieldsToValidate, _.bind(function(fields, errors) {
            if (_.isEmpty(errors)) {
                this.trigger('massupdate:validation:complete', {
                    errors: errors,
                    emptyValues: emptyValues
                });
                if(this.$('.btn[name=update_button]').hasClass('disabled') === false) {
                    this.save();
                }
            } else {
                this.handleValidationError(errors);
            }
        }, this));
    },

    /**
     * Restore models state.
     *
     * @param {Array} models
     * @private
     */
    _restoreInitialState: function(models) {
        _.each(models, function(model) {
            model.revertAttributes();
        });
    },

    /**
     * Custom MassUpdate validation.
     *
     * @param {Object} models
     * @param {Object} fields
     * @param {Function} callback
     * @private
     */
    _doValidateMassUpdate: function(models, fields, callback) {
        var self = this,
            value = '',
            checkField = 'status',
            errorFields = [],
            messages = [],
            checkFieldIndex = _.find(fields, function(field) {
                return field.name === checkField;
            }),
            errors = {};
        if (undefined !== checkFieldIndex) {
            value = this.model.get(checkField);
            _.each(models, function(model) {
                switch (value) {
                    case 'published':
                        self._doValidateExpDateField(model, fields, errors, function(model, fields, errors){
                            var fieldName = 'exp_date';
                            if (!_.isEmpty(errors[fieldName])) {
                                errors[checkField] = errors[fieldName];
                                errorFields.push(fieldName);
                                messages.push(app.lang.get('LBL_MODIFY_EXP_DATE_LOW', 'KBContents'));
                            }

                        });
                        break;
                    case 'approved':
                        self._doValidateActiveDateField(model, fields, errors, function(model, fields, errors){
                            var fieldName = 'active_date';
                            if (!_.isEmpty(errors[fieldName])) {
                                errors[checkField] = errors[fieldName];
                                errorFields.push(fieldName);
                                messages.push(app.lang.get('LBL_SPECIFY_PUBLISH_DATE', 'KBContents'));
                            }
                        });
                        break;

                }
            });
            if (!_.isEmpty(errorFields)) {
                errorFields.push(checkField);
                app.alert.show('save_without_publish_date_confirmation', {
                    level: 'confirmation',
                    messages: _.uniq(messages),
                    confirm: {
                        label: app.lang.get('LBL_YES')
                    },
                    cancel: {
                        label: app.lang.get('LBL_NO')
                    },
                    onConfirm: function() {
                        errors = _.filter(errors, function(error, key) {
                            _.indexOf(errorFields, key) === -1;
                        });
                        callback(fields, errors);
                    }
                });
            } else {
                callback(fields, errors);
            }
        } else {
            callback(fields, errors);
        }
    },

    /**
     * We don't need to initialize KB listeners.
     * @override.
     * @private
     */
    _initKBListeners: function() {},
    
    /**
     * @inheritdoc
     */
    cancelClicked: function(evt) {
        this._restoreInitialState(this.getMassUpdateModel(this.module).models);
        this._super('cancelClicked', [evt]);
    }
})
