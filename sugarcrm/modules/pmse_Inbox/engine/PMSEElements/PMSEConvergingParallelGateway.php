<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

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


require_once 'PMSEConvergingGateway.php';

class PMSEConvergingParallelGateway extends PMSEConvergingGateway
{
    public function run($flowData, $bean = null, $externalAction = '', $arguments = array())
    {
        $routeAction = 'WAIT';
        $flowAction = 'NONE';
        $filters = array();
        $previousFlows = $this->retrievePreviousFlows('PASSED', $flowData['bpmn_id'], $flowData['cas_id']);
        $totalFlows = $this->retrievePreviousFlows('ALL', $flowData['bpmn_id']);
        if (sizeof($previousFlows) === sizeof($totalFlows)) {
            $routeAction = 'ROUTE';
            $flowAction = 'CREATE';
        }
        return $this->prepareResponse($flowData, $routeAction, $flowAction, $filters);
    }

}
