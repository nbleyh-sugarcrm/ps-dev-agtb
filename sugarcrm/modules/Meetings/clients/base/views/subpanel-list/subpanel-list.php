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
$viewdefs['Meetings']['base']['view']['subpanel-list'] = array(
  'panels' =>
  array(
    array(
      'name' => 'panel_header',
      'label' => 'LBL_PANEL_1',
      'fields' =>
      array(
        array(
          'name' => 'name',
          'label' => 'LBL_LIST_SUBJECT',
          'enabled' => true,
          'default' => true,
          'link' => true,
        ),
        array(
          'name' => 'status',
          'label' => 'LBL_LIST_STATUS',
          'enabled' => true,
          'default' => true,
        ),
        array(
          'target_record_key' => 'contact_id',
          'target_module' => 'Contacts',
          'name' => 'contact_name',
          'label' => 'LBL_LIST_CONTACT',
          'link' => true,
          'module' => 'Contacts',
          'enabled' => true,
          'default' => true,
          'related_fields' => array('contact_id'),
        ),
        array(
          'name' => 'date_start',
          'label' => 'LBL_LIST_DATE',
          'enabled' => true,
          'default' => true,
        ),
        array(
          'name' => 'date_end',
          'label' => 'LBL_DATE_END',
          'enabled' => true,
          'default' => true,
        ),
        array(
          'name' => 'assigned_user_name',
          'target_record_key' => 'assigned_user_id',
          'target_module' => 'Employees',
          'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
          'enabled' => true,
          'default' => true,
          'sortable' => false,
        ),
      ),
    ),
  ),
  'rowactions' => array(
    'actions' => array(
      array(
          'type' => 'rowaction',
          'name' => 'edit_button',
          'icon' => 'icon-pencil',
          'label' => 'LBL_EDIT_BUTTON',
          'event' => 'list:editrow:fire',
          'acl_action' => 'edit',
          'allow_bwc' => true,
      ),
      array(
        'type' => 'unlink-action',
        'icon' => 'icon-unlink',
        'label' => 'LBL_UNLINK_BUTTON',
      ),
      array(
        'type' => 'closebutton',
        'icon' => 'icon-remove-circle',
        'name' => 'record-close',
        'label' => 'LBL_CLOSE_BUTTON_TITLE',
        'acl_action' => 'edit',
      ),
    ),
  ),
);
