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

$viewdefs['Opportunities']['base']['view']['recent-used-product'] = array(
    'tabs' => array(
        array(
            'active' => true,
            'label' => 'LBL_DASHLET_RECENT_USED_PRODUCT_RECENT_TAB',
            'link' => '',
            'module' => '',
            'row_actions' => array(
                array(
                    'type' => 'unlink-action',
                    'icon' => 'fa-chain-broken',
                    'css_class' => 'btn btn-mini',
                    'event' => 'tabbed-dashlet:unlink-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_UNLINK_BUTTON',
                    'acl_action' => 'edit',
                ),
                'include_child_items' => true,
                'invitation_actions' => array(
                    'name' => 'accept_status_users',
                    'type' => 'invitation-actions',
                ),
            ),
        ),
        array(
            'label' => 'LBL_DASHLET_RECENT_USED_PRODUCT_FAVORITES_TAB',
            'link' => '',
            'module' => '',
            'row_actions' => array(
                array(
                    'type' => 'unlink-action',
                    'icon' => 'fa-chain-broken',
                    'css_class' => 'btn btn-mini',
                    'event' => 'tabbed-dashlet:unlink-record:fire',
                    'target' => 'view',
                    'tooltip' => 'LBL_UNLINK_BUTTON',
                    'acl_action' => 'edit',
                ),
                'include_child_items' => true,
                'invitation_actions' => array(
                    'name' => 'accept_status_users',
                    'type' => 'invitation-actions',
                ),
            ),
        ),
    ),
);
