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
 * @class View.Fields.Base.NotificationCenterCarrierField
 * @alias SUGAR.App.view.fields.BaseNotificationCenterCarrierField
 * @extends View.Fields.Base.BaseField
 */
({
    fieldTag: 'input[data-type=carrier]',

    /**
     * Config model carriers.
     */
    carriers: {},

    /**
     * Globally configured carriers. Only for user mode.
     */
    carriersGlobal: null,

    /**
     * @inheritDoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        if (this.model.get('configMode') === 'user') {
            this.carriers = this.model.get('personal')['carriers'];
            this.carriersGlobal = this.model.get('global')['carriers'];
        } else {
            this.carriers = this.model.get('carriers');
        }

        this.model.on('reset:all', this.render, this);
    },

    /**
     * @inheritDoc
     */
    format: function(value) {
        if (this.carriersGlobal) {
            this.def.isGloballyEnabled = this.carriersGlobal[this.def.name].status;
        }
        return this.carriers[this.def.name].status;
    },

    /**
     * @inheritDoc
     */
    bindDomChange: function() {
        var $el = this.$(this.fieldTag + '[name=' + this.def.name + ']');
        $el.on('change', _.bind(function() {
            var modifiedCarriers = _.clone(this.carriers);
            modifiedCarriers[this.def.name].status = $el.prop('checked');

            this.model.set('carriers', modifiedCarriers);

            var eventName = (this.model.get('configMode') === 'user') ?
                'change:personal:carrier:' + this.def.name :
                'change:carrier:' + this.def.name;
            this.model.trigger(eventName);
        }, this));
    }
})

