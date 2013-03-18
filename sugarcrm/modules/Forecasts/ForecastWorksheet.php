<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
/********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

class ForecastWorksheet extends SugarBean
{

    public $id;
    public $worksheet_id;
    public $currency_id;
    public $base_rate;
    public $args;
    public $name;
    public $commit_stage;
    public $probability;
    public $best_case;
    public $likely_case;
    public $worst_case;
    public $sales_stage;
    public $product_id;
    public $assigned_user_id;
    public $timeperiod_id;
    public $draft = 0; // default to 0, it will be set to 1 by the args that get passed in;
    public $parent_type;
    public $parent_id;
    public $object_name = 'ForecastWorksheet';
    public $module_name = 'ForecastWorksheets';
    public $module_dir = 'Forecasts';
    public $table_name = 'forecast_worksheets';
    public $disable_custom_fields = true;

    /**
     * Update the real table with the values when a save happens on the front end
     *
     * @param bool $check_notify        Should we send the notifications
     */
    public function saveWorksheet($check_notify = false)
    {
        //Update the Opportunities bean -- should update the product line item as well through SaveOverload.php
        /* @var $bean Opportunity|Product */
        $bean = BeanFactory::getBean($this->parent_type, $this->parent_id);
        $bean->probability = $this->probability;
        $bean->best_case = $this->best_case;
        if ($bean instanceof Product) {
            $bean->likely_case = $this->likely_case;
        } else {
            $bean->amount = $this->likely_case;
        }
        $bean->sales_stage = $this->sales_stage;
        $bean->commit_stage = $this->commit_stage;
        $bean->worst_case = $this->worst_case;
        $bean->commit_stage = $this->commit_stage;
        $bean->save($check_notify);
    }

    /**
     * Sets Worksheet args so that we save the supporting tables.
     * @param array $args Arguments passed to save method through PUT
     */
    public function setWorksheetArgs($args)
    {
        // save the args variable
        $this->args = $args;

        // loop though the args and assign them to the corresponding key on the object
        foreach ($args as $arg_key => $arg) {
            $this->$arg_key = $arg;
        }
    }

    /**
     * Save an Opportunity as a worksheet
     *
     * @param Opportunity $opp      The Opportunity that we want to save a snapshot of
     * @param bool $isCommit        Is the Opportunity being committed
     */
    public function saveRelatedOpportunity(Opportunity $opp, $isCommit = false)
    {
        $this->retrieve_by_string_fields(
            array(
                'parent_type' => 'Opportunities',
                'parent_id' => $opp->id,
                'draft' => ($isCommit === false) ? 1 : 0,
                'deleted' => 0,
            )
        );

        $fields = array(
            'name',
            'account_id',
            array('likely_case' => 'amount'),
            'best_case',
            'base_rate',
            'worst_case',
            'currency_id',
            'date_closed',
            'date_closed_timestamp',
            'sales_stage',
            'probability',
            'commit_stage',
            'assigned_user_id',
            'created_by',
            'date_entered',
            'deleted',
            'team_id',
            'team_set_id'
        );

        $this->copyValues($fields, $opp);

        // set the parent types
        $this->parent_type = 'Opportunities';
        $this->parent_id = $opp->id;
        $this->draft = ($isCommit === false) ? 1 : 0;

        $this->save(false);

        //BEGIN SUGARCRM flav=pro && flav!=ent ONLY
        $this->saveOpportunityProducts($opp, $isCommit);
        //END SUGARCRM flav=pro && flav!=ent ONLY
    }

    /**
     * Commit All Related Products from an Opportunity
     *
     * @param Opportunity $opp
     * @param $isCommit
     */
    public function saveOpportunityProducts(Opportunity $opp, $isCommit = false)
    {
        // remove the relationship if it exists as it could cause errors with the cached beans in the BeanFactory
        if (isset($opp->products)) {
            unset($opp->products);
        }
        // now save all related products to the opportunity
        // commit every product associated with the Opportunity
        $products = $opp->get_linked_beans('products', 'Products');
        /* @var $product Product */
        foreach ($products as $product) {
            /* @var $product_wkst ForecastWorksheet */
            $product_wkst = BeanFactory::getBean('ForecastWorksheets');
            $product_wkst->saveRelatedProduct($product, $isCommit);
            unset($product_wkst);   // clear the cache
        }
    }

    /**
     * Save a snapshot of a Product to the ForecastWorksheet Table
     *
     * @param Product $product          The Product to commit
     * @param bool $isCommit            Are we committing a product for the forecast
     */
    public function saveRelatedProduct(Product $product, $isCommit = false)
    {
        $this->retrieve_by_string_fields(
            array(
                'parent_type' => 'Products',
                'parent_id' => $product->id,
                'draft' => ($isCommit === false) ? 1 : 0,
                'deleted' => 0,
            )
        );

        $fields = array(
            'name',
            'account_id',
            'likely_case',
            'best_case',
            'base_rate',
            'worst_case',
            'currency_id',
            'date_closed',
            'date_closed_timestamp',
            'probability',
            'commit_stage',
            'sales_stage',
            'assigned_user_id',
            'created_by',
            'date_entered',
            'deleted',
            'team_id',
            'team_set_id'
        );

        $this->copyValues($fields, $product);

        // set the parent types
        $this->parent_type = 'Products';
        $this->parent_id = $product->id;
        $this->draft = ($isCommit === false) ? 1 : 0;

        $this->save(false);
    }

    /**
     * Copy the fields from the $seed bean to the worksheet object
     *
     * @param array $fields
     * @param SugarBean $seed
     */
    protected function copyValues($fields, SugarBean $seed)
    {
        foreach ($fields as $field) {
            $key = $field;
            if (is_array($field)) {
                // if we have an array it should be a key value pair, where the key is the destination value and the value,
                // is the seed value
                $key = array_shift(array_keys($field));
                $field = array_shift($field);
            }
            // make sure the field is set, as not to cause a notice since a field might get unset() from the $seed class
            if(isset($seed->$field)) {
                $this->$key = $seed->$field;
            }
        }
    }

    public static function reassignForecast($fromUserId, $toUserId)
    {
        global $current_user;

        $db = DBManagerFactory::getInstance();

        // reassign Opportunities
        $_object = BeanFactory::getBean('Opportunities');
        $_query = "update {$_object->table_name} set " .
            "assigned_user_id = '{$toUserId}', " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.assigned_user_id = '{$fromUserId}'";
        $res = $db->query($_query, true);
        $affected_rows = $db->getAffectedRowCount($res);

        // Products
        // reassign only products that have related opportunity - products created from opportunity::save()
        // other products will be reassigned if module Product is selected by user
        $_object = BeanFactory::getBean('Products');
        $_query = "update {$_object->table_name} set " .
            "assigned_user_id = '{$toUserId}', " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.assigned_user_id = '{$fromUserId}' and {$_object->table_name}.opportunity_id IS NOT NULL ";
        $db->query($_query, true);

        // delete Forecasts
        $_object = BeanFactory::getBean('Forecasts');
        $_query = "update {$_object->table_name} set " .
            "deleted = 1, " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.user_id = '{$fromUserId}'";
        $db->query($_query, true);

        // delete Expected Opportunities
        $_object = BeanFactory::getBean('ForecastSchedule');
        $_query = "update {$_object->table_name} set " .
            "deleted = 1, " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.user_id = '{$fromUserId}'";
        $db->query($_query, true);

        // delete Quotas
        $_object = BeanFactory::getBean('Quotas');
        $_query = "update {$_object->table_name} set " .
            "deleted = 1, " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.user_id = '{$fromUserId}'";
        $db->query($_query, true);

        // clear reports_to for inactive users
        $objFromUser = BeanFactory::getBean('Users');
        $objFromUser->retrieve($fromUserId);
        $fromUserReportsTo = !empty($objFromUser->reports_to_id) ? $objFromUser->reports_to_id : '';
        $objFromUser->reports_to_id = '';
        $objFromUser->save();

        if (User::isManager($fromUserId)) {
            // setup report_to for user
            $objToUserId = BeanFactory::getBean('Users');
            $objToUserId->retrieve($toUserId);
            $objToUserId->reports_to_id = $fromUserReportsTo;
            $objToUserId->save();

            // reassign users (reportees)
            $_object = BeanFactory::getBean('Users');
            $_query = "update {$_object->table_name} set " .
                "reports_to_id = '{$toUserId}', " .
                "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
                "modified_user_id = '{$current_user->id}' " .
                "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.reports_to_id = '{$fromUserId}' " .
                "and {$_object->table_name}.id != '{$toUserId}'";
            $db->query($_query, true);
        }

        // Worksheets
        // reassign worksheets for products (opportunities)
        $_object = BeanFactory::getBean('Worksheet');
        $_query = "update {$_object->table_name} set " .
            "user_id = '{$toUserId}', " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.user_id = '{$fromUserId}' ";
        $db->query($_query, true);

        // delete worksheet where related_id is user id - rollups
        $_object = BeanFactory::getBean('Worksheet');
        $_query = "update {$_object->table_name} set " .
            "deleted = 1, " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 " .
            "and {$_object->table_name}.forecast_type = 'Rollup' and {$_object->table_name}.related_forecast_type = 'Direct' " .
            "and {$_object->table_name}.related_id = '{$fromUserId}' ";
        $db->query($_query, true);

        // ForecastWorksheets
        // reassign entries in forecast_worksheets
        $_object = BeanFactory::getBean('ForecastWorksheets');
        $_query = "update {$_object->table_name} set " .
            "assigned_user_id = '{$toUserId}', " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.assigned_user_id = '{$fromUserId}' ";
        $db->query($_query, true);

        // ForecastManagerWorksheets
        // reassign entries in forecast_manager_worksheets
        $_object = BeanFactory::getBean('ForecastManagerWorksheets');
        $_query = "update {$_object->table_name} set " .
            "assigned_user_id = '{$toUserId}', " .
            "user_id = '{$toUserId}', " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.assigned_user_id = '{$fromUserId}' ";
        $db->query($_query, true);

        //todo: forecast_tree
        return $affected_rows;
    }

    /**
     * This method emulates the Forecast Rep Worksheet calculateTotals method.
     *
     * @param $timeperiod_id
     * @param $user_id
     * @param $forecast_by
     * @return bool
     */
    public function worksheetTotals($timeperiod_id, $user_id, $forecast_by)
    {
        /* @var $tp TimePeriod */
        $tp = BeanFactory::getBean('TimePeriods', $timeperiod_id);
        if (empty($tp->id)) {
            // timeperiod not found
            return false;
        }

        $forecast_by = ucfirst(strtolower($forecast_by));

        // setup the return array
        $return = array(
            'amount' => '0',
            'best_case' => '0',
            'worst_case' => '0',
            'overall_amount' => '0',
            'overall_best' => '0',
            'overall_worst' => '0',
            'timeperiod_id' => $tp->id,
            'lost_count' => '0',
            'lost_amount' => '0',
            'won_count' => '0',
            'won_amount' => '0',
            'included_opp_count' => 0,
            'total_opp_count' => 0,
            'includedClosedCount' => 0,
            'includedClosedAmount' => '0',
            'pipeline_amount' => '0',
            'pipeline_opp_count' => 0,
        );

        $sq = new SugarQuery();
        $sq->select(array('*'));
        $sq->from(BeanFactory::getBean($this->module_name))->where()
            ->equals('assigned_user_id', $user_id)
            ->equals('parent_type', $forecast_by)
            ->equals('deleted', 0)
            ->equals('draft', 1)
            ->queryAnd()
                ->gte('date_closed_timestamp', $tp->start_date_timestamp)
                ->lte('date_closed_timestamp', $tp->end_date_timestamp);
        $results = $sq->execute();

        foreach ($results as $row) {
            $worst_base = SugarCurrency::convertWithRate($row['worst_case'], $row['base_rate']);
            $amount_base = SugarCurrency::convertWithRate($row['likely_case'], $row['base_rate']);
            $best_base = SugarCurrency::convertWithRate($row['best_case'], $row['base_rate']);

            $closed_won = false;
            if ($row['sales_stage'] == Opportunity::STAGE_CLOSED_WON) {
                $return['won_amount'] = SugarMath::init($return['won_amount'], 6)->add($amount_base)->result();
                $return['won_count']++;
                $closed_won = true;
            } elseif ($row['sales_stage'] == Opportunity::STAGE_CLOSED_LOST) {
                $return['lost_amount'] = SugarMath::init($return['lost_amount'], 6)->add($amount_base)->result();
                $return['lost_count']++;
                $closed_won = true;
            }

            if ($row['commit_stage'] == "include") {
                $return['amount'] = SugarMath::init($return['amount'], 6)->add($amount_base)->result();
                $return['best_case'] = SugarMath::init($return['best_case'], 6)->add($best_base)->result();
                $return['worst_case'] = SugarMath::init($return['worst_case'], 6)->add($worst_base)->result();
                $return['included_opp_count']++;
                if ($closed_won) {
                    $return['includedClosedCount']++;
                    $return['includedClosedAmount'] = SugarMath::init($return['includedClosedAmount'], 6)
                        ->add($amount_base)->result();
                }
            }

            $return['total_opp_count']++;
            $return['overall_amount'] = SugarMath::init($return['overall_amount'], 6)->add($amount_base)->result();
            $return['overall_best'] = SugarMath::init($return['overall_best'], 6)->add($best_base)->result();
            $return['overall_worst'] = SugarMath::init($return['overall_worst'], 6)->add($worst_base)->result();
        }

        // send back the totals
        return $return;

    }
}
