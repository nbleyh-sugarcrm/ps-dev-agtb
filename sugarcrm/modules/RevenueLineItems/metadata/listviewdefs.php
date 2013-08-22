<?php

/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright 2004-2013 SugarCRM Inc. All rights reserved.
 */

//BEGIN SUGARCRM flav=pro && flav!=ent ONLY
$fields = array(
    'NAME' => array (
        'width' => '40',
        'label' => 'LBL_LIST_NAME',
        'link' => true,
        'default' => true
    ),
    'ACCOUNT_NAME' => array (
        'width' => '20',
        'label' => 'LBL_LIST_ACCOUNT_NAME',
        'id' => 'ACCOUNT_ID',
        'module' => 'Accounts',
        'link' => true,
        'default' => true,
        'ACLTag' => 'ACCOUNT',
        'related_fields' => array (
            'account_id'
        ),
        'sortable' => true
    ),
    'STATUS' => array (
        'width' => '10',
        'label' => 'LBL_LIST_STATUS',
        'link' => false,
        'default' => true
    ),
    'QUANTITY' => array (
        'width' => '10',
        'label' => 'LBL_LIST_QUANTITY',
        'link' => false,
        'default' => true
    ),
    'DISCOUNT_USDOLLAR' => array (
        'width' => '10',
        'label' => 'LBL_LIST_DISCOUNT_PRICE',
        'link' => false,
        'default' => true,
        'currency_format' => true,
        'align' => 'right'
    ),
    'LIST_USDOLLAR' => array (
        'width' => '10',
        'label' => 'LBL_LIST_LIST_PRICE',
        'link' => false,
        'default' => true,
        'currency_format' => true,
        'align' => 'right',
        
    ),
    'COST_PRICE' =>  array(
        'width' => '10',
        'label' => 'LBL_LIST_COST_PRICE',
        'link' => false,
        'default' => true,
        'currency_format' => true,
        'align' => 'right',
        ),
    'DATE_ENTERED' => array (
        'type' => 'datetime',
        'label' => 'LBL_DATE_ENTERED',
        'width' => '10',
        'default' => true
    )
);
//END SUGARCRM flav=pro && flav!=ent ONLY
 
//BEGIN SUGARCRM flav=ent ONLY
// ENT/ULT only fields
$fields = array(
    'NAME' => array (
        'width' => '40',
        'label' => 'LBL_LIST_NAME',
        'link' => true,
        'default' => true
    ),
    'ACCOUNT_NAME' => array (
        'width' => '20',
        'label' => 'LBL_LIST_ACCOUNT_NAME',
        'id' => 'ACCOUNT_ID',
        'module' => 'Accounts',
        'link' => true,
        'default' => true,
        'ACLTag' => 'ACCOUNT',
        'related_fields' => array (
            'account_id'
        ),
        'sortable' => true
    ),
    'OPPORTUNITY_NAME' => array (
        'width' => '20',
        'label' => 'LBL_LIST_OPPORTUNITY_NAME',
        'id' => 'OPPORTUNITY_ID',
        'module' => 'Opportunities',
        'link' => true,
        'default' => true,
        'ACLTag' => 'OPPORTUNITY',
        'related_fields' => array (
            'opportunity_id'
        ),
        'sortable' => true
    ),
    'SALES_STAGE' => array (
        'width' => '10',
        'label' => 'LBL_LIST_SALES_STAGE',
        'link' => false,
        'default' => true
    ),
    'PROBABILITY' => array (
        'width' => '10',
        'label' => 'LBL_LIST_PROBABILITY',
        'link' => false,
        'default' => true
    ),
    'COMMIT_STAGE' => array (
        'width' => '10',
        'label' => 'LBL_LIST_COMMIT_STAGE',
        'link' => false,
        'default' => true
    ),
    'PRODUCT_TEMPLATE_NAME' => array (
        'type' => 'relate',
        'link' => 'revenuelineitems_templates_link',
        'label' => 'LBL_LIST_PRODUCT_TEMPLATE',
        'width' => '10%',
        'default' => false
    ),
    'CATEGORY_NAME' => array (
        'type' => 'relate',
        'link' => 'revenuelineitems_categories_link',
        'label' => 'LBL_CATEGORY_NAME',
        'width' => '10%',
        'default' => false
    ),
    'QUANTITY' => array (
        'width' => '10',
        'label' => 'LBL_LIST_QUANTITY',
        'link' => false,
        'default' => true
    ),
    'LIKELY_CASE' =>  array(
        'width' => '10',
        'label' => 'LBL_LIKELY',
        'link' => false,
        'default' => true,
        'currency_format' => true,
        'align' => 'right',
    ),
    'BEST_CASE' =>  array(
        'width' => '10',
        'label' => 'LBL_BEST',
        'link' => false,
        'default' => true,
        'currency_format' => true,
        'align' => 'right',
    ),
    'WORST_CASE' =>  array(
        'width' => '10',
        'label' => 'LBL_WORST',
        'link' => false,
        'default' => true,
        'currency_format' => true,
        'align' => 'right',
    ),
    'QUOTE_NAME' => array (
        'type' => 'relate',
        'link' => 'quotes',
        'label' => 'LBL_QUOTE_NAME',
        'width' => '10%',
        'default' => false
    ),
    'ASSIGNED_USER_NAME' => array (
        'width' => '8',
        'label' => 'LBL_LIST_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => true
    ),
);
//END SUGARCRM flav=ent ONLY

$listViewDefs['RevenueLineItems'] = $fields;
