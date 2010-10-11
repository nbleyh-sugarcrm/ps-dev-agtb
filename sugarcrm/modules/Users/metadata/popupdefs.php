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
 *$Id: popupdefs.php 52634 2009-11-20 14:27:02Z jmertic $
 ********************************************************************************/

$popupMeta = array(
	'moduleMain' => 'User',
	'varName' => 'USER',
	'orderBy' => 'user_name',
	'whereClauses' => array(
		'first_name' => 'users.first_name',
		'last_name' => 'users.last_name',
		'user_name' => 'users.user_name',
		//BEGIN SUGARCRM flav!=sales ONLY
		'is_group' => 'users.is_group',
		//END SUGARCRM flav!=sales ONLY
	),
	'whereStatement'=> " users.status = 'Active' and users.portal_only= '0' and users.is_group = '0'",
	'searchInputs' => array(
		'first_name',
		'last_name',
		'user_name',
		//BEGIN SUGARCRM flav!=sales ONLY
		'is_group',
		//END SUGARCRM flav!=sales ONLY
	),
);
