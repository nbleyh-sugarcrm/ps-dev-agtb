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
 * $Id$
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

class ReportsController extends SugarController{

	
	function ReportsController(){
		parent::SugarController();

	}
	
	public function loadBean(){
				
		if(!empty($_REQUEST['record']) && $_REQUEST['action'] == 'ReportsWizard'){
			$_REQUEST['id'] = $this->record;
			$_REQUEST['page'] = 'report';
			$this->view_object_map['action'] =  'ReportsWizard';
		}
		else if(empty($this->record) && !empty($_REQUEST['id'])){
			$this->record = $_REQUEST['id'];
			$GLOBALS['action'] = 'detailview';
			$this->view_object_map['action'] =  'ReportCriteriaResults';
		}elseif(!empty($this->record)){
			if ($_REQUEST['action'] == 'DetailView') {
				$_REQUEST['id'] = $this->record;
				unset($_REQUEST['record']);
			}
			$_REQUEST['page'] = 'report';
			$this->view_object_map['action'] =  'ReportCriteriaResults';
		}
		
		parent::loadBean();
	}
	
	public function action_add_schedule() {
	    $this->view = 'schedule';
	}
	
	public function action_detailview(){
		$this->view = 'classic';
	}
	//BEGIN SUGARCRM flav=pro ONLY
	public function action_get_teamset_field() {
		require_once('include/SugarFields/Fields/Teamset/ReportsSugarFieldTeamsetCollection.php');
		$view = new ReportsSugarFieldTeamsetCollection(true);
		$view->setup();
		$view->process();
		$view->init_tpl();
		echo $view->display();
	}
	//END SUGARCRM flav=pro ONLY
}
?>