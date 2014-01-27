/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement (""License"") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the ""Powered by SugarCRM"" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
//FILE SUGARCRM flav=ent ONLY
/**
 * Add support for changing application language when the Portal user's preferred language changes
 * @class View.PortalContactsRecordView
 * @alias SUGAR.App.view.views.PortalContactsRecordView
 * @extends View.RecordView
 */
({
    extendsFrom: 'RecordView',
    /**
     * @override
     */
    bindDataChange: function(){
        this._super("bindDataChange");
        this.context.on("button:save_button:click", this._setPreferredLanguage, this);
    },
    /**
     * Update application language based on Portal user's preferred language
     * @private
     */
    _setPreferredLanguage: function(){
        var newLang = this.model.get("preferred_language");
        if(_.isString(newLang) && newLang !== app.lang.getLanguage()){
            app.alert.show('language', {level: 'warning', title: 'LBL_LOADING_LANGUAGE', autoclose: false});
            app.lang.setLanguage(newLang, function(){
                app.alert.dismiss('language');
            });

        }
    }
})
