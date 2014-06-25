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
/*********************************************************************************
 * $Id: SubPanelView.php 45763 2009-04-01 19:16:18Z majed $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/





global $app_strings;
//we don't want the parent module's string file, but rather the string file specifc to this subpanel
global $current_language;
$current_module_strings = return_module_language($current_language, 'DataSets');

global $currentModule;
global $theme;
global $focus;
global $action;




// focus_list is the means of passing data to a SubPanelView.
global $focus_list;

global $current_user;
$header_text = '';

//if(is_admin($current_user) && $_REQUEST['module'] != 'DynamicLayout' && !empty($_SESSION['editinplace'])){	
//		$header_text = "&nbsp;<a href='index.php?action=index&module=DynamicLayout&from_action=SubPanelView&from_module=Leads&record=". $_REQUEST['record']."'>".SugarThemeRegistry::current()->getImage("EditLayout","border='0' align='bottom'",null,null,'.gif',$mod_strings['LBL_EDIT_LAYOUT'])."</a>";
//}

$button  = "<table cellspacing='0' border='0' width='100%'><tr><td nowrap><form  action='index.php' method='post' name='form' id='form'>\n";
$button .= "<input type='hidden' name='module' value='DataSets'>\n";
$button .= "<input type='hidden' name='return_module' value='".$currentModule."'>\n";
$button .= "<input type='hidden' name='return_action' value='".$action."'>\n";
$button .= "<input type='hidden' name='return_id' value='".$focus->id."'>\n";
$button .= "<input type='hidden' name='record' value=''>\n";
$button .= "<input type='hidden' name='action'>\n";
$button .= "<input title='".$mod_strings['LBL_NEW_BUTTON_TITLE']."' class='button' onclick=\"this.form.action.value='EditView'\" type='submit' name='button' value='  ".$mod_strings['LBL_NEW_BUTTON_LABEL']."  '>\n";
$button .= "<input title='".$mod_strings['LBL_ADD_BUTTON_TITLE']."' class='button' onclick='return window.open(\"index.php?module=DataSets&action=Popup&form=AddDataSetEditView&form_submit=true\",\"test\",\"width=600,height=400,resizable=1,scrollbars=1\");' type='button' name='button' value='  ".$mod_strings['LBL_ADD_BUTTON_LABEL']."  '>\n";
$button .= "</td></tr></form></table>\n";


$ListView = new ListView();
$ListView->initNewXTemplate( 'modules/DataSets/SubPanelView.html',$current_module_strings);
$ListView->xTemplateAssign('UPARROW_INLINE', SugarThemeRegistry::current()->getImage('uparrow_inline','align="absmiddle" border="0"', null,null,'.gif',$mod_strings['LNK_UP']));
$ListView->xTemplateAssign('DOWNARROW_INLINE', SugarThemeRegistry::current()->getImage('downarrow_inline','align="absmiddle" border="0"', null,null,'.gif',$mod_strings['LNK_DOWN']));
$ListView->xTemplateAssign('DELETE_INLINE', SugarThemeRegistry::current()->getImage('delete_inline','align="absmiddle" border="0"', null,null,'.gif',$app_strings['LNK_DELETE']));
$ListView->xTemplateAssign('UPARROW_TEXT', $mod_strings['LNK_UP']);
$ListView->xTemplateAssign('DOWNARROW_TEXT', $mod_strings['LNK_DOWN']);
$ListView->xTemplateAssign('DELETE_TEXT', $app_strings['LNK_DELETE']);
$ListView->xTemplateAssign('REPORT_ID', $focus->id);
$ListView->setHeaderTitle($current_module_strings['LBL_MODULE_NAME'] . $header_text);
$ListView->setHeaderText($button);
$ListView->setQuery("", "", "list_order_y  ASC", "DATA_SET", false);

//need to pass the main id to have the dataset subpanel href properly
if(!empty($focus->id)) $ListView->xTemplateAssign("RECORD", $focus->id);
$ListView->xTemplateAssign("EDIT_INLINE_PNG",  SugarThemeRegistry::current()->getImage('edit_inline','align="absmiddle" border="0"', null,null,'.gif',$app_strings['LNK_EDIT']));

$ListView->processListView($focus_list, "main", "DATA_SET");
?>
