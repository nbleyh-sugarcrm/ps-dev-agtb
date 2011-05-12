<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You may
 * not use this file except in compliance with the License. Under the terms of the
 * license, You shall not, among other things: 1) sublicense, resell, rent, lease,
 * redistribute, assign or otherwise transfer Your rights to the Software, and 2)
 * use the Software for timesharing or service bureau purposes such as hosting the
 * Software for commercial gain and/or for the benefit of a third party.  Use of
 * the Software may be subject to applicable fees and any use of the Software
 * without first paying applicable fees is strictly prohibited.  You do not have
 * the right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.  Your Warranty, Limitations of liability and Indemnity are
 * expressly stated in the License.  Please refer to the License for the specific
 * language governing these rights and limitations under the License.
 * Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.;
 * All Rights Reserved.
 ********************************************************************************/



require_once('modules/Schedulers/Job.php');

$header_text = '';
global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;

$focus = new Job();
$focus->retrieve();
$focus->get_list_view_data();

//_pp($_REQUEST);
$where = '';
$limit = 20;
$varName = $focus->object_name;
$allowByOverride = true;
if(!empty($_REQUEST['Schedulers_'.$varName.'_ORDER_BY'])) {
	$orderBy = $_REQUEST['Schedulers_'.$varName.'_ORDER_BY'];
} else {
	$orderBy = $focus->order_by;
}

$listView = new ListView();
$listView->initNewXTemplate('modules/Schedulers/Scheduled.html', $mod_strings);
$listView->setHeaderTitle($mod_strings['LBL_LIST_TITLE']);
$listView->setQuery($where, $limit, $orderBy, $varName, $allowByOverride);
$listView->xTemplateAssign("REMOVE_INLINE_PNG", SugarThemeRegistry::current()->getImage('delete_inline','align="absmiddle" alt="'.$app_strings['LNK_REMOVE'].'" border="0"')); 
$listView->processListView($focus, "main", "JOB");

?>