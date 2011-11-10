<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 *(i) the "Powered by SugarCRM" logo and
 *(ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright(C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once('include/MVC/View/views/view.edit.php');
require_once('modules/Users/UserViewHelper.php');


class UsersViewEdit extends ViewEdit {

 	function UsersViewEdit(){
 		parent::ViewEdit();
 	}

    function preDisplay() {
        $this->fieldHelper = new UserViewHelper($this->ss, $this->bean, 'EditView');
        $this->fieldHelper->setupAdditionalFields();

        parent::preDisplay();
    }

    public function getMetaDataFile() {
        $userType = 'Regular';
        //BEGIN SUGARCRM flav=ent ONLY
        if($this->fieldHelper->usertype == 'PORTAL_ONLY'){
            $userType = 'Portal';
        }
        //END SUGARCRM flav=ent ONLY
        //BEGIN SUGARCRM flav!=sales ONLY
        if($this->fieldHelper->usertype == 'GROUP'){
            $userType = 'Group';
        }
        //END SUGARCRM flav!=sales ONLY

        if ( $userType != 'Regular' ) {
            $oldType = $this->type;
            $this->type = $oldType.'group';
        }
        $metadataFile = parent::getMetaDataFile();
        if ( $userType != 'Regular' ) {
            $this->type = $oldType;
        }
        return $metadataFile;
    }

    function display() {
        global $current_user, $app_list_strings;


        //lets set the return values
        if(isset($_REQUEST['return_module'])){
            $this->ss->assign('RETURN_MODULE',$_REQUEST['return_module']);
        }

        if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
            $this->ss->assign('RETURN_MODULE', $_REQUEST['return_module']);
            $this->ss->assign('RETURN_ACTION', $_REQUEST['return_action']);
            $this->ss->assign('RETURN_ID', $_REQUEST['record']);
            $this->bean->id = "";
            $this->bean->user_name = "";
            $this->ss->assign('ID','');
        } else {
            if(isset($_REQUEST['return_module']))
            {
                $this->ss->assign('RETURN_MODULE', $_REQUEST['return_module']);
            } else {
                $this->ss->assign('RETURN_MODULE', $this->bean->module_dir);
            }

            if(isset($_REQUEST['return_id']))
            {
                $this->ss->assign('RETURN_ID', $_REQUEST['return_id']);
            } else {
                $this->ss->assign('RETURN_ID', $this->bean->id);
            }

            if(isset($_REQUEST['return_action']))
            {
                $this->ss->assign('RETURN_ACTION', $_REQUEST['return_action']);
            } else {
                $this->ss->assign('RETURN_ACTION', 'DetailView');
            }
        }


        ///////////////////////////////////////////////////////////////////////////////
        ////	REDIRECTS FROM COMPOSE EMAIL SCREEN
        if(isset($_REQUEST['type']) && (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Emails')) {
            $this->ss->assign('REDIRECT_EMAILS_TYPE', $_REQUEST['type']);
        }
        ////	END REDIRECTS FROM COMPOSE EMAIL SCREEN
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////
        ////	NEW USER CREATION ONLY
        if(empty($this->bean->id)) {
            $this->ss->assign('SHOW_ADMIN_CHECKBOX','height="30"');
            $this->ss->assign('NEW_USER','1');
        }else{
            $this->ss->assign('NEW_USER','0');
            $this->ss->assign('NEW_USER_TYPE','DISABLED');
            //BEGIN SUGARCRM flav=pro ONLY
            $this->ss->assign('REASSIGN_JS', "return confirmReassignRecords();");
            //END SUGARCRM flav=pro ONLY
        }

        ////	END NEW USER CREATION ONLY
        ///////////////////////////////////////////////////////////////////////////////

	    //BEGIN SUGARCRM lic=sub ONLY
        global $sugar_flavor;
        $admin = new Administration();
        $admin->retrieveSettings();

        if((isset($sugar_flavor) && $sugar_flavor != null) &&
           ($sugar_flavor=='CE' || isset($admin->settings['license_enforce_user_limit']) && $admin->settings['license_enforce_user_limit'] == 1)){
            if (empty($this->bean->id)) {
                $license_users = $admin->settings['license_users'];
                if ($license_users != '') {
                    $license_seats_needed = count( get_user_array(false, "", "", false, null, " AND ".User::getLicensedUsersWhere(), false) ) - $license_users;
                } else {
                    $license_seats_needed = -1;
                }
                if( $license_seats_needed >= 0 ){
                    displayAdminError( translate('WARN_LICENSE_SEATS_USER_CREATE', 'Administration') . translate('WARN_LICENSE_SEATS2', 'Administration')  );
                    if( isset($_SESSION['license_seats_needed'])) {
                        unset($_SESSION['license_seats_needed']);
                    }
                    //die();
                }
            }
        }
	    //END SUGARCRM lic=sub ONLY

        // FIXME: Translate error prefix
        if(isset($_REQUEST['error_string'])) $this->ss->assign('ERROR_STRING', '<span class="error">Error: '.$_REQUEST['error_string'].'</span>');
        if(isset($_REQUEST['error_password'])) $this->ss->assign('ERROR_PASSWORD', '<span id="error_pwd" class="error">Error: '.$_REQUEST['error_password'].'</span>');




        // Build viewable versions of a few fields for non-admins
        if(!empty($this->bean->id)) {
            if( !empty($this->bean->status) ) {
                $this->ss->assign('STATUS_READONLY',$app_list_strings['user_status_dom'][$this->bean->status]); }
            if( !empty($this->bean->employee_status) ) {
                $this->ss->assign('EMPLOYEE_STATUS_READONLY', $app_list_strings['employee_status_dom'][$this->bean->employee_status]);
            }
            if( !empty($this->bean->reports_to_id) ) {
                $this->ss->assign('REPORTS_TO_READONLY', get_assigned_user_name($this->bean->reports_to_id));
            }
        }

        $processSpecial = false;
        $processFormName = '';
        //BEGIN SUGARCRM flav!=sales ONLY
        if ( $this->fieldHelper->usertype == 'GROUP'
             //BEGIN SUGARCRM flav=ent ONLY
             || $this->fieldHelper->usertype == 'PORTAL_ONLY'
             //END SUGARCRM flav=ent ONLY
            ) {
            $this->ev->formName = 'EditViewGroup';
            
            $processSpecial = true;
            $processFormName = 'EditViewGroup';            
        }
        //END SUGARCRM flav!=sales ONLY

        $this->ev->process($processSpecial,$processFormName);

		echo $this->ev->display($this->showTitle);
        
    }

}