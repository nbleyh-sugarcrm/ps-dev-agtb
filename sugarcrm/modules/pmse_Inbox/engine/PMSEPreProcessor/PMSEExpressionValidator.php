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


/**
 * Description of PMSERecordValidator
 *
 */
class PMSEExpressionValidator extends PMSEBaseValidator implements PMSEValidate
{
    /**
     *
     * @param PMSERequest $request
     * @return \PMSERequest
     */
    public function validateRequest(PMSERequest $request)
    {
        $this->logger->info("Validate Request " . get_class($this));
        $this->logger->debug(array("Request data:", $request));

        $flowData = $request->getFlowData();
        $bean = $request->getBean();
        if ($flowData['evn_id'] != 'TERMINATE') {
            $paramsRelated = $this->validateParamsRelated($bean, $flowData, $request);
            if ($request->isValid()) {
                $this->validateExpression($bean, $flowData, $request, $paramsRelated);
            }
        }
        return $request;
    }

    /**
     *
     * @param type $bean
     * @param type $flowData
     * @param type $request
     * @param type $paramsRelated
     * @return type
     */
    public function validateExpression($bean, $flowData, $request, $paramsRelated = array())
    {
        $conditionResult = $this->evaluator->evaluateExpression(trim($flowData['evn_criteria']), $bean, $paramsRelated);
        if ($flowData['evn_criteria'] == '' || $flowData['evn_criteria'] == '[]' || $conditionResult) {
            $request->validate();
        } else {
            $request->invalidate();
        }

        $condition = $this->evaluator->condition();
        $this->logger->debug("Eval: $condition returned " . ($request->isValid()));
        return $request;
    }

    /**
     *
     * @param type $bean
     * @param type $flowData
     * @param type $request
     * @return array
     */
    public function validateParamsRelated($bean, $flowData, $request)
    {
        $paramsRelated = array();
        if ($request->getExternalAction() == 'EVALUATE_RELATED_MODULE') {
            if ($this->hasValidRelationship($bean, $flowData)) {
                $paramsRelated = array(
                    'replace_fields' => array(
                        $flowData['rel_element_relationship'] => $flowData['rel_element_module']
                    )
                );
            } else {
                $request->invalidate();
            }
        }

        if ($request->getExternalAction() == 'EVALUATE_MAIN_MODULE') {
            if (
                $bean->module_name != $flowData['cas_sugar_module']
                || $bean->id != $flowData['cas_sugar_object_id']
            ) {
                $request->invalidate();
            }
        }

        if ($request->getExternalAction() == 'NEW') {
            if (!PMSEEngineUtils::isTargetModule($flowData, $bean)
            ) {
                $paramsRelated = array(
                    'replace_fields' => array(
                        $flowData['rel_element_relationship'] => $flowData['rel_element_module']
                    )
                );
            }
        }

        $this->logger->debug("Parameters related returned :" . print_r($paramsRelated, true));
        return $paramsRelated;
    }


    /**
     * Return true if bean specified by data in flowdata and bean specified by bean have a link defined
     * @param $bean
     * @param $flowData
     * @return bool
     */
    public function hasValidRelationship($bean, $flowData)
    {
        // We don't need the entire retrieved bean for this operation...
        $seedBean = BeanFactory::getBean($flowData['cas_sugar_module']);

        // We just need the ID to be able to check relationships
        $seedBean->id = $flowData['cas_sugar_object_id'];

        // Get the relationship field and see if we have it
        $relField = $flowData['rel_element_relationship'];
        $hasRel = $seedBean->load_relationship($relField);

        // If there is a seed bean and there is a valid relationship...
        if ($seedBean->id && $hasRel) {
            // Get the row for this relationship by query instead through beans
            // which is much more expensive
            $relWhere = array(
                'where' => array(
                    'lhs_field' => 'id',
                    'operator' => '=',
                    'rhs_value' => $bean->id,
                ),
            );

            $query = $seedBean->$relField->getRelationshipObject()->getQuery($seedBean->$relField, $relWhere);

            /** @var MysqliManager $db */
            $db = $seedBean->db;
            $row = $db->fetchOne($query);

            // And verify that the relationship is actually valid record to record
            return $row && $row['id'] == $bean->id;
        }

        // Otherwise just return whether there is a relationship
        return $hasRel;
    }
}
