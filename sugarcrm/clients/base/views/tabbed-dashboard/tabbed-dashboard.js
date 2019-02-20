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
/**
 * @class View.Views.Base.TabbedDashboardView
 * @alias SUGAR.App.view.views.BaseTabbedDashboardView
 * @extends View.View
 */
({
    events: {
        'click [data-toggle=tab]': 'tabClicked',
    },

    activeTab: 0,
    tabs: [],

    /**
     * Initialize this component.
     * @param {Object} options Initialization options.
     * @param {Object} options.meta Metadata.
     * @inheritdoc
     */
    initialize: function(options) {
        this._initTabs(options.meta);
        this._super('initialize', [options]);
    },

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this.context.on('tabbed-dashboard:update', this._setTabs, this);
    },

    /**
     * Switch the active dashboard based on the clicked tab.
     * @param {Event} event Click event.
     */
    tabClicked: function(event) {
        var index = this.$(event.currentTarget).data('index');
        if (index === this.activeTab) {
            return;
        }

        this.context.trigger('tabbed-dashboard:switch-tab', index);
    },

    /**
     * Initialize tabs.
     * @param {Object} [options={}] Tab options.
     * @private
     */
    _initTabs: function(options) {
        options = options || {};
        if (!_.isUndefined(options.activeTab)) {
            this.activeTab = options.activeTab;
        }
        if (!_.isUndefined(options.tabs)) {
            this.tabs = options.tabs;
        }
    },

    /**
     * Set tab options, then re-render.
     * @param {Object} [options] Tab options.
     * @private
     */
    _setTabs: function(options) {
        this._initTabs(options);
        this.render();
    }
})
