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
 * (i) the "Powered by SugarCRM" logo and 
 * (ii) the SugarCRM copyright notice 
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer 
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.  
 ********************************************************************************/
/*********************************************************************************
 * $Id: Delete.php 50002 2009-08-06 23:43:57Z sgandhi $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


$focus = BeanFactory::getBean('Emails');

if(!isset($_REQUEST['record']))
	sugar_die("A record number must be specified to delete the email.");
$focus->retrieve($_REQUEST['record']);
$email_type = $focus->type;
if(!$focus->ACLAccess('Delete')){
	ACLController::displayNoAccess(true);
	sugar_cleanup(true);
}
$focus->mark_deleted($_REQUEST['record']);

// make sure assigned_user_id is set - during testing this isn't always set
if (!isset($_REQUEST['assigned_user_id'])) {
	$_REQUEST['assigned_user_id'] = '';
}

if ($email_type == 'archived') {
	global $current_user;
    $loc = 'Location: index.php?module=Emails';
} else {
$loc = 'Location: index.php?module='.$_REQUEST['return_module'].'&action='.$_REQUEST['return_action'].'&record='.$_REQUEST['return_id'].'&type='.$_REQUEST['type'].'&assigned_user_id='.$_REQUEST['assigned_user_id'];
}

header($loc);
?>
