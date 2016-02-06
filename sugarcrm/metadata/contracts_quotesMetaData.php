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

// $Id: contracts_quotesMetaData.php 55782 2010-04-02 21:07:20Z clee $


$dictionary['contracts_quotes'] = array(
    'table' => 'contracts_quotes',
    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'quote_id', 'type' => 'id'),
        array('name' => 'contract_id', 'type' => 'id'),
		array('name' => 'date_modified', 'type' => 'datetime'),
		array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false),
	),
	'indices' => array (
		array('name' => 'contracts_quot_pk', 'type' =>'primary', 'fields'=>array('id')),
		array('name' => 'contracts_quot_alt', 'type'=>'alternate_key', 'fields'=>array('contract_id', 'quote_id')),
	),
	'relationships' => array (
		'contracts_quotes' => array(
			'lhs_module' => 'Contracts',
			'lhs_table' => 'contracts',
			'lhs_key' => 'id',
			'rhs_module' => 'Quotes',
			'rhs_table' => 'quotes',
			'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'contracts_quotes',
			'join_key_lhs' => 'contract_id',
			'join_key_rhs' => 'quote_id'
		),
	),
);
?>
