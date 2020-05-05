<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

$dictionary['Note'] = [
    'favorites'=>true,
    'table' => 'notes',
    'unified_search' => true,
    'comment' => 'Notes and Attachments'
                               ,'fields' =>  [
  'id' =>
   [
    'name' => 'id',
    'vname' => 'LBL_ID',
    'type' => 'id',
    'required'=>true,
    'reportable'=>true,
    'comment' => 'Unique identifier',
                               ],
    'date_entered' =>
     [
    'name' => 'date_entered',
    'vname' => 'LBL_DATE_ENTERED',
    'type' => 'datetime',
    'comment' => 'Date record created',
                               ],
    'date_modified' =>
     [
    'name' => 'date_modified',
    'vname' => 'LBL_DATE_MODIFIED',
    'type' => 'datetime',
    'comment' => 'Date record last modified',
                               ],
    'modified_user_id' =>
       [
        'name' => 'modified_user_id',
        'rname' => 'user_name',
        'id_name' => 'modified_user_id',
        'vname' => 'LBL_MODIFIED',
        'type' => 'assigned_user_name',
        'table' => 'users',
        'isnull' => 'false',
         'group'=>'modified_by_name',
        'dbType' => 'id',
        'reportable'=>true,
        'comment' => 'User who last modified record',
                               ],
      'modified_by_name' =>
       [
        'name' => 'modified_by_name',
    'vname' => 'LBL_MODIFIED_BY',
        'type' => 'relate',
        'reportable'=>false,
        'source'=>'non-db',
        'rname'=>'user_name',
        'table' => 'users',
        'id_name' => 'modified_user_id',
        'module'=>'Users',
        'link'=>'modified_user_link',
        'duplicate_merge'=>'disabled',
                               ],
      'created_by' =>
       [
        'name' => 'created_by',
        'rname' => 'user_name',
        'id_name' => 'modified_user_id',
        'vname' => 'LBL_CREATED_BY',
        'type' => 'assigned_user_name',
        'table' => 'users',
        'isnull' => 'false',
        'dbType' => 'id',
    'comment' => 'User who created record',
                               ],
        'created_by_name' =>
       [
        'name' => 'created_by_name',
        'vname' => 'LBL_CREATED_BY',
        'type' => 'relate',
        'reportable'=>false,
        'link' => 'created_by_link',
        'rname' => 'user_name',
        'source'=>'non-db',
        'table' => 'users',
        'id_name' => 'created_by',
        'module'=>'Users',
        'duplicate_merge'=>'disabled',
        'importable' => 'false',
                               ],
    'name' =>
     [
    'name' => 'name',
    'vname' => 'LBL_NOTE_SUBJECT',
    'dbType' => 'varchar',
    'type' => 'name',
    'len' => '255',
    'unified_search' => true,
    'comment' => 'Name of the note',
    'importable' => 'required',
    'required' => true,
                               ],
    'filename' =>
     [
    'name' => 'filename',
    'vname' => 'LBL_FILENAME',
    'type' => 'varchar',
    'len' => '255',
    'reportable'=>true,
    'comment' => 'File name associated with the note (attachment)',
    'importable' => false,
                               ],
    'file_mime_type' =>
     [
    'name' => 'file_mime_type',
    'vname' => 'LBL_FILE_MIME_TYPE',
    'type' => 'varchar',
    'len' => '100',
    'comment' => 'Attachment MIME type',
    'importable' => false,
                               ],
    'file_url'=>
    [
    'name'=>'file_url',
    'vname' => 'LBL_FILE_URL',
    'type'=>'function',
    'function_class'=>'UploadFile',
    'function_name'=>'get_upload_url',
    'function_params'=> ['$this'],
    'source'=>'function',
    'reportable'=>false,
    'comment' => 'Path to file (can be URL)',
    'importable' => false,
                               ],
    'parent_type'=>
    [
    'name'=>'parent_type',
    'vname'=>'LBL_PARENT_TYPE',
    'type' =>'parent_type',
    'dbType' => 'varchar',
    'group'=>'parent_name',
    'len'=> '255',
    'comment' => 'Sugar module the Note is associated with',
                               ],
    'parent_id'=>
    [
    'name'=>'parent_id',
    'vname'=>'LBL_PARENT_ID',
    'type'=>'id',
    'required'=>false,
    'reportable'=>true,
    'comment' => 'The ID of the Sugar item specified in parent_type',
                               ],
    'contact_id'=>
    [
    'name'=>'contact_id',
    'vname'=>'LBL_CONTACT_ID',
    'type'=>'id',
    'required'=>false,
    'reportable'=>false,
    'comment' => 'Contact ID note is associated with',
                               ],
    'portal_flag' =>
     [
    'name' => 'portal_flag',
    'vname' => 'LBL_PORTAL_FLAG',
    'type' => 'bool',
    'required' => true,
    'comment' => 'Portal flag indicator determines if note created via portal',
                               ],
    'embed_flag' =>
     [
    'name' => 'embed_flag',
    'vname' => 'LBL_EMBED_FLAG',
    'type' => 'bool',
    'default' => 0,
    'comment' => 'Embed flag indicator determines if note embedded in email',
                               ],
    'description' =>
     [
    'name' => 'description',
    'vname' => 'LBL_NOTE_STATUS',
    'type' => 'text',
    'comment' => 'Full text of the note',
                               ],
    'deleted' =>
     [
    'name' => 'deleted',
    'vname' => 'LBL_DELETED',
    'type' => 'bool',
    'required' => false,
    'default' => '0',
    'reportable'=>false,
    'comment' => 'Record deletion indicator',
                               ],



    'parent_name'=>
    [
        'name'=> 'parent_name',
        'parent_type'=>'record_type_display' ,
        'type_name'=>'parent_type',
        'id_name'=>'parent_id', 'vname'=>'LBL_RELATED_TO',
        'type'=>'parent',
        'source'=>'non-db',
        'options'=> 'record_type_display_notes',
                               ],

    'contact_name'=>
    [
        'name'=>'contact_name',
        'rname'=>'last_name',
        'id_name'=>'contact_id',
        'vname'=>'LBL_CONTACT_NAME',
        'table'=>'contacts',
        'type'=>'relate',
        'link'=>'contact',
        'join_name'=>'contacts',
        'db_concat_fields'=> [0=>'first_name', 1=>'last_name'],
        'isnull'=>'true',
        'module'=>'Contacts',
        'source'=>'non-db',
                               ],

    'contact_phone'=>
    [
        'name'=>'contact_phone',
        'vname' => 'LBL_PHONE',
        'type'=>'phone',
        'vname' => 'LBL_PHONE',
        'source'=>'non-db',
                               ],

    'contact_email'=>
    [
        'name'=>'contact_email',
        'type'=>'varchar',
        'vname' => 'LBL_EMAIL_ADDRESS',
        'source' => 'non-db',
                               ],

    'account_id' =>
     [
    'name' => 'account_id',
    'vname' => 'LBL_ACCOUNT_ID',
    'type' => 'id',
    'reportable'=>false,
    'source'=>'non-db',
                               ],
    'opportunity_id' =>
     [
    'name' => 'opportunity_id',
    'vname' => 'LBL_OPPORTUNITY_ID',
    'type' => 'id',
    'reportable'=>false,
    'source'=>'non-db',
                               ],
    'acase_id' =>
     [
    'name' => 'acase_id',
    'vname' => 'LBL_CASE_ID',
    'type' => 'id',
    'reportable'=>false,
    'source'=>'non-db',
                               ],
    'lead_id' =>
     [
    'name' => 'lead_id',
    'vname' => 'LBL_LEAD_ID',
    'type' => 'id',
    'reportable'=>false,
    'source'=>'non-db',
                               ],
    'product_id' =>
     [
    'name' => 'product_id',
    'vname' => 'LBL_PRODUCT_ID',
    'type' => 'id',
    'reportable'=>false,
    'source'=>'non-db',
                               ],
    'quote_id' =>
     [
    'name' => 'quote_id',
    'vname' => 'LBL_QUOTE_ID',
    'type' => 'id',
    'reportable'=>false,
    'source'=>'non-db',
                               ],

    'created_by_link' =>
     [
        'name' => 'created_by_link',
    'type' => 'link',
    'relationship' => 'notes_created_by',
    'vname' => 'LBL_CREATED_BY_USER',
    'link_type' => 'one',
    'module'=>'Users',
    'bean_name'=>'User',
    'source'=>'non-db',
                               ],
    'modified_user_link' =>
     [
        'name' => 'modified_user_link',
    'type' => 'link',
    'relationship' => 'notes_modified_user',
    'vname' => 'LBL_MODIFIED_BY_USER',
    'link_type' => 'one',
    'module'=>'Users',
    'bean_name'=>'User',
    'source'=>'non-db',
                               ],

    'contact' =>
     [
    'name' => 'contact',
    'type' => 'link',
    'relationship' => 'contact_notes',
    'vname' => 'LBL_LIST_CONTACT_NAME',
    'source'=>'non-db',
                               ],
    'cases' =>
     [
    'name' => 'cases',
    'type' => 'link',
    'relationship' => 'case_notes',
    'vname' => 'LBL_CASES',
    'source'=>'non-db',
                               ],
    'accounts' =>
     [
    'name' => 'accounts',
    'type' => 'link',
    'relationship' => 'account_notes',
    'source'=>'non-db',
    'vname'=>'LBL_ACCOUNTS',
                               ],
    'opportunities' =>
     [
    'name' => 'opportunities',
    'type' => 'link',
    'relationship' => 'opportunity_notes',
    'source'=>'non-db',
    'vname'=>'LBL_OPPORTUNITIES',
                               ],
    'leads' =>
     [
    'name' => 'leads',
    'type' => 'link',
    'relationship' => 'lead_notes',
    'source'=>'non-db',
    'vname'=>'LBL_LEADS',
                               ],
    'products' =>
     [
    'name' => 'products',
    'type' => 'link',
    'relationship' => 'product_notes',
    'source'=>'non-db',
    'vname'=>'LBL_PRODUCTS',
                               ],
    'quotes' =>
     [
    'name' => 'quotes',
    'type' => 'link',
    'relationship' => 'quote_notes',
    'vname' => 'LBL_QUOTES',
    'source'=>'non-db',
                               ],
    'contracts' =>
     [
    'name' => 'contracts',
    'type' => 'link',
    'relationship' => 'contract_notes',
    'source' => 'non-db',
    'vname' => 'LBL_CONTRACTS',
                               ],
    'bugs' =>
     [
    'name' => 'bugs',
    'type' => 'link',
    'relationship' => 'bug_notes',
    'source'=>'non-db',
    'vname'=>'LBL_BUGS',
                               ],
    'emails' =>
    [
    'name'=> 'emails',
    'vname'=> 'LBL_EMAILS',
    'type'=> 'link',
    'relationship'=> 'emails_notes_rel',
    'source'=> 'non-db',
                               ],
    'projects' =>
     [
    'name' => 'projects',
    'type' => 'link',
    'relationship' => 'projects_notes',
    'source'=>'non-db',
    'vname'=>'LBL_PROJECTS',
                               ],
    'project_tasks' =>
     [
    'name' => 'project_tasks',
    'type' => 'link',
    'relationship' => 'project_tasks_notes',
    'source'=>'non-db',
    'vname'=>'LBL_PROJECT_TASKS',
                               ],
    'meetings' =>
     [
    'name' => 'meetings',
    'type' => 'link',
    'relationship' => 'meetings_notes',
    'source'=>'non-db',
    'vname'=>'LBL_MEETINGS',
                               ],
    'calls' =>
     [
    'name' => 'calls',
    'type' => 'link',
    'relationship' => 'calls_notes',
    'source'=>'non-db',
    'vname'=>'LBL_CALLS',
                               ],
    'description' =>
       [
        'name' => 'description',
        'vname' => 'LBL_DESCRIPTION',
        'type' => 'text',
        'comment' => 'Full text of the note',
        'rows' => 30,
        'cols' => 90,
                               ],
    ],
    'relationships'=>[
    'notes_modified_user' =>
    ['lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
    'rhs_module'=> 'Notes', 'rhs_table'=> 'notes', 'rhs_key' => 'modified_user_id',
    'relationship_type'=>'one-to-many']

    ,'notes_created_by' =>
    ['lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
    'rhs_module'=> 'Notes', 'rhs_table'=> 'notes', 'rhs_key' => 'created_by',
    'relationship_type'=>'one-to-many'],


    ]
                                                      , 'indices' =>  [
       ['name' =>'notespk', 'type' =>'primary', 'fields'=>['id']],
       ['name' =>'idx_note_name', 'type'=>'index', 'fields'=>['name']],
       ['name' =>'idx_notes_parent', 'type'=>'index', 'fields'=>['parent_id', 'parent_type']],
       ['name' =>'idx_note_contact', 'type'=>'index', 'fields'=>['contact_id']],
                                                      ]


                                                      //This enables optimistic locking for Saves From EditView
    ,'optimistic_locking'=>true,
                            ];

VardefManager::createVardef('Notes', 'Note', ['assignable',
'team_security',
]);
