<?php
/**
 * THIS CLASS IS GENERATED BY MODULE BUILDER
 * PLEASE DO NOT CHANGE THIS CLASS
 * PLACE ANY CUSTOMIZATIONS IN pmse_BpmFlow
 */
class pmse_BpmFlow_sugar extends Basic {
	var $new_schema = true;
	var $module_dir = 'pmse_Project/pmse_BpmFlow';
	var $object_name = 'pmse_BpmFlow';
	var $table_name = 'pmse_bpm_flow';
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
    var $cas_id;
    var $cas_index;
    var $pro_id;
    var $cas_previous;
    var $cas_reassign_level;
    var $bpmn_id;
    var $bpmn_type;
    var $cas_user_id;
    var $cas_thread;
    var $cas_flow_status;
    var $cas_sugar_module;
    var $cas_sugar_object_id;
    var $cas_sugar_action;
    var $cas_adhoc_type;
    var $cas_task_start_date;
    var $cas_delegate_date;
    var $cas_start_date;
    var $cas_finish_date;
    var $cas_due_date;
    var $cas_queue_duration;
    var $cas_duration;
    var $cas_delay_duration;
    var $cas_started;
    var $cas_finished;
    var $cas_delayed;

	/**
	 * This is a depreciated method, please start using __construct() as this method will be removed in a future version
     *
     * @see __construct
     * @depreciated
	 */
	function pmse_BpmFlow_sugar(){
		self::__construct();
	}

	public function __construct(){
		parent::__construct();
	}

	public function bean_implements($interface){
		switch($interface){
			case 'ACL': return true;
		}
		return false;
}

}
?>