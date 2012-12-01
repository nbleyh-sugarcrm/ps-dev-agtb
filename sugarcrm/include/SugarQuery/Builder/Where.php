<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


/**
 * This is the base object for building SugarQueries Where's
 * ************ WARNING**********************************************
 * THIS CLASS AND ALL RELATED CLASSES WILL BE FUNDAMENTALLY CHANGING
 * DO NOT USE THIS TO BUILD YOUR QUERIES.  
 * ******************************************************************
 * 
 */
abstract class SugarQuery_Builder_Where
{
    /**
     * @var null|string
     */
    public $raw = null;

    /**
     * @var array
     */
    public $conditions = array();

	public function __construct() {
	}

    /**
     * @param $field
     * @param $value
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function equals($field, $value, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setOperator('=')->setField($field)->setValues($value);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;
		return $this;
	}

    /**
     * Creates a condition for two fields to check equality
     * @param string $field1
     * @param string $field2 
     * @param bool|object $bean 
     * @return object
     */
    public function equalsField($field1, $field2, $bean = false) {
        $condition = new SugarQuery_Builder_Condition();
        $condition->setOperator('EQUALFIELD')->setField($field1)->setValues($field2);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * Creates a condition to check not equals
     * @param string $field 
     * @param string $value 
     * @param bool|object $bean 
     * @return object
     */
    public function notEquals($field, $value, $bean=false) {
        $condition = new SugarQuery_Builder_Condition();
        $condition->setOperator('!=')->setField($field)->setValues($value);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * Creates a condition for two fields to check non-equality
     * @param string $field1 
     * @param string $field2 
     * @param bool|object $bean 
     * @return object
     */
    public function notEqualsField($field1, $field2, $bean=false) {
        $condition = new SugarQuery_Builder_Condition();
        $condition->setOperator('NOTEQUALFIELD')->setField($field1)->setValues($field2);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * @param $field
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function isNull($field, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setField($field)->isNull();
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;	
		return $this;
	}

    /**
     * @param $field
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function notNull($field, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setField($field)->notNull();
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;
		return $this;
	}

    /**
     * @param $field
     * @param $value
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function contains($field, $value, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setOperator('CONTAINS')->setField($field)->setValues($value);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;
		return $this;
	}

    /**
     * @param $field
     * @param $value
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function starts($field, $value, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setOperator('STARTS')->setField($field)->setValues($value);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;
		return $this;
	}

    /**
     * Creates a condition like field LIKE '%value';
     * @param string $field 
     * @param string $value 
     * @param bool|object $bean 
     * @return object
     */
    public function ends($field, $value, $bean = false) {
        $condition = new SugarQuery_Builder_Condition();
        $condition->setOperator('ENDS')->setField($field)->setValues($value);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * @param $field
     * @param array|SugarQuery $vals
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function in($field, $vals, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setOperator('IN')->setField($field)->setValues($vals);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;
		return $this;
	}

    /**
     * @param $field
     * @param $min
     * @param $max
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function between($field, $min, $max, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setOperator('BETWEEN')->setField($field)->setValues(array('min' => $min, 'max' => $max));
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;
		return $this;
	}

    /**
     * @param $field
     * @param $value
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function lt($field, $value, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setOperator('<')->setField($field)->setValues($value);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;		
		return $this;
	}

    /**
     * @param $field
     * @param $value
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function lte($field, $value, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setOperator('<=')->setField($field)->setValues($value);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;		
		return $this;
	}

    /**
     * @param $field
     * @param $value
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function gt($field, $value, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setOperator('>')->setField($field)->setValues($value);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;		
		return $this;
	}

    /**
     * @param $field
     * @param $value
     * @param bool $bean
     * @return SugarQuery_Builder_Where
     */
    public function gte($field, $value, $bean = false) {
		$condition = new SugarQuery_Builder_Condition();
		$condition->setOperator('>=')->setField($field)->setValues($value);
        if($bean instanceof SugarBean) {
            $condition->setBean($bean);
        }
		$this->conditions[] = $condition;		
		return $this;
	}

    /**
     * @param $sql
     */
    public function addRaw($sql) {
		$this->raw = $sql;
	}

    /**
     * @param $condition
     */
    public function add($condition) {
		$this->conditions[] = $condition;
	}

    /**
     * @return SugarQuery_Builder_Andwhere
     */
    public function queryAnd() {
		$where = new SugarQuery_Builder_Andwhere();
		$this->conditions[] = $where;
		return $where;
	}

    /**
     * @return SugarQuery_Builder_Orwhere
     */
    public function queryOr() {
		$where = new SugarQuery_Builder_Orwhere();
		$this->conditions[] = $where;
		return $where;
	}

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name) {
		return $this->$name;
	}

}