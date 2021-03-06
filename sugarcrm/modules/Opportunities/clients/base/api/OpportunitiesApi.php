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
class OpportunitiesApi extends ModuleApi
{
    public function registerApiRest()
    {
        return array(
            'update' => array(
                'reqType' => 'PUT',
                'path' => array('Opportunities', '?'),
                'pathVars' => array('module', 'record'),
                'method' => 'updateRecord',
                'shortHelp' => 'This method updates a record of the specified type',
                'longHelp' => 'include/api/help/module_record_put_help.html',
            ),
        );
    }

    /**
     * Updates the opportunities record
     *
     * {@inheritdoc}
     */
    public function updateRecord(ServiceBase $api, array $args)
    {
        $this->requireArgs($args,array('module','record'));

        parent::updateRecord($api, $args);

        // BEGIN SUGARCRM flav = ent ONLY
        $settings = Opportunity::getSettings();
        if ($settings['opps_view_by'] === 'RevenueLineItems') {
            $data = array();
            $bean = $this->loadBean($api, $args, 'save');

            foreach (['commit_stage', 'probability',] as $prop) {
                if (!empty($args[$prop]) && $bean->{$prop} !== $args[$prop]) {
                    $data[$prop] = $args[$prop];
                }
            }

            if (!empty($data)) {
                $this->updateRevenueLineItems($bean, $data);
            }
        }
        // END SUGARCRM flav = ent ONLY

        return $this->getLoadedAndFormattedBean($api, $args);
    }

    // BEGIN SUGARCRM flav = ent ONLY
    /**
     * Rolls up data to all RLIs that are not won/lost.
     *
     * @param $bean SugarBean The Opportunity Bean
     * @param array $data Data being upgraded on the Opportunity
     */
    protected function updateRevenueLineItems($bean, $data)
    {
        Activity::disable();

        if ($bean && $bean->load_relationship('revenuelineitems')) {
            $rlis = $bean->revenuelineitems->getBeans();
            foreach ($rlis as $rli) {
                $hasChanged = false;
                if (in_array($rli->sales_stage, $bean->getClosedStages())) {
                    continue;
                }
                foreach ($data as $fieldName => $fieldValue) {
                    if ($rli->{$fieldName} !== $fieldValue) {
                        $hasChanged = true;
                        $rli->{$fieldName} = $fieldValue;
                    }
                }
                if ($hasChanged) {
                    $rli->save();
                }
            }
        }

        Activity::restoreToPreviousState();
    }
    // END SUGARCRM flav = ent ONLY
}
