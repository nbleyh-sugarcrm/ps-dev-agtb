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

// Needed by VarDef manager when running the load_fields directive
SugarAutoLoader::load('modules/pmse_Project/pmse_BpmProcessDefinition/LockedFieldsRelatedModulesUtilities.php');

$dictionary['pmse_BpmProcessDefinition'] = array(
    'table' => 'pmse_bpm_process_definition',
    'audited' => false,
    'activity_enabled' => false,
    'duplicate_merge' => true,
    'reassignable' => false,
    'fields' => array(
        'prj_id' => array(
            'required' => true,
            'name' => 'prj_id',
            'vname' => 'Project Identifier',
            'type' => 'id',
            'massupdate' => false,
            'default' => '',
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'size' => '36',
        ),
        'pro_module' => array(
            'required' => true,
            'name' => 'pro_module',
            'vname' => 'The default Module Name for the whole process',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => '',
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'len' => '255',
            'size' => '255',
        ),
        'pro_status' => array(
            'required' => true,
            'name' => 'pro_status',
            'vname' => 'The process status, can be ACTIVE, INACTIVE',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => '',
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'len' => '255',
            'size' => '255',
        ),
        'pro_locked_variables' => array(
            'required' => true,
            'name' => 'pro_locked_variables',
            'vname' => 'array of locked variables, these variables are not able to be modified by SugarCrm Forms',
            'type' => 'text',
            'massupdate' => false,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'size' => '20',
            'rows' => '4',
            'cols' => '20',
        ),
        'pro_terminate_variables' => array(
            'required' => true,
            'name' => 'pro_terminate_variables',
            'vname' => 'array of variables and their values used to halt (terminate) the case',
            'type' => 'text',
            'massupdate' => false,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'size' => '20',
            'rows' => '4',
            'cols' => '20',
        ),
        'execution_mode' => array(
            'required' => true,
            'name' => 'execution_mode',
            'vname' => 'script to be executed',
            'type' => 'varchar',
            'massupdate' => false,
            'default' => 'SYNC',
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => 'true',
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'len' => '10',
            'size' => '10',
        ),
    ),
    'optimistic_locking' => true,
    'unified_search' => true,
    'relationships' => array(),
    'indices' => array(
        'prj_id' => array(
            'name' => 'idx_pd_prj_id',
            'type' => 'index',
            'fields' => array('prj_id'),
        ),
        'pro_status' => array(
            'name' => 'idx_pd_pro_status',
            'type' => 'index',
            'fields' => array('pro_status'),
        ),
    ),
    'uses' => array(
        'basic',
        'assignable',
    ),
    'load_fields' => array(
        'class' =>'LockedFieldsRelatedModulesUtilities',
        'method' => 'getRelatedFields',
    ),
    'ignore_templates' => array(
        'lockable_fields',
    ),
);

VardefManager::createVardef(
    'pmse_BpmProcessDefinition',
    'pmse_BpmProcessDefinition'
);
