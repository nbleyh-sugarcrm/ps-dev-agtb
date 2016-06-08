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

$dictionary['Addressee'] = array(
    'table' => 'addressees',
    'reassignable' => false,
    'audited' => true,
    'activity_enabled' => false,
    'duplicate_merge' => true,
    'fields' => array(
        'meetings' => array(
            'name' => 'meetings',
            'type' => 'link',
            'relationship' => 'meetings_addressees',
            'source' => 'non-db',
            'vname' => 'LBL_MEETINGS',
        ),
        'calls' => array(
            'name' => 'calls',
            'type' => 'link',
            'relationship' => 'calls_addressees',
            'source' => 'non-db',
            'vname' => 'LBL_CALLS',
        ),
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
    ),

    'acls' => array('SugarACLAddressees' => true),

    'relationships' => array(),
    'optimistic_locking' => true,
    'unified_search' => true,
    'full_text_search' => false,
    'ignore_templates' => array(
        'taggable',
    ),
    'uses' => array(
        'basic',
        'assignable',
        'team_security',
        'person',
    )
);

VardefManager::createVardef('Addressees', 'Addressee');
