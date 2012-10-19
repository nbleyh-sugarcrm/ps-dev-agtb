<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
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
/*********************************************************************************
 * $Id: TimePeriod.php 54636 2010-02-19 02:54:46Z jmertic $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/SugarQueue/SugarJobQueue.php');

// User is used to store customer information.
class TimePeriod extends SugarBean {

    const ANNUAL_TYPE = 'Annual';
    const QUARTER_TYPE = 'Quarter';
    const MONTH_TYPE = 'Month';

	//time period stored fields.
	var $id;
	var $name;
	var $parent_id;
	var $start_date;
	var $end_date;
    var $start_date_timestamp;
   	var $end_date_timestamp;
	var $created_by;
	var $date_entered;
	var $date_modified;
	var $deleted;
	var $fiscal_year;
	var $is_fiscal_year = 0;
    var $is_fiscal;
	//end time period stored fields.
	var $table_name = "timeperiods";
	var $fiscal_year_checked;
	var $module_dir = 'TimePeriods';
    var $time_period_type = 'Annual';
    var $leaf_period_type = 'Quarter';
    var $leaf_periods = 4;
    var $leaf_name_template;
    var $name_template;
	var $object_name = "TimePeriod";
	var $user_preferences;
    var $date_modifier;
    var $is_leaf = false;
	var $encodeFields = Array("name");

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('reports_to_name');

	
	var $new_schema = true;

	public function __construct() {
		parent::__construct();
		$this->disable_row_level_security =true;
	}

	public function save($check_notify = false){
		//if (empty($this->id)) $this->parent_id = null;

        $timedate = TimeDate::getInstance();

        //TODO: change to check globals flag instead for cleaner if statement
        //override the unix time stamp setting here for setting start date timestamp by going with 00:00:00 for the time
        $date_start_datetime = $this->start_date;
        if ($timedate->check_matching_format($this->start_date, TimeDate::DB_DATE_FORMAT)) {
            $date_start_datetime = $timedate->fromDbDate($this->start_date);
        } else if ($timedate->check_matching_format($this->start_date, $timedate->get_user_date_format())) {
            $date_start_datetime = $timedate->fromUserDate($this->start_date, true);
        }

        $date_start_datetime->setTime(0,0,0);
        $this->start_date_timestamp = $date_start_datetime->getTimestamp();

        //override the unix time stamp setting here for setting end date timestamp by going with 23:59:59 for the time to get the max time of the day
        $date_close_datetime = $this->end_date;
        if ($timedate->check_matching_format($this->end_date, TimeDate::DB_DATE_FORMAT)) {
            $date_close_datetime = $timedate->fromDbDate($this->end_date);
        } else if ($timedate->check_matching_format($this->end_date, $timedate->get_user_date_format())) {
            $date_close_datetime = $timedate->fromUserDate($this->end_date, true);
        }

        $date_close_datetime->setTime(23,59,59);
        $this->end_date_timestamp = $date_close_datetime->getTimestamp();
		return parent::save($check_notify);
	}



	public function get_summary_text()
	{
		return "$this->name";
	}

    /**
     * custom override of retrieve function to disable the date formatting and reset it again after the bean has been retrieved.
     *
     * @param string $id
     * @param bool $encode
     * @param bool $deleted
     * @return null|SugarBean
     */
    public function retrieve($id, $encode=false, $deleted=true){
        global $disable_date_format;
        $previous_disable_date_format = $disable_date_format;
        $disable_date_format = 1;
   		$ret = parent::retrieve($id, $encode, $deleted);
        $disable_date_format = $previous_disable_date_format;
   		return $ret;
   	}

    public function is_authenticated()
	{
		return $this->authenticated;
	}

    public function fill_in_additional_list_fields() {
		$this->fill_in_additional_detail_fields();
	}

    public function fill_in_additional_detail_fields()
	{
		if (isset($this->parent_id) && !empty($this->parent_id)) {
		
		  $query ="SELECT name from timeperiods where id = '$this->parent_id' and deleted = 0";
		  $result =$this->db->query($query, true, "Error filling in additional detail fields") ;
		  $row = $this->db->fetchByAssoc($result);
		  $GLOBALS['log']->debug("additional detail query results: $row");

		  
		  if($row != null) {
			 $this->fiscal_year = $row['name'];
		  }
		}
	}


    public function get_list_view_data(){

		$timeperiod_fields = $this->get_list_view_array();		
		$timeperiod_fields['FISCAL_YEAR'] = $this->fiscal_year;
	
		if ($this->is_fiscal_year == 1)
			$timeperiod_fields['FISCAL_YEAR_CHECKED'] = "checked";
		
		return $timeperiod_fields;
	}

    public function list_view_parse_additional_sections(&$list_form, $xTemplateSection){
		return $list_form;
	}

    public function create_export_query($order_by, $where)
	{
		$query = "SELECT
				timeperiods.*";
		$query .= " FROM timeperiods ";

		$where_auto = " timeperiods.deleted = 0";

		if($where != "")
			$query .= " WHERE $where AND " . $where_auto;
		else
			$query .= " WHERE " . $where_auto;

		if($order_by != "")
			$query .= " ORDER BY $order_by";
		else
			$query .= " ORDER BY timeperiods.name";

		return $query;
	}

    /**
     * creates a new timeperiod to start to use
     *
     * @return mixed
     */
    public function createNextTimePeriod() {
        $timedate = TimeDate::getInstance();
        $nextStartDate = $timedate->fromDbDate($this->end_date);
        $nextStartDate = $nextStartDate->modify('+1 day');
        $nextPeriod = BeanFactory::newBean($this->time_period_type."TimePeriods");
        $nextPeriod->is_leaf = $this->is_leaf;
        $nextPeriod->is_fiscal = $this->is_fiscal;
        $nextPeriod->name = "";
        $nextPeriod->setStartDate($timedate->asDbDate($nextStartDate));
        $nextPeriod->save();

        return $nextPeriod;
    }

    /**
     * creates a new timeperiod to keep past records
     *
     * @return mixed
     */
    public function createPreviousTimePeriod() {
        $timedate = TimeDate::getInstance();
        $previousStartDate = $timedate->fromDbDate($this->start_date);
        $previousStartDate = $previousStartDate->modify('-'.$this->date_modifier);
        $previousPeriod = BeanFactory::newBean($this->time_period_type."TimePeriods");
        $previousPeriod->name = "";
        $previousPeriod->is_leaf = $this->is_leaf;
        $previousPeriod->is_fiscal = $this->is_fiscal;
        $previousPeriod->setStartDate($timedate->asDbDate($previousStartDate));
        $previousPeriod->save();

        return $previousPeriod;
    }

    /**
     * sets the start date, based on a db formatted date string passed in.  If null is passed in, now is used.
     * The end date is adjusted as well to hold to the contract of this being a time period
     *
     * @param null $startDate db format date string to set the start date of the time period
     */
    public function setStartDate($start_date = null) {
        $timedate = TimeDate::getInstance();

        //check start_date, put it to now if it's not passed in
        if(is_null($start_date))
        {
            $start_date = $timedate->asDbDate($timedate->getNow());
        }

        //set the start/end date
        $this->start_date = $start_date;

        //the end date is set to the the increment of the date_modifier value minus one day
        $this->end_date = $timedate->asDbDate($timedate->fromDbDate($start_date)->modify($this->next_date_modifier)->modify('-1 day'));
    }


	//Fiscal year domain is stored in the timeperiods table, and not statically defined like the rest of the
	//domains, This method builds the domain array.
    public static function get_fiscal_year_dom() {

		static $fiscal_years;

		if (!isset($fiscal_years)) {

			$query = 'select id, name from timeperiods where deleted=0 and is_fiscal_year = 1 order by name';
			$db = DBManagerFactory::getInstance();
			$result = $db->query($query,true," Error filling in fiscal year domain: ");

			while (($row  =  $db->fetchByAssoc($result)) != null) {

				$fiscal_years[$row['id']]=$row['name'];
			}
			
			if (!isset($fiscal_years)) {
				$fiscal_years=array();
			}
		}
		return $fiscal_years;
	}


    /**
     * getTimePeriod
     * @param
     */
    public static function getTimePeriod($timedate=null)
    {
        //get current timeperiod
        $timeperiod_id = self::getCurrentId();

        if(!empty($timeperiod_id))
        {
           $timeperiod = new TimePeriod();
           $timeperiod->retrieve($timeperiod_id);
           return $timeperiod;
        }

        return null;
    }

    /**
     * loads leaf TimePeriods and returns instances as an array
     *
     * @return mixed Array of leaf TimePeriod instances
     */
    public function getLeaves()
    {
        $leaves = array();
        $db = DBManagerFactory::getInstance();
        $query = "select id, time_period_type from timeperiods WHERE parent_id = '{$this->id}' AND is_leaf = 1 AND deleted = 0 order by start_date_timestamp AND time_period_type = '{$this->time_period_type}'";
        $result = $db->query($query);
        while($row = $db->fetchByAssoc($result))
        {
            array_push($leaves, BeanFactory::getBean($row['time_period_type']."TimePeriods", $row['id']));
        }
        return $leaves;
    }

    /**
     * Returns true if TimePeriod instance has leaves, false otherwise
     *
     * @return bool true if TimePeriod instance has leaves, false otherwise
     */
    public function hasLeaves() {
        return count($this->getLeaves());
    }

    /**
     * removes related timeperiods
     */
    public function removeLeaves() {
        $this->load_relationship('related_timeperiods');
        $this->related_timeperiods->delete($this->id);
    }


    /**
     * Return a timeperiod object for a given database date
     *
     * @param $db_date String value of database date (ex: 2012-12-30)
     * @return bool|TimePeriod TimePeriod instance for corresponding database date; false if nothing found
     */
    public static function retrieveFromDate($db_date)
    {
        global $app_strings;
        $db = DBManagerFactory::getInstance();
        $db_date = $db->quote($db_date);
        $timeperiod_id = $db->getOne("SELECT id FROM timeperiods WHERE start_date <= '{$db_date}' AND end_date >= '{$db_date}' and is_fiscal_year = 0", false, string_format($app_strings['ERR_TIMEPERIOD_UNDEFINED_FOR_DATE'], array($db_date)));

        if(!empty($timeperiod_id)) {
            return BeanFactory::getBean('TimePeriods', $timeperiod_id);
        }

        return false;
    }



    /**
     * getCurrentName
     *
     * Returns the current timeperiod name if a timeperiod entry is found
     *
     */
    public static function getCurrentName()
    {
        global $app_strings;
        $timedate = TimeDate::getInstance();
        //get current timeperiod
        $db = DBManagerFactory::getInstance();
        $queryDate = $timedate->getNow();
        $date = $db->convert($db->quoted($queryDate->asDbDate()), 'date');
        $timeperiod = $db->getOne("SELECT name FROM timeperiods WHERE start_date <= {$date} AND end_date >= {$date} and is_leaf = 1 and is_fiscal_year = 0 and deleted = 0", false, string_format($app_strings['ERR_TIMEPERIOD_UNDEFINED_FOR_DATE'], array($queryDate->asDbDate())));
        $timeperiods = array();
        if(!empty($timeperiod))
        {
            $timeperiods[$timeperiod] = $app_strings['LBL_CURRENT_TIMEPERIOD'];
        }
        return $timeperiods;
    }

    /**
     * getCurrentId
     *
     * Returns the current TimePeriod instance's id if a leaf entry is found for the current date
     * @return $currentId String id of the TimePeriod instance's id
     */
    public static function getCurrentId()
    {
        static $currentId;

        if(!isset($currentId))
        {
            global $app_strings;
            $timedate = TimeDate::getInstance();
            $db = DBManagerFactory::getInstance();
            $queryDate = $timedate->getNow();
            $date = $db->convert($db->quoted($queryDate->asDbDate()), 'date');
            $currentId = $db->getOne("SELECT id FROM timeperiods WHERE start_date <= {$date} AND end_date >= {$date} and is_leaf = 1 and is_fiscal_year = 0 and deleted = 0", false, string_format($app_strings['ERR_TIMEPERIOD_UNDEFINED_FOR_DATE'], array($queryDate->asDbDate())));
        }
        return $currentId;
    }

    /**
     * getCurrentType
     *
     * Returns the current timeperiod type if a timeperiod entry is found
     *
     */
    public static function getCurrentType()
    {
        static $currentType;
        if(!isset($currentType))
        {
            global $app_strings;
            $timedate = TimeDate::getInstance();
            //get current timeperiod
            $db = DBManagerFactory::getInstance();
            $queryDate = $timedate->getNow();
            $date = $db->convert($db->quoted($queryDate->asDbDate()), 'date');
            $currentType = $db->getOne("SELECT time_period_type FROM timeperiods WHERE start_date <= {$date} AND end_date >= {$date} and is_leaf = 0 and is_fiscal_year = 0 and deleted = 0", false, string_format($app_strings['ERR_TIMEPERIOD_UNDEFINED_FOR_DATE'], array($queryDate->asDbDate())));
        }
        return $currentType;
    }

    public static function getCurrentTypeClass() {
        return TimePeriod::getCurrentType()."TimePeriods";
    }

    /**
     * getLastCurrentNextIds
     * Returns the quarterly ids of the last, current and next timeperiod
     * @static
     * @param $timedate Optional TimeDate instance to calculate values off of
     * @return $ids Mixed array of id=>name value(s) depending on the current system date or timedate parameter (if supplied)
     */
    public static function getLastCurrentNextIds($timedate=null)
    {
        global $app_strings;

        $admin = BeanFactory::getBean('Administration');
        $settings = $admin->getConfigForModule('Forecasts');

        //Retrieve Forecasts_timeperiod_interval
        $interval = isset($settings['timeperiod_interval']) ? $settings['timeperiod_interval'] : 'Quarter';

        if ($interval == 'Quarter')
        {
            $toLast = '-3 month';
            $toNext = '+6 month';
        }
        else if ($interval == 'Annual')
        {
            $toLast = '-12 month';
            $toNext = '+24 month';
        }

        $timedate = !is_null($timedate) ? $timedate : TimeDate::getInstance();
        $timeperiods = array();

        //get current timeperiod
        $timeperiod = self::getCurrentId();

        if(!empty($timeperiod))
        {
            $timeperiods[$timeperiod] = $app_strings['LBL_CURRENT_TIMEPERIOD'];
        }

        //previous timeperiod
        $db = DBManagerFactory::getInstance();
        $queryDate = $timedate->getNow()->modify($toLast);
        $date = $db->convert($db->quoted($queryDate->asDbDate()), 'date');
        $timeperiod = $db->getOne("SELECT id FROM timeperiods WHERE start_date <= {$date} AND end_date >= {$date} and is_fiscal_year = 0", false, string_format($app_strings['ERR_TIMEPERIOD_UNDEFINED_FOR_DATE'], array($queryDate->asDbDate())));

        if(!empty($timeperiod))
        {
            $timeperiods[$timeperiod] = $app_strings['LBL_PREVIOUS_TIMEPERIOD'];
        }

        //next timeperiod
        $queryDate = $queryDate->modify($toNext);
        $date = $db->convert($db->quoted($queryDate->asDbDate()), 'date');
        $timeperiod = $db->getOne("SELECT id FROM timeperiods WHERE start_date <= {$date} AND end_date >= {$date} and is_fiscal_year = 0", false, string_format($app_strings['ERR_TIMEPERIOD_UNDEFINED_FOR_DATE'], array($queryDate->asDbDate())));

        if(!empty($timeperiod))
        {
            $timeperiods[$timeperiod] = $app_strings['LBL_NEXT_TIMEPERIOD'];
        }
        return $timeperiods;
    }

    /**
     * get_timeperiods_dom
     * @static
     * @return array
     */
    public static function get_timeperiods_dom()
    {
        static $timeperiods;

        if(!isset($timeperiods))
        {
            $db = DBManagerFactory::getInstance();
            $timeperiods = array();
            $result = $db->query('SELECT id, name FROM timeperiods WHERE deleted=0');
            while(($row = $db->fetchByAssoc($result)))
            {
                if(!isset($timeperiods[$row['id']]))
                {
                    $timeperiods[$row['id']]=$row['name'];
                }
            }
        }
        return $timeperiods;
    }

    public static function get_not_fiscal_timeperiods_dom()
    {
        static $not_fiscal_timeperiods;

        if(!isset($not_fiscal_timeperiods))
        {
            $db = DBManagerFactory::getInstance();
            $not_fiscal_timeperiods = array();
            $result = $db->query('SELECT id, name FROM timeperiods WHERE is_fiscal_year = 0 AND is_leaf = 1 AND deleted=0');
            while(($row = $db->fetchByAssoc($result)))
            {
                if(!isset($not_fiscal_timeperiods[$row['id']]))
                {
                    $not_fiscal_timeperiods[$row['id']]=$row['name'];
                }
            }
        }
        return $not_fiscal_timeperiods;
    }

    /**
     * Takes the current time period and finds the next one that is in the db of the same type.  If none exists it returns null
     *
     * @return mixed
     */
    public function getNextTimePeriod() {
        $timedate = TimeDate::getInstance();

        $query = "select id, time_period_type from timeperiods where ";
        $query .= " time_period_type = " . $this->db->quoted($this->time_period_type);
        $query .= " AND deleted = 0";

        $queryDate = $timedate->fromDbDate($this->end_date);
        $queryDate = $queryDate->modify('+1 day');
        $queryDate = $this->db->convert($this->db->quoted($queryDate->asDbDate()), 'date');

        $query .= " AND start_date = {$queryDate}";

        $result = $this->db->query($query);
        $row = $this->db->fetchByAssoc($result);

        if($row == null) {
            return null;
        }

        $nextTimePeriod = BeanFactory::getBean($row['time_period_type'].'TimePeriods');
        $nextTimePeriod->retrieve($row['id']);
        return $nextTimePeriod;
    }


    /**
     * Grabs the time period previous of this one and returns it.  If none is found, it returns null
     *
     * @return null|SugarBean
     */
    public function getPreviousTimePeriod() {
        $timedate = TimeDate::getInstance();

        $query = "select id, time_period_type from timeperiods where ";
        $query .= " time_period_type = " . $this->db->quoted($this->time_period_type);
        $query .= " AND deleted = 0";

        $queryDate = $timedate->fromDbDate($this->start_date);
        $queryDate = $queryDate->modify('-1 day');
        $queryDate = $this->db->convert($this->db->quoted($queryDate->asDbDate()), 'date');

        $query .= " AND end_date = {$queryDate}";

        $result = $this->db->query($query);
        $row = $this->db->fetchByAssoc($result);

        if($row == null)
        {
           return null;
        }

        $previousTimePeriod = BeanFactory::getBean($row['time_period_type'].'TimePeriods');
        $previousTimePeriod->retrieve($row['id']);
        return $previousTimePeriod;
    }

    /**
     * Examines the config values and rebuilds the time periods based on the new settings
     *
     * @param $priorSettings Array of the previous timeperiod admin properties
     * @param $currentSettings Array of the current timeperiod admin settings
     *
     * @return void
     */
    public function rebuildForecastingTimePeriods($priorSettings, $currentSettings)
    {
       $timedate = TimeDate::getInstance();
       $db = DBManagerFactory::getInstance();

       //determine today
       $currentDate = $timedate->getNow();
       $targetStartDate = $timedate->getNow();

       $this->time_period_type = $currentSettings['timeperiod_interval']; // Annual by default
       $this->leaf_period_type = $currentSettings['timeperiod_leaf_interval']; // Quarter by default

       //set the target date
       $targetStartDate->setDate($targetStartDate->format("Y"), $currentSettings["timeperiod_start_month"], $currentSettings["timeperiod_start_day"]);

       //if the target date is after the current year then set the year to be one back as our start date
       if($currentDate < $targetStartDate)
       {
           $targetStartDate->modify('-1 year');
       }

       $this->setStartDate($targetStartDate->asDbDate());

       //First check if they have changed the start date and/or month to be different than what was previously set
       $targetDateDifferent = $this->isTargetDateDifferentFromPrevious($targetStartDate, $priorSettings);
       $targetIntervalDifferent = $this->isTargetIntervalDifferent($priorSettings, $currentSettings);

       //Now check if we need to add more timeperiods
       $shownBackwardDifference = $this->getShownDifference($priorSettings, $currentSettings, 'timeperiod_shown_backward');
       $shownForwardDifference = $this->getShownDifference($priorSettings, $currentSettings, 'timeperiod_shown_forward');

       //The simplest case is if we are just creating additional timeperiods to show
       if(!$targetDateDifferent && !$targetIntervalDifferent)
       {
          $this->buildLeaves($shownBackwardDifference, $shownForwardDifference);
       } else {

       }

       //Right now we only support chronological AnnualTimePeriods so just hard code this for now
       //$currentTimePeriod = BeanFactory::newBean($periodsInterval . 'TimePeriods');
       /*
       $currentTimePeriod = BeanFactory::newBean('AnnualTimePeriods');

       //Set the start date for the current time period
       $currentTimePeriod->setStartDate($targetStartDate->asDbDate());

       //Save the current timeperiod
       $currentTimePeriod->save();

       //Now build out the leaves (Quarter based for now)
       $currentTimePeriod->buildLeaves($periodsLeafInterval);
        */
       /*
       $currentTimePeriod->buildLeaves($forecastSettings['timeperiod_leaf_interval']);

       //create the back periods
       $priorTimePeriod = BeanFactory::getBean($forecastSettings['timeperiod_interval']."TimePeriods",$currentTimePeriod->id);
       $forwardTimePeriod = BeanFactory::getBean($forecastSettings['timeperiod_interval']."TimePeriods",$currentTimePeriod->id);

       for($i = 1; $i <= $periodsBack; $i++) {
           $priorTimePeriod = $priorTimePeriod->createPreviousTimePeriod();
           $priorTimePeriod->buildLeaves($forecastSettings['timeperiod_leaf_interval']);
       }

       //create the forward periods
       for($i = 1; $i <= $periodsForward; $i++) {
           $forwardTimePeriod = $forwardTimePeriod->createNextTimePeriod();
           $forwardTimePeriod->buildLeaves($forecastSettings['timeperiod_leaf_interval']);
        }

        //clear job scheduler
        $job_id = $db->getOne("SELECT id FROM job_queue WHERE name = ".$db->quoted('TimePeriodAutomationJob'));

        $jobQueue = new SugarJobQueue();
        if($job_id) {
            $jobQueue->deleteJob($job_id);
        }

        //schedule job to run on the end_date of the last time period
        global $current_user;
        $job = BeanFactory::newBean('SchedulersJobs');
        $job->name = "TimePeriodAutomationJob";
        $job->target = "class::SugarJobCreateNextTimePeriod";
        $endDate = $timedate->fromDbDate($currentTimePeriod->end_date);
        $job->execute_time = $timedate->asUserDate($endDate,true);
        $job->retry_count = 0;
        $job->assigned_user_id = $current_user->id;
        $jobQueue->submitJob($job);
       */
    }

    /**
     * buildLeaves
     *
     * Builds the leaves based on the TimePeriods earliest and latest start dates and the
     * specified backward and forward values for the number of timeperiods to build
     *
     * @param $shownBackwardDifference int value of the shown backward difference
     * @param $shownForwardDifference int value of the shown forward
     */
    public function buildLeaves($shownBackwardDifference, $shownForwardDifference)
    {
          if($shownBackwardDifference > 0)
          {
              $earliestTimePeriod = $this->getEarliest($this->time_period_type);
              if(is_null($earliestTimePeriod))
              {
                  $earliestTimePeriod = TimePeriod::getByType($this->time_period_type);
                  $earliestTimePeriod->setStartDate($this->start_date);
              }
              $earliestTimePeriod->buildTimePeriods($shownBackwardDifference, $this->previous_date_modifier);
          }

          if($shownForwardDifference > 0)
          {
              $latestTimePeriod = $this->getLatest($this->time_period_type);
              if(is_null($latestTimePeriod))
              {
                  $latestTimePeriod = TimePeriod::getByType($this->time_period_type);
                  $latestTimePeriod->setStartDate($this->start_date);
              }
              $latestTimePeriod->buildTimePeriods($shownForwardDifference, $this->next_date_modifier);
          }
    }

    /**
     * buildTimePeriods
     *
     * @param $timePeriods int value of the number of parent level TimePeriods to create
     * @param $dateModifier String value of the date modifier (1 year, -1 year, etc.) to use when creating the parent level TimePeriods
     */
    protected function buildTimePeriods($timePeriods, $dateModifier)
    {
        $timedate = TimeDate::getInstance();

        for($i=0; $i < $timePeriods; $i++)
        {
            //Create annual TimePeriod instance
            $timePeriod = TimePeriod::getByType($this->time_period_type);
            $timePeriod->name = sprintf($this->name_template, $timedate->fromDbDate($this->start_date)->format('Y'));
            $timePeriod->setStartDate($timedate->fromDbDate($this->start_date)->modify($dateModifier)->asDbDate());
            $timePeriod->time_period_type = $this->time_period_type;
            $timePeriod->is_fiscal_year = true;
            $timePeriod->is_fiscal = $this->is_fiscal;
            $timePeriod->save();

            $leafStartDate = $timedate->fromDbDate($timePeriod->start_date);
            $leafYear = $timedate->fromDbDate($timePeriod->start_date)->format('Y');

            for($x=1; $x <= $this->leaf_periods; $x++)
            {
                $leafPeriod = TimePeriod::getByType($this->leaf_period_type);
                $leafPeriod->name = sprintf($this->leaf_name_template, $x, $leafYear);
                $startDate = ($x == 1) ? $leafStartDate->asDbDate() : $leafStartDate->modify($leafPeriod->next_date_modifier)->asDbDate();
                $leafPeriod->setStartDate($startDate);
                $leafPeriod->parent_id = $timePeriod->id;
                $leafPeriod->is_fiscal_year = false;
                $leafPeriod->is_fiscal = $timePeriod->is_fiscal;
                $leafPeriod->save();
            }

            //Set start_date to be modified with $this->previous_date_modifier
            $this->start_date = $timedate->fromDbDate($timePeriod->start_date)->asDbDate();
        }
    }

    /**
     * Checks if the targetStartDate is different based on prior settings
     *
     * @param $targetStartDate SugarDateTime instance of start date based on current settings
     * @param $priorSettings Array of previous forecast settings
     *
     * @return bool true if different false otherwise
     */
    public function isTargetDateDifferentFromPrevious($targetStartDate, $priorSettings)
    {
        //First check if prior settings are empty
        if(empty($priorSettings) || !isset($priorSettings['timeperiod_start_month']) || !isset($priorSettings['timeperiod_start_day']))
        {
            return true;
        }

        $timedate = TimeDate::getInstance();
        $priorDate = $timedate->getNow();
        $priorDate->setDate(intval($targetStartDate->format("Y")), $priorSettings['timeperiod_start_month'], $priorSettings['timeperiod_start_day']);

        return $targetStartDate != $priorDate;
    }


    /**
     * Checks if the interval settings are different based on prior settings
     *
     * @param $priorSettings Array of the previous timeperiod admin properties
     * @param $currentSettings Array of the current timeperiod admin settings
     *
     * @return bool true if different false otherwise
     */
    public function isTargetIntervalDifferent($priorSettings, $currentSettings)
    {
        //First check if prior settings are empty
        if(empty($priorSettings) || !isset($priorSettings['timeperiod_interval']) || !isset($priorSettings['timeperiod_leaf_interval']))
        {
            return true;
        }

        return $priorSettings['timeperiod_interval'] != $currentSettings['timeperiod_interval'] ||
               $priorSettings['timeperiod_leaf_interval'] != $currentSettings['timeperiod_leaf_interval'];
    }

    /**
     * reflags all current timeperiods as deleted based on the previous and current settings
     *
     * @param $priorSettings Array of the previous timeperiod admin properties
     * @param $currentSettings Array of the current timeperiod admin settings
     * @return void
     */
    public function deleteTimePeriods($priorSettings, $currentSettings)
    {

    }

    /**
     * getShownDifference
     *
     * This function returns the numeric difference of the shown backward or forward differences
     *
     * @param $priorSettings Array of previous forecast settings
     * @param $currentSettings Array of current forecast settings
     * @param $key String value of the key (timeperiod_shown_forward or timeperiod_shown_backward)
     */
    public function getShownDifference($priorSettings, $currentSettings, $key)
    {
        //If no prior settings exists, the difference is the new setting
        if(!isset($priorSettings[$key]))
        {
           return $currentSettings[$key];
        }
        return $currentSettings[$key] - $priorSettings[$key];
    }

    /**
     * This function compares two Arrays of settings and returns boolean indicating whether they are identical or not
     *
     * @param $priorSettings
     * @param $currentSettings
     *
     * @return bool True if settings are the same, false otherwise
     */
    public function isSettingIdentical($priorSettings, $currentSettings)
    {
        if(!isset($priorSettings['timeperiod_interval']) || ($currentSettings['timeperiod_interval'] != $priorSettings['timeperiod_interval'])) {
            return false;
        }
        if(!isset($priorSettings['timeperiod_type']) || ($currentSettings['timeperiod_type'] != $priorSettings['timeperiod_type'])) {
            return false;
        }
        if(!isset($priorSettings['timeperiod_start_month']) || ($currentSettings['timeperiod_start_month'] != $priorSettings['timeperiod_start_month'])) {
            return false;
        }
        if(!isset($priorSettings['timeperiod_start_day']) || ($currentSettings['timeperiod_start_day'] != $priorSettings['timeperiod_start_day'])) {
            return false;
        }
        if(!isset($priorSettings['timeperiod_leaf_interval']) || ($currentSettings['timeperiod_leaf_interval'] != $priorSettings['timeperiod_leaf_interval'])) {
            return false;
        }
        if(!isset($priorSettings['timeperiod_shown_backward']) || ($currentSettings['timeperiod_shown_backward'] != $priorSettings['timeperiod_shown_backward'])) {
            return false;
        }
        if(!isset($priorSettings['timeperiod_shown_forward']) || ($currentSettings['timeperiod_shown_forward'] != $priorSettings['timeperiod_shown_forward'])) {
            return false;
        }

        return true;
    }


    /**
     * subtracts the end from the start date to return the date length in days
     *
     * @return mixed
     */
    public function getLengthInDays()
    {
        return ceil(($this->end_date_timestamp - $this->start_date_timestamp) / 86400);
    }

    /**
     * Returns the TimePeriod bean instance for the given time period id
     *
     * @param $id String id of the bean
     * @return $bean TimePeriod bean instance
     */
    public static function getBean($id)
    {
        $db = DBManagerFactory::getInstance();
        $result = $db->query(sprintf("SELECT id, time_period_type FROM timeperiods WHERE id = '%s' AND deleted = 0", $id));
        if($result) {
            $row = $db->fetchByAssoc($result);
            if($row) {
                return BeanFactory::getBean($row['time_period_type'] . 'TimePeriods', $id);
            }
        }

        return null;
    }


    /**
     * Returns the earliest TimePeriod bean instance for the given timeperiod interval type
     *
     * @param $type String value of the timeperiod interval type
     * @return $bean The earliest TimePeriod bean instance; null if none found
     */
    public static function getEarliest($type)
    {
        $db = DBManagerFactory::getInstance();
        $result = $db->limitQuery(sprintf("SELECT * FROM timeperiods WHERE time_period_type = '%s' AND deleted = 0 ORDER BY start_date_timestamp ASC", $type), 0, 1);
        if($result)
        {
            $row = $db->fetchByAssoc($result);
            if(!empty($row))
            {
               $bean = BeanFactory::getBean("{$type}TimePeriods");
               $bean->retrieve($row['id']);
               return $bean;
            }
        }
        return null;
    }

    /**
     * Returns the latest TimePeriod bean instance for the given timeperiod interval type
     *
     * @param $type String value of the timeperiod interval type
     * @return $bean The latest TimePeriod bean instance; null if none found
     */
    public static function getLatest($type)
    {
        $db = DBManagerFactory::getInstance();
        $result = $db->limitQuery(sprintf("SELECT * FROM timeperiods WHERE time_period_type = '%s' AND deleted = 0 ORDER BY start_date_timestamp DESC", $type), 0, 1);
        if($result)
        {
            $row = $db->fetchByAssoc($result);
            if(!empty($row))
            {
               $bean = BeanFactory::getBean("{$type}TimePeriods");
               $bean->retrieve($row['id']);
               return $bean;
            }
        }
        return null;
    }

    /**
     * Returns a TimePeriod bean instance based on the given interval type
     *
     * @param $type String value of the timeperiod interval type
     * @return bean A TimePeriod instance bean based on the interval type
     */
    public static function getByType($type)
    {
        return BeanFactory::getBean("{$type}TimePeriods");
    }
}

function get_timeperiods_dom()
{
    return TimePeriod::get_timeperiods_dom();
}

function get_not_fiscal_timeperiods_dom()
{
    return TimePeriod::get_not_fiscal_timeperiods_dom();
}
