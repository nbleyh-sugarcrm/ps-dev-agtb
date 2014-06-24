<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
//FILE SUGARCRM flav=pro ONLY
global $current_user;
$silent = isset($_REQUEST['silent']) ? true : false;
if(is_admin($current_user)){
    global $mod_strings;
    if (!$silent) { echo $mod_strings['LBL_CLEAR_PDFFONTS_DESC']; }
    require_once('include/Sugarpdf/FontManager.php');
    $fontManager = new FontManager();
    if($fontManager->clearCachedFile()){
        if( !$silent ) echo '<br><br><br><br>' . $mod_strings['LBL_CLEAR_PDFFONTS_DESC_SUCCESS'];
    }else{
        if( !$silent ) echo '<br><br><br><br>' . $mod_strings['LBL_CLEAR_PDFFONTS_DESC_FAILURE'];
    }
}
else{
    sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']); 
}
?>