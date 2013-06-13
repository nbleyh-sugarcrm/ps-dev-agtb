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
$dictionary['Note'] = array(
    'studio_enabled' => array(
    //BEGIN SUGARCRM flav=ent ONLY
        'portal' => false,
    //END SUGARCRM flav=ent ONLY
    ),
    //BEGIN SUGARCRM flav=pro ONLY
    'favorites'=>true,
    //END SUGARCRM flav=pro ONLY
    'table' => 'notes',
    // TODO: Figure out a way to selectively disable the activity stream for
    // posts -- useful when adding attachments to the activity stream.
    'activity_enabled'=>false,
	'unified_search' => true, 'full_text_search' => true, 'unified_search_default_enabled' => true,

	'comment' => 'Notes and Attachments'
                               ,'fields' => array (
  'name' =>
  array (
    'name' => 'name',
    'vname' => 'LBL_NOTE_SUBJECT',
    'dbType' => 'varchar',
    'type' => 'name',
    'len' => '255',
	'unified_search' => true,
	'full_text_search' => array('boost' => 3),
    'comment' => 'Name of the note',
    'importable' => 'required',
    'required' => true,
  ),
  'file_mime_type' =>
  array (
    'name' => 'file_mime_type',
    'vname' => 'LBL_FILE_MIME_TYPE',
    'type' => 'varchar',
    'len' => '100',
    'comment' => 'Attachment MIME type',
    'importable' => false,
  ),
  'file_url'=>
  array(
  	'name'=>'file_url',
    'vname' => 'LBL_FILE_URL',
    'type'=>'varchar',
   	'source'=>'non-db',
  	'reportable'=>false,
  	'comment' => 'Path to file (can be URL)',
    'importable' => false,
  	),
  'filename' =>
  array (
    'name' => 'filename',
    'vname' => 'LBL_FILENAME',
    'type' => 'file',
    'dbType' => 'varchar',
    'len' => '255',
    'reportable'=>true,
    'comment' => 'File name associated with the note (attachment)',
    'importable' => false,
  ),
  'parent_type'=>
  array(
  	'name'=>'parent_type',
  	'vname'=>'LBL_PARENT_TYPE',
  	'type' =>'parent_type',
    'dbType' => 'varchar',
    'group'=>'parent_name',
    'options'=> 'parent_type_display',
  	'len'=> '255',
    'studio' => array('wirelesslistview'=>false),
  	'comment' => 'Sugar module the Note is associated with'
  ),
  'parent_id'=>
  array(
  	'name'=>'parent_id',
  	'vname'=>'LBL_PARENT_ID',
  	'type'=>'id',
  	'required'=>false,
  	'reportable'=>true,
  	'comment' => 'The ID of the Sugar item specified in parent_type'
  ),
  'contact_id'=>
  array(
  	'name'=>'contact_id',
  	'vname'=>'LBL_CONTACT_ID',
  	'type'=>'id',
  	'required'=>false,
  	'reportable'=>false,
  	'comment' => 'Contact ID note is associated with'
  ),
  'portal_flag' =>
  array (
    'name' => 'portal_flag',
    'vname' => 'LBL_PORTAL_FLAG',
    'type' => 'bool',
    'default' => '0',
	'required' => true,
	'comment' => 'Portal flag indicator determines if note created via portal'
  ),
  'embed_flag' =>
  array (
    'name' => 'embed_flag',
    'vname' => 'LBL_EMBED_FLAG',
    'type' => 'bool',
    'default' => 0,
	'comment' => 'Embed flag indicator determines if note embedded in email'
  ),
  'description' =>
  array (
    'name' => 'description',
    'vname' => 'LBL_NOTE_STATUS',
    'type' => 'text',
    'comment' => 'Full text of the note'
  ),

 'parent_name'=>
 	array(
		'name'=> 'parent_name',
		'parent_type'=>'record_type_display' ,
		'type_name'=>'parent_type',
		'id_name'=>'parent_id', 'vname'=>'LBL_RELATED_TO',
		'type'=>'parent',
		'source'=>'non-db',
		'options'=> 'record_type_display_notes',
    'studio' => true,
		),

 'contact_name'=>
 	array(
		'name'=>'contact_name',
		'rname'=>'name',
		'id_name'=>'contact_id',
		'vname'=>'LBL_CONTACT_NAME',
        'table'=>'contacts',
		'type'=>'relate',
		'link'=>'contact',
		'join_name'=>'contacts',
        'db_concat_fields'=> array(0=>'first_name', 1=>'last_name'),
		'isnull'=>'true',
		'module'=>'Contacts',
		'source'=>'non-db',
		),

  'contact_phone'=>
    array(
        'name'=>'contact_phone',
        'vname' => 'LBL_PHONE',
        'type'=>'phone',
        'vname' => 'LBL_PHONE',
        'source'=>'non-db'
    ),

 'contact_email'=>
    array(
        'name'=>'contact_email',
        'type'=>'varchar',
		'vname' => 'LBL_EMAIL_ADDRESS',
		'source' => 'non-db',
        'studio' => false
    ),

  'account_id' =>
  array (
    'name' => 'account_id',
    'vname' => 'LBL_ACCOUNT_ID',
    'type' => 'id',
    'reportable'=>false,
	'source'=>'non-db',
  ),
  'opportunity_id' =>
  array (
    'name' => 'opportunity_id',
    'vname' => 'LBL_OPPORTUNITY_ID',
    'type' => 'id',
    'reportable'=>false,
	'source'=>'non-db',
  ),
  'acase_id' =>
  array (
    'name' => 'acase_id',
    'vname' => 'LBL_CASE_ID',
    'type' => 'id',
    'reportable'=>false,
	'source'=>'non-db',
  ),
  'lead_id' =>
  array (
    'name' => 'lead_id',
    'vname' => 'LBL_LEAD_ID',
    'type' => 'id',
    'reportable'=>false,
	'source'=>'non-db',
  ),
  //BEGIN SUGARCRM flav=pro ONLY
  'product_id' =>
  array (
    'name' => 'product_id',
    'vname' => 'LBL_PRODUCT_ID',
    'type' => 'id',
    'reportable'=>false,
	'source'=>'non-db',
  ),
  'quote_id' =>
  array (
    'name' => 'quote_id',
    'vname' => 'LBL_QUOTE_ID',
    'type' => 'id',
    'reportable'=>false,
	'source'=>'non-db',
  ),
  //END SUGARCRM flav=pro ONLY

  'created_by_link' =>
  array (
        'name' => 'created_by_link',
    'type' => 'link',
    'relationship' => 'notes_created_by',
    'vname' => 'LBL_CREATED_BY_USER',
    'link_type' => 'one',
    'module'=>'Users',
    'bean_name'=>'User',
    'source'=>'non-db',
  ),
  'modified_user_link' =>
  array (
        'name' => 'modified_user_link',
    'type' => 'link',
    'relationship' => 'notes_modified_user',
    'vname' => 'LBL_MODIFIED_BY_USER',
    'link_type' => 'one',
    'module'=>'Users',
    'bean_name'=>'User',
    'source'=>'non-db',
  ),

  'contact' =>
  array (
    'name' => 'contact',
    'type' => 'link',
    'relationship' => 'contact_notes',
    'vname' => 'LBL_LIST_CONTACT_NAME',
    'source'=>'non-db',
  ),
  'cases' =>
  array (
    'name' => 'cases',
    'type' => 'link',
    'relationship' => 'case_notes',
    'vname' => 'LBL_CASES',
    'source'=>'non-db',
  ),
  'accounts' =>
  array (
    'name' => 'accounts',
    'type' => 'link',
    'relationship' => 'account_notes',
    'source'=>'non-db',
    'vname'=>'LBL_ACCOUNTS',
  ),
  'opportunities' =>
  array (
    'name' => 'opportunities',
    'type' => 'link',
    'relationship' => 'opportunity_notes',
    'source'=>'non-db',
    'vname'=>'LBL_OPPORTUNITIES',
  ),
  'leads' =>
  array (
    'name' => 'leads',
    'type' => 'link',
    'relationship' => 'lead_notes',
    'source'=>'non-db',
    'vname'=>'LBL_LEADS',
  ),
//BEGIN SUGARCRM flav=pro ONLY
  'products' =>
  array (
    'name' => 'products',
    'type' => 'link',
    'relationship' => 'product_notes',
    'source'=>'non-db',
    'vname'=>'LBL_PRODUCTS',
  ),
  'quotes' =>
  array (
    'name' => 'quotes',
    'type' => 'link',
    'relationship' => 'quote_notes',
    'vname' => 'LBL_QUOTES',
    'source'=>'non-db',
  ),
  'contracts' =>
  array (
    'name' => 'contracts',
    'type' => 'link',
    'relationship' => 'contract_notes',
    'source' => 'non-db',
    'vname' => 'LBL_CONTRACTS',
  ),
    'prospects' => array (
      'name' => 'prospects',
      'type' => 'link',
      'relationship' => 'prospect_notes',
      'source' => 'non-db',
      'vname' => 'LBL_PROSPECTS',
    ),
//END SUGARCRM flav=pro ONLY
  'bugs' =>
  array (
    'name' => 'bugs',
    'type' => 'link',
    'relationship' => 'bug_notes',
    'source'=>'non-db',
    'vname'=>'LBL_BUGS',
  ),
  'emails' =>
  array(
    'name'=> 'emails',
    'vname'=> 'LBL_EMAILS',
    'type'=> 'link',
    'relationship'=> 'emails_notes_rel',
    'source'=> 'non-db',
  ),
  'projects' =>
  array (
    'name' => 'projects',
    'type' => 'link',
    'relationship' => 'projects_notes',
    'source'=>'non-db',
    'vname'=>'LBL_PROJECTS',
  ),
  'project_tasks' =>
  array (
    'name' => 'project_tasks',
    'type' => 'link',
    'relationship' => 'project_tasks_notes',
    'source'=>'non-db',
    'vname'=>'LBL_PROJECT_TASKS',
  ),
  'meetings' =>
  array (
    'name' => 'meetings',
    'type' => 'link',
    'relationship' => 'meetings_notes',
    'source'=>'non-db',
    'vname'=>'LBL_MEETINGS',
  ),
  'calls' =>
  array (
    'name' => 'calls',
    'type' => 'link',
    'relationship' => 'calls_notes',
    'source'=>'non-db',
    'vname'=>'LBL_CALLS',
  ),
  'tasks' =>
  array (
    'name' => 'tasks',
    'type' => 'link',
    'relationship' => 'tasks_notes',
    'source'=>'non-db',
    'vname'=>'LBL_TASKS',
  ),
  'description' =>
      array (
        'name' => 'description',
        'vname' => 'LBL_DESCRIPTION',
        'type' => 'text',
        'comment' => 'Full text of the note',
        'rows' => 30,
        'cols' => 90,
      ),
),
'relationships'=>array(
)
                                                      , 'indices' => array (
       array('name' =>'idx_note_name', 'type'=>'index', 'fields'=>array('name')),
       array('name' =>'idx_notes_parent', 'type'=>'index', 'fields'=>array('parent_id', 'parent_type')),
       array('name' =>'idx_note_contact', 'type'=>'index', 'fields'=>array('contact_id')),
       array('name' =>'idx_notes_assigned_del', 'type' =>'index', 'fields'=>array( 'deleted', 'assigned_user_id')),
                                                      )


                                                      //This enables optimistic locking for Saves From EditView
	,'optimistic_locking'=>true,
                            );

VardefManager::createVardef('Notes','Note', array('default', 'assignable',
//BEGIN SUGARCRM flav=pro ONLY
'team_security',
//END SUGARCRM flav=pro ONLY
));
?>
