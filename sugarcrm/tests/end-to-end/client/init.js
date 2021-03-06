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
(function (app) {

    if (!app) {
        return false;
    }

    app.events.on('app:sync:complete', function() {
        app.user.set('show_wizard', false);
    });

    /**
     * Override SUGAR.App.config values when metadata fetch finished
     */
    app.events.on('app:sync:complete', function() {
        if (seedbed.sugarOverrideConfig && seedbed.utils.deepExtend) {
            app.config = seedbed.utils.deepExtend(app.config, seedbed.sugarOverrideConfig);
        }
    });

    $(document).on('ajaxSend', function(event, xhr, options) {
        console.log('ajax start: ' + options.url);
    });

    $(document).on('ajaxComplete', function(event, xhr, options) {
        console.log('ajax complete: ' + options.url);

        if (options.type.toLowerCase() === 'post' &&
            options.url.indexOf('link/product_bundles') !== -1) {

            const record = xhr.responseJSON.related_record;

            window.__e2e.create.push(record);
        }

    });

    $(document).on('ajaxError', function(xhr, options, error) {
        console.log('ajax error: ' + options.url + ' ' + error ? error.message : error);
    });

    document.body.addEventListener('click', function(e) {
        console.log('click element: ' + ((e.target.id ? 'id - ' + e.target.id : '') +
            (e.target.className ? ' className - ' + e.target.className : '')));
    }, true);

    $(document.body).on('keydown', 'input', function(e) {
        console.log('keydown: ' + '"' + $(e.target).val() + '" ' + ((e.target.id ? 'id - ' +
            e.target.id : '') + (e.target.className ? ' className - ' + e.target.className : '')));
    });

    $(document.body).on('focusin', 'input', function(e) {
        console.log('focusin: ' + '"' + $(e.target).val() + '" ' + ((e.target.id ? 'id - ' +
            e.target.id : '') + (e.target.className ? ' className - ' + e.target.className : '')));
    });

    let originalAlertShow = app.alert.show;

    app.alert.show = function show(...args) {

        if (!args[1]) {
            args[1] = {};
        }

        const options = args[1];

        options.autoClose = false;

        return originalAlertShow.apply(this, args);
    };

})(SUGAR && SUGAR.App ? SUGAR.App : null);
