<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

require_once('modules/Forecasts/data/IChartAndWorksheet.php');

class Individual implements IChartAndWorksheet {

    private $def = array (
        'opportunities' => array('Opportunities', 'ForecastSeedReport1', '{"display_columns":[{"name":"forecast","label":"Include in Forecast","table_key":"self"},{"name":"name","label":"Opportunity Name","table_key":"self"},{"name":"date_closed","label":"Expected Close Date","table_key":"self"},{"name":"sales_stage","label":"Sales Stage","table_key":"self"},{"name":"probability","label":"Probability (%)","table_key":"self"},{"name":"amount","label":"Opportunity Amount","table_key":"self"},{"name":"best_case_worksheet","label":"Best Case (adjusted)","table_key":"self"},{"name":"likely_case_worksheet","label":"Likely Case (adjusted)","table_key":"self"}],"module":"Opportunities","group_defs":[{"name":"date_closed","label":"Month: Expected Close Date","column_function":"month","qualifier":"month","table_key":"self","type":"date"},{"name":"sales_stage","label":"Sales Stage","table_key":"self","type":"enum"}],"summary_columns":[{"name":"date_closed","label":"Month: Expected Close Date","column_function":"month","qualifier":"month","table_key":"self"},{"name":"amount","label":"SUM: Opportunity Amount","field_type":"currency","group_function":"sum","table_key":"self"}],"report_name":"abc123","chart_type":"vBarF","do_round":0,"chart_description":"","numerical_chart_column":"self:likely_case_worksheet:sum","numerical_chart_column_type":"","assigned_user_id":"seed_chris_id","report_type":"summary","full_table_list":{"self":{"value":"Opportunities","module":"Opportunities","label":"Opportunities"}},"filters_def":[]}', 'detailed_summary', 'vBarF')
    );

    public $user_id;
    public $timeperiod_id;

    public function getFilters($args=array())
    {
        global $current_user;
        $user_id = (isset($args['user_id']) && !empty($args['user_id'])) ? $args['user_id'] : $current_user->id;
        $timeperiod = (isset($args['timeperiod_id']) && !empty($args['timeperiod_id'])) ? $args['timeperiod_id'] : TimePeriod::getCurrentId();

        $filters = array();
        $filters['assigned_user_link'] = array('id' => $user_id);
        $filters['timeperiod_id'] = array('$is' => $timeperiod);

        if(isset($args['category']) && $args['category'] == "Committed")
        {
            $filters['forecast'] = array('$is' => 1);
        }
        // also we can define other filters such as 'probability', 'sales_stage' etc.
        return $filters;
    }

    public function getGridData($report)
    {
        $report->run_query();
        
        $opps = array();

        while(($row=$GLOBALS['db']->fetchByAssoc($report->result))!=null)
        {
            $row['id'] = $row['primaryid'];
            $row['forecast'] = ($row['opportunities_forecast'] == 1) ? true : false;
            $row['name'] = $row['opportunities_name'];
            $row['amount'] = $row['opportunities_amount'];
            $row['date_closed'] = $row['opportunities_date_closed'];
            $row['probability'] = $row['opportunities_probability'];
            $row['sales_stage'] = $row['opportunities_sales_stage'];
            $row['best_case_worksheet'] = $row['OPPORTUNITIES_BEST_CAS81CC16'];
            $row['likely_case_worksheet'] = $row['OPPORTUNITIES_LIKELY_C7E6E04'];

            //Should we unset the data we don't need here so as to limit data sent back?

            $opps[] = $row;
        }

        return $opps;
    }


    public function getChartDefinition($id='')
    {
        return $this->getWorksheetDefinition($id);
    }

    public function getWorksheetDefinition($id='')
    {
        return isset($this->def[$id]) ? $this->def[$id] : array();
    }
    
}
