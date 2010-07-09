<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-enterprise-eula.html
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
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2007 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: Forms.php 56650 2010-05-24 18:53:17Z jenny $
 * Description:  Contains a variety of utility functions for the Import module
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/

/**
 * Returns the bean object of the given module
 *
 * @param  string $module
 * @return object
 */
function loadImportBean(
    $module
    )
{
    $focus = SugarModule::get($module)->loadBean(false);
    if ( $focus ) {
        if ( !$focus->importable )
            return false;
        if ( $module == 'Users' && !is_admin($GLOBALS['current_user']) && !is_admin_for_module($GLOBALS['current_user'],'Users'))
            return false;
        if ( $focus->bean_implements('ACL')){
            if(!ACLController::checkAccess($focus->module_dir, 'import', true)){
                ACLController::displayNoAccess();
                sugar_die('');
            }
        }
    }
    else {
        return false;
    }
    
    return $focus;
}

/**
 * Displays the Smarty template for an error
 *
 * @param string $message error message to show
 * @param string $module what module we were importing into
 * @param string $action what page we should go back to
 */
function showImportError(
    $message, 
    $module,
    $action = 'Step1'
    )
{
    $ss = new Sugar_Smarty();
    
	$ss->assign("MESSAGE",$message);
    $ss->assign("ACTION",$action);
    $ss->assign("IMPORT_MODULE",$module);
    $ss->assign("MOD", $GLOBALS['mod_strings']);
    $ss->assign("SOURCE","");
    if ( isset($_REQUEST['source']) )
        $ss->assign("SOURCE", $_REQUEST['source']);
    
    echo $ss->fetch('modules/Import/tpls/error.tpl');
}

/**
 * Replaces PHP error handler in Step4
 *
 * @param int    $errno
 * @param string $errstr
 * @param string $errfile
 * @param string $errline
 */
function handleImportErrors(
    $errno, 
    $errstr, 
    $errfile, 
    $errline
    )
{
    if ( !defined('E_DEPRECATED') )
        define('E_DEPRECATED','8192');
    if ( !defined('E_USER_DEPRECATED') )
        define('E_USER_DEPRECATED','16384');
    
    // check to see if current reporting level should be included based upon error_reporting() setting, if not
    // then just return
    if ( !(error_reporting() & $errno) )
        return true;
    
    switch ($errno) {
    case E_USER_ERROR:
        echo "ERROR: [$errno] $errstr on line $errline in file $errfile<br />\n";
        exit(1);
        break;
    case E_USER_WARNING:
    case E_WARNING:
        echo "WARNING: [$errno] $errstr on line $errline in file $errfile<br />\n";
        break;
    case E_USER_NOTICE:
    case E_NOTICE:
        echo "NOTICE: [$errno] $errstr on line $errline in file $errfile<br />\n";
        break;
    case E_STRICT:
    case E_DEPRECATED:
    case E_USER_DEPRECATED:
        // don't worry about these
        //echo "STRICT ERROR: [$errno] $errstr on line $errline in file $errfile<br />\n";
        break;
    default:
        echo "Unknown error type: [$errno] $errstr on line $errline in file $errfile<br />\n";
        break;
    }

    return true;
}

/**
 * Returns an input control for this fieldname given
 *
 * @param  string $module
 * @param  string $fieldname
 * @param  string $vardef
 * @param  string $value
 * @return string html for input element for this control
 */
function getControl(
    $module,
    $fieldname,
    $vardef = null,
    $value = ''
    )
{
    global $current_language, $app_strings, $dictionary, $app_list_strings;
    
    // use the mod_strings for this module
    $mod_strings = return_module_language($current_language,$module);
    
 	// set the filename for this control
    $file = create_cache_directory('modules/Import/') . $module . $fieldname . '.tpl';

    if ( !is_file($file) 
            || !empty($GLOBALS['sugar_config']['developerMode']) 
            || !empty($_SESSION['developerMode']) ) {
        
        if ( !isset($vardef) ) {
            $focus = loadBean($module);
            $vardef = $focus->getFieldDefinition($fieldname);
        }
        
        // if this is the id relation field, then don't have a pop-up selector.
        if( $vardef['type'] == 'relate' && $vardef['id_name'] == $vardef['name']) { 
            $vardef['type'] = 'varchar'; 
        }
        
        // create the dropdowns for the parent type fields
        if ( $vardef['type'] == 'parent_type' ) {
            $vardef['type'] = 'enum';
        }
        
        // remove the special text entry field function 'getEmailAddressWidget'
        if ( isset($vardef['function']) 
                && ( $vardef['function'] == 'getEmailAddressWidget' 
                    || $vardef['function']['name'] == 'getEmailAddressWidget' ) )
            unset($vardef['function']);
        
        // load SugarFieldHandler to render the field tpl file
        static $sfh;
        
        if(!isset($sfh)) {
            require_once('include/SugarFields/SugarFieldHandler.php');
            $sfh = new SugarFieldHandler();
        }
        
        $displayParams = array();
        $displayParams['formName'] = 'importstep3';    

        $view = 'EditView';
        //BEGIN SUGARCRM flav=pro ONLY
        //Special case handling for the teamset view
        if(!empty($vardef['custom_type']) && $vardef['custom_type'] == 'teamset') {
           $view = 'importView';
        }
        //END SUGARCRM flav=pro ONLY
        
        $contents = $sfh->displaySmarty('fields', $vardef, $view, $displayParams);
        
        // Remove all the copyright comments
        $contents = preg_replace('/\{\*[^\}]*?\*\}/', '', $contents);
        
        // hack to disable one of the js calls in this control
        if ( isset($vardef['function']) 
                && ( $vardef['function'] == 'getCurrencyDropDown' 
                    || $vardef['function']['name'] == 'getCurrencyDropDown' ) )
        $contents .= "{literal}<script>function CurrencyConvertAll() { return; }</script>{/literal}";
        
        // Save it to the cache file
        if($fh = @sugar_fopen($file, 'w')) {
            fputs($fh, $contents);
            fclose($fh);
        }
    }
    
    // Now render the template we received
    $ss = new Sugar_Smarty();
    
    // Create Smarty variables for the Calendar picker widget
    global $timedate;
    $time_format = $timedate->get_user_time_format();
    $date_format = $timedate->get_cal_date_format();
    $time_separator = ":";
    $match = array();
    if(preg_match('/\d+([^\d])\d+([^\d]*)/s', $time_format, $match)) {
        $time_separator = $match[1];
    }
    $t23 = strpos($time_format, '23') !== false ? '%H' : '%I';
    if(!isset($match[2]) || $match[2] == '') {
        $ss->assign('CALENDAR_FORMAT', $date_format . ' ' . $t23 . $time_separator . "%M");
    } 
    else {
        $pm = $match[2] == "pm" ? "%P" : "%p";
        $ss->assign('CALENDAR_FORMAT', $date_format . ' ' . $t23 . $time_separator . "%M" . $pm);
    }
    
    // populate the fieldlist from the vardefs
    $fieldlist = array();
    if ( !isset($focus) || !($focus instanceof SugarBean) )
        $focus = loadBean($module);
    // create the dropdowns for the parent type fields
    if ( $vardef['type'] == 'parent_type' ) {
        $focus->field_defs[$vardef['name']]['options'] = $focus->field_defs[$vardef['group']]['options'];
    }
    $vardefFields = $focus->getFieldDefinitions();
    foreach ( $vardefFields as $name => $properties ) {
        $fieldlist[$name] = $properties;
        // fill in enums
        if(isset($fieldlist[$name]['options']) && is_string($fieldlist[$name]['options']) && isset($app_list_strings[$fieldlist[$name]['options']]))
            $fieldlist[$name]['options'] = $app_list_strings[$fieldlist[$name]['options']];
        // Bug 32626: fall back on checking the mod_strings if not in the app_list_strings
        elseif(isset($fieldlist[$name]['options']) && is_string($fieldlist[$name]['options']) && isset($mod_strings[$fieldlist[$name]['options']]))
            $fieldlist[$name]['options'] = $mod_strings[$fieldlist[$name]['options']];
        // Bug 22730: make sure all enums have the ability to select blank as the default value.
        if(!isset($fieldlist[$name]['options']['']))
            $fieldlist[$name]['options'][''] = '';
    }
    // fill in function return values
    if ( !in_array($fieldname,array('email1','email2')) ) {
        if (!empty($fieldlist[$fieldname]['function']['returns']) && $fieldlist[$fieldname]['function']['returns'] == 'html'){
            $function = $fieldlist[$fieldname]['function']['name'];
            // include various functions required in the various vardefs
            if ( isset($fieldlist[$fieldname]['function']['include']) && is_file($fieldlist[$fieldname]['function']['include']))
                require_once($fieldlist[$fieldname]['function']['include']);
            $value = $function($focus, $fieldname, $value, 'EditView');
            // Bug 22730 - add a hack for the currency type dropdown, since it's built by a function.
            if ( preg_match('/getCurrency.*DropDown/s',$function)  )
                $value = str_ireplace('</select>','<option value="">'.$app_strings['LBL_NONE'].'</option></select>',$value);
        }
    }
    $fieldlist[$fieldname]['value'] = $value;
    $ss->assign("fields",$fieldlist);
    $ss->assign("form_name",'importstep3');
    $ss->assign("bean",$focus);
    
    // add in any additional strings
    $ss->assign("MOD", $mod_strings);
    $ss->assign("APP", $app_strings);
    return $ss->fetch($file);
}
