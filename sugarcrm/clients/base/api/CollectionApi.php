<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

require_once 'include/api/SugarApi.php';
require_once 'clients/base/api/RelateApi.php';

/**
 * Collection API
 */
class CollectionApi extends SugarApi
{
    /** @var RelateApi */
    protected $relateApi;

    /**
     * Function to compare string values when sorting records
     *
     * @var callable
     */
    protected $collator = 'strcasecmp';

    /**
     * Registers API
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function registerApiRest()
    {
        return array(
            'getCollection' => array(
                'reqType' => 'GET',
                'path' => array('<module>', '?', 'collection', '?'),
                'pathVars' => array('module', 'record', '', 'collection_name'),
                'method' => 'getCollection',
                'shortHelp' => 'Lists collection records.',
                'longHelp' => 'include/api/help/module_record_collection_collection_name_get_help.html',
            ),
        );
    }

    /**
     * Sets the function to compare string values when sorting records
     *
     * @param callable $collator
     */
    public function setCollator($collator)
    {
        $this->collator = $collator;
    }

    /**
     * API endpoint
     *
     * @param ServiceBase $api
     * @param array $args
     *
     * @return array
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function getCollection(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, array('module', 'record', 'collection_name'));
        $bean = $this->loadBean($api, $args);

        $definition = $this->getCollectionDefinition($bean, $args['collection_name']);
        $args = $this->normalizeArguments($args, $definition);

        $sortSpec = $this->getSortSpec($bean, $definition['links'], $args['order_by']);
        $sortFields = $this->getAdditionalSortFields($args, $definition['links'], $sortSpec);

        $data = $this->getData($api, $args, $bean, $definition['links'], $sortFields);
        $allRecords = $this->flattenData($data, $nextOffset);

        $this->sortRecords($allRecords, $sortSpec);

        $records = array_slice($allRecords, 0, $args['max_num']);
        $remainder = array_slice($allRecords, $args['max_num']);
        $nextOffset = $this->getNextOffset($args['offset'], $records, $nextOffset, $remainder);

        // remove unwanted fields from the data
        $records = $this->cleanData($records, $sortFields);

        return array(
            'records' => $records,
            'next_offset' => $nextOffset,
        );
    }

    /**
     * Retrieves records from collection links
     *
     * @param ServiceBase $api
     * @param array $args API arguments
     * @param SugarBean $bean Primary bean
     * @param array $links Collection link definitions
     * @param array $sortFields Additional fields required for client side sort
     *
     * @return array
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    protected function getData(
        ServiceBase $api,
        array $args,
        SugarBean $bean,
        array $links,
        array $sortFields
    ) {
        $data = array();
        foreach ($links as $link) {
            $linkName = $link['name'];
            if ($args['offset'][$linkName] >= 0) {
                $linkArgs = $this->getLinkArguments($api, $args, $bean, $link, $sortFields[$linkName]);
                $data[$linkName] = $this->getRelateApi()->filterRelated($api, $linkArgs);
            }
        }

        return $data;
    }

    /**
     * Creates arguments for RelateApi for specific link
     *
     * @param ServiceBase $api
     * @param array $args CollectionApi arguments
     * @param SugarBean $bean Primary bean
     * @param array $link Collection link definition
     * @param array $sortFields Additional fields required for client side sort
     *
     * @return array RelateApi arguments
     */
    protected function getLinkArguments(
        ServiceBase $api,
        array $args,
        SugarBean $bean,
        array $link,
        array $sortFields
    ) {
        $args = array_merge($args, array(
            'link_name' => $link['name'],
            'offset' => $args['offset'][$link['name']],
        ));

        $fields = $this->getFieldsFromArgs($api, $args, $bean);

        if (isset($link['field_map'])) {
            $fields = $this->mapFields($fields, $link['field_map']);
            if (isset($args['filter'])) {
                $args['filter'] = $this->mapFilter($args['filter'], $link['field_map']);
            }
            $args['order_by'] = $this->mapOrderBy($args['order_by'], $link['field_map']);
        }

        if (count($fields) > 0 && count($sortFields) > 0) {
            $fields = array_merge($fields, $sortFields);
        }

        $args['fields'] = $fields;
        $args['order_by'] = $this->formatOrderBy($args['order_by']);

        // view name is only applicable to primary module, and it doesn't make
        // sense to pass it to related module
        unset($args['view']);

        return $args;
    }

    /**
     * @param SugarBean $bean SugarBean instance that represents module metadata
     * @param string $collectionName Collection name
     *
     * @return array Link definition
     * @throws SugarApiExceptionError
     * @throws SugarApiExceptionNotFound
     */
    protected function getCollectionDefinition(SugarBean $bean, $collectionName)
    {
        $definition = $bean->getFieldDefinition($collectionName);
        if (!is_array($definition) || !isset($definition['type']) || $definition['type'] !== 'collection') {
            throw new SugarApiExceptionNotFound('Collection not found');
        }

        if (!isset($definition['links'])) {
            throw new SugarApiExceptionError(
                sprintf('Links are not defined for collection %s in module %s', $collectionName, $bean->module_name)
            );
        }

        $definition['links'] = $this->normalizeLinks($definition['links'], $collectionName, $bean->module_name);

        return $definition;
    }

    /**
     * Normalizes and validates link definitions in collection metadata
     *
     * @param array $links
     * @param $collectionName
     * @param $moduleName
     *
     * @return array Normalized definitions
     * @throws SugarApiExceptionError
     */
    protected function normalizeLinks($links, $collectionName, $moduleName)
    {
        if (!is_array($links)) {
            throw new SugarApiExceptionError(
                sprintf(
                    'Links must be array, %s is given for collection %s in module %s',
                    gettype($links),
                    $collectionName,
                    $moduleName
                )
            );
        }

        $normalized = array();
        foreach ($links as $i => $link) {
            if (is_string($link)) {
                $link = array('name' => $link);
            } elseif (is_array($link)) {
                if (!isset($link['name']) || !is_string($link['name'])) {
                    throw new SugarApiExceptionError(
                        sprintf(
                            'Link #%d name is not defined for collection %s in module %s',
                            $i,
                            $collectionName,
                            $moduleName
                        )
                    );
                }
            } else {
                throw new SugarApiExceptionError(
                    sprintf(
                        'Link definition must be string or array, %s is given for link #%d, collection %s in module %s',
                        gettype($link),
                        $i,
                        $collectionName,
                        $moduleName
                    )
                );
            }

            $normalized[] = $link;
        }

        return $normalized;
    }

    /**
     * Normalizes API arguments according to collection field definition
     *
     * @param array $args API arguments
     * @param array $definition Collection field definition
     *
     * @return array Normalized arguments
     */
    protected function normalizeArguments(array $args, array $definition)
    {
        $args['offset'] = $this->normalizeOffset($args, $definition['links']);
        if (!isset($args['max_num'])) {
            $args['max_num'] = $this->getDefaultLimit();
        }

        $args['order_by'] = $this->getOrderByFromArgs($args);

        if (!$args['order_by']) {
            if (isset($definition['order_by'])) {
                $args['order_by'] = $this->getOrderByFromArgs(array(
                    'order_by' => $definition['order_by'],
                ));
            } else {
                $args['order_by'] = $this->getDefaultOrderBy();
            }
        }

        // convert fields to a array for consistent behavior with SugarApi::formatBeans
        if (!empty($args['fields']) && !is_array($args['fields'])) {
            $args['fields'] = explode(',',$args['fields']);
        }

        return $args;
    }

    /**
     * Normalizes and validates offset API argument
     *
     * @param array $args API arguments
     * @param array $links Link definitions
     *
     * @return array Normalized value
     * @throws SugarApiExceptionInvalidParameter
     */
    protected function normalizeOffset(array $args, array $links)
    {
        if (isset($args['offset'])) {
            if (!is_array($args['offset'])) {
                throw new SugarApiExceptionInvalidParameter(
                    sprintf('Offset must be an array, %s given', gettype($args['offset']))
                );
            }

            $offset = $args['offset'];
        } else {
            $offset = array();
        }

        $keys = array();
        foreach ($links as $link) {
            $name = $link['name'];
            $keys[$name] = true;
            if (!isset($offset[$name])) {
                $offset[$name] = 0;
            } else {
                $offset[$name] = (int) $offset[$name];
                if ($offset[$name] < 0) {
                    $offset[$name] = -1;
                }
            }
        }

        // we remove all irrelevant offsets here, since later we'll be returning new offsets,
        // and we don't need irrelevant offsets to be returned
        $offset = array_intersect_key($offset, $keys);

        return $offset;
    }

    /**
     * Create one-dimensional array of records from multiple arrays
     *
     * @param array $data Multi-dimensional array of records retrieved from links
     * @param array $nextOffset Associative array of next offset for each link
     *
     * @return array Flattened array
     */
    protected function flattenData(array $data, &$nextOffset)
    {
        $flattened = array();
        foreach ($data as $linkName => $response) {
            foreach ($response['records'] as $record) {
                $record['_link'] = $linkName;
                $flattened[] = $record;
            }
            $nextOffset[$linkName] = $response['next_offset'];
        }

        return $flattened;
    }

    /**
     * Creates sorting specification from the given set of links and ORDER BY expression
     *
     * @param SugarBean $bean Primary bean
     * @param array $links Collection link definitions
     * @param array $orderBy ORDER BY expression
     *
     * @return array The sorting specification
     * @throws SugarApiExceptionError
     */
    protected function getSortSpec(SugarBean $bean, array $links, $orderBy)
    {
        $linkData = array();
        foreach ($links as $definition) {
            $linkName = $definition['name'];
            if (!$bean->load_relationship($linkName)) {
                throw new SugarApiExceptionError(
                    sprintf('Unable to load link %s on module %s', $linkName, $bean->module_name)
                );
            }

            $relatedModule = $bean->$linkName->getRelatedModuleName();
            $relatedBean = BeanFactory::getBean($relatedModule);
            if (isset($definition['field_map'])) {
                $fieldMap = $definition['field_map'];
            } else {
                $fieldMap = array();
            }
            $linkData[$linkName] = array($relatedBean, $fieldMap);
        }

        $spec = array();
        foreach ($orderBy as $alias => $direction) {
            $isNumeric = null;
            $map = array();
            foreach ($linkData as $linkName => $data) {
                /** @var SugarBean $relatedBean */
                list($relatedBean, $fieldMap) = $data;

                if (isset($fieldMap[$alias])) {
                    $field = $fieldMap[$alias];
                } else {
                    $field = $alias;
                }

                $fieldDef = $relatedBean->getFieldDefinition($field);
                if (!$fieldDef) {
                    // do not display alias since it may come from API arguments
                    throw new SugarApiExceptionError('Unable to load field definition');
                }

                $type = $relatedBean->db->getFieldType($fieldDef);
                if ($type) {
                    $isFieldNumeric = $relatedBean->db->isNumericType($type);
                } else {
                    // assume field is varchar in case type is not specified
                    $isFieldNumeric = false;
                }

                if (isset($fieldDef['sort_on'])) {
                    if ($isFieldNumeric && count($fieldDef['sort_on']) > 1) {
                        throw new SugarApiExceptionError(
                            'Cannot use "sort_on" more than one columns for numeric fields in collections'
                        );
                    }
                    $map[$linkName] = (array) $fieldDef['sort_on'];
                } else {
                    $map[$linkName] = array($field);
                }

                if ($isNumeric === null) {
                    $isNumeric = $isFieldNumeric;
                } elseif ($isNumeric != $isFieldNumeric) {
                    throw new SugarApiExceptionError(
                        sprintf('Alias %s points to both string and numeric fields', $field)
                    );
                }
            }

            $spec[] = array(
                'map' => $map,
                'is_numeric' => $isNumeric,
                'direction' => $direction,
            );
        }

        return $spec;
    }

    /**
     * Returns additional fields needed for client side sorting
     *
     * @param array $args API arguments
     * @param array $links Collection link definitions
     * @param array $sortSpec Sorting specification
     *
     * @return array Map of link names to their additional fields
     */
    protected function getAdditionalSortFields(array $args, $links, array $sortSpec)
    {
        $result = array();

        // make sure result contains entry for every link in order to make less checks in future
        foreach ($links as $link) {
            $result[$link['name']] = array();
        }

        if (!empty($args['fields'])) {
            foreach ($sortSpec as $column) {
                foreach ($column['map'] as $linkName => $sortFields) {
                    $addedFields = array_diff($sortFields, $args['fields']);
                    foreach ($addedFields as $addedField) {
                        $result[$linkName][$addedField] = true;
                    }
                }
            }
        }

        $result = array_map('array_keys', $result);

        return $result;
    }

    /**
     * Sorts collection data
     *
     * @param array $records Collection records
     * @param array $spec Sorting specification
     */
    protected function sortRecords(array &$records, array $spec)
    {
        $comparator = $this->getRecordComparator($spec);
        usort($records, $comparator);
    }

    /**
     * Builds column comparison function
     *
     * @param array $map Map of link name to field name for the given alias
     * @param boolean $isNumeric Whether the column is numeric
     * @param boolean $direction Sorting direction
     *
     * @return callable
     */
    protected function getColumnComparator($map, $isNumeric, $direction)
    {
        $comparator = $isNumeric ? function ($a, $b) {
            return $a - $b;
        } : $this->collator;

        $getValue = function ($row, $fields) {
            // do not concat values in case there's only one field in order to preserve value type
            if (count($fields) == 1) {
                return $row[$fields[0]];
            } else {
                return implode(' ', array_map(function ($field) use ($row) {
                    return $row[$field];
                }, $fields));
            }
        };

        $factor = $direction ? 1 : -1;

        return function ($a, $b) use ($comparator, $map, $getValue, $factor) {
            return $comparator(
                $getValue($a, $map[$a['_link']]),
                $getValue($b, $map[$b['_link']])
            ) * $factor;
        };
    }

    /**
     * Builds record comparison function according to specification
     *
     * @param array $spec
     *
     * @return callable
     */
    protected function getRecordComparator(array $spec)
    {
        $comparators = array();
        foreach ($spec as $alias => $properties) {
            $comparators[] = $this->getColumnComparator(
                $properties['map'],
                $properties['is_numeric'],
                $properties['direction']
            );
        }

        return function ($a, $b) use ($comparators) {
            foreach ($comparators as $comparator) {
                $result = $comparator($a, $b);
                if ($result != 0) {
                    return $result;
                }
            }

            return 0;
        };
    }

    /**
     * Generates the value of new offset based on initial offset and the set of records being returned
     *
     * @param array $offset Initial value of offset
     * @param array $records Returned records
     * @param array $nextOffset Collection of offsets returned by Relate API
     * @param array $remainder Not returned records
     *
     * @return array New value of offset
     */
    protected function getNextOffset(array $offset, array $records, array $nextOffset, array $remainder)
    {
        $returned = $truncated = array();

        foreach ($nextOffset as $linkName => $_) {
            $returned[$linkName] = 0;
        }

        foreach ($records as $record) {
            $returned[$record['_link']]++;
        }

        foreach ($remainder as $record) {
            $truncated[$record['_link']] = true;
        }

        foreach ($offset as $linkName => $value) {
            if (!isset($nextOffset[$linkName])) {
                $nextOffset[$linkName] = $value;
            } elseif (isset($truncated[$linkName])) {
                $nextOffset[$linkName] = $offset[$linkName] + $returned[$linkName];
            }
        }

        return $nextOffset;
    }

    /**
     * Maps field names
     *
     * @param array $fields
     * @param array $fieldMap
     *
     * @return array
     */
    protected function mapFields(array $fields, array $fieldMap)
    {
        return $this->mapArrayValues($fields, $fieldMap);
    }

    /**
     * Map filter definition using field map
     *
     * @param array $filter
     * @param array $fieldMap
     *
     * @return array
     */
    protected function mapFilter(array $filter, array $fieldMap)
    {
        foreach ($filter as $key => $value) {
            if (is_array($value)) {
                $filter[$key] = $this->mapFilter($filter[$key], $fieldMap);
            }
        }

        return $this->mapArrayKeys($filter, $fieldMap);
    }

    /**
     * Maps internal representation of ORDER BY definition
     *
     * @param array $orderBy
     * @param array $fieldMap
     *
     * @return array
     */
    protected function mapOrderBy(array $orderBy, array $fieldMap)
    {
        return $this->mapArrayKeys($orderBy, $fieldMap);
    }

    /**
     * Converts array by replacing aliased keys with real fields
     *
     * @param array $array
     * @param array $fieldMap
     *
     * @return array
     * @throws SugarApiExceptionInvalidParameter
     */
    protected function mapArrayKeys(array $array, array $fieldMap)
    {
        $mapped = array();
        foreach ($array as $alias => $value) {
            if (isset($fieldMap[$alias])) {
                $field = $fieldMap[$alias];
            } else {
                $field = $alias;
            }

            if (isset($mapped[$field])) {
                throw new SugarApiExceptionInvalidParameter(
                    'More than one alias pointing to the same field is used in expression'
                );
            }

            $mapped[$field] = $value;
        }

        return $mapped;
    }

    /**
     * Converts array by replacing aliased values with real fields
     *
     * @param array $array
     * @param array $fieldMap
     *
     * @return array
     */
    protected function mapArrayValues(array $array, array $fieldMap)
    {
        return array_map(function ($value) use ($fieldMap) {
            if (isset($fieldMap[$value])) {
                return $fieldMap[$value];
            }
            return  $value;
        }, $array);
    }

    /**
     * Formats ORDER BY from internal representation
     *
     * @param array $orderBy
     *
     * @return string
     */
    protected function formatOrderBy(array $orderBy)
    {
        $formatted = array();
        foreach ($orderBy as $field => $direction) {
            $column = $field;
            if (!$direction) {
                $column .= ':desc';
            }
            $formatted[] = $column;
        }

        return implode(',', $formatted);
    }

    /**
     * Returns default ORDER BY in internal representation
     *
     * @return array
     */
    protected function getDefaultOrderBy()
    {
        $orderBy = array();
        foreach ($this->getRelateApi()->getDefaultOrderBy() as $column) {
            $field = array_shift($column);
            $direction = array_shift($column);
            $orderBy[$field] = strtolower($direction) != 'desc';
        }

        return $orderBy;
    }

    /**
     * Returns default records limit
     *
     * @return int
     */
    protected function getDefaultLimit()
    {
        return $this->getRelateApi()->getDefaultLimit();
    }

    /**
     * Lazily loads Relate API
     *
     * @return RelateApi
     */
    protected function getRelateApi()
    {
        if (!$this->relateApi) {
            $this->relateApi = new RelateApi();
        }

        return $this->relateApi;
    }

    /**
     * Clean up the data from unwanted fields that were not requested. For the purpose of sorting
     * we may have requested additional fields from the database. However these need not be
     * displayed to the user.
     *
     * @param array $records Resulting set of records
     * @param array $sortFields Additionally requested sort fields
     * @return array Modified Data Array is returned back
     */
    protected function cleanData(array $records, array $sortFields)
    {
        $fieldsToRemove = array_map('array_flip', $sortFields);
        $records = array_map(function ($record) use ($fieldsToRemove) {
            return array_diff_key($record, $fieldsToRemove[$record['_link']]);
        }, $records);

        return $records;
    }
}
