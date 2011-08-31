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
* $Id: SqlsrvManager.php 38098 2008-07-22 14:37:49Z jmertic $
* Description: This file handles the Data base functionality for the application.
* It acts as the DB abstraction layer for the application. It depends on helper classes
* which generate the necessary SQL. This sql is then passed to PEAR DB classes.
* The helper class is chosen in DBManagerFactory, which is driven by 'db_type' in 'dbconfig' under config.php.
*
* All the functions in this class will work with any bean which implements the meta interface.
* The passed bean is passed to helper class which uses these functions to generate correct sql.
*
* The meta interface has the following functions:
* getTableName()	        	Returns table name of the object.
* getFieldDefinitions()	    	Returns a collection of field definitions in order.
* getFieldDefintion(name)		Return field definition for the field.
* getFieldValue(name)	    	Returns the value of the field identified by name.
*                           	If the field is not set, the function will return boolean FALSE.
* getPrimaryFieldDefinition()	Returns the field definition for primary key
*
* The field definition is an array with the following keys:
*
* name 		This represents name of the field. This is a required field.
* type 		This represents type of the field. This is a required field and valid values are:
*      		int
*      		long
*      		varchar
*      		text
*      		date
*      		datetime
*      		double
*      		float
*      		uint
*      		ulong
*      		time
*      		short
*      		enum
* length	This is used only when the type is varchar and denotes the length of the string.
*  			The max value is 255.
* enumvals  This is a list of valid values for an enum separated by "|".
*			It is used only if the type is ?enum?;
* required	This field dictates whether it is a required value.
*			The default value is ?FALSE?.
* isPrimary	This field identifies the primary key of the table.
*			If none of the fields have this flag set to ?TRUE?,
*			the first field definition is assume to be the primary key.
*			Default value for this field is ?FALSE?.
* default	This field sets the default value for the field definition.
*
*
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
* All Rights Reserved.
* Contributor(s): ______________________________________..
********************************************************************************/

include_once('include/database/MssqlManager.php');

class SqlsrvManager extends MssqlManager
{
    public $dbName = 'SQL Server';
    public $variant = 'sqlsrv';
    public $priority = 10;
    public $label = 'LBL_MSSQL_SQLSRV';

    protected $capabilities = array(
        "affected_rows" => true,
        'fulltext' => true,
        'limit_subquery' => true,
    );

    protected $type_map = array(
            'int'      => 'int',
            'double'   => 'float',
            'float'    => 'float',
            'uint'     => 'int',
            'ulong'    => 'int',
            'long'     => 'bigint',
            'short'    => 'smallint',
            'varchar'  => 'nvarchar',
            'text'     => 'nvarchar(max)',
            'longtext' => 'nvarchar(max)',
            'date'     => 'datetime',
            'enum'     => 'nvarchar',
            'relate'   => 'nvarchar',
            'multienum'=> 'nvarchar(max)',
            'html'     => 'nvarchar(max)',
            'datetime' => 'datetime',
            'datetimecombo' => 'datetime',
            'time'     => 'datetime',
            'bool'     => 'bit',
            'tinyint'  => 'tinyint',
            'char'     => 'char',
            'blob'     => 'nvarchar(max)',
            'longblob' => 'nvarchar(max)',
            'currency' => 'decimal(26,6)',
            'decimal'  => 'decimal',
            'decimal2' => 'decimal',
            'id'       => 'varchar(36)',
            'url'      => 'nvarchar',
            'encrypt'  => 'nvarchar',
            'file'     => 'nvarchar',
	        'decimal_tpl' => 'decimal(%d, %d)',
    );

	/**
     * @see DBManager::connect()
     */
    public function connect(array $configOptions = null, $dieOnError = false)
    {
        global $sugar_config;

        if (is_null($configOptions))
            $configOptions = $sugar_config['dbconfig'];

        //set the connections parameters
        $connect_param = '';
        $configOptions['db_host_instance'] = trim($configOptions['db_host_instance']);
        if (empty($configOptions['db_host_instance']))
            $connect_param = $configOptions['db_host_name'];
        else
            $connect_param = $configOptions['db_host_name']."\\".$configOptions['db_host_instance'];

        /*
         * Don't try to specifically use a persistent connection
         * since the driver will handle that for us
         */
        $options = array(
                    "UID" => $configOptions['db_user_name'],
                    "PWD" => $configOptions['db_password'],
                    "CharacterSet" => "UTF-8",
                    "ReturnDatesAsStrings" => true,
                    "MultipleActiveResultSets" => true,
                    );
        if(!empty($configOptions['db_name'])) {
            $options["Database"] = $configOptions['db_name'];
        }
        $this->database = sqlsrv_connect($connect_param, $options);
        if(empty($this->database)) {
            $GLOBALS['log']->fatal("Could not connect to server ".$configOptions['db_host_name']." as ".$configOptions['db_user_name'].".");
            if($dieOnError) {
                    if(isset($GLOBALS['app_strings']['ERR_NO_DB'])) {
                        sugar_die($GLOBALS['app_strings']['ERR_NO_DB']);
                    } else {
                        sugar_die("Could not connect to the database. Please refer to sugarcrm.log for details.");
                    }
            } else {
                return false;
            }
        }

        if($this->checkError('Could Not Connect:', $dieOnError))
            $GLOBALS['log']->info("connected to db");

        sqlsrv_query($this->database, 'SET DATEFORMAT mdy');

        $this->connectOptions = $configOptions;

        $GLOBALS['log']->info("Connect:".$this->database);
        return true;
    }

	/**
     * @see DBManager::query()
	 */
	public function query($sql, $dieOnError = false, $msg = '', $suppress = false, $keepResult = false)
    {
        if(is_array($sql)) {
            return $this->queryArray($sql, $dieOnError, $msg, $suppress);
        }
        $sql = $this->_appendN($sql);

        $this->countQuery($sql);
        $GLOBALS['log']->info('Query:' . $sql);
        $this->checkConnection();
        $this->query_time = microtime(true);

        $result = $suppress?@sqlsrv_query($this->database, $sql):sqlsrv_query($this->database, $sql);

        $this->query_time = microtime(true) - $this->query_time;
        $GLOBALS['log']->info('Query Execution Time:'.$this->query_time);

        //BEGIN SUGARCRM flav=pro ONLY
        if($this->dump_slow_queries($sql)) {
            $this->track_slow_queries($sql);
        }
        //END SUGARCRM flav=pro ONLY

        $this->checkError($msg.' Query Failed:' . $sql . '::', $dieOnError);

        //suppress non error messages
        sqlsrv_configure('WarningsReturnAsErrors',false);

        return $result;
    }

	/**
     * @see DBManager::getFieldsArray()
     */
	public function getFieldsArray($result, $make_lower_case = false)
	{
        $field_array = array();

        if ( !$result ) {
        	return false;
        }

        foreach ( sqlsrv_field_metadata($result) as $fieldMetadata ) {
            $key = $fieldMetadata['Name'];
            if($make_lower_case==true)
                $key = strtolower($key);

            $field_array[] = $key;
        }

        return $field_array;
	}

    /**
     * @see DBManager::fetchByAssoc()
     */
    public function fetchByAssoc($result, $rowNum = -1, $encode = true)
    {
        if (!$result) {
            return false;
        }

        $row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
        if (empty($row)) {
            return false;
        }

        foreach($row as $key => $column) {
            // MSSQL returns a space " " when a varchar column is empty ("") and not null.
            // We need to strip empty spaces
            // notice we only strip if one space is returned.  we do not want to strip
            // strings with intentional spaces (" foo ")
            if (!empty($column) && $column ==" ") {
                $row[$key] = '';
            }
            // Strip off the extra .000 off of datetime fields
            $matches = array();
            preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}).[0-9]{3}$/',$column,$matches);
            if ( !empty($matches) && !empty($matches[1]) ) {
                $row[$key] = $matches[1];
            }
            // HTML encode if needed
            if($encode && $this->encode) {
                $row[$key] = to_html($row[$key]);
            }
        }

        return $row;
	}

    /**
     * @see DBManager::convert()
     */
    public function convert($string, $type, array $additional_parameters = array())
    {
        if ( $type == 'datetime')
            return "CONVERT(varchar(25),$string,120)";
        else
            return parent::convert($string, $type, $additional_parameters);
    }

	/**
     * Compares two vardefs. Overriding 39098  due to bug: 39098 . IN 6.0 we changed the id columns to dbType = 'id'
     * for example emails_beans.  In 554 the field email_id was nvarchar but in 6.0 since it id dbType = 'id' we would want to alter
     * it to varchar. This code will prevent it.
     *
     * @param  array  $fielddef1
     * @param  array  $fielddef2
     * @return bool   true if they match, false if they don't
     */
    public function compareVarDefs($fielddef1,$fielddef2)
    {
        if((isset($fielddef2['dbType']) && $fielddef2['dbType'] == 'id') || preg_match('/(_id$|^id$)/', $fielddef2['name'])){
            if(isset($fielddef1['type']) && isset($fielddef2['type'])){
                $fielddef2['type'] = $fielddef1['type'];
            }
        }
        return parent::compareVarDefs($fielddef1, $fielddef2);
    }

    /**
     * Disconnects from the database
     *
     * Also handles any cleanup needed
     */
    public function disconnect()
    {
    	$GLOBALS['log']->debug('Calling Mssql::disconnect()');
        if(!empty($this->database)){
            $this->freeResult();
            sqlsrv_close($this->database);
            $this->database = null;
        }
    }

    /**
     * @see DBManager::freeDbResult()
     */
    protected function freeDbResult($dbResult)
    {
        if(!empty($dbResult))
            sqlsrv_free_stmt($dbResult);
    }


	/**
	 * Detect if no clustered index has been created for a table; if none created then just pick the first index and make it that
	 *
	 * @see MssqlHelper::indexSQL()
     */
    public function getConstraintSql($indices, $table)
    {
        if ( $this->doesTableHaveAClusteredIndexDefined($table) ) {
            return parent::getConstraintSql($indices, $table);
        }

        // check to see if one of the passed in indices is a primary one; if so we can bail as well
        foreach ( $indices as $index ) {
            if ( $index['type'] == 'primary' ) {
                return parent::getConstraintSql($indices, $table);
            }
        }

        // Change the first index listed to be a clustered one instead ( so we have at least one for the table )
        if ( isset($indices[0]) ) {
            $indices[0]['type'] = 'clustered';
        }

        return parent::getConstraintSql($indices, $table);
    }

    /**
     * @see DBHelper::get_columns()
     */
    public function get_columns($tablename)
    {
        //find all unique indexes and primary keys.
        $result = $this->query("sp_columns_90 $tablename");

        $columns = array();
        while (($row=$this->fetchByAssoc($result)) !=null) {
            $column_name = strtolower($row['COLUMN_NAME']);
            $columns[$column_name]['name']=$column_name;
            $columns[$column_name]['type']=strtolower($row['TYPE_NAME']);
            if ( $row['TYPE_NAME'] == 'decimal' ) {
                $columns[$column_name]['len']=strtolower($row['PRECISION']);
                $columns[$column_name]['len'].=','.strtolower($row['SCALE']);
            }
			elseif ( in_array($row['TYPE_NAME'],array('nchar','nvarchar')) ) {
				$columns[$column_name]['len']=strtolower($row['PRECISION']);
				if ( $row['TYPE_NAME'] == 'nvarchar' && $row['PRECISION'] == '0' ) {
				    $columns[$column_name]['len']='max';
				}
			}
            elseif ( !in_array($row['TYPE_NAME'],array('datetime','text')) ) {
                $columns[$column_name]['len']=strtolower($row['LENGTH']);
            }
            if ( stristr($row['TYPE_NAME'],'identity') ) {
                $columns[$column_name]['auto_increment'] = '1';
                $columns[$column_name]['type']=str_replace(' identity','',strtolower($row['TYPE_NAME']));
            }

            if (!empty($row['IS_NULLABLE']) && $row['IS_NULLABLE'] == 'NO' && (empty($row['KEY']) || !stristr($row['KEY'],'PRI')))
                $columns[strtolower($row['COLUMN_NAME'])]['required'] = 'true';

            $column_def = 1;
            if ( strtolower($tablename) == 'relationships' ) {
                $column_def = $this->getOne("select cdefault from syscolumns where id = object_id('relationships') and name = '$column_name'");
            }
            if ( $column_def != 0 && ($row['COLUMN_DEF'] != null)) {	// NOTE Not using !empty as an empty string may be a viable default value.
                $matches = array();
                $row['COLUMN_DEF'] = html_entity_decode($row['COLUMN_DEF'],ENT_QUOTES);
                if ( preg_match('/\([\(|\'](.*)[\)|\']\)/i',$row['COLUMN_DEF'],$matches) )
                    $columns[$column_name]['default'] = $matches[1];
                elseif ( preg_match('/\(N\'(.*)\'\)/i',$row['COLUMN_DEF'],$matches) )
                    $columns[$column_name]['default'] = $matches[1];
                else
                    $columns[$column_name]['default'] = $row['COLUMN_DEF'];
            }
        }
        return $columns;
    }

    /**
     * @see DBHelper::get_indices()
     */
    public function get_indices($tableName)
    {
        //find all unique indexes and primary keys.
        $query = <<<EOSQL
SELECT sys.tables.object_id, sys.tables.name as table_name, sys.columns.name as column_name,
        sys.indexes.name as index_name, sys.indexes.is_unique, sys.indexes.is_primary_key
    FROM sys.tables, sys.indexes, sys.index_columns, sys.columns
    WHERE (sys.tables.object_id = sys.indexes.object_id
            AND sys.tables.object_id = sys.index_columns.object_id
            AND sys.tables.object_id = sys.columns.object_id
            AND sys.indexes.index_id = sys.index_columns.index_id
            AND sys.index_columns.column_id = sys.columns.column_id)
        AND sys.tables.name = '$tableName'
EOSQL;
        $result = $this->query($query);

        $indices = array();
        while (($row=$this->fetchByAssoc($result)) != null) {
            $index_type = 'index';
            if ($row['is_primary_key'] == '1')
                $index_type = 'primary';
            elseif ($row['is_unique'] == 1 )
                $index_type = 'unique';
            $name = strtolower($row['index_name']);
            $indices[$name]['name']     = $name;
            $indices[$name]['type']     = $index_type;
            $indices[$name]['fields'][] = strtolower($row['column_name']);
        }
        return $indices;
    }

    /**
     * protected function to return true if the given tablename has any clustered indexes defined.
     *
     * @param  string $tableName
     * @return bool
     */
    protected function doesTableHaveAClusteredIndexDefined($tableName)
    {
        $query = <<<EOSQL
SELECT IST.TABLE_NAME
    FROM INFORMATION_SCHEMA.TABLES IST
    WHERE objectProperty(object_id(IST.TABLE_NAME), 'IsUserTable') = 1
        AND objectProperty(object_id(IST.TABLE_NAME), 'TableHasClustIndex') = 1
        AND IST.TABLE_NAME = '{$tableName}'
EOSQL;

        $result = $this->getOne($query);
        if ( !$result ) {
            return false;
        }

        return true;
    }

    /**
     * protected function to return true if the given tablename has any fulltext indexes defined.
     *
     * @param  string $tableName
     * @return bool
     */
    protected function doesTableHaveAFulltextIndexDefined($tableName)
    {
        $query = <<<EOSQL
SELECT 1
    FROM sys.fulltext_indexes i
        JOIN sys.objects o ON i.object_id = o.object_id
    WHERE o.name = '{$tableName}'
EOSQL;

        $result = $this->getOne($query);
        if ( !$result ) {
            return false;
        }

        return true;
    }

    /**
     * Override method to add support for detecting and dropping fulltext indices.
     *
     * @see DBHelper::changeColumnSQL()
     * @see MssqlHelper::changeColumnSQL()
     */
    protected function changeColumnSQL($tablename,$fieldDefs, $action, $ignoreRequired = false)
    {
        $sql = '';
        if ( $action == 'drop' && $this->doesTableHaveAFulltextIndexDefined($tablename) ) {
            $sql .= "DROP FULLTEXT INDEX ON {$tablename}";
        }

        $sql .= parent::changeColumnSQL($tablename, $fieldDefs, $action, $ignoreRequired);

        return $sql;
    }

    /**
     * Returns the number of rows returned by the result
     *
     * @param  resource $result
     * @return int
     */
     public function getRowCount($result)
    {
		return 0;
	}

	/**
	 * (non-PHPdoc)
	 * @see DBManager::lastDbError()
	 */
    public function lastDbError()
    {
        $errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);
        if(empty($errors)) return false;
        global $app_strings;
        if (empty($app_strings)
		    or !isset($app_strings['ERR_MSSQL_DB_CONTEXT'])
			or !isset($app_strings['ERR_MSSQL_WARNING']) ) {
        //ignore the message from sql-server if $app_strings array is empty. This will happen
        //only if connection if made before languge is set.
		    return false;
        }
        $messages = array();
        foreach($errors as $error) {
            $sqlmsg = $error['message'];
            $sqlpos = strpos($sqlmsg, 'Changed database context to');
            $sqlpos2 = strpos($sqlmsg, 'Warning:');
            $sqlpos3 = strpos($sqlmsg, 'Checking identity information:');
            if ( $sqlpos !== false || $sqlpos2 !== false || $sqlpos3 !== false ) {
                continue;
            }
            $sqlpos = strpos($sqlmsg, $app_strings['ERR_MSSQL_DB_CONTEXT']);
            $sqlpos2 = strpos($sqlmsg, $app_strings['ERR_MSSQL_WARNING']);
    		if ( $sqlpos !== false || $sqlpos2 !== false) {
                    continue;
            }
            $messages[] = $sqlmsg;
        }

        if(!empty($messages)) {
            return join("\n", $messages);
        }
        return false;
    }

    public function getDbInfo()
    {
        $info = array_merge(sqlsrv_client_info(), sqlsrv_server_info());
        return $info;
    }

    /**
     * Execute data manipulation statement, then roll it back
     * @param  $type
     * @param  $table
     * @param  $query
     * @return string
     */
    protected function verifyGenericQueryRollback($type, $table, $query)
    {
        $this->log->debug("verifying $type statement");
        if(!sqlsrv_begin_transaction($this->database)) {
            return "Failed to create transaction";
        }
        $this->query($query, false);
        $error = $this->lastError();
        sqlsrv_rollback($this->database);
        return $error;
    }

    /**
     * Tests an INSERT INTO query
     * @param string table The table name to get DDL
     * @param string query The query to test.
     * @return string Non-empty if error found
     */
    public function verifyInsertInto($table, $query)
    {
        return $this->verifyGenericQueryRollback("INSERT", $table, $query);
    }

    /**
     * Tests an UPDATE query
     * @param string table The table name to get DDL
     * @param string query The query to test.
     * @return string Non-empty if error found
     */
    public function verifyUpdate($table, $query)
    {
        return $this->verifyGenericQueryRollback("UPDATE", $table, $query);
    }

    /**
     * Tests an DELETE FROM query
     * @param string table The table name to get DDL
     * @param string query The query to test.
     * @return string Non-empty if error found
     */
    public function verifyDeleteFrom($table, $query)
    {
        return $this->verifyGenericQueryRollback("DELETE", $table, $query);
    }

    /**
     * Select database
     * @param string $dbname
     */
    protected function selectDb($dbname)
    {
        return $this->query("USE ".$this->quoted($dbname));
    }

    /**
     * Check if this driver can be used
     * @return bool
     */
    public function valid()
    {
        return function_exists("sqlsrv_connect");
    }
}
