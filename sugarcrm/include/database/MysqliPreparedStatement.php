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

/*********************************************************************************

 * Description: This file handles the Data base functionality for prepared Statements
 * It acts as the prepared statement abstraction layer for the application.
 *
 * All the functions in this class will work with any bean which implements the meta interface.
 * The passed bean is passed to helper class which uses these functions to generate correct sql.
 *
 * The meta interface has the following functions:
 */
require_once 'include/database/PreparedStatement.php';

class MysqliPreparedStatement extends PreparedStatement
{

    /**
     * Place to bind query vars to
     * @var array
     */
    protected $bound_vars = array();

    public $ps_type_map = array(
        'int'      => 'i',
        'double'   => 'd',
        'float'    => 'd',
        'uint'     => 'i',
        'ulong'    => 'i',
        'long'     => 'd',
        'short'    => 'i',
        'varchar'  => 's',
        'text'     => 'b',
        'longtext' => 'b',
        'date'     => 'd',
        'enum'     => 's',
        'relate'   => 's',
        'multienum'=> 's',
        'html'     => 's',
        'longhtml' => 's',
        'datetime' => 's',
        'datetimecombo' => 's',
        'time'     => 'i',
        'bool'     => 'i',
        'tinyint'  => 'i',
        'char'     => 's',
        'blob'     => 'b',
        'longblob' => 'b',
        'currency' => 's',
        'decimal'  => 'd',
        'decimal2' => 'd',
        'id'       => 's',
        'url'      => 's',
        'encrypt'  => 's',
        'file'     => 's',
        'decimal_tpl' => 's',

    );



  /**
   * Tracks slow queries in the tracker database table
   *
   * @param resource $dblink   database resource to use
   * @param string   $sqlText  the sql statement to prepare
   * @param array    $data     1D array of data to match the positional params
   * @param array    fieldDefs field definitions
   *
   */
  public function preparePreparedStatement($sqlText, array $data, array $fieldDefs = array() ){

      echo "preparePreparedStatement: entry  sqlText: >$sqlText <  data:\n" ;
      var_dump($data);

      if (!($this->stmt = $this->dblink->prepare($sqlText))) {
          echo "preparePreparedStatement: Prepare Failed! \n";
          return "Prepare failed: (" . $this->dblink->errno . ") " . $this->dblink->error;
      }
      $num_args = $this->stmt->param_count;
      echo "preparePreparedStatement: num_args from prepare: $num_args \n";
      $this->bound_vars = $bound = array_fill(0, $num_args, null);
      $types = "";
      for($i=0; $i<$num_args;$i++) {
          $types .= $this->ps_type_map[ $fieldDefs[$i] ];
          $bound[$i] =& $this->bound_vars[$i];
      }
      echo "types: >$types<\n";
      array_unshift($bound, $types);

      echo "Binding the data: types then vars\n";
      var_dump($bound);
      // Pre-bind the internal data array to    $this->bound_vars
      call_user_func_array(array($this->stmt, "bind_param"), $bound);

      return $this;
  }




   public function executePreparedStatement($data){

      echo "--------------------------------------------------\n";
      echo "executePreparedStatement: entry    data:\n";
      var_dump($data);

      if ($this->stmt->param_count != count($data) )
          return "incorrect number of elements. Expected " . $this->stmt->param_count . " but got " . count($data);

      // transfer the data from the input array to the bound array
      for($i=0; $i<count($data);$i++) {
         $this->bound_vars[$i] = $data[$i];
      }

      if (!($res = $this->stmt->execute())) {
          return "Execute Prepared Statement failed: (" . $dblink->errno . ") " . $dblink->error;
      }

      return $this->stmt;
   }

}
