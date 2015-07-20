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

// $Id: dbConfig.php 15268 2006-08-01 01:12:01 +0000 (Tue, 01 Aug 2006) eddy $
global $sugar_version, $js_custom_version;


if(empty($_SESSION['setup_db_host_name'])){
      $_SESSION['setup_db_host_name'] = (isset($sugar_config['db_host_name']))  ? $sugar_config['db_host_name'] :  $_SERVER['SERVER_NAME'];
}

if( !isset( $install_script ) || !$install_script ){
	die($mod_strings['ERR_NO_DIRECT_SCRIPT']);
}


// DB split
$createDbCheckbox = '';
$createDb = (!empty($_SESSION['setup_db_create_database'])) ? 'checked="checked"' : '';
$dropCreate = (!empty($_SESSION['setup_db_drop_tables'])) ? 'checked="checked"' : '';
$instanceName = '';
if (isset($_SESSION['setup_db_host_instance']) && !empty($_SESSION['setup_db_host_instance'])){
	$instanceName = $_SESSION['setup_db_host_instance'];
}

$setupDbPortNum ='';
if (isset($_SESSION['setup_db_port_num']) && !empty($_SESSION['setup_db_port_num'])){
	$setupDbPortNum = $_SESSION['setup_db_port_num'];
}

$db = getInstallDbInstance();

///////////////////////////////////////////////////////////////////////////////
////	BEGIN PAGE OUTPUT

$langHeader = get_language_header();
$versionToken = getVersionedPath(null);

$out =<<<EOQ
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html {$langHeader}>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <title>{$mod_strings['LBL_WIZARD_TITLE']} {$mod_strings['LBL_DBCONF_TITLE']}</title>
    <link rel="stylesheet" href="install/install.css" type="text/css" />
    <script type="text/javascript" src="install/installCommon.js"></script>
    <script type="text/javascript" src="install/dbConfig.js"></script>
    <link REL="SHORTCUT ICON" HREF="include/images/sugar_icon.ico">
    <script src="cache/include/javascript/sugar_grp1_yui.js?v={$versionToken}"></script>
    <script src="cache/include/javascript/sugar_grp1_jquery.js?v={$versionToken}"></script>
    <script type="text/javascript">
    <!--
    if ( YAHOO.env.ua )
        UA = YAHOO.env.ua;
    -->
    </script>
    <link rel='stylesheet' type='text/css' href='include/javascript/yui/build/container/assets/container.css' />

</head>
EOQ;
$out .= '<body onload="document.getElementById(\'button_next2\').focus();">';

$out2 =<<<EOQ2
<form action="install.php" method="post" name="setConfig" id="form">
<input type='hidden' name='setup_db_drop_tables' id='setup_db_drop_tables' value=''>
<input type="hidden" id="hidden_goto" name="goto" value="{$mod_strings['LBL_BACK']}" />
<table cellspacing="0" cellpadding="0" border="0" align="center" class="shell">

      <tr><td colspan="2" id="help"><a href="{$help_url}" target='_blank'>{$mod_strings['LBL_HELP']} </a></td></tr>
    <tr>
      <th width="500">
		<p>
		<img src="{$sugar_md}" alt="SugarCRM" border="0">
		</p>
		{$mod_strings['LBL_DBCONF_TITLE']}
	</th>
	<th width="200" height="30" style="text-align: right;"><a href="http://www.sugarcrm.com" target="_blank">
		<IMG src="include/images/sugarcrm_login.png" alt="SugarCRM" border="0"></a>
        </th>
</tr>
<tr>
	<td colspan="2">
EOQ2;

if ( !empty($si_errors) && sizeof($db_errors) > 0 )
{
    $out2 .= '<div id="errorMsgs"><ul>';
    foreach ($db_errors as $error)
    {
        $out2 .= '<li class="error">' . $error . '</li>';
    }
    $out2 .= '</ul></div>';
}
else
{
    $out2 .= '<div id="errorMsgs" style="display:none"></div>';
}

$out2 .=<<<EOQ2

<div class="required">{$mod_strings['LBL_REQUIRED']}</div>
<table width="100%" cellpadding="0" cellpadding="0" border="0" class="StyleDottedHr">
<tr><th colspan="3" align="left" >{$mod_strings['LBL_DBCONF_TITLE_NAME']} </td></tr>
EOQ2;

$config_params = $db->installConfig();
$form = '';
foreach($config_params as $group => $gdata) {
    $form .= "<tr><td colspan=\"3\" align=\"left\">{$mod_strings[$group]}</td></tr>\n";
    foreach($gdata as $name => $value) {
        if(!empty($value)) {
            $form .= "<tr>";
            if(!empty($value['required'])) {
               $form .= "<td><span class=\"required\">*</span></td>\n";
            } else {
                $form .= "<td>&nbsp;</td>\n";
            }
            if(!empty($_SESSION[$name])) {
                $sessval = $_SESSION[$name];
            } else {
                $sessval = '';
            }
            if(!empty($value["type"])) {
                $type = $value["type"];
            } else {
                $type = '';
            }

            $form .= <<<FORM
            <td nowrap><b>{$mod_strings[$value["label"]]}</b></td>
            <td align="left">
FORM;
            //if the type is password, set a hidden field to capture the value.  This is so that we can properly encode special characters, which is a limitation with password fields
            if($type=='password'){
                $form .= "<input type='$type' name='{$name}_entry' id='{$name}_entry' value='".urldecode($sessval)."'><input type='hidden' name='$name' id='$name' value='".urldecode($sessval)."'>";
            }else{
                $form .= "<input type='$type' name='$name' id='$name' value='$sessval'>";
            }



            $form .= <<<FORM
            </td></tr>
FORM;

        } else {
            $form .= "<input name=\"$name\" id=\"$name\" value=\"\" type=\"hidden\">\n";
        }
    }
}

$out2 .= $form;

//if we are installing in custom mode, include the following html
if($db->supports("create_user")){
// create / set db user dropdown
$auto_select = '';$provide_select ='';$create_select = '';$same_select = '';
if(isset($_SESSION['dbUSRData'])){
//    if($_SESSION['dbUSRData']=='auto')    {$auto_select ='selected';}
    if($_SESSION['dbUSRData']=='provide') {$provide_select ='selected';}
if(isset($_SESSION['install_type'])  && !empty($_SESSION['install_type'])  && strtolower($_SESSION['install_type'])=='custom'){
    if($_SESSION['dbUSRData']=='create')  {$create_select ='selected';}
}
    if($_SESSION['dbUSRData']=='same')  {$same_select ='selected';}
}else{
    $same_select ='selected';
}
$dbUSRDD   = "<select name='dbUSRData' id='dbUSRData' onchange='toggleDBUser();'>";
$dbUSRDD  .= "<option value='provide' $provide_select>".$mod_strings['LBL_DBCONFIG_PROVIDE_DD']."</option>";
$dbUSRDD  .= "<option value='create' $create_select>".$mod_strings['LBL_DBCONFIG_CREATE_DD']."</option>";
$dbUSRDD  .= "<option value='same' $same_select>".$mod_strings['LBL_DBCONFIG_SAME_DD']."</option>";
$dbUSRDD  .= "</select><br>&nbsp;";



$setup_db_sugarsales_password = urldecode($_SESSION['setup_db_sugarsales_password']);
$setup_db_sugarsales_user = urldecode($_SESSION['setup_db_sugarsales_user']);
$setup_db_sugarsales_password_retype = urldecode($_SESSION['setup_db_sugarsales_password_retype']);

$out2 .=<<<EOQ2

<table width="100%" cellpadding="0" cellpadding="0" border="0" class="StyleDottedHr">
<tr><td colspan="3" align="left"><br>{$mod_strings['LBL_DBCONFIG_SECURITY']}</td></tr>
<tr><td width='1%'>&nbsp;</td><td width='60%'><div id='sugarDBUser'><b>{$mod_strings['LBL_DBCONF_SUGAR_DB_USER']}</b></div>&nbsp;</td><td width='35%'>$dbUSRDD</td></tr>
</table>

<span id='connection_user_div' style="display:none">
<table width="100%" cellpadding="0" cellpadding="0" border="0" class="StyleDottedHr">
    <tr>
        <td width='1%'><span class="required">*</span></td>
        <td nowrap width='60%'><b>{$mod_strings['LBL_DBCONF_SUGAR_DB_USER']}</b></td>
        <td  width='35%'nowrap align="left">
         <input type="text" name="setup_db_sugarsales_user" maxlength="16" value="{$_SESSION['setup_db_sugarsales_user']}" />
        </td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td nowrap><b>{$mod_strings['LBL_DBCONF_DB_PASSWORD']}</b></td>
    <td nowrap align="left">
        <input type="password" name="setup_db_sugarsales_password_entry" value="{$setup_db_sugarsales_password}" />
        <input type="hidden" name="setup_db_sugarsales_password" value="{$setup_db_sugarsales_password}" />
    </td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td nowrap><b>{$mod_strings['LBL_DBCONF_DB_PASSWORD2']}</b></td>
    <td nowrap align="left"><input type="password" name="setup_db_sugarsales_password_retype_entry" value="{$setup_db_sugarsales_password_retype}"  /><input type="hidden" name="setup_db_sugarsales_password_retype" value="{$setup_db_sugarsales_password_retype}" /></td>
</tr></table>
</span>

EOQ2;
}

$demoDD = "<select name='demoData' id='demoData'><option value='no' >".$mod_strings['LBL_NO']."</option><option value='yes'>".$mod_strings['LBL_YES']."</option>";
$demoDD .= "</select><br>&nbsp;";


$out3 =<<<EOQ3
<table width="100%" cellpadding="0" cellpadding="0" border="0" class="StyleDottedHr">
<tr><th colspan="3" align="left">{$mod_strings['LBL_DBCONF_DEMO_DATA_TITLE']}</th></tr>
<tr>
    <td width='1%'>&nbsp;</td>
    <td  width='60%'nowrap><b>{$mod_strings['LBL_DBCONF_DEMO_DATA']}</b></td>
    <td  width='35%'nowrap align="left">
        {$demoDD}
    </td>
</tr>
</table>
EOQ3;


$GLOBALS['sugar_config']['default_language'] = 'en_us';
$app_list_strings = return_app_list_strings_language($GLOBALS['sugar_config']['default_language']);
$ftsTypeDropdown = "<select name='setup_fts_type' id='setup_fts_type'>";
$ftsTypeDropdown .= get_select_options_with_id($app_list_strings['fts_type'], '');
$ftsTypeDropdown .= "</select>";

$outFTS =<<<EOQ3
<table width="100%" cellpadding="0" cellpadding="0" border="0" class="StyleDottedHr">
<tr><th colspan="3" align="left">{$mod_strings['LBL_FTS_TABLE_TITLE']}</th></tr>
<tr><td colspan='3'>{$mod_strings['LBL_FTS_HELP']}</td></tr>
<tr>
        <td width='1%'><span class="required">*</span></td>
        <td nowrap width='60%'><b>{$mod_strings['LBL_FTS_TYPE']}</b></td>
        <td  width='35%'nowrap align="left">
            $ftsTypeDropdown
        </td>
</tr>
<tr id='fts_host_row'>
        <td width='1%'><span class="required">*</span></td>
        <td nowrap width='60%'><b>{$mod_strings['LBL_FTS_HOST']}</b></td>
        <td  width='35%'nowrap align="left">
         <input type="text" name="setup_fts_host" id="setup_fts_host" value="localhost" />
        </td>
</tr>
<tr id='fts_port_row'>
        <td width='1%'><span class="required">*</span></td>
        <td nowrap width='60%'><b>{$mod_strings['LBL_FTS_PORT']}</b></td>
        <td  width='35%'nowrap align="left">
         <input type="text" name="setup_fts_port" id="setup_fts_port" maxlength="10" value="9200" />
        </td>
</tr>
</table>
EOQ3;


$out4 =<<<EOQ4
</td>
</tr>
<tr>
<td align="right" colspan="2">
<hr>
     <input type="hidden" name="current_step" value="{$next_step}">
     <table cellspacing="0" cellpadding="0" border="0" class="stdTable">
        <tr>
            <td>
                <input class="button" type="button" name="goto" value="{$mod_strings['LBL_BACK']}" id="button_back_dbConfig" onclick="document.getElementById('form').submit();" />
            </td>
            <td>
                <input class="button" type="button" name="goto" id="button_next2" value="{$mod_strings['LBL_NEXT']}" onClick="callDBCheck();"/>
            </td>
        </tr>
     </table>
</td>
</tr>
</table>
</form>
<br>

<script>

function toggleDBUser(){
     if(typeof(document.getElementById('dbUSRData')) !='undefined'
     && document.getElementById('dbUSRData') != null){

        ouv = document.getElementById('dbUSRData').value;
        if(ouv == 'provide' || ouv == 'create'){
            document.getElementById('connection_user_div').style.display = '';
            document.getElementById('sugarDBUser').style.display = 'none';
        }else{
            document.getElementById('connection_user_div').style.display = 'none';
            document.getElementById('sugarDBUser').style.display = '';
        }
    }
}
    toggleDBUser();

var msgPanel;
function callDBCheck(){

            //begin main function that will be called
            ajaxCall = function(msg_panel){
                //create success function for callback

                getPanel = function() {
                var args = {    width:"300px",
                                modal:true,
                                fixedcenter: true,
                                constraintoviewport: false,
                                underlay:"shadow",
                                close:false,
                                draggable:true,

                                effect:{effect:YAHOO.widget.ContainerEffect.FADE, duration:.5}
                               } ;
                        msg_panel = new YAHOO.widget.Panel('p_msg', args);

                        msg_panel.setHeader("{$mod_strings['LBL_LICENSE_CHKDB_HEADER']}");
                        msg_panel.setBody(document.getElementById("checkingDiv").innerHTML);
                        msg_panel.render(document.body);
                        msgPanel = msg_panel;
                }


                passed = function(url){
                    document.setConfig.goto.value="{$mod_strings['LBL_NEXT']}";
                    document.getElementById('hidden_goto').value="{$mod_strings['LBL_NEXT']}";
                    document.setConfig.current_step.value="{$next_step}";
                    document.setConfig.submit();
                }
                success = function(o) {

                    //condition for just the preexisting database
                    if (o.responseText.indexOf('preexeest')>=0){

                        //  throw confirmation message
                        msg_panel.setBody(document.getElementById("sysCheckMsg").innerHTML);
                        msg_panel.render(document.body);
                        msgPanel = msg_panel;
                        document.getElementById('accept_btn').focus();
                    //condition for no errors
                    }else if (o.responseText.indexOf('dbCheckPassed')>=0){
                        //make navigation
                        passed("install.php?goto={$mod_strings['LBL_NEXT']}");

                    //condition for other errors
                    }else{
                        //turn off loading message
                        msgPanel.hide();
                        document.getElementById("errorMsgs").innerHTML = o.responseText;
                        document.getElementById("errorMsgs").style.display = '';
                        return false;
                    }


                }//end success


                //copy the db values over to the hidden field counterparts
                document.setConfig.setup_db_admin_password.value = document.setConfig.setup_db_admin_password_entry.value;



                //set loading message and create url
                postData = "checkDBSettings=true&to_pdf=1&sugar_body_only=1";
                postData += "&setup_db_database_name="+document.setConfig.setup_db_database_name.value;
                if(typeof(document.setConfig.setup_db_host_instance) != 'undefined'){
                    postData += "&setup_db_host_instance="+document.setConfig.setup_db_host_instance.value;
                }
                if(typeof(document.setConfig.setup_db_port_num) != 'undefined'){
                    postData += "&setup_db_port_num="+document.setConfig.setup_db_port_num.value;
                }
                postData += "&setup_db_host_name="+document.setConfig.setup_db_host_name.value;
                postData += "&setup_db_admin_user_name="+document.setConfig.setup_db_admin_user_name.value;
                postData += "&setup_db_admin_password="+encodeURIComponent(document.setConfig.setup_db_admin_password.value);
                if(typeof(document.setConfig.setup_db_sugarsales_user) != 'undefined'){
                    postData += "&setup_db_sugarsales_user="+document.setConfig.setup_db_sugarsales_user.value;
                }
                if(typeof(document.setConfig.setup_db_sugarsales_password) != 'undefined'){
                document.setConfig.setup_db_sugarsales_password.value = document.setConfig.setup_db_sugarsales_password_entry.value;
                    postData += "&setup_db_sugarsales_password="+encodeURIComponent(document.setConfig.setup_db_sugarsales_password.value);
                }
                if(typeof(document.setConfig.setup_db_sugarsales_password_retype) != 'undefined'){
                    document.setConfig.setup_db_sugarsales_password_retype.value = document.setConfig.setup_db_sugarsales_password_retype_entry.value;
                    postData += "&setup_db_sugarsales_password_retype="+encodeURIComponent(document.setConfig.setup_db_sugarsales_password_retype.value);
                }
                if(typeof(document.setConfig.dbUSRData) != 'undefined'){
                    postData += "&dbUSRData="+document.getElementById('dbUSRData').value;
                }

EOQ4;


$out4 .= <<<FTSTEST
    postData += "&setup_fts_type=" + $('#setup_fts_type').val();
    postData += "&setup_fts_host=" + $('#setup_fts_host').val();
    postData += "&setup_fts_port=" + $('#setup_fts_port').val();
FTSTEST;

$out4 .= <<<WSTEST
    postData += "&websockets_client_url=" + $('#websockets_client_url').val();
    postData += "&websockets_server_url=" + $('#websockets_server_url').val();
WSTEST;

$out_dd = 'postData += "&demoData="+document.setConfig.demoData.value;';
$out5 =<<<EOQ5
                postData += "&to_pdf=1&sugar_body_only=1";

                //if this is a call already in progress, then just return
                    if(typeof ajxProgress != 'undefined'){
                        return;
                    }

                getPanel();
                msgPanel.show;
                var ajxProgress = YAHOO.util.Connect.asyncRequest('POST','install.php', {success: success, failure: success}, postData);


            };//end ajaxCall method
              ajaxCall();
            return;
}

function confirm_drop_tables(yes_no){

        if(yes_no == true){
            document.getElementById('setup_db_drop_tables').value = true;
           //make navigation
                    document.setConfig.goto.value="{$mod_strings['LBL_NEXT']}";
                    document.getElementById('hidden_goto').value="{$mod_strings['LBL_NEXT']}";
                    document.setConfig.current_step.value="{$next_step}";
                    document.setConfig.submit();
        }else{
            //set drop tables to false
            document.getElementById('setup_db_drop_tables').value = false;
            msgPanel.hide();
        }
}

</script>



           <div id="checkingDiv" style="display:none">
           <table cellspacing="0" cellpadding="0" border="0">
               <tr><td>
                    <p><img alt="{$mod_strings['LBL_LICENSE_CHKDB_HEADER']}" src='install/processing.gif'> <br>{$mod_strings['LBL_LICENSE_CHKDB_HEADER']}</p>
                </td></tr>
            </table>
            </div>

          <div id='sysCheckMsg' style="display:none">
           <table cellspacing="0" cellpadding="0" border="0" >
               <tr><td>
                    <p>{$mod_strings['LBL_DROP_DB_CONFIRM']}</p>
               </td></tr>
               <tr><td align='center'>
                    <input id='accept_btn' type='button' class='button' onclick='confirm_drop_tables(true)' value="{$mod_strings['LBL_ACCEPT']}">
                    <input type='button' class='button' onclick='confirm_drop_tables(false)' id="button_cancel_dbConfig" value="{$mod_strings['LBL_CANCEL']}">
                </td></tr>
            </table>

          <div>

</body>
</html>




EOQ5;




////	END PAGE OUTPUT
///////////////////////////////////////////////////////////////////////////////

$sugar_smarty = new Sugar_Smarty();

$sugar_smarty->assign('icon', $icon);
$sugar_smarty->assign('css', $css);
$sugar_smarty->assign('loginImage', $loginImage);
$sugar_smarty->assign('APP', $app_strings);
$sugar_smarty->assign('MOD', $mod_strings);

echo $out;
echo $out2;

if(!isset($_SESSION['oc_install']) || $_SESSION['oc_install'] == false){
    echo $out3;

    echo $outFTS;
}
$sugar_smarty->display("install/templates/websocketConfig.tpl");
echo $out4;

if(!isset($_SESSION['oc_install']) || $_SESSION['oc_install'] == false){
    echo $out_dd;

}
echo $out5;

?>
