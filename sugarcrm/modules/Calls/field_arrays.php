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
 * $Id: field_arrays.php 51719 2009-10-22 17:18:00Z mitani $
 * Description:  Contains field arrays that are used for caching
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
$fields_array['Call'] = array ('column_fields' => Array("id"
		, "date_entered"
		, "date_modified"
		, "assigned_user_id"
		, "modified_user_id"
		, "created_by"
		//BEGIN SUGARCRM flav=pro ONLY
		,"team_id"
		//END SUGARCRM flav=pro ONLY
		, "description"
		, "status"
		, "direction"
		, "name"
		, "date_start"
		, "time_start"
		, "duration_hours"
		, "duration_minutes"
		, "date_end"
		, "parent_type"
		, "parent_id"
		,'reminder_time'
		,'outlook_id'
		),
        'list_fields' => Array('id', 'duration_hours', 'direction', 'status', 'name', 'parent_type', 'parent_name', 'parent_id', 'date_start', 'time_start', 'assigned_user_name', 'assigned_user_id', 'contact_name', 'contact_id','first_name','last_name','required','outlook_id','accept_status'
	//BEGIN SUGARCRM flav=pro ONLY
	, "team_id"
	, "team_name"
	//END SUGARCRM flav=pro ONLY
		),
        'required_fields' => array("name"=>1, "date_start"=>2, "time_start"=>3,),
);
?>