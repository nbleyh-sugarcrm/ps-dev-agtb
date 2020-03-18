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

namespace Sugarcrm\Sugarcrm\Denormalization\Relate;

use SugarBean;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Db\Db;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Db\OnlineOperations;

/**
 *
 * Logic hook handler
 *
 */
final class HookHandler
{
    /** @var array */
    private static $settingsCache;

    /** @var OnlineOperations */
    private $db;

    /** @var HookConfig */
    private $config;

    public function __construct()
    {
        $this->db = Db::getInstance();
        $this->config = new HookConfig();
    }

    /**
     * To be used from logic hooks
     *
     * @param SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function handleBeforeUpdate(?SugarBean $bean, string $event, array $arguments): void
    {
        foreach ($this->getSettings($bean) as $sourceLinkedFieldName => $options) {
            if ($options['is_main']) {
                $this->db->updateLinkedBean(
                    $bean,
                    $options['link']['linked_field_name'],
                    $options['link']['linked_key'],
                    $options['link']['join_table'],
                    $options['link']['join_main_key'],
                    $options['link']['join_linked_key'],
                    $options['denorm_field_name'],
                    $options['link']['main_table'],
                    $options['link']['main_key']
                );
                if (!empty($options['synchronization_in_progress'])) {
                    $this->db->updateTemporaryTable(
                        $bean,
                        $options['link']['linked_field_name'],
                        $options['link']['linked_table'],
                        $options['link']['linked_key']
                    );
                }
            } else {
                $bean->{$options['denorm_field_name']} = $bean->$sourceLinkedFieldName;
                if (!empty($options['synchronization_in_progress'])) {
                    $this->db->updateTemporaryTableWithValue($bean, $bean->$sourceLinkedFieldName);
                }
            }
        }
    }

    /**
     * To be used from logic hooks
     *
     * @param SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function handleAfterUpdate(?SugarBean $bean, string $event, array $arguments): void
    {
        foreach ($this->getSettings($bean) as $sourceLinkedFieldName => $options) {
            // "track_field" uses to track direct bean->link_id modification and update appropriate denorm field
            if (isset($options['track_field']) && isset($arguments['dataChanges'][$options['track_field']])) {
                if (!empty($bean->{$options['track_field']}) && !$options['is_main']) {
                    // "track field" value changed and now we have to update related field
                    $this->db->updateBeanWithLinkId(
                        $bean,
                        $options['link']['linked_field_name'],
                        $options['link']['linked_table'],
                        $options['link']['linked_key'],
                        $options['link']['main_table'],
                        $options['denorm_field_name'],
                        $bean->{$options['track_field']}
                    );
                    if (!empty($options['synchronization_in_progress'])) {
                        $this->db->updateTemporaryTable(
                            $bean,
                            $options['link']['linked_field_name'],
                            $options['link']['linked_table'],
                            $options['link']['linked_key']
                        );
                    }
                }
            }
        }
    }

    /**
     * To be used from logic hooks
     *
     * @param SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function handleDeleteRelationship(?SugarBean $bean, string $event, array $arguments): void
    {
        foreach ($this->getSettings($bean) as $sourceLinkedFieldName => $options) {
            if ($options['module'] !== $arguments['related_module']) {
                continue;
            }

            if (!$options['is_main']) {
                $bean->{$options['denorm_field_name']} = '';
                $this->db->updateBean($bean, $options['denorm_field_name']);
                if (!empty($options['synchronization_in_progress'])) {
                    $this->db->updateTemporaryTable(
                        $bean,
                        $options['link']['linked_field_name'],
                        $options['link']['linked_table'],
                        $options['link']['linked_key']
                    );
                }
            }
        }
    }

    /**
     * To be used from logic hooks
     *
     * @param SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function handleAddRelationship(?SugarBean $bean, string $event, array $arguments): void
    {
        foreach ($this->getSettings($bean) as $sourceLinkedFieldName => $options) {
            if ($options['module'] !== $arguments['related_module']) {
                continue;
            }
            if (!$options['is_main']) {
                $this->handleRelationshipModification($sourceLinkedFieldName, $bean, $options);
            } else {
                $this->db->updateLinkedBean(
                    $bean,
                    $options['link']['linked_field_name'],
                    $options['link']['linked_key'],
                    $options['link']['join_table'],
                    $options['link']['join_main_key'],
                    $options['link']['join_linked_key'],
                    $options['denorm_field_name'],
                    $options['link']['main_table'],
                    $options['link']['main_key']
                );
                if (!empty($options['synchronization_in_progress'])) {
                    $this->db->updateTemporaryTable(
                        $bean,
                        $options['link']['linked_field_name'],
                        $options['link']['linked_table'],
                        $options['link']['linked_key']
                    );
                }
            }
        }
    }

    private function getSettings($bean): array
    {
        if (!$bean instanceof SugarBean) {
            return [];
        }

        return $this->config->getModuleConfiguration($bean->getModuleName());
    }

    private function handleRelationshipModification(string $fieldName, SugarBean $bean, array $options): void
    {
        $modifiedLinkId = $this->getModifiedLinkId($fieldName, $bean);

        // if the link ID was modified but the value wasn't updated - update it
        if ($modifiedLinkId) {
            $bean->{$options['denorm_field_name']} = $bean->$fieldName = $this->db->fetchValue(
                $options['link']['linked_table'],
                $options['link']['linked_field_name'],
                $modifiedLinkId
            );
        } else {
            $bean->{$options['denorm_field_name']} = $bean->$fieldName;
            if (!empty($options['synchronization_in_progress'])) {
                $this->db->updateTemporaryTableWithValue($bean, $bean->$fieldName);
            }
        }
    }

    private function getModifiedLinkId(string $fieldName, SugarBean $bean): ?string
    {
        $idName = $bean->getFieldDefinition($fieldName)['id_name'] ?? null;
        if (!$idName) {
            return null;
        }

        $oldId = $bean->fetched_rel_row[$idName] ?? null;
        $newId = $bean->$idName;

        $oldValue = $bean->fetched_rel_row[$fieldName] ?? null;
        $newValue = $bean->$fieldName;

        // the link ID was changed but the value still wasn't updated
        if ($oldId !== $newId && $oldValue === $newValue) {
            return $newId;
        }

        return null;
    }
}
