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
    extendsFrom: 'RecordView',

    MIN_EDITOR_HEIGHT: 300,
    EDITOR_RESIZE_PADDING: 5,
    buttons: null,

    initialize: function(options) {
        _.bindAll(this);
        var self = this;

        app.view.invokeParent(this, {type: 'view', name: 'record', method: 'initialize', args:[options]});

        this.events = _.extend({}, this.events, {
            'click [name=save_button]': 'save',
            'click [name=save_buttonExit]': 'saveExit',
            'click [name=cancel_button]': 'cancel'
        });

        this.context.on('tinymce:oninit', this.handleTinyMceInit, this);

        this.action = 'edit';
        this.createMode = true;
        this._lastSelectedSignature = app.user.getPreference("signature_default");
    },

    _render: function () {
        var self= this;
        var url,
            $editor;

        this._super("_render");
        if (this.createMode) {
            if (this.getField('name')) {
                this.setTitle(app.lang.get('LBL_PMSE_EMAIL_TEMPLATES_DASHLET', this.module) + ' | ' + this.getField('name').value);
            }
        }
    },

    /**
     * Cancel and close the drawer
     */
    cancel: function() {
            this.toggleEdit(false);
            this.inlineEditMode = false;
            App.router.navigate("pmse_Emails_Templates", {trigger: true});
    },

    /**
     * Send the email immediately or warn if user did not provide subject or body
     */
    save: function() {
        this.model.doValidate(this.getFields(this.module), _.bind(function(isValid) {
            if (isValid) {
                this.validationCompleteApprove(this.model,false);
            }
        }, this));
    },
    validationCompleteApprove: function (model,exit) {
        var url, attributes, bodyHtml, subject, route = this.context.get("module");

        url = App.api.buildURL('pmse_Emails_Templates', null, {id: this.context.attributes.modelId});
        bodyHtml = model.get('body_html');//bodyHtml = this.model.get('body_html');
        subject = model.get('subject');//subject = this.model.get('subject');

        attributes = {
            body_html: bodyHtml,
            subject: subject,
            description:model.get('description'),//description:this.model.get('description'),
            name: model.get('name')//name: this.model.get('name'),
        };
        App.alert.show('upload', {level: 'process', title: 'LBL_SAVING', autoclose: false});
        App.api.call('update', url, attributes, {
            success: function (data) {
                App.alert.dismiss('upload');
                if(exit)
                {
                    model.revertAttributes();
                    App.router.redirect(route);
                }
            },
            error: function (err) {
                App.alert.dismiss('upload');
            }
        });
    },
    saveExit: function() {
        this.model.doValidate(this.getFields(this.module), _.bind(function(isValid) {
            if (isValid) {
                this.validationCompleteApprove(this.model,true);
            }
        }, this));
    },
    _dispose: function() {
        if (App.drawer) {
            App.drawer.off(null, null, this);
        }
        this._super("_dispose");
    },
    handleTinyMceInit: function() {
        this.resizeEditor();
    },
    /**
     * Resize the html editor based on height of the drawer it is in
     *
     * @param drawerHeight current height of the drawer or height the drawer will be after animations
     */
    resizeEditor: function(drawerHeight) {
        var $editor, headerHeight, recordHeight, showHideHeight, diffHeight, editorHeight, newEditorHeight;

        $editor = this.$('.mceLayout .mceIframeContainer iframe');
        //if editor not already rendered, cannot resize
        if ($editor.length === 0) {
            return;
        }

        drawerHeight = drawerHeight || app.drawer.getHeight();
        headerHeight = this.$('.headerpane').outerHeight(true);
        recordHeight = this.$('.record').outerHeight(true);
        showHideHeight = this.$('.show-hide-toggle').outerHeight(true);
        editorHeight = $editor.height();

        //calculate the space left to fill - subtracting padding to prevent scrollbar
        diffHeight = drawerHeight - headerHeight - recordHeight - showHideHeight - this.EDITOR_RESIZE_PADDING;

        //add the space left to fill to the current height of the editor to get a new height
        newEditorHeight = editorHeight + diffHeight;

        //maintain min height
        if (newEditorHeight < this.MIN_EDITOR_HEIGHT) {
            newEditorHeight = this.MIN_EDITOR_HEIGHT;
        }

        //set the new height for the editor
        $editor.height(newEditorHeight);
    }

})
