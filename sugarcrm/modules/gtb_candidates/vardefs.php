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

$dictionary['gtb_candidates'] = array(
    'table' => 'gtb_candidates',
    'audited' => true,
    'activity_enabled' => false,
    'duplicate_merge' => true,
    'fields' => array (
  'gender' => 
  array (
    'required' => true,
    'name' => 'gender',
    'vname' => 'LBL_GENDER',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_gender_list',
    'dependency' => false,
  ),
  'first_name' => 
  array (
    'name' => 'first_name',
    'vname' => 'LBL_FIRST_NAME',
    'type' => 'varchar',
    'len' => '100',
    'unified_search' => true,
    'duplicate_on_record_copy' => 'always',
    'full_text_search' => 
    array (
      'enabled' => true,
      'boost' => '1.81',
      'searchable' => true,
    ),
    'comment' => 'First name of the contact',
    'merge_filter' => 'disabled',
    'audited' => true,
    'pii' => true,
    'required' => true,
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => 'First name of the contact',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'reportable' => true,
    'default' => '',
    'calculated' => false,
    'size' => '20',
  ),
  'org_unit' => 
  array (
    'required' => true,
    'name' => 'org_unit',
    'vname' => 'LBL_ORG_UNIT',
    'type' => 'varchar',
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'audited' => true,
    'reportable' => true,
    'unified_search' => true,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'full_text_search' => 
    array (
      'enabled' => true,
      'boost' => '1',
      'searchable' => true,
    ),
    'calculated' => false,
    'len' => '255',
    'size' => '20',
  ),
  'title' => 
  array (
    'name' => 'title',
    'vname' => 'LBL_TITLE',
    'type' => 'varchar',
    'len' => '100',
    'duplicate_on_record_copy' => 'always',
    'comment' => 'The title of the contact',
    'audited' => true,
    'pii' => true,
    'required' => true,
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => 'The title of the contact',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'default' => '',
    'full_text_search' => 
    array (
      'enabled' => '0',
      'boost' => '1',
      'searchable' => false,
    ),
    'calculated' => false,
    'size' => '20',
  ),
  'gtb_cluster' => 
  array (
    'required' => true,
    'name' => 'gtb_cluster',
    'vname' => 'LBL_GTB_CLUSTER',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_cluster_list',
    'dependency' => false,
  ),
  'gtb_function' => 
  array (
    'required' => true,
    'name' => 'gtb_function',
    'vname' => 'LBL_GTB_FUNCTION',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_function_list',
    'dependency' => false,
  ),
  'functional_mobility' => 
  array (
    'required' => true,
    'name' => 'functional_mobility',
    'vname' => 'LBL_FUNCTIONAL_MOBILITY',
    'type' => 'multienum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'calculated' => false,
    'size' => '20',
    'options' => 'gtb_function_list',
    'default' => '',
    'dependency' => '',
    'isMultiSelect' => true,
  ),
  'oe_mobility' => 
  array (
    'required' => true,
    'name' => 'oe_mobility',
    'vname' => 'LBL_OE_MOBILITY',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => 'ADEUS',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_oe_mobility_list',
    'dependency' => false,
  ),
  'target_roles' => 
  array (
    'required' => false,
    'name' => 'target_roles',
    'vname' => 'LBL_TARGET_ROLES',
    'type' => 'text',
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => true,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'full_text_search' => 
    array (
      'enabled' => true,
      'boost' => '1',
      'searchable' => true,
    ),
    'calculated' => false,
    'size' => '20',
    'studio' => 'visible',
    'rows' => '4',
    'cols' => '20',
  ),
  'mobility_comments' => 
  array (
    'required' => false,
    'name' => 'mobility_comments',
    'vname' => 'LBL_MOBILITY_COMMENTS',
    'type' => 'text',
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => true,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'full_text_search' => 
    array (
      'enabled' => true,
      'boost' => '1',
      'searchable' => true,
    ),
    'calculated' => false,
    'size' => '20',
    'studio' => 'visible',
    'rows' => '4',
    'cols' => '20',
  ),
  'career_discussion' => 
  array (
    'required' => false,
    'name' => 'career_discussion',
    'vname' => 'LBL_CAREER_DISCUSSION',
    'type' => 'date',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'calculated' => false,
    'size' => '20',
    'enable_range_search' => false,
  ),
  'primary_address_country' => 
  array (
    'name' => 'primary_address_country',
    'vname' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
    'type' => 'varchar',
    'group' => 'primary_address',
    'comment' => 'Country for primary address',
    'merge_filter' => 'disabled',
    'duplicate_on_record_copy' => 'always',
    'audited' => true,
    'pii' => true,
    'required' => false,
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => 'Country for primary address',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'reportable' => true,
    'unified_search' => false,
    'default' => '',
    'full_text_search' => 
    array (
      'enabled' => '0',
      'boost' => '1',
      'searchable' => false,
    ),
    'calculated' => false,
    'len' => '255',
    'size' => '20',
  ),
  'geo_mobility' => 
  array (
    'required' => true,
    'name' => 'geo_mobility',
    'vname' => 'LBL_GEO_MOBILITY',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'enabled',
    'duplicate_merge_dom_value' => '1',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_geo_mobility_list',
    'dependency' => false,
  ),
  'lead_source' => 
  array (
    'required' => false,
    'name' => 'lead_source',
    'vname' => 'LBL_LEAD_SOURCE',
    'type' => 'varchar',
    'massupdate' => false,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => true,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'full_text_search' => 
    array (
      'enabled' => true,
      'boost' => '1',
      'searchable' => true,
    ),
    'calculated' => false,
    'len' => '255',
    'size' => '20',
  ),
  'language_2' => 
  array (
    'required' => false,
    'name' => 'language_2',
    'vname' => 'LBL_LANGUAGE',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_language_list',
    'dependency' => false,
  ),
  'language_3' => 
  array (
    'required' => false,
    'name' => 'language_3',
    'vname' => 'LBL_LANGUAGE',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_language_list',
    'dependency' => false,
  ),
  'language_4' => 
  array (
    'required' => false,
    'name' => 'language_4',
    'vname' => 'LBL_LANGUAGE',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_language_list',
    'dependency' => false,
  ),
  'language_5' => 
  array (
    'required' => false,
    'name' => 'language_5',
    'vname' => 'LBL_LANGUAGE',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_language_list',
    'dependency' => false,
  ),
  'language_6' => 
  array (
    'required' => false,
    'name' => 'language_6',
    'vname' => 'LBL_LANGUAGE',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_language_list',
    'dependency' => false,
  ),
  'prof_level_1' => 
  array (
    'required' => true,
    'name' => 'prof_level_1',
    'vname' => 'LBL_PROF_LEVEL',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'dependency' => 'greaterThan(strlen($language_1),0)',
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_proficiency_list',
  ),
  'language_1' => 
  array (
    'required' => false,
    'name' => 'language_1',
    'vname' => 'LBL_LANGUAGE',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_language_list',
    'dependency' => false,
  ),
  'prof_level_2' => 
  array (
    'required' => true,
    'name' => 'prof_level_2',
    'vname' => 'LBL_PROF_LEVEL',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'dependency' => 'greaterThan(strlen($language_2),0)',
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_proficiency_list',
  ),
  'prof_level_3' => 
  array (
    'required' => true,
    'name' => 'prof_level_3',
    'vname' => 'LBL_PROF_LEVEL',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'dependency' => 'greaterThan(strlen($language_3),0)',
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_proficiency_list',
  ),
  'prof_level_4' => 
  array (
    'required' => true,
    'name' => 'prof_level_4',
    'vname' => 'LBL_PROF_LEVEL',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'dependency' => 'greaterThan(strlen($language_4),0)',
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_proficiency_list',
  ),
  'prof_level_5' => 
  array (
    'required' => true,
    'name' => 'prof_level_5',
    'vname' => 'LBL_PROF_LEVEL',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'dependency' => 'greaterThan(strlen($language_5),0)',
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_proficiency_list',
  ),
  'prof_level_6' => 
  array (
    'required' => true,
    'name' => 'prof_level_6',
    'vname' => 'LBL_PROF_LEVEL',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'default' => '',
    'calculated' => false,
    'dependency' => 'greaterThan(strlen($language_6),0)',
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_proficiency_list',
  ),
),
    'relationships' => array (
),
    'optimistic_locking' => true,
    'unified_search' => true,
    'full_text_search' => true,
);

if (!class_exists('VardefManager')){
}
VardefManager::createVardef('gtb_candidates','gtb_candidates', array('basic','team_security','assignable','taggable','person'));