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

/**
 * THIS CLASS IS GENERATED BY MODULE BUILDER
 * PLEASE DO NOT CHANGE THIS CLASS
 * PLACE ANY CUSTOMIZATIONS IN pmse_BpmnActivity
 */
class pmse_BpmnActivity_sugar extends Basic {
	var $new_schema = true;
	var $module_dir = 'pmse_Project/pmse_BpmnActivity';
	var $object_name = 'pmse_BpmnActivity';
	var $table_name = 'pmse_bpmn_activity';
	var $importable = false;
        var $id;
		var $name;
		var $date_entered;
		var $date_modified;
		var $modified_user_id;
		var $modified_by_name;
		var $created_by;
		var $created_by_name;
		var $description;
		var $deleted;
		var $created_by_link;
		var $modified_user_link;
		var $activities;
		var $assigned_user_id;
		var $assigned_user_name;
		var $assigned_user_link;
    var $act_uid;
    var $prj_id;
    var $pro_id;
    var $act_type;
    var $act_is_for_compensation;
    var $act_start_quantity;
    var $act_completion_quantity;
    var $act_task_type;
    var $act_implementation;
    var $act_instantiate;
    var $act_script_type;
    var $act_script;
    var $act_loop_type;
    var $act_test_before;
    var $act_loop_maximum;
    var $act_loop_condition;
    var $act_loop_cardinality;
    var $act_loop_behavior;
    var $act_is_adhoc;
    var $act_is_collapsed;
    var $act_completion_condition;
    var $act_ordering;
    var $act_cancel_remaining_instances;
    var $act_protocol;
    var $act_method;
    var $act_is_global;
    var $act_referer;
    var $act_default_flow;
    var $act_master_diagram;

    /**
     * @deprecated Use __construct() instead
     */
    public function pmse_BpmnActivity_sugar()
    {
        self::__construct();
    }

	public function __construct(){
		parent::__construct();
	}
}
