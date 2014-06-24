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
 * @class View.Fields.Base.FloatField
 * @alias SUGAR.App.view.fields.BaseFloatField
 * @extends View.Field
 */
({
    /**
     * {@inheritDoc}
     *
     * Unformats the float based on userPreferences (grouping/decimal separator).
     * If we weren't able to parse the value, the original value is returned.
     *
     * @param {String} value the formatted value based on user preferences.
     * @return {Number|String} the unformatted value, or original string if invalid.
     */
    unformat: function(value) {
        var unformattedValue = app.utils.unformatNumberStringLocale(value);
        // if unformat failed, return original value
        return _.isFinite(unformattedValue) ? unformattedValue : value;

    },

    /**
     * {@inheritDoc}
     *
     * Formats the float based on user preferences (grouping separator).
     * If the field definition has `disabled_num_format` as `true` the value
     * won't be formatted. Also, if the value isn't a finite float it will
     * return `undefined`.
     *
     * @param {Number} value the float value to format as per user preferences.
     * @return {String|undefined} the formatted value based as per user
     *   preferences.
     */
    format: function(value) {
        if (this.def.disable_num_format) {
            return value;
        }
        return app.utils.formatNumber(
            value,
            this.def.round || 4,
            this.def.precision || 4,
            app.user.getPreference('number_grouping_separator') || ',',
            app.user.getPreference('decimal_separator') || '.');
    }
})
