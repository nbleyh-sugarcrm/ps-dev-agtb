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

namespace Sugarcrm\Sugarcrm\Audit;

use BeanFactory;
use DBManagerFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use InvalidArgumentException;
use JsonSerializable;
use SugarBean;
use Sugarcrm\Sugarcrm\DataPrivacy\Erasure\FieldList as ErasureFieldList;
use Sugarcrm\Sugarcrm\Security\Context;
use Sugarcrm\Sugarcrm\Security\Subject;
use Sugarcrm\Sugarcrm\Util\Uuid;
use TimeDate;

class EventRepository
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var Context
     */
    private $context;

    /**
     * Constructor
     *
     * @param Connection $conn
     * @param Context $context
     */
    public function __construct(Connection $conn, Context $context)
    {
        $this->conn = $conn;
        $this->context = $context;
    }

    /**
     * Registers update in EventRepository. Then saves audited fields.
     * @param SugarBean $bean
     * @param FieldChangeList $changedFields
     * @return string id of audit event created
     * @throws DBALException
     */
    public function registerUpdate(SugarBean $bean, FieldChangeList $changedFields)
    {
        return $this->save($bean, 'update', $this->context, $changedFields);
    }

    /**
     * Registers the update and attributes it to the provided subject
     *
     * @param SugarBean $bean The updated bean
     * @param Subject $subject The subject to attribute the update to
     * @param FieldChangeList $changedFields
     *
     * @return string id of audit event created
     * @throws DBALException
     */
    public function registerUpdateAttributedToSubject(
        SugarBean $bean,
        Subject $subject,
        FieldChangeList $changedFields
    ) {
        return $this->save($bean, 'update', [
            'subject' => $subject,
            'attributes' => [],
        ], $changedFields);
    }

    /**
     * Registers erasure EventRepository. Then saves audited fields.
     * @param SugarBean $bean
     * @param ErasureFieldList $fields list of fields to be erased
     * @return string id of audit event created
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function registerErasure(SugarBean $bean, ErasureFieldList $fields)
    {
        if (count($fields) === 0) {
            throw new InvalidArgumentException("Fields to be erased can not be empty.");
        }

        return $this->save($bean, 'erasure', $this->context, $fields);
    }

    /**
     * Saves EventRepository
     * @param SugarBean $bean SugarBean that was changed
     * @param string $eventType Audit event type
     * @param array|JsonSerializable $source The source of the event
     * @param array|jsonSerializable $data Event data
     * @return string id of record saved
     * @throws DBALException
     */
    private function save(SugarBean $bean, string $eventType, $source, $data)
    {
        $id =  Uuid::uuid1();

        $this->conn->insert(
            'audit_events',
            ['id' => $id,
            'type' => $eventType,
            'parent_id' => $bean->id,
            'module_name' => $bean->module_name,
            'source' => json_encode($source),
            'data' => json_encode($data),
            'date_created' => TimeDate::getInstance()->nowDb(),]
        );

        return $id;
    }

    /**
     * Retrieves latest audit events for given instance of bean and fields
     *
     * @param $module module name
     * @param $id
     * @param array $fields
     * @return array[]
     */
    public function getLatestBeanEvents($module, $id, array $fields)
    {
        $bean = BeanFactory::newBean($module);
        if (empty($fields) || empty($bean)) {
            return array();
        }

        $auditTable = $bean->get_audit_table_name();

        $sql = "SELECT  atab.field_name, atab.date_created, ae.source, ae.type
                FROM {$auditTable} atab
                LEFT JOIN {$auditTable} atab2 ON (atab2.parent_id = atab.parent_id 
                        AND atab2.field_name = atab.field_name
                        AND (atab2.date_created > atab.date_created
                        OR (atab2.date_created = atab.date_created
                            AND atab2.id > atab.id)))
                LEFT JOIN audit_events ae ON (ae.id = atab.event_id)
                WHERE  atab.parent_id = ?
                AND atab.field_name IN (?)
                AND atab2.id is NULL";

        $stmt = $this->conn->executeQuery($sql, [$id, $fields], [null, Connection::PARAM_STR_ARRAY]);

        $db = DBManagerFactory::getInstance();

        $return = array();
        while ($row = $stmt->fetch()) {
            $row['source'] = json_decode($row['source'], true);
            //convert date
            $row['date_created'] = $db->fromConvert($row['date_created'], 'datetime');
            $return[$row['field_name']] = $row;
        }

        return $return;
    }
}
