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
$module_name = 'Manufacturers';
$viewdefs[$module_name]['base']['menu']['header'] = array(
    array(
        'route'=>'#'.'ProductTemplates'.'/create',
        'label' =>'LNK_NEW_PRODUCT',
        'acl_action'=>'',
        'acl_module'=>'',
        'icon' => 'icon-plus',
    ),
    array(
        'route'=>'#' . 'ProductTemplates',
        'label' =>'LNK_PRODUCT_LIST',
        'acl_action'=>'',
        'acl_module'=>'',
        'icon' => 'icon-list',
    ),
    array(
        'route'=>'#bwc/index.php?module=Manufacturers&action=EditView&return_module=Manufacturers&return_action=DetailView',
        'label' =>'LNK_NEW_MANUFACTURER',
        'acl_action'=>'',
        'acl_module'=>'',
        'icon' => 'icon-list',
    ),
    array(
        'route'=>'#ProductCategories',
        'label' =>'LNK_NEW_PRODUCT_CATEGORY',
        'acl_action'=>'',
        'acl_module'=>'',
        'icon' => 'icon-list',
    ),
    array(
        'route'=>'#bwc/index.php?module=ProductTypes&action=EditView&return_module=ProductTypes&return_action=DetailView',
        'label' =>'LNK_NEW_PRODUCT_TYPE',
        'acl_action'=>'',
        'acl_module'=>'',
        'icon' => 'icon-list',
    ),
    array(
        'route'=>'#bwc/index.php?module=Import&action=Step1&import_module=Manufacturers&return_module=Manufacturers&return_action=index',
        'label' =>'LNK_IMPORT_MANUFACTURERS',
        'acl_action'=>'',
        'acl_module'=>'',
        'icon' => 'icon-upload',
    ),

);
