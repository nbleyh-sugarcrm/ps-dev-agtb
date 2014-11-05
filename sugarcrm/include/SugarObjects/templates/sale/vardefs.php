<?php

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

$vardefs = array(
    'fields' => array(
        'name' =>
        array(
            'name' => 'name',
            'type' => 'name',
            'link' => true, // bug 39288
            'dbType' => 'varchar',
            'vname' => 'LBL_NAME',
            'comment' => 'Name of the Sale',
            'unified_search' => true,
            'full_text_search' => array('enabled' => true, 'boost' => 3),
            'audited' => true,
            'merge_filter' => 'selected',
            'required' => true,
            'importable' => 'required',
            'duplicate_on_record_copy' => 'always',
        ),
        strtolower($object_name) . '_type' =>
        array(
            'name' => strtolower($object_name) . '_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => strtolower($object_name) . '_type_dom',
            'len' => 100,
            'duplicate_on_record_copy' => 'always',
            'comment' => 'The Sale is of this type',
        ),
        'description' =>
        array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
            'comment' => 'Description of the sale',
            'rows' => 6,
            'cols' => 80,
            'duplicate_on_record_copy' => 'always',
        ),
        'lead_source' =>
        array(
            'name' => 'lead_source',
            'vname' => 'LBL_LEAD_SOURCE',
            'type' => 'enum',
            'options' => 'lead_source_dom',
            'len' => '50',
            'duplicate_on_record_copy' => 'always',
            'comment' => 'Source of the sale',
        ),
        'amount' =>
        array(
            'name' => 'amount',
            'vname' => 'LBL_AMOUNT',
            'type' => 'currency',
            'comment' => 'Unconverted amount of the sale',
            'duplicate_merge' => 'disabled',
            'required' => true,
            'duplicate_on_record_copy' => 'always',
            'related_fields' => array(
                'currency_id',
                'base_rate'
            ),
            'convertToBase' => true,
            'showTransactionalAmount' => true,
        ),
        'amount_usdollar' =>
        array(
            'name' => 'amount_usdollar',
            'vname' => 'LBL_AMOUNT_USDOLLAR',
            'type' => 'currency',
            'group' => 'amount',
            'disable_num_format' => true,
            'audited' => true,
            'duplicate_on_record_copy' => 'always',
            'comment' => 'Formatted amount of the sale',
            'studio' => array(
                'mobile' => false,
            ),
            'readonly' => true,
            'is_base_currency' => true,
            'related_fields' => array(
                'currency_id',
                'base_rate'
            ),
            'formula' => 'divide($amount,$base_rate)',
            'calculated' => true,
            'enforced' => true,
        ),
        'currency_id' =>
        array(
            'name' => 'currency_id',
            'type' => 'currency_id',
            'dbType' => 'id',
            'group' => 'currency_id',
            'vname' => 'LBL_CURRENCY',
            'function' => 'getCurrencies',
            'function_bean' => 'Currencies',
            'reportable' => false,
            'default' => '-99',
            'duplicate_on_record_copy' => 'always',
            'comment' => 'Currency used for display purposes'
        ),
        'base_rate' => array(
            'name' => 'base_rate',
            'vname' => 'LBL_CURRENCY_RATE',
            'type' => 'decimal',
            'len' => '26,6',
            'studio' => false
        ),
        'currency_name' =>
        array(
            'name' => 'currency_name',
            'rname' => 'name',
            'id_name' => 'currency_id',
            'vname' => 'LBL_CURRENCY_NAME',
            'type' => 'relate',
            'isnull' => 'true',
            'table' => 'currencies',
            'module' => 'Currencies',
            'source' => 'non-db',
            'function' => 'getCurrencies',
            'function_bean' => 'Currencies',
            'studio' => 'false',
            'duplicate_on_record_copy' => 'always',
        ),
        'currency_symbol' =>
        array(
            'name' => 'currency_symbol',
            'rname' => 'symbol',
            'id_name' => 'currency_id',
            'vname' => 'LBL_CURRENCY_SYMBOL',
            'type' => 'relate',
            'isnull' => 'true',
            'table' => 'currencies',
            'module' => 'Currencies',
            'source' => 'non-db',
            'function' => 'getCurrencySymbols',
            'function_bean' => 'Currencies',
            'duplicate_on_record_copy' => 'always',
        ),
        'date_closed' =>
        array(
            'name' => 'date_closed',
            'vname' => 'LBL_DATE_CLOSED',
            'type' => 'date',
            'audited' => true,
            'required' => true,
            'comment' => 'Expected or actual date the sale will close',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
            'duplicate_on_record_copy' => 'always',
        ),
        'next_step' =>
        array(
            'name' => 'next_step',
            'vname' => 'LBL_NEXT_STEP',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The next step in the sales process',
            'duplicate_on_record_copy' => 'always',
            //BEGIN SUGARCRM flav=pro ONLY
            'merge_filter' => 'enabled',
            //END SUGARCRM flav=pro ONLY
        ),
        'sales_stage' =>
        array(
            'name' => 'sales_stage',
            'vname' => 'LBL_SALES_STAGE',
            'type' => 'enum',
            'options' => 'sales_stage_dom',
            'len' => 100,
            'audited' => true,
            'comment' => 'Indication of progression towards closure',
            'required' => true,
            'importable' => 'required',
            'duplicate_on_record_copy' => 'always',
            //BEGIN SUGARCRM flav=pro ONLY
            'merge_filter' => 'enabled',
            //END SUGARCRM flav=pro ONLY
        ),
        'probability' =>
        array(
            'name' => 'probability',
            'vname' => 'LBL_PROBABILITY',
            'type' => 'int',
            'dbType' => 'double',
            'audited' => true,
            'comment' => 'The probability of closure',
            'validation' => array('type' => 'range', 'min' => 0, 'max' => 100),
            'duplicate_on_record_copy' => 'always',
            //BEGIN SUGARCRM flav=pro ONLY
            'merge_filter' => 'enabled',
            //END SUGARCRM flav=pro ONLY
        )
    ),
    'uses' => array(
        'taggable',
    ),
    'duplicate_check' => array(
        'enabled' => true,
        'FilterDuplicateCheck' => array(
            'filter_template' => array(
                array('name' => array('$starts' => '$name')),
            ),
            'ranking_fields' => array(
                array('in_field_name' => 'name', 'dupe_field_name' => 'name'),
            )
        )
    )
);
