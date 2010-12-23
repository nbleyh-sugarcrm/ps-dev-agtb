<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-professional-eula.html
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
 * by SugarCRM are Copyright (C) 2004-2006 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

/**
 * Contains a variety of utility functions used to display UI components such as form headers and footers.
 *
 * @todo refactor out these functions into the base UI objects as indicated
 */

/**
 * Create HTML to display formatted form title.
 * 
 * @param  $form_title string to display as the title in the header
 * @param  $other_text string to next to the title.  Typically used for form buttons.
 * @param  $show_help  boolean which determines if the print and help links are shown.
 * @return string HTML
 */
function get_form_header(
    $form_title, 
    $other_text, 
    $show_help
    )
{
    global $sugar_version, $sugar_flavor, $server_unique_key, $current_language, $current_module, $current_action;
    global $app_strings;
    
    $blankImageURL = SugarThemeRegistry::current()->getImageURL('blank.gif');
    $printImageURL = SugarThemeRegistry::current()->getImageURL("print.gif");
    $helpImageURL  = SugarThemeRegistry::current()->getImageURL("help.gif");
    
    $is_min_max = strpos($other_text,"_search.gif");
    if($is_min_max !== false)
        $form_title = "{$other_text}&nbsp;{$form_title}";

    $the_form = <<<EOHTML
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="formHeader h3Row">
<tr>
<td nowrap><h3><span>{$form_title}</span></h3></td>
EOHTML;
    
    $keywords = array("/class=\"button\"/","/class='button'/","/class=button/","/<\/form>/");
    $match="";
    foreach ($keywords as $left)
        if (preg_match($left,$other_text))
            $match = true;
    
    if ($other_text && $match) {
        $the_form .= <<<EOHTML
<td colspan='10' width='100%'><IMG height='1' width='1' src='$blankImageURL' alt=''></td>
</tr>
<tr>
<td align='left' valign='middle' nowrap style='padding-bottom: 2px;'>$other_text</td>
<td width='100%'><IMG height='1' width='1' src='$blankImageURL' alt=''></td>
EOHTML;
        if ($show_help) {
            $the_form .= "<td align='right' nowrap>";
            if ($_REQUEST['action'] != "EditView") {
                $the_form .= <<<EOHTML
    <a href='index.php?{$GLOBALS['request_string']}' class='utilsLink'>
    <img src='{$printImageURL}' alt='Print' border='0' align='absmiddle'>
    </a>&nbsp;
    <a href='index.php?{$GLOBALS['request_string']}' class='utilsLink'>
    {$app_strings['LNK_PRINT']}
    </a>
EOHTML;
            }
            $the_form .= <<<EOHTML
&nbsp;
    <a href='index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$current_module}&help_action={$current_action}&key={$server_unique_key}'
       class='utilsLink' target='_blank'>
    <img src='{$helpImageURL}' alt='Help' border='0' align='absmiddle'>
    </a>&nbsp;
    <a href='index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$current_module}&help_action={$current_action}&key={$server_unique_key}'
        class='utilsLink' target='_blank'>
    {$app_strings['LNK_HELP']}
    </a>
</td>
EOHTML;
        }
    } 
    else {
        if ($other_text && $is_min_max === false) {
            $the_form .= <<<EOHTML
<td width='20'><img height='1' width='20' src='$blankImageURL' alt=''></td>
<td valign='middle' nowrap width='100%'>$other_text</td>
EOHTML;
        }
        else {
            $the_form .= <<<EOHTML
<td width='100%'><IMG height='1' width='1' src='$blankImageURL' alt=''></td>
EOHTML;
        }
    
        if ($show_help) {
            $the_form .= "<td align='right' nowrap>";
            if ($_REQUEST['action'] != "EditView") {
                $the_form .= <<<EOHTML
    <a href='index.php?{$GLOBALS['request_string']}' class='utilsLink'>
    <img src='{$printImageURL}' alt='Print' border='0' align='absmiddle'>
    </a>&nbsp;
    <a href='index.php?{$GLOBALS['request_string']}' class='utilsLink'>
    {$app_strings['LNK_PRINT']}</a>
EOHTML;
            }
            $the_form .= <<<EOHTML
    &nbsp;
    <a href='index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$current_module}&help_action={$current_action}&key={$server_unique_key}'
       class='utilsLink' target='_blank'>
    <img src='{$helpImageURL}' alt='Help' border='0' align='absmiddle'>
    </a>&nbsp;
    <a href='index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$current_module}&help_action={$current_action}&key={$server_unique_key}'
        class='utilsLink' target='_blank'>{$app_strings['LNK_HELP']}</a>
</td>
EOHTML;
        }
    }
    
    $the_form .= <<<EOHTML
</tr>
</table>
EOHTML;
    
    return $the_form;
}

/**
 * Wrapper function for the get_module_title function, which is mostly used for pre-MVC modules.
 * 
 * @deprecated use SugarView::getModuleTitle() for MVC modules, or getClassicModuleTitle() for non-MVC modules
 *
 * @param  $module       string  to next to the title.  Typically used for form buttons.
 * @param  $module_title string  to display as the module title
 * @param  $show_help    boolean which determines if the print and help links are shown.
 * @return string HTML
 */
function get_module_title(
    $module, 
    $module_title, 
    $show_help
    )
{
    global $sugar_version, $sugar_flavor, $server_unique_key, $current_language, $action;
    global $app_strings;
    
    $the_title = "<div class='moduleTitle'>\n<h2>";
    $module = preg_replace("/ /","",$module);
    $iconPath = "";
    if(is_file(SugarThemeRegistry::current()->getImageURL('icon_'.$module.'_32.png',false)))
    {
    	$iconPath = SugarThemeRegistry::current()->getImageURL('icon_'.$module.'_32.png');
    } else if (is_file(SugarThemeRegistry::current()->getImageURL('icon_'.ucfirst($module).'_32.png',false)))
    {
        $iconPath = SugarThemeRegistry::current()->getImageURL('icon_'.ucfirst($module).'_32.png');
    }
    if (!empty($iconPath)) {
        $the_title .= "<a href='index.php?module={$module}&action=index'><img src='{$iconPath}' " 
                    . "alt='".$module."' title='".$module."' align='absmiddle'></a>".$module_title;	
    } else {
		$the_title .= $module_title;
	}
    $the_title .= "</h2>\n";
    
    if ($show_help) {
        $the_title .= "<span class='utils'>";
        if (isset($action) && $action != "EditView") {
            $printImageURL = SugarThemeRegistry::current()->getImageURL('print.gif');
            $the_title .= <<<EOHTML
<a href="javascript:void window.open('index.php?{$GLOBALS['request_string']}','printwin','menubar=1,status=0,resizable=1,scrollbars=1,toolbar=0,location=1')" class='utilsLink'>
<img src="{$printImageURL}" alt="{$GLOBALS['app_strings']['LNK_PRINT']}"></a>
<a href="javascript:void window.open('index.php?{$GLOBALS['request_string']}','printwin','menubar=1,status=0,resizable=1,scrollbars=1,toolbar=0,location=1')" class='utilsLink'>
{$GLOBALS['app_strings']['LNK_PRINT']}
</a>
EOHTML;
        }
        $helpImageURL = SugarThemeRegistry::current()->getImageURL('help.gif');
        $the_title .= <<<EOHTML
&nbsp;
<a href="index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$module}&help_action={$action}&key={$server_unique_key}" class="utilsLink" target="_blank">
<img src='{$helpImageURL}' alt='{$GLOBALS['app_strings']['LNK_HELP']}'></a>
<a href="index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugar_version}&edition={$sugar_flavor}&lang={$current_language}&help_module={$module}&help_action={$action}&key={$server_unique_key}" class="utilsLink" target="_blank">
{$GLOBALS['app_strings']['LNK_HELP']}
</a>
EOHTML;
        $the_title .= '</span>';
    }
    
    $the_title .= "</div>\n";
    return $the_title;
}

/**
 * Handles displaying the header for classic view modules
 *
 * @param  $module      string  to next to the title.  Typically used for form buttons.
 * @param array $params optional, params to display in the breadcrumb, overriding SugarView::_getModuleTitleParams()
 * These should be in the form of array('label' => '<THE LABEL>', 'link' => '<HREF TO LINK TO>');
 * the first breadcrumb should be index at 0, and built from there e.g.
 * <code>
 * array(
 *    '<a href="index.php?module=Contacts&action=index">Contacts</a>',
 *    '<a href="index.php?module=Contacts&action=DetailView&record=123">John Smith</a>',
 *    'Edit',
 *    );
 * </code>
 * would display as:
 * <a href='index.php?module=Contacts&action=index'>Contacts</a> >> <a href='index.php?module=Contacts&action=DetailView&record=123'>John Smith</a> >> Edit   
 * @param  $show_help    boolean which determines if the print and help links are shown.
 * @return string HTML
 */
function getClassicModuleTitle(
    $module, 
    $params, 
    $show_help)
{
	$module_title = '';
	$count = count($params);
	$index = 0;
	foreach($params as $parm){
		$index++;
	   	$module_title .= $parm;
	   	if($index < $count){
	    	$module_title .= "<span class='pointer'>&raquo;</span>";
	    }
	}
	return get_module_title($module, $module_title, $show_help, true);
}

/**
 * Create a header for a popup.
 *
 * @todo refactor this into the base Popup_Picker class
 *
 * @param  $theme string the name of the current theme, ignorred to use SugarThemeRegistry::current() instead.
 * @return string HTML
 */
function insert_popup_header(
    $theme = null
    )
{
    global $app_strings, $sugar_config;
    
    $charset = isset($app_strings['LBL_CHARSET']) 
        ? $app_strings['LBL_CHARSET'] : $sugar_config['default_charset'];
    
    $themeCSS = SugarThemeRegistry::current()->getCSS();
    
    echo <<<EOHTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset="{$charset}">
<title>{$app_strings['LBL_BROWSER_TITLE']}</title>
{$themeCSS}
EOHTML;
    echo '<script type="text/javascript" src="' . getJSPath('include/javascript/sugar_grp1_yui.js') . '"></script>';
    echo '<script type="text/javascript" src="' . getJSPath('include/javascript/sugar_grp1.js') . '"></script>';
    echo <<<EOHTML
</head>
<body class="popupBody">
EOHTML;
}

/**
 * Create a footer for a popup.
 *
 * @todo refactor this into the base Popup_Picker class
 *
 * @return string HTML
 */
function insert_popup_footer()
{
    echo <<<EOQ
</body>
</html>
EOQ;
}
