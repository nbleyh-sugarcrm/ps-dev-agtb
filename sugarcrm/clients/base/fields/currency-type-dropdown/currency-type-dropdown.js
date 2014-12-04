/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/**
 * @class View.Fields.Base.CurrencyTypeDropdownField
 * @alias SUGAR.App.view.fields.BaseCurrencyTypeDropdownField
 * @extends View.Fields.Base.EnumField
 */
({
    extendsFrom: 'EnumField',

    /**
     * Holds the compiled currencies templates with symbol/iso by currencyID key
     * @type {Object}
     */
    currenciesTpls: undefined,

    /**
     * The currency ID field name to use on the model when changing currency ID
     * Defaults to 'currency_id' if no currency_field exists in metadata
     * @type {String}
     */
    currencyIdFieldName: undefined,


    /**
     * The base rate field name to use on the model
     * Defaults to 'base_rate' if no base_rate_field exists in metadata
     * @type {String}
     */
    baseRateFieldName: undefined,

    /**
     * The last known record currency id
     * @type {String}
     */
    _lastCurrencyId: undefined,

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        // get the currencies and run them through the template
        this.currenciesTpls = app.currency.getCurrenciesSelector(Handlebars.compile('{{symbol}} ({{iso4217}})'));

        // update options defs with
        options.def.options = options.def.options || this.currenciesTpls;
        options.def.enum_width = options.def.enum_width || '100%';
        options.def.searchBarThreshold = options.def.searchBarThreshold || 7;

        // get the default field names from metadata
        this.currencyIdFieldName = options.def.currency_field || 'currency_id';
        this.baseRateFieldName = options.def.base_rate_field || 'base_rate';

        this._super('initialize', [options]);

        // check to make sure this is a new model or currency_id has not been set, and the model is not a copy
        // so we don't overwrite the models previously entered values
        if ((this.model.isNew() || _.isUndefined(this.model.get(this.currencyIdFieldName)))
            && !this.model.isCopy()) {
            var currencyFieldValue = app.user.getPreference('currency_id'),
                baseRateFieldValue = app.metadata.getCurrency(currencyFieldValue).conversion_rate;

            // set the currency_id to the user's preferred currency
            this.model.set(this.currencyIdFieldName, currencyFieldValue);

            // set the base_rate to the preferred currency conversion_rate
            this.model.set(this.baseRateFieldName, baseRateFieldValue);

            // if this.name is not the same as the currency ID field, also set this.name on the model
            if (this.name !== this.currencyIdFieldName) {
                this.model.set(this.name, currencyFieldValue);
            }

            // Modules such as `Forecasts` uses models that aren't `Data.Bean`
            if (_.isFunction(this.model.setDefault)) {
                var defaults = {};
                defaults[this.currencyIdFieldName] = currencyFieldValue;
                defaults[this.baseRateFieldName] = baseRateFieldValue;
                this.model.setDefault(defaults);
            }
        }

        // track the last currency id to convert the value on change
        this._lastCurrencyId = this.model.get(this.currencyIdFieldName);
    },

    /**
     * @inheritdoc
     *
     * Overriding to make sure the currency ID is set on the field,
     * not the formatted symbol
     *
     * @override
     */
    bindDataChange: function() {
        this.model.on('change:' + this.name, function(ctx, changedCurrencyId) {
            // if this.name is not the same as the currency ID field, also set this.currencyIdFieldName on the model
            if (this.name !== this.currencyIdFieldName) {
                this.model.set(this.currencyIdFieldName, changedCurrencyId);
            }
            // update select2
            this.$(this.fieldTag).select2('val', changedCurrencyId);
        }, this);
    },

    /**
     * @inheritdoc
     *
     * Overriding to use the currencyFieldIdName instead of this.name
     *
     * @override
     */
    getFormattedValue: function() {
        return this.format(this.model.has(this.currencyIdFieldName) ? this.model.get(this.currencyIdFieldName) : null);
    },

    /**
     * @inheritdoc
     *
     * Overriding so the translated currency symbol/iso is used
     *
     * @override
     */
    format: function(value) {
        return this.currenciesTpls[value];
    }
})
