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
//FILE SUGARCRM flav=pro ONLY
$dictionary['opp_line_bundle_opp'] = array (
	'table' => 'opp_line_bundle_opp',
	'fields' => array (
       array('name' =>'id', 'type' =>'varchar', 'len'=>'36')
      , array ('name' => 'date_modified','type' => 'datetime')
      , array('name' =>'deleted', 'type' =>'bool', 'len'=>'1', 'default'=>'0', 'required' => false,)
      , array('name' =>'bundle_id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'opportunity_id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'bundle_index', 'type' =>'int', 'len'=>'11', 'default'=>'0', 'required' => true,)      
	),
	'indices' => array (
       array('name' =>'opp_bundl_opppk', 'type' =>'primary', 'fields'=>array('id'))
      , array('name' =>'idx_olbo_bundle', 'type' =>'index', 'fields'=>array('bundle_id'))
      , array('name' =>'idx_olbo_opp', 'type' =>'index', 'fields'=>array('opportunity_id'))
      , array('name' =>'idx_olbo_bq', 'type'=>'alternate_key', 'fields'=>array('opportunity_id','bundle_id'))
	),

	'relationships' => array (
        'opp_line_bundle_opp' => array(
            'lhs_module'=> 'OpportunityLineBundles', 'lhs_table'=> 'opp_line_bundles', 'lhs_key' => 'id',
            'rhs_module'=> 'Opportunity', 'rhs_table'=> 'opportunities', 'rhs_key' => 'id',
            'relationship_type'=>'many-to-many',
            'join_table'=> 'opp_line_bundle_opp', 'join_key_lhs'=>'bundle_id', 'join_key_rhs'=>'opportunity_id'
        )
    )
);
?>
