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
 *Portions created by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights
 *Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: Locale.php 55866 2010-04-07 19:53:06Z jmertic $
 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 * *******************************************************************************/
global $current_user, $sugar_config;
if (!is_admin($current_user)) sugar_die("Unauthorized access to administration.");

require_once('modules/Configurator/Configurator.php');


echo getClassicModuleTitle(
        "Administration", 
        array(
            "<a href='index.php?module=Administration&action=index'>".translate('LBL_MODULE_NAME','Administration')."</a>",
           $mod_strings['LBL_MANAGE_LOCALE'],
           ), 
        true
        );

$cfg			= new Configurator();
$sugar_smarty	= new Sugar_Smarty();
$errors			= '';

///////////////////////////////////////////////////////////////////////////////
////	HANDLE CHANGES
if(isset($_REQUEST['process']) && $_REQUEST['process'] == 'true') {
	if(isset($_REQUEST['collation']) && !empty($_REQUEST['collation'])) {
		//kbrill Bug #14922
		if(array_key_exists('collation', $sugar_config['dbconfigoption']) && $_REQUEST['collation'] != $sugar_config['dbconfigoption']['collation']) {
			$GLOBALS['db']->disconnect();
			$GLOBALS['db']->connect();
		}

		$cfg->config['dbconfigoption']['collation'] = $_REQUEST['collation'];
	}
	$cfg->populateFromPost();
	$cfg->handleOverride();
	header('Location: index.php?module=Administration&action=index');
}

///////////////////////////////////////////////////////////////////////////////
////	DB COLLATION
if($GLOBALS['db']->dbType == 'mysql') {
	// set sugar default if not set from before
	if(!isset($sugar_config['dbconfigoption']['collation'])) {
		$sugar_config['dbconfigoption']['collation'] = 'utf8_general_ci';
	}

	$sugar_smarty->assign('dbType', 'mysql');
	$q = "SHOW COLLATION LIKE 'utf8%'";
	$r = $GLOBALS['db']->query($q);
	$collationOptions = '';
	while($a = $GLOBALS['db']->fetchByAssoc($r)) {
		$selected = '';
		if($sugar_config['dbconfigoption']['collation'] == $a['Collation']) {
			$selected = " SELECTED";
		}
		$collationOptions .= "\n<option value='{$a['Collation']}'{$selected}>{$a['Collation']}</option>";
	}
	$sugar_smarty->assign('collationOptions', $collationOptions);
}
////	END DB COLLATION
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	PAGE OUTPUT
$sugar_smarty->assign('MOD', $mod_strings);
$sugar_smarty->assign('APP', $app_strings);
$sugar_smarty->assign('APP_LIST', $app_list_strings);
$sugar_smarty->assign('LANGUAGES', get_languages());
$sugar_smarty->assign("JAVASCRIPT",get_set_focus_js());
$sugar_smarty->assign('config', $sugar_config);
$sugar_smarty->assign('error', $errors);
$sugar_smarty->assign("exportCharsets", get_select_options_with_id($locale->getCharsetSelect(), $sugar_config['default_export_charset']));
//$sugar_smarty->assign('salutation', 'Mr.');
//$sugar_smarty->assign('first_name', 'John');
//$sugar_smarty->assign('last_name', 'Doe');
$sugar_smarty->assign('getNameJs', $locale->getNameJs());

$sugar_smarty->display('modules/Administration/Locale.tpl');

?>