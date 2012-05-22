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



class SugarWidgetFieldDateTime extends SugarWidgetReportField
{
	var $reporter;
	var $assigned_user=null;

    function SugarWidgetFieldDateTime($layout_manager)
    {
        parent::SugarWidgetReportField($layout_manager);
    }

	// get the reporter attribute
    // deprecated, now called in the constructor
    /**
     * @deprecated
     */
	function getReporter() {
	}

	// get the assigned user of the report
	function getAssignedUser()
	{
		$json_obj = getJSONobj();

		$report_def_str = $json_obj->decode($this->reporter->report_def_str);

		if(empty($report_def_str['assigned_user_id'])) return null;

		$this->assigned_user = new User();
		$this->assigned_user->retrieve($report_def_str['assigned_user_id']);
		return $this->assigned_user;
	}

	function queryFilterOn($layout_def)
	{
		global $timedate;
        $begin = $layout_def['input_name0'];
        $hasTime = $this->hasTime($begin);
        if(!$hasTime)
        {
            return $this->queryDay($layout_def, $timedate->fromDbDate($begin));
        }
        return $this->queryDateOp($this->_get_column_select($layout_def), $begin, '=', "datetime");
	}

    /**
     * expandDate
     *
     * This function helps to convert a date only value to have a time value as well.  It first checks
     * to see if a time value exists.  If a time value exists, the function just returns the date value
     * passed in.  If the date value is the 'Today' macro then some special processing occurs as well.
     * Finally the time portion is applied depending on whether or not this date should be for the end
     * in which case the 23:59:59 time value is applied otherwise 00:00:00 is used.
     *
     * @param $date String value of the date value to expand
     * @param bool $end Boolean value indicating whether or not this is for an end time period or not
     * @return $date TimeDate object with time value applied
     */
	protected function expandDate($date, $end = false)
	{
	    global $timedate;
	    if($this->hasTime($date)) {
	        return $date;
	    }

        //C.L. Bug 48616 - If the $date is set to the Today macro, then adjust accordingly
        if(strtolower($date) == 'today')
        {
           $startEnd = $timedate->getDayStartEndGMT($timedate->getNow(true));
           return $end ? $startEnd['end'] : $startEnd['start'];
        }

        $parsed = $timedate->fromDbDate($date);
        $date = $timedate->tzUser(new SugarDateTime());
        $date->setDate($parsed->year, $parsed->month, $parsed->day);

	    if($end) {
	        return $date->setTime(23, 59, 59);
	    } else {
	        return $date->setTime(0, 0, 0);
	    }
	}

	function queryFilterBefore($layout_def)
	{
        $begin = $this->expandDate($layout_def['input_name0']);
        return $this->queryDateOp($this->_get_column_select($layout_def), $begin, '<', "datetime");
	}

	function queryFilterAfter($layout_def)
	{
        $begin = $this->expandDate($layout_def['input_name0'], true);
        return $this->queryDateOp($this->_get_column_select($layout_def), $begin, '>', "datetime");
	}

	function queryFilterBetween_Dates($layout_def)
	{
        $begin = $this->expandDate($layout_def['input_name0']);
     	$end = $this->expandDate($layout_def['input_name1'], true);
        $column = $this->_get_column_select($layout_def);
	    return "(".$this->queryDateOp($column, $begin, ">=", "datetime")." AND ".
            $this->queryDateOp($column, $end, "<=", "datetime").")\n";
	}

	function queryFilterNot_Equals_str($layout_def)
	{
		global $timedate;

        $column = $this->_get_column_select($layout_def);
        $begin = $layout_def['input_name0'];
        $hasTime = $this->hasTime($begin);
        if(!$hasTime){
     	    $end = $this->expandDate($begin, true);
            $begin = $this->expandDate($begin);
            $cond = $this->queryDateOp($column, $begin, "<", "datetime")." OR ".
                $this->queryDateOp($column, $end, ">", "datetime");
        } else {
            $cond =  $this->queryDateOp($column, $begin, "!=", "datetime");
        }
        return "($column IS NULL OR $cond)";
	}

    /**
     * Get assigned or logged in user's current date and time value.
     * @param boolean $timestamp Format of return value, if set to true, return unix like timestamp , else a formatted date.
     */
	function get_users_current_date_time($timestamp=false)
	{
	 	global $current_user;
        global $timedate;

        $begin = TimeDate::getInstance()->nowDb();
        //kbrill bug #13884
       	//$begin = $timedate->to_display_date_time($begin,true,true,$this->assigned_user);
		$begin = $timedate->handle_offset($begin, $timedate->get_db_date_time_format(), false, $this->assigned_user);

        if (!$timestamp) {
        	return $begin;
        } else {
        	$begin_parts = explode(' ', $begin);
        	$date_parts=explode('-', $begin_parts[0]);
        	$time_parts=explode(':', $begin_parts[1]);
        	$curr_timestamp=mktime($time_parts[0],$time_parts[1],0,$date_parts[1], $date_parts[2],$date_parts[0]);
        	return $curr_timestamp;
        }

	}
	/**
	 * Get specified date and time for a particalur day, in current user's timezone.
	 * @param int $days Adjust date by this number of days, negative values are valid.
	 * @param time string falg for desired time value, start: minimum time, end: maximum time, default: current time
	 */
	function get_db_date($days,$time) {
        global $timedate;

        $begin = date($GLOBALS['timedate']->get_db_date_time_format(), time()+(86400 * $days));  //gmt date with day adjustment applied.
        //kbrill bug #13884
        //$begin = $timedate->to_display_date_time($begin,true,true,$this->assigned_user);
		$begin = $timedate->handle_offset($begin, $timedate->get_db_date_time_format(), false, $this->assigned_user);

        if ($time=='start') {
            $begin_parts = explode(' ', $begin);
            $be = $begin_parts[0] . ' 00:00:00';
        }
        else if ($time=='end') {
            $begin_parts = explode(' ', $begin);
            $be = $begin_parts[0] . ' 23:59:59';
        } else {
            $be=$begin;
        }

        //convert date to db format without converting to GMT.
        $begin = $timedate->handle_offset($be, $timedate->get_db_date_time_format(), false, $this->assigned_user);

        return $begin;
	}

	/**
	 * Get filter string for a date field.
	 * @param array layout_def field def for field being filtered
	 * @param string $begin start date value (in DB format)
	 * @param string $end End date value (in DB format)
	 */
	function get_start_end_date_filter(& $layout_def, $begin,$end)
	{
	    if (isset ($layout_def['rel_field'])) {
	        $field_name = $this->reporter->db->convert(
	            $this->reporter->db->convert($this->_get_column_select($layout_def), 'date_format', '%Y-%m-%d'),
	            "CONCAT",
	            array("' '", $this->reporter->db->convert($layout_def['rel_field'], 'time_format'))
	        );
	    } else {
	       $field_name = $this->_get_column_select($layout_def);
	    }
	    return $field_name.">=".$this->reporter->db->quoted($begin)." AND ".$field_name."<=".$this->reporter->db->quoted($end)."\n";
	}

	/**
	 * Create query for binary operation of field of certain type
	 * Produces query like:
	 * arg1 op to_date(arg2), e.g.:
	 * 		date_closed < '2009-12-01'
	 * @param string $arg1 1st arg - column name
	 * @param string|DateTime $arg2 2nd arg - value to be converted
	 * @param string $op
	 * @param string $type
	 */
    protected function queryDateOp($arg1, $arg2, $op, $type)
    {
        global $timedate;
        if($arg2 instanceof DateTime) {
            $arg2 = $timedate->asDbType($arg2, $type);
        }
        return "$arg1 $op ".$this->reporter->db->convert($this->reporter->db->quoted($arg2), $type)."\n";
    }

    /**
     * Return current date in required user's TZ
     * @return SugarDateTime
     */
    protected function now()
    {
        global $timedate;
        return $timedate->tzUser($timedate->getNow(), $this->getAssignedUser());
    }

	/**
     * Create query from the beginning to the end of certain day
     * @param array $layout_def
     * @param SugarDateTime $day
     */
    protected function queryDay($layout_def, SugarDateTime $day)
    {
        $begin = $day->get_day_begin();
        $end = $day->get_day_end();
        return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
    }

	function queryFilterTP_yesterday($layout_def)
	{
		global $timedate;
		return $this->queryDay($layout_def, $this->now()->get("-1 day"));
	}

	function queryFilterTP_today($layout_def)
	{
		global $timedate;
		return $this->queryDay($layout_def, $this->now());
	}

	function queryFilterTP_tomorrow(& $layout_def)
	{
		global $timedate;
		return $this->queryDay($layout_def, $this->now()->get("+1 day"));
	}

	function queryFilterTP_last_7_days($layout_def)
	{
		global $timedate;

		$begin = $this->now()->get("-6 days")->get_day_begin();
		$end = $this->now()->get_day_end();

		return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
	}

	function queryFilterTP_next_7_days($layout_def)
	{
		global $timedate;

		$begin = $this->now()->get_day_begin();
		$end = $this->now()->get("+6 days")->get_day_end();

		return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
	}

    /**
     * Create query from the beginning to the end of certain month
     * @param array $layout_def
     * @param SugarDateTime $month
     */
    protected function queryMonth($layout_def, $month)
    {
        $begin = $month->setTime(0, 0, 0);
        $end = clone($begin);
		$end->setDate($begin->year, $begin->month, $begin->days_in_month)->setTime(23, 59, 59);
        return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
    }

    function queryFilterTP_last_month($layout_def)
	{
		global $timedate;
		$month = $this->now();
		return $this->queryMonth($layout_def, $month->setDate($month->year, $month->month-1, 1));
	}

	function queryFilterTP_this_month($layout_def)
	{
		global $timedate;
		return $this->queryMonth($layout_def, $this->now()->get_day_by_index_this_month(0));
	}

	function queryFilterTP_next_month($layout_def)
	{
		global $timedate;
		$month = $this->now();
		return $this->queryMonth($layout_def, $month->setDate($month->year, $month->month+1, 1));
	}

	function queryFilterTP_last_30_days($layout_def)
	{
		global $timedate;
		$begin = $this->now()->get("-29 days")->get_day_begin();
		$end = $this->now()->get_day_end();
		return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
	}

	function queryFilterTP_next_30_days($layout_def)
	{
		global $timedate;
		$begin = $this->now()->get_day_begin();
		$end = $this->now()->get("+29 days")->get_day_end();
		return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
	}


	function queryFilterTP_this_quarter($layout_def)
	{
		global $timedate;
		$begin = $this->now();
		$begin->setDate($begin->year, floor(($begin->month-1)/3)*3+1, 1)->setTime(0, 0);
		$end = $begin->get("+3 month")->setTime(23, 59, 59);
		return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
	}

	function queryFilterTP_last_year($layout_def)
	{
		global $timedate;
		$begin = $this->now();
		$begin->setDate($begin->year-1, 1, 1)->setTime(0, 0);
		$end = clone $begin;
		$end->setDate($end->year, 12, 31)->setTime(23, 59, 59);
		return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
	}

	function queryFilterTP_this_year($layout_def)
	{
		global $timedate;
		$begin = $this->now();
		$begin->setDate($begin->year, 1, 1)->setTime(0, 0);
		$end = clone $begin;
		$end->setDate($end->year, 12, 31)->setTime(23, 59, 59);
		return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
	}

	function queryFilterTP_next_year(& $layout_def)
	{
		global $timedate;
		$begin = $this->now();
		$begin->setDate($begin->year+1, 1, 1)->setTime(0, 0);
		$end = clone $begin;
		$end->setDate($end->year, 12, 31)->setTime(23, 59, 59);
		return $this->get_start_end_date_filter($layout_def,$begin->asDb(),$end->asDb());
	}

	function queryGroupBy($layout_def)
	{
		// i guess qualifier and column_function are the same..
		if (!empty ($layout_def['qualifier'])) {
			$func_name = 'queryGroupBy'.$layout_def['qualifier'];
			if (method_exists($this, $func_name)) {
				return $this-> $func_name ($layout_def)." \n";
			}
		}
		return parent :: queryGroupBy($layout_def)." \n";
	}

	function queryOrderBy($layout_def)
	{
        if (!empty ($layout_def['qualifier'])) {
			$func_name ='queryOrderBy'.$layout_def['qualifier'];
			if (method_exists($this, $func_name)) {
				return $this-> $func_name ($layout_def)."\n";
			}
		}
		$order_by = parent :: queryOrderBy($layout_def)."\n";
		return $order_by;
	}

    function displayListPlain($layout_def) {
        global $timedate;
        $content = parent:: displayListPlain($layout_def);
        // awu: this if condition happens only in Reports where group by month comes back as YYYY-mm format
        if (count(explode('-',$content)) == 2){
            return $content;
        // if date field
        }elseif(substr_count($layout_def['type'], 'date') > 0){
            // if date time field
            if(substr_count($layout_def['type'], 'time') > 0 && $this->get_time_part($content)!= false){
                $td = $timedate->to_display_date_time($content);
                return $td;
            }else{// if date only field
                $td = $timedate->to_display_date($content, false); // Avoid PHP notice of returning by reference.
                return $td;
            }
        }
    }

    function get_time_part($date_time_value)
    {
        global $timedate;

        $date_parts=$timedate->split_date_time($date_time_value);
        if (count($date_parts) > 1) {
            return $date_parts[1];
        } else {
            return false;
        }
    }

    function displayList($layout_def) {
        global $timedate;
        // i guess qualifier and column_function are the same..
        if (!empty ($layout_def['column_function'])) {
            $func_name = 'displayList'.$layout_def['column_function'];
            if (method_exists($this, $func_name)) {
                return $this-> $func_name ($layout_def);
            }
        }
        $content = parent :: displayListPlain($layout_def);
        return $timedate->to_display_date_time($content);
    }

	function querySelect(& $layout_def) {
		// i guess qualifier and column_function are the same..
		if (!empty ($layout_def['column_function'])) {
			$func_name = 'querySelect'.$layout_def['column_function'];
			if (method_exists($this, $func_name)) {
				return $this-> $func_name ($layout_def)." \n";
			}
		}
		return parent :: querySelect($layout_def)." \n";
	}
	function & displayListday(& $layout_def) {
		return parent:: displayListPlain($layout_def);
	}

	function & displayListyear(& $layout_def) {
		global $app_list_strings;
    	return parent:: displayListPlain($layout_def);
	}

	function displayListmonth($layout_def)
	{
		global $app_list_strings;
		$display = '';
		$match = array();
        if (preg_match('/(\d{4})-(\d\d)/', $this->displayListPlain($layout_def), $match)) {
			$match[2] = preg_replace('/^0/', '', $match[2]);
			$display = $app_list_strings['dom_cal_month_long'][$match[2]]." {$match[1]}";
		}
		return $display;

	}

	function querySelectmonth($layout_def)
	{
	    return $this->reporter->db->convert($this->_get_column_select($layout_def), "date_format", array('%Y-%m'))." ".$this->_get_column_alias($layout_def)."\n";
	}

	function queryGroupByMonth($layout_def)
	{
        return $this->reporter->db->convert($this->_get_column_select($layout_def), "date_format", array('%Y-%m'))."\n";
	}

	function querySelectday($layout_def)
	{
	    return $this->reporter->db->convert($this->_get_column_select($layout_def), "date_format", array('%Y-%m-%d'))." ".$this->_get_column_alias($layout_def)."\n";
	}

	function queryGroupByDay($layout_def)
	{
	    return $this->reporter->db->convert($this->_get_column_select($layout_def), "date_format", array('%Y-%m-%d'))."\n";
	}

	function querySelectyear($layout_def)
	{
	    return $this->reporter->db->convert($this->_get_column_select($layout_def), "date_format", array('%Y'))." ".$this->_get_column_alias($layout_def)."\n";
	}

	function queryGroupByYear($layout_def)
	{
	    return $this->reporter->db->convert($this->_get_column_select($layout_def), "date_format", array('%Y'))."\n";
	}

	function querySelectquarter($layout_def)
	{
	    $column = $this->_get_column_select($layout_def);
	    return $this->reporter->db->convert($this->reporter->db->convert($column, "date_format", array('%Y')),
	        	'CONCAT',
	            array("'-'", $this->reporter->db->convert($column, "quarter")))
	        ." ".$this->_get_column_alias($layout_def)."\n";
	}

	function displayListquarter(& $layout_def) {
		$match = array();
        if (preg_match('/(\d{4})-(\d)/', $this->displayListPlain($layout_def), $match)) {
			return "Q".$match[2]." ".$match[1];
		}
		return '';

	}

	function queryGroupByQuarter($layout_def)
	{
		$this->getReporter();
        $column = $this->_get_column_select($layout_def);
	    return $this->reporter->db->convert($this->reporter->db->convert($column, "date_format", array('%Y')),
	        	'CONCAT',
	            array("'-'", $this->reporter->db->convert($column, "quarter")));
	}

    function displayInput(&$layout_def) {
    	global $timedate, $current_language, $app_strings;
        $home_mod_strings = return_module_language($current_language, 'Home');
        $filterTypes = array(' '                 => $app_strings['LBL_NONE'],
                             'TP_today'         => $home_mod_strings['LBL_TODAY'],
                             'TP_yesterday'     => $home_mod_strings['LBL_YESTERDAY'],
                             'TP_tomorrow'      => $home_mod_strings['LBL_TOMORROW'],
                             'TP_this_month'    => $home_mod_strings['LBL_THIS_MONTH'],
                             'TP_this_year'     => $home_mod_strings['LBL_THIS_YEAR'],
                             'TP_last_30_days'  => $home_mod_strings['LBL_LAST_30_DAYS'],
                             'TP_last_7_days'   => $home_mod_strings['LBL_LAST_7_DAYS'],
                             'TP_last_month'    => $home_mod_strings['LBL_LAST_MONTH'],
                             'TP_last_year'     => $home_mod_strings['LBL_LAST_YEAR'],
                             'TP_next_30_days'  => $home_mod_strings['LBL_NEXT_30_DAYS'],
                             'TP_next_7_days'   => $home_mod_strings['LBL_NEXT_7_DAYS'],
                             'TP_next_month'    => $home_mod_strings['LBL_NEXT_MONTH'],
                             'TP_next_year'     => $home_mod_strings['LBL_NEXT_YEAR'],
                             );

        $cal_dateformat = $timedate->get_cal_date_format();
        $str = "<select name='type_{$layout_def['name']}'>";
        $str .= get_select_options_with_id($filterTypes, (empty($layout_def['input_name0']) ? '' : $layout_def['input_name0']));
//        foreach($filterTypes as $value => $label) {
//            $str .= '<option value="' . $value . '">' . $label. '</option>';
//        }
        $str .= "</select>";


        return $str;
    }

    /**
     * @param  $date
     * @return bool false if the date is a only a date, true if the date includes time.
     */
    protected function hasTime($date)
    {
        return strlen(trim($date)) < 11 ? false : true;
    }

}
