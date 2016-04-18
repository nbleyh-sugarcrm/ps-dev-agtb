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
 * @class View.Views.Base.Emails.ComposeAddressbookListBottomView
 * @alias SUGAR.App.view.views.BaseEmailsComposeAddressbookListBottomView
 * @extends View.Views.Base.ListBottomView
 *
 * @deprecated 7.9.0 Will be removed in 7.11.0.
 */
({
    extendsFrom: "ListBottomView",

    /**
     * Assign proper label for 'show more' link.
     * Label should be "More recipients...".
     */
    setShowMoreLabel: function() {
        this.showMoreLabel = app.lang.get('LBL_SHOW_MORE_RECIPIENTS', this.module);
    }
})
