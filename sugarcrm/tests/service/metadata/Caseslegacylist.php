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




$listViewDefs['Cases'] = array(
	'CASE_NUMBER' => array(
		'width' => '5',
		'label' => 'LBL_NUMBER',
		'link' => true,
        'default' => true),
	'NAME' => array(
		'width' => '32',
		'label' => 'LBL_SUBJECT',
		'default' => true,
        'link' => true),
	'STATUS' => array(
		'width' => '10',
		'label' => 'LBL_STATUS',
        'default' => true),
    'PRIORITY' => array(
        'width' => '10',
        'label' => 'LBL_PRIORITY',
        'default' => true),
    'RESOLUTION' => array(
        'width' => '10',
        'label' => 'LBL_RESOLUTION',
        'default' => true),
	'TEAM_NAME' => array(
		'width' => '9',
		'label' => 'LBL_TEAM',
        'default' => true),
	'ASSIGNED_USER_NAME' => array(
		'width' => '9',
		'label' => 'LBL_ASSIGNED_USER',
        'default' => true),

);
?>
