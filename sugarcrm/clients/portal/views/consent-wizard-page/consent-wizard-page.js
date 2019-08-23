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
 * @class View.Views.Portal.ConsentWizardPageView
 * @alias SUGAR.App.view.views.PortalConsentWizardPageView
 * @extends View.Views.Base.WizardPageView
 */
({
    extendsFrom: 'BaseWizardPageView',

    events: {
        'click [name=continue_button]:not(.disabled)': 'continueConsent',
        'click [name=cancel_button]': 'cancelConsent'
    },

    /**
     * @inheritdoc
     */
    initialize: function(options) {
        options.template = app.template.getView('consent-wizard-page');
        this._super('initialize', [options]);
        Handlebars.registerPartial('consent-wizard-page.header', app.template.get('consent-wizard-page.header'));
        this.href = 'https://www.sugarcrm.com/legal/privacy-policy/';
    },

    /**
     * Also set up event listener on the cookie_consent field to enable/disable the continue button
     * @private
     */
    _render: function() {
        this._super('_render');
        this._bindConsentField();
    },

    /**
     * Set up an event listener on the cookie_consent field to update the continue button
     * @private
     */
    _bindConsentField: function() {
        if (this._isBound) {
            return;
        }
        var field = this.getField('cookie_consent');
        var $el = field.$(field.fieldTag);
        // this should be disabled by default
        this.getField('continue_button').setDisabled(true);
        $el.on('change', _.bind(function() {
            var isChecked = $el.prop('checked');
            this.getField('continue_button').setDisabled(!isChecked);
        }, this));
        this._isBound = true;
    },

    /**
     * Event handler for clicking the continue button
     * Saves the user's cookie_consent
     */
    continueConsent: function() {
        var consent = this.model.get('cookie_consent');
        if (consent !== true) {
            return;
        }
        this.model.doValidate(this.fieldsToValidate,
            _.bind(function(isValid) {
                if (isValid) {
                    var payload = {cookie_consent: this.model.get('cookie_consent')};
                    app.alert.show('wizardprofile', {
                        level: 'process',
                        title: app.lang.get('LBL_LOADING'),
                        autoClose: false
                    });
                    app.user.updateProfile(payload, _.bind(function(err) {
                        app.alert.dismiss('wizardprofile');
                        if (!err) {
                            this.layout.finished();
                            app.router.navigate('#Home', {trigger: true});
                        }
                    }, this));
                }
            }, this)
        );
    },

    /**
     * Event handler for clicking the cancel button
     */
    cancelConsent: function() {
        app.router.logout();
    }
})
