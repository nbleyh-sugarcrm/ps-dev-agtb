<?php
/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (\“MSA\”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */

//BEGIN SUGARCRM flav=pro && flav!=ent ONLY
// PRO/CORP only fields
$fields = array(
    array(
        'name' => 'name',
        'link' => true,
        'label' => 'LBL_LIST_NAME',
        'enabled' => true,
        'default' => true
    ),
    array(
        'name' => 'account_name',
        'sortable' => false
    ),
    'status',
    'quantity',
    array(
        'name' => 'discount_price',
        'type' => 'currency',
        'related_fields' => array(
            'currency_id',
            'base_rate',
        ),
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',

    ),
    array(
        'name' => 'list_price',
        'type' => 'currency',
        'related_fields' => array(
            'currency_id',
            'base_rate',
        ),
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',

    ),
    array(
        'name' => 'cost_price',
        'type' => 'currency',
        'related_fields' => array(
            'currency_id',
            'base_rate',
        ),
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',
    ),
    'date_entered'
);
//END SUGARCRM flav=pro && flav!=ent ONLY

//BEGIN SUGARCRM flav=ent ONLY
// ENT/ULT only fields
$fields = array(
    array(
        'name' => 'name',
        'link' => true,
        'label' => 'LBL_LIST_NAME',
        'enabled' => true,
        'default' => true
    ),
    array(
        'name' => 'opportunity_name',
        'sortable' => false
    ),
    array(
        'name' => 'account_name',
        'readonly' => true,
        'sortable' => false
    ),
    'sales_stage',
    'probability',
    'date_closed',
    'commit_stage',
    array(
        'name' => 'product_template_name',
        'sortable' => false
    ),
    array(
        'name' => 'category_name',
        'sortable' => false
    ),
    'quantity',
    array(
        'name' => 'likely_case',
        'required' => true,
        'type' => 'currency',
        'related_fields' => array(
            'currency_id',
            'base_rate',
        ),
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',
    ),
    array(
        'name' => 'best_case',
        'required' => true,
        'type' => 'currency',
        'related_fields' => array(
            'currency_id',
            'base_rate',
        ),
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',
    ),
    array(
        'name' => 'worst_case',
        'required' => true,
        'type' => 'currency',
        'related_fields' => array(
            'currency_id',
            'base_rate',
        ),
        'convertToBase' => true,
        'currency_field' => 'currency_id',
        'base_rate_field' => 'base_rate',
    ),
    array(
        'name' => 'quote_name',
        'label' => 'LBL_ASSOCIATED_QUOTE',
        'related_fields' => array('quote_id'),
        // this is a hack to get the quote_id field loaded
        'readonly' => true,
    ),
    array(
        'name' => 'assigned_user_name',
        'sortable' => false
    )
);
//END SUGARCRM flav=ent ONLY

$viewdefs['RevenueLineItems']['base']['view']['subpanel-list'] = array(
    'favorite' => true,
    'panels' => array(
        array(
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => $fields
        ),
    ),
    'selection' => array (
        'type' => 'multi',
        'actions' => array (
            array (
                'name' => 'quote_button',
                'type' => 'button',
                'label' => 'LBL_CREATE_QUOTE',
                'primary' => true,
                'events' => array (
                    'click' => 'function(e) {
                                    this.view.layout.trigger("list:massquote:fire");
                                }'
                ),
                'acl_action' => 'massquote',
            ),
        ),
    ),
    'rowactions' => array(
        'css_class' => 'pull-right',
        'actions' => array(
            array(
                'type' => 'rowaction',
                'css_class' => 'btn',
                'tooltip' => 'LBL_PREVIEW',
                'event' => 'list:preview:fire',
                'icon' => 'icon-eye-open',
                'acl_action' => 'view',
            ),
            array(
                'type' => 'rowaction',
                'name' => 'edit_button',
                'icon' => 'icon-pencil',
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'edit',
            ),
        ),
    ),
);
