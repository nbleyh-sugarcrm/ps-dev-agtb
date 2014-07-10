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
$dictionary['User'] = array(
    'table' => 'users',
//BEGIN SUGARCRM flav=pro ONLY
    'favorites' => false,
//END SUGARCRM flav=pro ONLY
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
        ) ,
        'user_name' => array(
            'name' => 'user_name',
            'vname' => 'LBL_USER_NAME',
            'type' => 'user_name',
            'dbType' => 'varchar',
            'len' => '60',
            'importable' => 'required',
            'required' => true,
            'studio' => array(
               'no_duplicate' => true,
               'editview' => false,
               'detailview' => true,
               'quickcreate' => false,
               'basic_search' => false,
               'advanced_search' => false,
               //BEGIN SUGARCRM flav=pro ONLY
               'wirelesseditview' => false,
               'wirelessdetailview' => true,
               'wirelesslistview' => false,
               'wireless_basic_search' => false,
               'wireless_advanced_search' => false,
               'rollup' => false,
               //END SUGARCRM flav=pro ONLY
               ),
        ) ,
        'user_hash' => array(
            'name' => 'user_hash',
            'vname' => 'LBL_USER_HASH',
            'type' => 'password',
            'dbType' => 'varchar',            
            'len' => '255',
            'reportable' => false,
            'importable' => 'false',
            'sensitive' => true,
            'studio' => array(
                'no_duplicate'=>true,
                'listview' => false,
                'searchview'=>false,
                //BEGIN SUGARCRM flav=pro ONLY
                'related' => false,
                'formula' => false,
                'rollup' => false,
                //END SUGARCRM flav=pro ONLY
            ),
        ) ,
        'system_generated_password' => array(
            'name' => 'system_generated_password',
            'vname' => 'LBL_SYSTEM_GENERATED_PASSWORD',
            'type' => 'bool',
            'required' => true,
            'reportable' => false,
            'massupdate' => false,
            'studio' => array(
                'listview' => false,
                'searchview'=>false,
                'editview'=>false,
                'quickcreate'=>false,
                //BEGIN SUGARCRM flav=pro ONLY
                'wirelesseditview' => false,
                'related' => false,
                'formula' => false,
                'rollup' => false,
                //END SUGARCRM flav=pro ONLY
            ),
        ) ,

        'pwd_last_changed' => array(
            'name' => 'pwd_last_changed',
            'vname' => 'LBL_PSW_MODIFIED',
            'type' => 'datetime',
            'required' => false,
            'massupdate' => false,
            'studio' => array('formula' => false),
        ) ,
        /**
         * authenticate_id is used by authentication plugins so they may place a quick lookup key for looking up a given user after authenticating through the plugin
         */
        'authenticate_id' => array(
            'name' => 'authenticate_id',
            'vname' => 'LBL_AUTHENTICATE_ID',
            'type' => 'varchar',
            'len' => '100',
            'reportable' => false,
            'importable' => 'false',
            'studio' => array('listview' => false, 'searchview'=>false, 'related' => false),
        ) ,
        /**
         * sugar_login will force the user to use sugar authentication
         * regardless of what authentication the system is configured to use
         */
        'sugar_login' => array(
            'name' => 'sugar_login',
            'vname' => 'LBL_SUGAR_LOGIN',
            'type' => 'bool',
            'default' => '1',
            'reportable' => false,
            'massupdate' => false,
            'importable' => false,
            'studio' => array('listview' => false, 'searchview'=>false, 'formula' => false),
        ) ,
        //BEGIN SUGARCRM flav!=com ONLY
        'picture' => array(
            'name' => 'picture',
            'vname' => 'LBL_PICTURE_FILE',
            'type' => 'image',
            'dbType' => 'varchar',
            'len' => '255',
            'width' => '42',
            'height' => '42',
            'border' => '',
        ) ,
        //END SUGARCRM flav!=com ONLY
        'first_name' => array(
            'name' => 'first_name',
            'vname' => 'LBL_FIRST_NAME',
            'dbType' => 'varchar',
            'type' => 'name',
            'len' => '30',
        ) ,
        'last_name' => array(
            'name' => 'last_name',
            'vname' => 'LBL_LAST_NAME',
            'dbType' => 'varchar',
            'type' => 'name',
            'len' => '30',
            'importable' => 'required',
        	'required' => true,
        ) ,
        'full_name' => array(
            'name' => 'full_name',
            'vname' => 'LBL_NAME',
            'type' => 'fullname',
            'fields' => array('first_name', 'last_name'),
            'source' => 'non-db',
            'sort_on' => 'last_name',
            'sort_on2' => 'first_name',
            'db_concat_fields' => array(
                0 => 'first_name',
                1 => 'last_name'
            ) ,
            'len' => '510',
            'studio' => array('formula' => false),
        ) ,
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'fullname',
            'fields' => array('first_name', 'last_name'),
            'source' => 'non-db',
            'sort_on' => 'last_name',
            'sort_on2' => 'first_name',
            'db_concat_fields' => array(
                0 => 'first_name',
                1 => 'last_name'
            ) ,
            'len' => '510',
            'studio' => array('formula' => false),
        ),
        'is_admin' => array(
            'name' => 'is_admin',
            'vname' => 'LBL_IS_ADMIN',
            'type' => 'bool',
            'default' => '0',
            'studio' => array('listview' => false, 'searchview'=>false, 'related' => false),
        ) ,
        'external_auth_only' => array(
            'name' => 'external_auth_only',
            'vname' => 'LBL_EXT_AUTHENTICATE',
            'type' => 'bool',
            'reportable' => false,
            'massupdate' => false,
            'default' => '0',
            'studio' => array('listview' => false, 'searchview'=>false, 'related' => false),
        ) ,
        'receive_notifications' => array(
            'name' => 'receive_notifications',
            'vname' => 'LBL_RECEIVE_NOTIFICATIONS',
            'type' => 'bool',
            'default' => '1',
            'massupdate' => false,
            'studio' => false,
        ) ,
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
        ) ,
        'date_entered' => array(
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
            'studio' => array(
                'editview' => false,
                'quickcreate' => false,
//BEGIN SUGARCRM flav=pro ONLY
                'wirelesseditview' => false,
//END SUGARCRM flav=pro ONLY
            ),
            'readonly' => true,
        ) ,
        'date_modified' => array(
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
            'studio' => array(
                'editview' => false,
                'quickcreate' => false,
//BEGIN SUGARCRM flav=pro ONLY
                'wirelesseditview' => false,
//END SUGARCRM flav=pro ONLY
            ),
            'readonly' => true,
        ),
        'last_login' => array(
            'name' => 'last_login',
            'vname' => 'LBL_LAST_LOGIN',
            'type' => 'datetime',
            'required' => false,
            'readonly' => true,
        ),
        'modified_user_id' => array(
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED_BY_ID',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'readonly' => true,
        ) ,
        'modified_by_name' => array(
            'name' => 'modified_by_name',
            'vname' => 'LBL_MODIFIED_BY',
            'type' => 'varchar',
            'source' => 'non-db',
            'studio' => false,
            'readonly' => true,
        ) ,
        'created_by' => array(
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'studio' => false,
            'readonly' => true,
        ) ,
        'created_by_name' => array(
            'name' => 'created_by_name',
	        'vname' => 'LBL_CREATED_BY_NAME', //bug 48978
            'type' => 'varchar',
            'source' => 'non-db',
            'importable' => 'false',
            //BEGIN SUGARCRM flav=pro ONLY
            'studio' => array(
                'related' => false,
                'formula' => false,
                'rollup' => false,
            ),
            //END SUGARCRM flav=pro ONLY
            'readonly' => true,
        ) ,
        'title' => array(
            'name' => 'title',
            'vname' => 'LBL_TITLE',
            'type' => 'varchar',
            'len' => '50',
        ) ,
        'department' => array(
            'name' => 'department',
            'vname' => 'LBL_DEPARTMENT',
            'type' => 'varchar',
            'len' => '50',
        ) ,
        'phone_home' => array(
            'name' => 'phone_home',
            'vname' => 'LBL_HOME_PHONE',
            'type' => 'phone',
			'dbType' => 'varchar',
            'len' => '50',
        ) ,
        'phone_mobile' => array(
            'name' => 'phone_mobile',
            'vname' => 'LBL_MOBILE_PHONE',
            'type' => 'phone',
			'dbType' => 'varchar',
            'len' => '50',
        ) ,
        'phone_work' => array(
            'name' => 'phone_work',
            'vname' => 'LBL_WORK_PHONE',
            'type' => 'phone',
			'dbType' => 'varchar',
            'len' => '50',
        ) ,
        'phone_other' => array(
            'name' => 'phone_other',
            'vname' => 'LBL_OTHER_PHONE',
            'type' => 'phone',
			'dbType' => 'varchar',
            'len' => '50',
        ) ,
        'phone_fax' => array(
            'name' => 'phone_fax',
            'vname' => 'LBL_FAX_PHONE',
            'type' => 'phone',
			'dbType' => 'varchar',
            'len' => '50',
        ) ,
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'user_status_dom',
            'importable' => 'required',
            'required' => true,
        ) ,
        'address_street' => array(
            'name' => 'address_street',
            'vname' => 'LBL_ADDRESS_STREET',
            'type' => 'text',
            'dbType' => 'varchar',
            'len' => '150',
            'group' => 'address',
        ) ,
        'address_city' => array(
            'name' => 'address_city',
            'vname' => 'LBL_ADDRESS_CITY',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'address',
        ) ,
        'address_state' => array(
            'name' => 'address_state',
            'vname' => 'LBL_ADDRESS_STATE',
            'type' => 'varchar',
            'len' => '100',
            'group' => 'address',
        ) ,
        'address_country' => array(
            'name' => 'address_country',
            'vname' => 'LBL_ADDRESS_COUNTRY',
            'type' => 'varchar',
            'len' => 100,
            'group' => 'address',
        ) ,
        'address_postalcode' => array(
            'name' => 'address_postalcode',
            'vname' => 'LBL_ADDRESS_POSTALCODE',
            'type' => 'varchar',
            'len' => '20',
            'group' => 'address',
        ) ,
        // This is a fake field for the edit view
        'UserType' => array(
            'name' => 'UserType',
            'vname' => 'LBL_USER_TYPE',
            'type' => 'enum',
            'len' => 50,
            'options' => 'user_type_dom',
            'source' => 'non-db',
            'import' => false,
            'reportable' => false,
            'studio' => array('formula' => false),
        ),
        //BEGIN SUGARCRM flav=pro ONLY
        'default_team' => array(
            'name' => 'default_team',
            'vname' => 'LBL_DEFAULT_TEAM',
            'reportable' => false,
            'type' => 'id',
            'len' => '36',
            'studio' => array(
                'listview' => false,
                'searchview'=>false,
                'formula' => false,
                'wirelesslistview' => false,
                'wirelessdetailview' => false,
                'wirelesseditview' => false,
            ),
        ) ,
        'team_id' => array(
            'name' => 'team_id',
            'vname' => 'LBL_DEFAULT_TEAM',
            'reportable' => false,
        	'source' => 'non-db',
            'type' => 'id',
            'len' => '36',
            'studio' => array('listview' => false, 'searchview'=>false, 'formula' => false),
        ) ,
			'team_set_id' =>
			array (
				'name' => 'team_set_id',
				'rname' => 'id',
				'id_name' => 'team_set_id',
				'vname' => 'LBL_TEAM_SET_ID',
				'type' => 'id',
			    'audited' => true,
			    'studio' => 'false',
			),
			'team_count' =>
			array (
				'name' => 'team_count',
				'rname' => 'team_count',
				'id_name' => 'team_id',
				'vname' => 'LBL_TEAMS',
				'join_name'=>'ts1',
				'table' => 'team_sets',
				'type' => 'relate',
	            'required' => 'true',
				'table' => 'teams',
				'isnull' => 'true',
				'module' => 'Teams',
				'link' => 'team_count_link',
				'massupdate' => false,
				'dbType' => 'int',
				'source' => 'non-db',
				'importable' => 'false',
				'reportable'=>false,
			    'duplicate_merge' => 'disabled',
				'studio' => 'false',
			),
			'team_name' =>
			array (
				'name' => 'team_name',
				'db_concat_fields'=> array(0=>'name', 1=>'name_2'),
				'rname' => 'name',
				'id_name' => 'team_id',
				'vname' => 'LBL_TEAMS',
				'type' => 'relate',
	            'required' => true,
				'table' => 'teams',
				'isnull' => 'true',
				'module' => 'Teams',
				'link' => 'team_link',
				'massupdate' => false,
				'dbType' => 'varchar',
				'source' => 'non-db',
				'len' => 36,
				'custom_type' => 'teamset',
                'studio' => array(
                    'listview'    => false,
                    'searchview'  =>false,
                    'editview'    =>false,
                    'quickcreate' =>false,
                    'wirelesslistview' => false,
                    'wirelessdetailview' => false,
                    'wirelesseditview' => false,
                ),
                'exportable'=> true,
			),
			'team_link' =>
		    array (
		      'name' => 'team_link',
		      'type' => 'link',
		      'relationship' => 'users_team',
		      'vname' => 'LBL_TEAMS_LINK',
		      'link_type' => 'one',
		      'module' => 'Teams',
		      'bean_name' => 'Team',
		      'source' => 'non-db',
		      'duplicate_merge' => 'disabled',
		      'studio' => 'false',
                'reportable'=>false,
		    ),
            'default_primary_team' => array (
                'name' => 'default_primary_team',
                'type' => 'link',
                'relationship' => 'users_team',
                'vname' => 'LBL_DEFAULT_PRIMARY_TEAM',
                'link_type' => 'one',
                'module' => 'Teams',
                'bean_name' => 'Team',
                'source' => 'non-db',
                'duplicate_merge' => 'disabled',
                'studio' => 'false',
            ),
		    'team_count_link' =>
	  			array (
	  			'name' => 'team_count_link',
	    		'type' => 'link',
	    		'relationship' => 'users_team_count_relationship',
	            'link_type' => 'one',
			    'module' => 'Teams',
			    'bean_name' => 'TeamSet',
			    'source' => 'non-db',
			    'duplicate_merge' => 'disabled',
	  			'reportable'=>false,
	  			'studio' => 'false',
	  		),
	  		'teams' =>
			array (
				'name' => 'teams',
		        'type' => 'link',
				'relationship' => 'users_teams',
				'bean_filter_field' => 'team_set_id',
				'rhs_key_override' => true,
		        'source' => 'non-db',
				'vname' => 'LBL_TEAMS',
				'link_class' => 'TeamSetLink',
				'link_file' => 'modules/Teams/TeamSetLink.php',
				'studio' => 'false',
				'reportable'=>false,
			),
			'team_memberships' => array(
	            'name' => 'team_memberships',
	            'type' => 'link',
	            'relationship' => 'team_memberships',
	            'source' => 'non-db',
	            'vname' => 'LBL_TEAM_MEMBERSHIP'
        	) ,
            'team_sets' => array(
                'name' => 'team_sets',
                'type' => 'link',
                'relationship' => 'users_team_sets',
                'source' => 'non-db',
                'vname' => 'LBL_TEAM_SET'
            ),
			'users_signatures' => array(
			    'name' => 'users_signatures',
			    'type' => 'link',
			    'relationship' => 'users_users_signatures',
			    'source' => 'non-db',
			    'studio' => 'false',
			    'reportable'=>false,
			    ),

        //END SUGARCRM flav=pro ONLY
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
        ) ,
        'portal_only' => array(
            'name' => 'portal_only',
            'vname' => 'LBL_PORTAL_ONLY_USER',
            'type' => 'bool',
            'massupdate' => false,
            'default' => '0',
            'studio' => array('listview' => false, 'searchview'=>false, 'formula' => false),
        ) ,
        'show_on_employees' => array(
            'name' => 'show_on_employees',
            'vname' => 'LBL_SHOW_ON_EMPLOYEES',
            'type' => 'bool',
            'massupdate' => true,
            'importable' => true,
            'default' => true,
            'studio' => array('formula' => false),
        ) ,
        'employee_status' => array(
            'name' => 'employee_status',
            'vname' => 'LBL_EMPLOYEE_STATUS',
            'type' => 'enum',
            'options' => 'employee_status_dom',
            'len' => 100,
        ) ,
        'messenger_id' => array(
            'name' => 'messenger_id',
            'vname' => 'LBL_MESSENGER_ID',
            'type' => 'varchar',
            'len' => 100,
        ) ,
        'messenger_type' => array(
            'name' => 'messenger_type',
            'vname' => 'LBL_MESSENGER_TYPE',
            'type' => 'enum',
            'options' => 'messenger_type_dom',
            'len' => 100,
        ) ,
        'calls' => array(
            'name' => 'calls',
            'type' => 'link',
            'relationship' => 'calls_users',
            'source' => 'non-db',
            'vname' => 'LBL_CALLS'
        ) ,
        'meetings' => array(
            'name' => 'meetings',
            'type' => 'link',
            'relationship' => 'meetings_users',
            'source' => 'non-db',
            'vname' => 'LBL_MEETINGS'
        ) ,
        'contacts_sync' => array(
            'name' => 'contacts_sync',
            'type' => 'link',
            'relationship' => 'contacts_users',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS_SYNC',
            'reportable' => false,
        ) ,
        'reports_to_id' => array(
            'name' => 'reports_to_id',
            'vname' => 'LBL_REPORTS_TO_ID',
            'type' => 'id',
            'required' => false,
        ) ,
        'reports_to_name' => array(
            'name' => 'reports_to_name',
            'rname' => 'last_name',
            'id_name' => 'reports_to_id',
            'vname' => 'LBL_REPORTS_TO_NAME',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'Users',
            'table' => 'users',
            'link' => 'reports_to_link',
            'reportable' => false,
            'source' => 'non-db',
            'duplicate_merge' => 'disabled',
            'side' => 'right',
        ) ,
        'reports_to_link' => array(
            'name' => 'reports_to_link',
            'type' => 'link',
            'relationship' => 'user_direct_reports',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db',
            'vname' => 'LBL_REPORTS_TO',
        ) ,
        'reportees' => array(
            'name' => 'reportees',
            'type' => 'link',
            'relationship' => 'user_direct_reports',
            'link_type' => 'many',
            'side' => 'left',
            'source' => 'non-db',
            'vname' => 'LBL_REPORTS_TO',
            'reportable' => false,
        ) ,
       'email1' => 
        array(
            'name'      => 'email1',
            'vname'     => 'LBL_EMAIL_ADDRESS',
            'type'      => 'varchar',
            'function'  => array(
                'name'      => 'getEmailAddressWidget',
                'returns'   => 'html'),
            'source'    => 'non-db',
            'group'=>'email1',
            'required' => true,
            'merge_filter' => 'enabled',
            'studio' => array('editField' => true, 'searchview' => false, 'popupsearch' => false), // bug 46859
            'full_text_search' => array('enabled' => true, 'boost' => 3, 'index' => 'not_analyzed'), //bug 54567
            'exportable'=>true,
        ), 
        'email'=> array(
            'name' => 'email',
            'type' => 'email',
            'query_type' => 'default',
            'source' => 'non-db',
            'operator' => 'subquery',
            'subquery' => 'SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address LIKE',
            'db_field' => array(
                'id',
            ),
            'vname' =>'LBL_ANY_EMAIL',
            'studio' => array('visible'=>false, 'searchview'=>true),
            'sort_on' => 'email_addresses',
        ),
        'email_addresses' => array(
            'name' => 'email_addresses',
            'type' => 'link',
            'relationship' => 'users_email_addresses',
            'module' => 'EmailAddress',
            'bean_name' => 'EmailAddress',
            'source' => 'non-db',
            'vname' => 'LBL_EMAIL_ADDRESSES',
            'reportable' => false,
            'required' => true,
            'link' => 'email_addresses_primary',
            'rname' => 'email_address',
        ) ,
        'email_addresses_primary' => array(
            'name' => 'email_addresses_primary',
            'type' => 'link',
            'relationship' => 'users_email_addresses_primary',
            'source' => 'non-db',
            'vname' => 'LBL_EMAIL_ADDRESS_PRIMARY',
            'duplicate_merge' => 'disabled',
            'required' => true,
        ),
        /* Virtual email fields so they will display on the main user page */
        'email_link_type' => array(
            'name' => 'email_link_type',
            'vname' => 'LBL_EMAIL_LINK_TYPE',
            'type' => 'enum',
            'options' => 'dom_email_link_type',
            'importable' => false,
            'reportable' => false,
            'source' => 'non-db',
            'studio' => false,
        ),

        'aclroles' => array(
            'name' => 'aclroles',
            'type' => 'link',
            'relationship' => 'acl_roles_users',
            'source' => 'non-db',
            'side' => 'right',
            'vname' => 'LBL_ROLES',
        ) ,
        'is_group' => array(
            'name' => 'is_group',
            'vname' => 'LBL_GROUP_USER',
            'type' => 'bool',
            'massupdate' => false,
            'studio' => array('listview' => false, 'searchview'=>false, 'formula' => false),
        ) ,
        /* to support Meetings SubPanels */
        // Deprecated: Use rname_link instead
        'c_accept_status_fields' => array(
            'name' => 'c_accept_status_fields',
            'rname' => 'id',
            'relationship_fields' => array(
                'id' => 'accept_status_id',
                'accept_status' => 'accept_status_name'
            ) ,
            'vname' => 'LBL_LIST_ACCEPT_STATUS',
            'type' => 'relate',
            'link' => 'calls',
            'link_type' => 'relationship_info',
            'source' => 'non-db',
            'importable' => 'false',
            'studio' => array('listview' => false, 'searchview'=>false, 'formula' => false),
        ) ,
        // Deprecated: Use rname_link instead
        'm_accept_status_fields' => array(
            'name' => 'm_accept_status_fields',
            'rname' => 'id',
            'relationship_fields' => array(
                'id' => 'accept_status_id',
                'accept_status' => 'accept_status_name'
            ) ,
            'vname' => 'LBL_LIST_ACCEPT_STATUS',
            'type' => 'relate',
            'link' => 'meetings',
            'link_type' => 'relationship_info',
            'source' => 'non-db',
            'importable' => 'false',
            'studio' => array('listview' => false, 'searchview'=>false, 'formula' => false),
        ) ,
        // Deprecated: Use rname_link instead
        'accept_status_id' => array(
            'name' => 'accept_status_id',
            'type' => 'varchar',
            'source' => 'non-db',
            'vname' => 'LBL_LIST_ACCEPT_STATUS',
            'importable' => 'false',
        	'studio' => array('listview' => false, 'searchview'=>false, 'formula' => false),
        ) ,
        // Deprecated: Use rname_link instead
        'accept_status_name' => array(
            'name' => 'accept_status_name',
            'type' => 'enum',
            'source' => 'non-db',
            'vname' => 'LBL_LIST_ACCEPT_STATUS',
            'options' => 'dom_meeting_accept_status',
            'massupdate' => false,
            'studio' => array('listview' => false, 'searchview'=>false, 'formula' => false),
        ) ,
        'accept_status_calls' => array(
            'massupdate' => false,
            'name' => 'accept_status_calls',
            'type' => 'enum',
            'studio' => 'false',
            'source' => 'non-db',
            'vname' => 'LBL_LIST_ACCEPT_STATUS',
            'options' => 'dom_meeting_accept_status',
            'importable' => 'false',
            'link' => 'calls',
            'rname_link' => 'accept_status',
        ),
        'accept_status_meetings' => array(
            'massupdate' => false,
            'name' => 'accept_status_meetings',
            'type' => 'enum',
            'studio' => 'false',
            'source' => 'non-db',
            'vname' => 'LBL_LIST_ACCEPT_STATUS',
            'options' => 'dom_meeting_accept_status',
            'importable' => 'false',
            'link' => 'meetings',
            'rname_link' => 'accept_status',
        ),
        'prospect_lists' => array(
            'name' => 'prospect_lists',
            'type' => 'link',
            'relationship' => 'prospect_list_users',
            'module' => 'ProspectLists',
            'source' => 'non-db',
            'vname' => 'LBL_PROSPECT_LIST',
        ) ,
        'emails_users' => array(
            'name' => 'emails_users',
            'type' => 'link',
            'relationship' => 'emails_users_rel',
            'module' => 'Emails',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS'
        ),
//BEGIN SUGARCRM flav=pro ONLY
        'holidays' => array(
            'name' => 'holidays',
            'type' => 'link',
            'relationship' => 'users_holidays',
            'source' => 'non-db',
            'side' => 'right',
            'vname' => 'LBL_HOLIDAYS',
        ) ,
//END SUGARCRM flav=pro ONLY

       'eapm' =>
		  array (
		    'name' => 'eapm',
		    'type' => 'link',
		    'relationship' => 'eapm_assigned_user',
		    'vname' => 'LBL_ASSIGNED_TO_USER',
		    'source'=>'non-db',
		  ),
	 'oauth_tokens' =>
      array (
        'name' => 'oauth_tokens',
        'type' => 'link',
        'relationship' => 'oauthtokens_assigned_user',
        'vname' => 'LBL_OAUTH_TOKENS',
        'link_type' => 'one',
        'module'=>'OAuthTokens',
        'bean_name'=>'OAuthToken',
        'source'=>'non-db',
        'side' => 'left',
      ),
//BEGIN SUGARCRM flav=pro ONLY
        'project_resource'=>
		array (
			'name' => 'project_resource',
			'type' => 'link',
			'relationship' => 'projects_users_resources',
			'source' => 'non-db',
			'vname' => 'LBL_PROJECTS',
		),
        'quotas' =>
        array (
            'name' => 'quotas',
            'type' => 'link',
            'relationship' => 'users_quotas',
            'source'=>'non-db',
            'link_type'=>'one',
            'vname'=>'LBL_QUOTAS',
        ),
        'forecasts' =>
        array (
            'name' => 'forecasts',
            'type' => 'link',
            'relationship' => 'users_forecasts',
            'source'=>'non-db',
            'link_type'=>'one',
            'vname'=>'LBL_FORECASTS',
        ),
//END SUGARCRM flav=pro ONLY

    'preferred_language' =>
      array(
         'name' => 'preferred_language',
         'type' => 'enum',
         'vname' => 'LBL_PREFERRED_LANGUAGE',
         'options' => 'available_language_dom',
      ),


        'activities' => array (
            'name' => 'activities',
            'type' => 'link',
            'relationship' => 'activities_users',
            'link_type' => 'many',
            'module' => 'Activities',
            'bean_name' => 'Activity',
            'source' => 'non-db',
        ),

    ) ,
    'name_format_map' => array(
        'f' => 'first_name',
        'l' => 'last_name',
        't' => 'title',
    ),
    'indices' => array(
        array(
            'name' => 'userspk',
            'type' => 'primary',
            'fields' => array(
                'id'
            )
        ) ,
        array(
            'name' => 'idx_user_name',
            'type' => 'index',
            'fields' => array(
                'user_name',
                'is_group',
                'status',
                'last_name',
                'first_name',
                'id'
            )
        ) ,
        array(
			'name' => 'idx_users_reports_to_id',
			'type' => 'index',
			'fields' => array('reports_to_id', 'id')
		),
        array(
            'name' => 'idx_last_login',
            'type' => 'index',
            'fields' => array('last_login')
        ),
     //BEGIN SUGARCRM flav=pro ONLY
		array(
			'name' => 'idx_users_tmst_id',
			'type' => 'index',
			'fields' => array('team_set_id')
		),
	//END SUGARCRM flav=pro ONLY
        array('name' => 'idx_user_title', 'type' => 'index', 'fields' => array('title')),
        array('name' => 'idx_user_department', 'type' => 'index', 'fields' => array('department')),
    ) ,
	'relationships' => array (
  		'user_direct_reports' => array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id', 'rhs_module'=> 'Users', 'rhs_table'=> 'users', 'rhs_key' => 'reports_to_id', 'relationship_type'=>'one-to-many'),
  		'users_users_signatures' =>
  		   array(
  		       'lhs_module'=> 'Users',
  		       'lhs_table'=> 'users',
  		       'lhs_key' => 'id',
  		       'rhs_module'=> 'UserSignatures',
  		       'rhs_table'=> 'users_signatures',
  		       'rhs_key' => 'user_id',
  		       'relationship_type'=>'one-to-many'
  		       ),
    	'users_email_addresses' =>
		    array(
		        'lhs_module'=> "Users", 'lhs_table'=> 'users', 'lhs_key' => 'id',
		        'rhs_module'=> 'EmailAddresses', 'rhs_table'=> 'email_addresses', 'rhs_key' => 'id',
		        'relationship_type'=>'many-to-many',
		        'join_table'=> 'email_addr_bean_rel', 'join_key_lhs'=>'bean_id', 'join_key_rhs'=>'email_address_id',
		        'relationship_role_column'=>'bean_module',
		        'relationship_role_column_value'=>"Users"
		    ),
		'users_email_addresses_primary' =>
		    array('lhs_module'=> "Users", 'lhs_table'=> 'users', 'lhs_key' => 'id',
		        'rhs_module'=> 'EmailAddresses', 'rhs_table'=> 'email_addresses', 'rhs_key' => 'id',
		        'relationship_type'=>'many-to-many',
		        'join_table'=> 'email_addr_bean_rel', 'join_key_lhs'=>'bean_id', 'join_key_rhs'=>'email_address_id',
		        'relationship_role_column'=>'primary_address',
		        'relationship_role_column_value'=>'1'
		    ),
		//BEGIN SUGARCRM flav=pro ONLY
		'users_team_count_relationship' =>
			 array(
			 	'lhs_module'=> 'Teams',
			 	'lhs_table'=> 'team_sets',
			 	'lhs_key' => 'id',
	    		'rhs_module'=> 'Users',
	    		'rhs_table'=> 'users',
	    		'rhs_key' => 'team_set_id',
	   			'relationship_type'=>'one-to-many'
			 ),
		'users_teams' =>
			array (
				'lhs_module'        => 'Users',
	            'lhs_table'         => 'users',
	            'lhs_key'           => 'team_set_id',
	            'rhs_module'        => 'Teams',
	            'rhs_table'         => 'teams',
	            'rhs_key'           => 'id',
	            'relationship_type' => 'many-to-many',
	            'join_table'        => 'team_sets_teams',
	            'join_key_lhs'      => 'team_set_id',
	            'join_key_rhs'      => 'team_id',
			),
        'users_forecasts' => array(
            'rhs_module'		=> 'Forecasts',
            'rhs_table'			=> 'forecasts',
            'rhs_key'			=> 'user_id',
            'lhs_module'		=> 'Users',
            'lhs_table'			=> 'users',
            'lhs_key'			=> 'id',
            'relationship_type'	=> 'one-to-many',
            'relationship_role_column'=>'forecast_type',
            'relationship_role_column_value'=>'Rollup'
        ),

        'users_quotas' => array(
            'rhs_module'		=> 'Quotas',
            'rhs_table'			=> 'quotas',
            'rhs_key'			=> 'user_id',
            'lhs_module'		=> 'Users',
            'lhs_table'			=> 'users',
            'lhs_key'			=> 'id',
            'relationship_type'	=> 'one-to-many',
            'relationship_role_column'=>'quota_type',
            'relationship_role_column_value'=>'Direct'
        ),

        'users_team_sets' => array (
            'lhs_module'        => 'Teams',
            'lhs_table'         => 'teams',
            'lhs_key'           => 'id',
            'rhs_module'        => 'Users',
            'rhs_table'         => 'users',
            'rhs_key'           => 'team_set_id',
            'relationship_type' => 'many-to-many',
            'join_table'        => 'team_sets_teams',
            'join_key_lhs'      => 'team_id',
            'join_key_rhs'      => 'team_set_id',
        ),
        'users_team' => array(
            'lhs_module'=> 'Teams',
            'lhs_table'=> 'teams',
            'lhs_key' => 'id',
            'rhs_module'=> 'Users',
            'rhs_table'=> 'users',
            'rhs_key' => 'default_team',
            'relationship_type'=>'one-to-many'
        ),
	   //END SUGARCRM flav=pro ONLY
    ),

    'acls' => array('SugarACLUsers' => true, 'SugarACLStatic' => true),
);
