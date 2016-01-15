<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
$searchFields['Users'] = 
	array (
	    'user_name' => array( 'query_type'=>'default'),
		'first_name' => array( 'query_type'=>'default'),
		'last_name'=> array('query_type'=>'default'),
        'search_name'=> array('query_type'=>'default','db_field'=>array('first_name','last_name'),'force_unifiedsearch'=>true),
        'is_admin'=> array('query_type'=>'default', 'operator'=>'=', 'input_type' => 'checkbox'),
        'is_group'=> array('query_type'=>'default', 'operator'=>'=', 'input_type' => 'checkbox'),
        'status'=> array('query_type'=>'default', 'options' => 'user_status_dom', 'template_var' => 'STATUS_OPTIONS', 'options_add_blank' => true),
        'email'=> array(
            'query_type' => 'default',
            'operator' => 'subquery',
            'subquery' => 'SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 and ea.email_address LIKE',
            'db_field' => array(
                'id',
            )
        ),
        'phone'=> array(
            'query_type' => 'default',
            'operator' => 'subquery',
            'subquery' => array('SELECT id FROM users where phone_home LIKE ',
                'SELECT id FROM users where phone_fax LIKE',
                'SELECT id FROM users where phone_other LIKE',
                'SELECT id FROM users where phone_work LIKE',
                'SELECT id FROM users where phone_mobile LIKE',
                'OR' =>true              
            ),
            'db_field' => array(
                'id',
            )
        ),
	);

?>
