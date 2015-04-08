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
    extendsFrom: 'RecordlistView',

    /**
     * @override
     * @param {Object} options
     */
    initialize: function(options) {
        this.contextEvents = _.extend({}, this.contextEvents, {
            "list:opendesigner:fire": "openDesigner",
            "list:exportprocess:fire": "showExportingWarning",
            "list:enabledRow:fire": "enabledProcess",
            "list:disabledRow:fire": "disabledProcess"
        });
        app.view.invokeParent(this, {type: 'view', name: 'recordlist', method: 'initialize', args:[options]});
    },

    openDesigner: function(model) {
        app.navigate(this.context, model, 'layout/designer');
    },

    showExportingWarning: function (model) {
        var that = this, lang;
        if (App.cache.get("show_export_warning")) {
            lang = App.lang.get("pmse_Project");
            App.alert.show('project-export-confirmation',  {
                level: 'confirmation',
                messages: lang['LBL_PMSE_IMPORT_EXPORT_WARNING'] + "<br/><br/>"
                    + lang['LBL_PMSE_EXPORT_CONFIRMATION'],
                onConfirm: function () {
                    App.cache.set("show_export_warning", false);
                    that.exportProcess(model);
                },
                onCancel: $.noop
            });
        } else {
            that.exportProcess(model);
        }
    },

    exportProcess: function(model) {
        var url = app.api.buildURL(model.module, 'dproject', {id: model.id}, {platform: app.config.platform});

        if (_.isEmpty(url)) {
            app.logger.error('Unable to get the Project download uri.');
            return;
        }

        app.api.fileDownload(url, {
            error: function(data) {
                // refresh token if it has expired
                app.error.handleHttpError(data, {});
            }
        }, {iframe: this.$el});
    },
    enabledProcess: function(model) {
        var self = this;
        var name = model.get('name') || '';
        app.alert.show(model.get('id') + ':deleted', {
            level: 'confirmation',
            messages: app.utils.formatString(app.lang.get('LBL_PRO_ENABLE_CONFIRMATION', model.module),[name.trim()]),
            onConfirm: function() {
                self._updateProStatusEnabled(model);
            }
        });
    },
    _updateProStatusEnabled: function(model) {
        var self = this;
        url = App.api.buildURL(model.module, null, {id: model.id});
        attributes = {prj_status: 'ACTIVE'};
        app.api.call('update', url, attributes,{
            success:function(){
                self.reloadList();
            }
        });
        app.alert.show(model.id + ':refresh', {
            level:"process",
            title: app.lang.get('LBL_PRO_ENABLE', model.module),
            autoClose: true
        });
//        self.reloadList();
    },
    disabledProcess: function(model) {
        var self = this;
        var name = model.get('name') || '';
        app.alert.show(model.get('id') + ':deleted', {
            level: 'confirmation',
            messages: app.utils.formatString(app.lang.get('LBL_PRO_DISABLE_CONFIRMATION', model.module),[name.trim()]),
            onConfirm: function() {
                self._updateProStatusDisabled(model);
            }
        });
    },
    _updateProStatusDisabled: function(model) {
        var self = this;
        url = App.api.buildURL(model.module, null, {id: model.id});
        attributes = {prj_status: 'INACTIVE'};
        app.api.call('update', url, attributes,{
            success:function(){
                self.reloadList();
            }
        });
        app.alert.show(model.id + ':refresh', {
            level:"process",
            title: app.lang.get('LBL_PRO_DISABLE', model.module),
            autoClose: true
        });
//        self.reloadList();
    },
    reloadList: function() {
        var self = this;
        self.context.reloadData({
            recursive:false,
            error:function(error){
                console.log(error);
            }
        });
    },
    warnDelete: function(model) {
        var verifyURL = app.api.buildURL(
            this.module,
            'verify',
            {
                id : model.get('id')
            }
        ),
            self = this;
        app.api.call('read', verifyURL, null, {
            success: function(data) {
                if (!data) {
                    self._super('warnDelete', [model]);
                } else {
                    app.alert.show('message-id', {
                        level: 'warning',
                        title: app.lang.get('LBL_WARNING'),
                        messages: app.lang.get('LBL_PA_PRODEF_HAS_PENDING_PROCESSES'),
                        autoClose: false
                    });
                }
            }
        });
    }
})
