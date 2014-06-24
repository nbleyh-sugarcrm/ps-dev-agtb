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
({
    extendsFrom: 'HeaderpaneView',

    events:{
        'click [name=save_button]:not(".disabled")': 'initiateSave',
        'click [name=cancel_button]': 'initiateCancel'
    },

    initialize: function(options) {
        this._super("initialize", [options]);
        this.context.on('lead:convert-save:toggle', this.toggleSaveButton, this);
    },

    /**
     * Grab the lead's name and set the title to Convert: Name
     */
    _renderHtml: function() {
        var leadsModel = this.context.get('leadsModel');
        var name = !_.isUndefined(leadsModel.get('name')) ? leadsModel.get('name') : leadsModel.get('first_name') + ' ' + leadsModel.get('last_name');
        this.title = app.lang.get("LBL_CONVERTLEAD", this.module) + ': ' + name;
        this._super("_renderHtml");
    },

    /**
     * When finish button is clicked, send this event down to the convert layout to wrap up
     */
    initiateSave: function() {
        this.context.trigger('lead:convert:save');
    },

    /**
     * When cancel clicked, hide the drawer
     */
    initiateCancel : function() {
        app.drawer.close();
    },

    /**
     * Enable/disable the Save button
     *
     * @param enable true to enable, false to disable
     */
    toggleSaveButton: function(enable) {
        this.$('[name=save_button]').toggleClass('disabled', !enable);
    }
})
