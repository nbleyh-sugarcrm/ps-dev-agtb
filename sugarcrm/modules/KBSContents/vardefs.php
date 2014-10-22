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
 * Copyright  2004-2014 SugarCRM Inc.  All rights reserved.
 */

$dictionary['KBSContent'] = array(
    'table' => 'kbscontents',
    'audited' => true,
    'activity_enabled' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'full_text_search_filter' => array(
        'term' => array(
            'active_rev' => 1,
        )
    ),
    'unified_search_default_enabled' => true,
    'comment' => 'A content represents information about document',
    'fields' => array(
        'kbdocument_body' => array(
            'name' => 'kbdocument_body',
            'vname' => 'LBL_TEXT_BODY',
            'type' => 'longtext',
            'comment' => 'Article body',
            'full_text_search' => array(
                'enabled' => true,
            ),
            'audited' => true,
        ),
        'language' => array(
            'name' => 'language',
            'type' => 'varchar',
            'len' => '2',
            'required' => true,
            'vname' => 'LBL_LANG',
            'audited' => true,
            'studio' => false,
        ),
        'active_date' => array(
            'name' => 'active_date',
            'vname' => 'LBL_PUBLISH_DATE',
            'type' => 'date',
            'importable' => 'required',
            'sortable' => true,
            'studio' => false,
        ),
        'exp_date' => array(
            'name' => 'exp_date',
            'vname' => 'LBL_EXP_DATE',
            'type' => 'date',
            'sortable' => true,
            'studio' => false,
        ),
        'approved' => array(
            'name' => 'approved',
            'vname' => 'LBL_APPROVED',
            'type' => 'bool',
            'sortable' => true,
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
        ),
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'kbsdocument_status_dom',
            'reportable' => false,
            'audited' => true,
            'studio' => false,
            'full_text_search' => array(
                'enabled' => true,
            ),
        ),
        'viewcount' => array(
            'name' => 'viewcount',
            'vname' => 'LBL_VIEWED_COUNT',
            'type' => 'int',
            'importable' => 'required',
            'default' => 0,
            'sortable' => true,
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
        ),
        'revision' => array(
            'name' => 'revision',
            'vname' => 'LBL_REVISION',
            'type' => 'varchar',
            'len' => '10',
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
        ),
        'useful' => array(
            'name' => 'useful',
            'vname' => 'LBL_USEFUL',
            'type' => 'int',
            'default' => '0',
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
        ),
        'notuseful' => array(
            'name' => 'notuseful',
            'vname' => 'LBL_NOT_USEFUL',
            'type' => 'int',
            'default' => '0',
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
        ),
        'attachment_list' => array(
            'name' => 'attachment_list',
            'type' => 'file',
            'source' => 'non-db',
            'vname' => 'LBL_RATING',
            'studio' => false,
        ),
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'link',
            'relationship' => 'kbscontent_notes',
            'module' => 'Notes',
            'bean_name' => 'Note',
            'source' => 'non-db',
        ),
        'attachments' => array(
            'name' => 'attachments',
            'vname' => 'LBL_ATTACHMENTS',
            'type' => 'link',
            'relationship' => 'kbscontent_attachments',
            'module' => 'Notes',
            'bean_name' => 'Note',
            'source' => 'non-db',
        ),
        'kbsdocuments_kbscontents' => array(
            'name' => 'kbsdocuments_kbscontents',
            'type' => 'link',
            'vname' => 'LBL_KBSDOCUMENTS',
            'relationship' => 'kbsdocuments_kbscontents',
            'source' => 'non-db',
        ),
        'kbsdocument_id' => array(
            'name' => 'kbsdocument_id',
            'id_name' => 'kbsdocument_id',
            'vname' => 'LBL_KBSDOCUMENT_ID',
            'rname' => 'id',
            'type' => 'id',
            'table' => 'kbsdocuments',
            'isnull' => 'true',
            'module' => 'KBSDocuments',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'studio' => false,
        ),
        'kbsdocument_name' => array(
            'name' => 'kbsdocument_name',
            'rname' => 'name',
            'vname' => 'LBL_KBSDOCUMENT',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'kbsdocuments',
            'id_name' => 'kbsdocument_id',
            'link' => 'kbsdocuments_kbscontents',
            'module' => 'KBSDocuments',
            'duplicate_merge' => 'disabled',
            'studio' => false,
        ),
        'active_rev' => array(
            'name' => 'active_rev',
            'vname' => 'LBL_ACTIVE_REV',
            'type' => 'tinyint',
            'isnull' => 'true',
            'comment' => 'Active revision flag',
            'default' => 0,
            'duplicate_on_record_copy' => 'no',
            'full_text_search' => array(
                'enabled' => true,
                'type' => 'bool',
            ),
            'studio' => false,
            'readonly' => true,
        ),
        'internal_rev' => array(
            'name' => 'internal_rev',
            'vname' => 'LBL_INTERNAL_REV',
            'type' => 'tinyint',
            'isnull' => 'true',
            'comment' => 'Internal revision flag',
            'default' => 0,
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
        ),
        'kbsarticles_kbscontents' => array(
            'name' => 'kbsarticles_kbscontents',
            'type' => 'link',
            'vname' => 'LBL_KBSARTICLES',
            'relationship' => 'kbsarticles_kbscontents',
            'source' => 'non-db',
        ),
        'kbsarticle_id' => array(
            'name' => 'kbsarticle_id',
            'id_name' => 'kbsarticle_id',
            'vname' => 'LBL_KBSARTICLE_ID',
            'rname' => 'id',
            'type' => 'id',
            'table' => 'kbsarticles',
            'isnull' => 'true',
            'module' => 'KBSArticles',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'audited' => true,
            'studio' => false,
        ),
        'kbsarticle_name' => array(
            'name' => 'kbsarticle_name',
            'rname' => 'name',
            'vname' => 'LBL_KBSARTICLE',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'kbsarticles',
            'id_name' => 'kbsarticle_id',
            'link' => 'kbsarticles_kbscontents',
            'module' => 'KBSArticles',
            'duplicate_merge' => 'disabled',
            'studio' => false,
        ),
        'localizations' => array(
            'name' => 'localizations',
            'type' => 'link',
            'link_file' => 'modules/KBSContents/LocalizationsLink.php',
            'link_class' => 'LocalizationsLink',
            'source' => 'non-db',
            'vname' => 'LBL_KBSLOCALIZATIONS',
            'relationship' => 'localizations',
            'studio' => false,
        ),
        'revisions' => array(
            'name' => 'revisions',
            'type' => 'link',
            'link_file' => 'modules/KBSContents/RevisionsLink.php',
            'link_class' => 'RevisionsLink',
            'source' => 'non-db',
            'vname' => 'LBL_KBSREVISIONS',
            'relationship' => 'revisions',
            'studio' => false,
        ),
        'related_languages' => array(
            'name' => 'related_languages',
            'type' => 'enum',
            'source' => 'non-db',
            'vname' => 'LBL_KBSLOCALIZATIONS',
            'studio' => false,
        ),
        'kbsapprovers_kbscontents' => array(
            'name' => 'kbsapprovers_kbscontents',
            'type' => 'link',
            'vname' => 'LBL_KBSAPPROVERS',
            'relationship' => 'kbsapprovers_kbscontents',
            'source' => 'non-db',
        ),
        'kbsapprover_id' => array(
            'name' => 'kbsapprover_id',
            'id_name' => 'kbsapprover_id',
            'vname' => 'LBL_KBSAPPROVER_ID',
            'rname' => 'id',
            'type' => 'id',
            'table' => 'users',
            'isnull' => 'true',
            'module' => 'Users',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'audited' => true,
            'studio' => false,
            'comment' => 'User who approved article',
        ),
        'kbsapprover_name' => array(
            'name' => 'kbsapprover_name',
            'rname' => 'full_name',
            'vname' => 'LBL_KBSAPPROVER',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'kbsapprover_id',
            'link' => 'kbsapprovers_kbscontents',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'studio' => false,
        ),

        'relcases_kbscontents' => array(
            'name' => 'relcases_kbscontents',
            'type' => 'link',
            'vname' => 'LBL_KBSCASES',
            'relationship' => 'relcases_kbscontents',
            'source' => 'non-db',
        ),
        'kbscase_id' => array(
            'name' => 'kbscase_id',
            'id_name' => 'kbscase_id',
            'vname' => 'LBL_KBSCASE_ID',
            'rname' => 'id',
            'type' => 'id',
            'table' => 'cases',
            'isnull' => 'true',
            'module' => 'Cases',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'audited' => true,
            'studio' => false,
            'comment' => 'Related case',
        ),
        'kbscase_name' => array(
            'name' => 'kbscase_name',
            'rname' => 'name',
            'vname' => 'LBL_KBSCASE',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'cases',
            'id_name' => 'kbscase_id',
            'link' => 'relcases_kbscontents',
            'module' => 'Cases',
            'duplicate_merge' => 'disabled',
            'studio' => false,
        ),
        'category_id' => array(
            'name' => 'category_id',
            'vname' => 'LBL_TOPIC_ID',
            'type' => 'id',
            'isnull' => 'true',
            'comment' => 'Topic ID',
            'audited' => true,
            'studio' => false,
        ),
        'category_name' => array(
            'name' => 'category_name',
            'rname' => 'name',
            'id_name' => 'category_id',
            'vname' => 'LBL_TOPIC_NAME',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'Categories',
            'table' => 'categories',
            'massupdate' => false,
            'source' => 'non-db',
            'link' => 'category',
        ),
        'category' => array(
            'name' => 'category',
            'type' => 'link',
            'relationship' => 'kbscontent_category',
            'module' => 'Categories',
            'bean_name' => 'Category',
            'source' => 'non-db',
            'vname' => 'LNK_TOPICS',
            'side' => 'right',
        ),
    ),
    'relationships' => array(
        'kbscontent_notes' => array(
            'lhs_module' => 'KBSContents',
            'lhs_table' => 'kbscontents',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'KBSContentsNotes',
        ),
        'kbscontent_attachments' => array(
            'lhs_module' => 'KBSContents',
            'lhs_table' => 'kbscontents',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'KBSContentsAttachments',
        ),
        'kbscontent_category' => array(
            'lhs_module' => 'KBSContents',
            'lhs_table' => 'kbscontents',
            'lhs_key' => 'category_id',
            'rhs_module' => 'Categories',
            'rhs_table' => 'categories',
            'rhs_key' => 'id',
            'relationship_type' => 'one-to-many'
        ),
        'kbsdocuments_kbscontents' => array (
            'lhs_module' => 'KBSDocuments',
            'lhs_table' => 'kbsdocuments',
            'lhs_key' => 'id',
            'rhs_module' => 'KBSContents',
            'rhs_table' => 'kbscontents',
            'rhs_key' => 'kbsdocument_id',
            'relationship_type' => 'one-to-many'
        ),
        'kbsarticles_kbscontents' => array (
            'lhs_module' => 'KBSArticles',
            'lhs_table' => 'kbsarticles',
            'lhs_key' => 'id',
            'rhs_module' => 'KBSContents',
            'rhs_table' => 'kbscontents',
            'rhs_key' => 'kbsarticle_id',
            'relationship_type' => 'one-to-many'
        ),
        'localizations' => array (
            'lhs_module' => 'KBSContents',
            'lhs_table' => 'kbscontents',
            'lhs_key' => 'kbsdocument_id',
            'rhs_module' => 'KBSContents',
            'rhs_table' => 'kbscontents',
            'rhs_key' => 'kbsdocument_id',
            'join_table' => 'kbscontents',
            'join_key_lhs' => 'kbsdocument_id',
            'join_key_rhs' => 'kbsdocument_id',
            'relationship_type' => 'many-to-many',
        ),
        'revisions' => array (
            'lhs_module' => 'KBSContents',
            'lhs_table' => 'kbscontents',
            'lhs_key' => 'kbsarticle_id',
            'rhs_module' => 'KBSContents',
            'rhs_table' => 'kbscontents',
            'rhs_key' => 'kbsarticle_id',
            'join_table' => 'kbscontents',
            'join_key_lhs' => 'kbsarticle_id',
            'join_key_rhs' => 'kbsarticle_id',
            'relationship_type' => 'many-to-many',
        ),
        'kbsapprovers_kbscontents' => array (
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'KBSContents',
            'rhs_table' => 'kbscontents',
            'rhs_key' => 'kbsapprover_id',
            'relationship_type' => 'one-to-many'
        ),
        'relcases_kbscontents' => array (
            'lhs_module' => 'Cases',
            'lhs_table' => 'cases',
            'lhs_key' => 'id',
            'rhs_module' => 'KBSContents',
            'rhs_table' => 'kbscontents',
            'rhs_key' => 'kbscase_id',
            'relationship_type' => 'one-to-many'
        ),
    ),

    'duplicate_check' => array(
        'enabled' => false,
    ),
);

VardefManager::createVardef(
    'KBSContents',
    'KBSContent',
    array(
        'basic',
        'team_security',
        'taggable',
    )
);
$dictionary['KBSContent']['fields']['name']['audited'] = true;
