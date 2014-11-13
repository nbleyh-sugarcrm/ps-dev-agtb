<?php
/**
 * Class that analyzes the data type of a bean
 * getting the value of this field according to the data type
 * if there is a date data type used the classes TimeDate()
 *
 */
class PMSEFieldParser implements PMSEDataParserInterface
{
    /**
     * Object Bean
     * @var object
     */
    private $evaluatedBean;

    /**
     * Lists modules Bean
     * @var array
     */
    private $beanList;
    private $currentUser;

    /**
     * gets the bean list
     * @return array
     * @codeCoverageIgnore
     */
    public function getBeanList()
    {
        return $this->beanList;
    }

    /**
     * sets the bean list
     * @param array $beanList
     */
    public function setBeanList($beanList)
    {
        $this->beanList = $beanList;
    }

    /**
     * gets the bean
     * @return object
     * @codeCoverageIgnore
     */
    public function getEvaluatedBean()
    {
        return $this->evaluatedBean;
    }

    /**
     * sets the bean
     * @param object $evaluatedBean
     */
    public function setEvaluatedBean($evaluatedBean)
    {
        $this->evaluatedBean = $evaluatedBean;
    }

    /**
     * sets the current user
     * @param object $currentUser
     * @codeCoverageIgnore
     */
    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * get the class TimeDate()
     * @return object
     * @codeCoverageIgnore
     */
    public function getTimeDate ()
    {
        if (!isset($this->timeDate) || empty($this->timeDate)){
            $this->timeDate = new TimeDate();
        }
        return $this->timeDate;
    }

    /**
     * set the class TimeDate()
     * @param object $timeDate
     * @codeCoverageIgnore
     */
    public function setTimeDate ($timeDate)
    {
        $this->timeDate = $timeDate;
    }

    /**
     * Parser token incorporando el tipo de dato, en el caso de tipo de dato date, datetime se usa la clase TimeDate
     * @global object $current_user cuurrent user
     * @param object $criteriaToken token to be parsed
     * @param array $params
     * @return object
     */
    public function parseCriteriaToken($criteriaToken, $params =array())
    {
        switch ($criteriaToken->expOperator) {
            case 'equals':
                $delimiter = '==';
                break;
            case 'not_equals':
                $delimiter = '!=';
                break;
            case 'major_equals_than':
                $delimiter = '>=';
                break;
            case 'minor_equals_than':
                $delimiter = '<=';
                break;
            case 'minor_than':
                $delimiter = '<';
                break;
            case 'major_than':
                $delimiter = '>';
                break;
            case 'within':
                $delimiter = 'within';
                break;
            case 'not_within':
                $delimiter = 'not within';
                break;
            default:
                $delimiter = '==';
                break;
        }

        //$tokenValueArray = explode($delimiter, $criteriaToken->expLabel);
        $tokenDelimiter = '::';
        $newTokenArray = array('{','future',$criteriaToken->expModule,$criteriaToken->expField,'}');
        $assembledTokenString = implode($tokenDelimiter, $newTokenArray);
        $tokenValue = $this->parseTokenValue($assembledTokenString);
        $criteriaToken->expToken = $assembledTokenString;
        $criteriaToken->currentValue = $tokenValue;
        if($this->evaluatedBean->field_name_map[$criteriaToken->expField]['type']=='date') {
            $criteriaToken->expFieldType= 'date';
        } elseif($this->evaluatedBean->field_name_map[$criteriaToken->expField]['type']=='datetime' || $this->evaluatedBean->field_name_map[$criteriaToken->expField]['type']=='datetimecombo') {
            $criteriaToken->expFieldType= 'date';
            global $current_user;
            // Instantiate the TimeDate Class
            $timeDate = $this->getTimeDate();//new TimeDate();
            // Call the function
            $localDate = $timeDate->to_display_date_time($tokenValue, true, true, $current_user);
            $criteriaToken->currentValue = $localDate;
        }
        return $criteriaToken;
    }

    /**
     * parser a token for a field element, is this: bool or custom fields
     * @param string $token field contains a parser
     * @return string field value
     */
    public function parseTokenValue($token)
    {
        $tokenArray = $this->decomposeToken($token);
        $all = array();
        
        if ($this->evaluatedBean->parent_type == $tokenArray[1]) {
            $bean = BeanFactory::retrieveBean($this->evaluatedBean->parent_type, $this->evaluatedBean->parent_id);
            $all[] = $this->evaluatedBean;
        } else {
            $bean = $this->evaluatedBean;
        }
        
        $value = '';
        $isAValidBean = true;
        if (!empty($tokenArray)) {
            $status = $tokenArray[0];
            $module = isset($this->beanList[$tokenArray[1]])?$tokenArray[1]:'';
            if ($module == ''){// @codeCoverageIgnoreStart
                $relationships = new DeployedRelationships($bean->module_name);
                $rel_module = $relationships->get($tokenArray[1])->getDefinition();
                $conditionModule = strtolower($rel_module['rhs_module']);
                $join_key_b = strtolower($rel_module['join_key_rhs']);
                
                
                if ($bean->load_relationship($conditionModule)) {
                    //Normal Related
                    $relatedField = $rel_module['rhs_table'];
                    $relationship = $bean->$relatedField;
                    reset($relationship->rows);
                    $id = key($relationship->rows);
                    if (isset($id) && !empty($id)) {
                        $all = array(BeanFactory::retrieveBean($rel_module['rhs_module'], $id));
                    } else {
                        $all = array();
                    }
                } else {
                    //Custom related
                    global $db;
                    $join_key_a = strtolower($rel_module['join_key_lhs']);
                    $query = "select * from $tokenArray[1]_c where $join_key_a = '" . $bean->id . "'";
                    $result = $db->Query($query);
                    $row = $db->fetchByAssoc($result);
                    $moduleBean = BeanFactory::getBean($rel_module['rhs_module'], $row[$join_key_b]);
                    $all = $moduleBean->get_full_list('date_entered');
                }
            }// @codeCoverageIgnoreEnd
            $field  = $tokenArray[2];
        }
        $isAValidBean = (trim($module) == trim($bean->module_name));
        $isBoolean = ('bool' == $bean->field_name_map[$field]['type'])?true:false;
        if ($isAValidBean) {
            $value = $bean->$field;
            if ($isBoolean) {
                $value = ($value==1)?'Yes':'No';
            }
        } else {
            $value = !empty($all)?array_pop($all)->$tokenArray[2]:NULL;
        }
        return $value;
    }

    /**
     * converts a string {:: future :: Users :: id ::} to an array ('future','Users','id')
     * @param string $token @example {:: future :: Users :: id ::}
     * @return array
     */
    public function decomposeToken($token)
    {
        $response = array();
        $tokenArray = explode('::', $token);
        foreach ($tokenArray as $key => $value) {
            if ($value!='{' && $value!='}' && !empty($value)) {
                $response[] = $value;
            }
        }
        return $response;
    }
}
