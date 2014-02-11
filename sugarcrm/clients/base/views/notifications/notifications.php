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
 * Copyright (C) 2004-2014 SugarCRM Inc. All rights reserved.
 */

$viewdefs['base']['view']['notifications'] = array(
    // currently we don't support different filters per module
    // (Calls and Meetings) because this is temporary code.
    'remindersFilterDef' => array(
        'reminder_time' => array(
            '$gte' => 0,
        ),
        'status' => array(
            '$equals' => 'Planned',
        ),
        'accept_status_users' => array(
            '$not_equals' => 'decline',
        ),
    ),
    'remindersLimit' => 100,
    'fields' => array(
        'severity' => array(
            'name' => 'severity',
            'type' => 'severity',
        ),
    ),
);
