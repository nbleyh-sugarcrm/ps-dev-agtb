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
    contextEvents: {
        "list:reassign:fire": "reassignCase"
    },

    reassignCase: function (model) {
        //console.log('Unattended Cases: ', model);
        //open drawer
        var self=this;
        app.drawer.open({
            layout: 'reassignCases',
            context: {
                module: 'pmse_Inbox',
                parent: this.context,
                cas_id: model.get('cas_id'),
                unattended: true
            }

        },
            function(variables) {
                if(variables==='saving'){
                    self.reloadList();
                }
            });
    },
    reloadList: function() {
        var self = this;
        self.context.reloadData({
            recursive:false,
            error:function(error){
                console.log(error);
            }
        });
    }
})