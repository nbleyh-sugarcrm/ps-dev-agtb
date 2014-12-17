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
 * $Id: Tracker.php 20894 2007-03-13 02:01:42Z eddy $
 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

// logic will be added here at a later date to track campaigns
// this script; currently forwards to site_URL variable of $sugar_config
// redirect URL will also be added so specified redirect URL can be used

// additionally, another script using fopen will be used to call this
// script externally

require_once('modules/Campaigns/utils.php');

$GLOBALS['log'] = LoggerManager::getLogger('Campaign Tracker v2');

$db = DBManagerFactory::getInstance();

if(empty($_REQUEST['track'])) {
	$track = "";
} else {
	$track = $_REQUEST['track'];
}
if(!empty($_REQUEST['identifier'])) {
	$keys=log_campaign_activity($_REQUEST['identifier'],'link',true,$track);
    
}else{
    //if this has no identifier, then this is a web/banner campaign
    //pass in with id set to string 'BANNER'
    $keys=log_campaign_activity('BANNER','link',true,$track);

}

$track = $db->quote($track);

if(preg_match('/^[0-9A-Za-z\-]*$/', $track))
{
	$query = "SELECT tracker_url FROM campaign_trkrs WHERE id='$track'";
	$res = $db->query($query);

	$row = $db->fetchByAssoc($res);

    $redirect_URL = str_replace('&amp;','&',$row['tracker_url']);
	sugar_cleanup();
	header("Location: $redirect_URL");
}
else
{
	sugar_cleanup();
}
exit;
?>
