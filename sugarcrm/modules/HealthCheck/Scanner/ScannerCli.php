<?php
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

require_once __DIR__ . '/Scanner.php';

/**
 *
 * HealthCheck Scanner CLI support
 *
 */
class HealthCheckScannerCli extends HealthCheckScanner
{
    /**
     *
     * @param array $argv
     */
    public function parseCliArgs($argv)
    {
        for ($i = 1; $i < (count($argv) - 1); $i++) {

            // logfile name
            if ($argv[$i] == '-l') {
                $i++;
                $this->logfile = $argv[$i];
            }

            // verbose level 1
            if ($argv[$i] == '-v') {
                $this->verbose = 1;
            }

            // verbose level 2 (curently not used)
            if ($argv[$i] == '-vv') {
                $this->verbose = 2;
            }

            // instance directory
            $this->instance = $argv[count($argv) - 1];

            // generic properties
            if ($argv[$i] == '-d') {
                while (strpos($argv[++$i], '=')) {
                    list($property, $value) = $this->parsePropertyValuePair($argv[$i]);
                    if (property_exists($this, $property)) {
                        $this->$property = $value;
                    }
                }
            }
        }
    }

    /**
     * Consumes key=value string and returns key => value hash
     *
     * @param $string
     * @return array
     */
    protected function parsePropertyValuePair($string)
    {
        return array_map('trim', explode('=', $string));
    }

    /**
     *
     * Console output - temp solution, need proper central logging
     * @see Scanner::fail()
     */
    public function fail($msg)
    {
        $result = parent::fail($msg);
        echo "$msg\n";
        return $result;
    }

    /**
     *
     * Console output - temp solution, need proper central logging
     * @see Scanner::log()
     */
    protected function log($msg, $tag = 'INFO')
    {
        $fmsg = parent::log($msg, $tag);
        if ($this->verbose) {
            echo $fmsg;
        }
    }

    /**
     * @see HealthCheckScanner::init
     *
     * @return bool
     */
    protected function init()
    {
        if(!is_dir($this->instance)) {
            return $this->fail("{$this->instance} is not a directory");
        }
        $this->log("Initializing the environment");
        chdir($this->instance);
        if(!file_exists("include/entryPoint.php")) {
            return $this->fail("{$this->instance} is not a Sugar instance");
        }
        define('ENTRY_POINT_TYPE', 'api');
        global $beanFiles, $beanList, $objectList, $timedate, $moduleList, $modInvisList, $sugar_config, $locale,
               $sugar_version, $sugar_flavor, $sugar_build, $sugar_db_version, $sugar_timestamp, $db, $locale,
               $installing, $bwcModules, $app_list_strings, $modules_exempt_from_availability_check;
        if(!defined('sugarEntry'))define('sugarEntry', true);
        require_once('include/entryPoint.php');

        $GLOBALS['current_user'] = new BlackHole();

        return parent::init();
    }
}

/**
 *
 * Standalone CLI HealthCheck runner
 *
 */

if (empty($argv) || empty($argc) || $argc < 2) {
    die("Use php ScannerCli.php [-d property1=value1... property1=valueN] [-l logfile] [-v] /path/to/instance\n");
}

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    die("This is a command-line only script");
}

$scanner = new HealthCheckScannerCli();
$scanner->parseCliArgs($argv);
$scanner->scan();

exit($scanner->getResultCode());
