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
$module_name = '<module_name>';
$_module_name = '<_module_name>';
$viewdefs[$module_name]['mobile']['view']['edit'] = array(
	'templateMeta' => array('maxColumns' => '1',
                            'widths' => array(
                                            array('label' => '10', 'field' => '30'),
                                            array('label' => '10', 'field' => '30')
                                            ),
                            ),


	'panels' => array (
    	array (
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => array(
                array(
                    'name' => $_module_name . '_number',
                    'displayParams' => array(
                        'required' => false,
                        'wireless_detail_only' => true,
                    ),
                ),
                'priority',
                'status',
                array (
                    'name' => 'name',
                    'label' => 'LBL_SUBJECT',
                ),
                'assigned_user_name',
                //BEGIN SUGARCRM flav=pro ONLY
                'team_name',
                //END SUGARCRM flav=pro ONLY
            ),
		),
	),
);
