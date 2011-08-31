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
 * $Id: Menu.php 31648 2008-02-07 16:05:08Z jmertic $
 * Description:  
 ********************************************************************************/

global $mod_strings;
$module_menu = Array(
	Array("index.php?module=ProductTemplates&action=EditView&return_module=ProductTemplates&return_action=DetailView", $mod_strings['LNK_NEW_PRODUCT'],"Products"),
	Array("index.php?module=ProductTemplates&action=index&return_module=ProductTemplates&return_action=DetailView", $mod_strings['LNK_PRODUCT_LIST'],"Price_List"),
	Array("index.php?module=Manufacturers&action=EditView&return_module=Manufacturers&return_action=DetailView", $mod_strings['LNK_NEW_MANUFACTURER'],"Manufacturers"),
	Array("index.php?module=ProductCategories&action=EditView&return_module=ProductCategories&return_action=DetailView", $mod_strings['LNK_NEW_PRODUCT_CATEGORY'],"Product_Categories"),
	Array("index.php?module=ProductTypes&action=EditView&return_module=ProductTypes&return_action=DetailView", $mod_strings['LNK_NEW_PRODUCT_TYPE'],"Product_Types"),
    Array("index.php?module=Import&action=Step1&import_module=ProductTypes&return_module=ProductTypes&return_action=index", $mod_strings['LNK_IMPORT_PRODUCT_TYPES'],"Import"),
	);

?>
