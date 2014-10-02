/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */

({
    extendsFrom: 'RelatecollectionField',

    relateModule: 'Tags',

    // creation of new tags is always allowed
    collectionCreate: true,

    plugins: ['Tooltip'],

    /**
     * {@inheritDoc}
     */
    initialize: function(options) {
        this._super('initialize', [options]);
    },

    /**
     * {@inheritDoc}
     */
    _render: function() {
        // Set up tagList variable for use in the list view
        this.value = this.getFormattedValue();
        if (this.value) {
            this.tagList = _.pluck(this.value, 'name').join(', ');
        }

        this._super('_render');
    }
})
