<?php
/*********************************************************************************
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (“MSA”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright (C) 2004-2014 SugarCRM Inc.  All rights reserved.
 ********************************************************************************/


class HealthCheck 
{
    // plain vanilla sugar
    const VANILLA = 'A';
    // studio mods
    const STUDIO = 'B';
    // studio and MB mods
    const STUDIO_MB = 'C';
    // studio and MB mods that need to BWC some modules
    const STUDIO_MB_BWC = 'D'; 
    // heavy customization, needs fixes
    const CUSTOM = 'E';
    // manual customization required
    const MANUAL = 'F';
    // already on 7
    const UPGRADED = 'G';
    
    // failure status
    const FAIL = 99;


    private  $errorCodes = array(
        // B
        'hasStudioHistory' => 101,
        'hasExtensions' => 102,
        'hasCustomVardefs' => 103,
        'hasCustomLayoutdefs' => 104,
        'hasCustomViewdefs' => 105,
        // C
        'notStockModule' => 201,
        // D
        'toBeRunAsBWC' => 301,
        'unknownFileViews' => 302,
        'nonEmptyFormFile' => 303,
        'isNotMBModule' => 304,
        'badVardefsKey' => 305,
        'badVardefsRelate' => 306,
        'badVardefsLink' => 307,
        'vardefHtmlFunction' => 308,
        'badMd5' => 309,
        'unknownFile' => 310,
        // E
        'vendorFilesInclusion' => 401,
        'badModule' => 402,
        'logicHookAfterUIFrame' => 403,
        'logicHookAfterUIFooter' => 404,
        'incompatIntegration' => 405,
        'hasCustomViews' => 406,
        'hasCustomViewsModDir' => 407,
        'extensionDir' => 408,
        'foundCustomCode' => 409,
        'maxFieldsView' => 410,
        'subPanelWithFunction' => 411,
        'badSubpanelLink' => 412,
        'unknownWidgetClass' => 413,
        'unknownField' => 414,
        'badHookFile' => 415,
        'byRefInHookFile' => 416,
        'incompatModule' => 417,
        'subpanelLinkNonExistModule' => 418,
        'badVardefsKeyCustom' => 419,
        'badVardefsRelateCustom' => 420,
        'badVardefsLinkCustom' => 421,
        'vardefHtmlFunctionCustom' => 422,
        'badVardefsCustom' => 423,
        'inlineHtmlCustom' => 424,
        'foundEchoCustom' => 425,
        'foundPrintCustom' => 426,
        'foundDieExitCustom' => 427,
        'foundPrintRCustom' => 428,
        'foundVarDumpCustom' => 429,
        'foundOutputBufferingCustom' => 430,
        // F
        'missingFile' => 501,
        'md5Mismatch' => 502,
        'sameModuleName' => 503,
        'fieldTypeMissing' => 504,
        'typeChange' => 505,
        'thisUsage' => 506,
        'badVardefs' => 507,
        'inlineHtml' => 508,
        'foundEcho' => 509,
        'foundPrint' => 510,
        'foundDieExit' => 511,
        'foundPrintR' => 512,
        'foundVarDump' => 513,
        'foundOutputBuffering' => 514,
    );

    private $errorBuckets = array(
        // B
        'hasStudioHistory' => self::STUDIO,
        'hasExtensions' => self::STUDIO,
        'hasCustomVardefs' => self::STUDIO,
        'hasCustomLayoutdefs' => self::STUDIO,
        'hasCustomViewdefs' => self::STUDIO,
        // C
        'notStockModule' => self::STUDIO_MB,
        // D
        'toBeRunAsBWC' => self::STUDIO_MB_BWC,
        'unknownFileViews' => self::STUDIO_MB_BWC,
        'nonEmptyFormFile' => self::STUDIO_MB_BWC,
        'isNotMBModule' => self::STUDIO_MB_BWC,
        'badVardefsKey' => self::STUDIO_MB_BWC,
        'badVardefsRelate' => self::STUDIO_MB_BWC,
        'badVardefsLink' => self::STUDIO_MB_BWC,
        'vardefHtmlFunction' => self::STUDIO_MB_BWC,
        'badMd5' => self::STUDIO_MB_BWC,
        'unknownFile' => self::STUDIO_MB_BWC,
        // E
        'vendorFilesInclusion' => self::CUSTOM,
        'badModule' => self::CUSTOM,
        'logicHookAfterUIFrame' => self::CUSTOM,
        'logicHookAfterUIFooter' => self::CUSTOM,
        'incompatIntegration' => self::CUSTOM,
        'hasCustomViews' => self::CUSTOM,
        'hasCustomViewsModDir' => self::CUSTOM,
        'extensionDir' => self::CUSTOM,
        'foundCustomCode' => self::CUSTOM,
        'maxFieldsView' => self::CUSTOM,
        'subPanelWithFunction' => self::CUSTOM,
        'badSubpanelLink' => self::CUSTOM,
        'unknownWidgetClass' => self::CUSTOM,
        'unknownField' => self::CUSTOM,
        'badHookFile' => self::CUSTOM,
        'byRefInHookFile' => self::CUSTOM,
        'incompatModule' => self::CUSTOM,
        'subpanelLinkNonExistModule' => self::CUSTOM,
        'badVardefsKeyCustom' => self::CUSTOM,
        'badVardefsRelateCustom' => self::CUSTOM,
        'badVardefsLinkCustom' => self::CUSTOM,
        'vardefHtmlFunctionCustom' => self::CUSTOM,
        'badVardefsCustom' => self::CUSTOM,
        'inlineHtmlCustom' => self::CUSTOM,
        'foundEchoCustom' => self::CUSTOM,
        'foundPrintCustom' => self::CUSTOM,
        'foundDieExitCustom' => self::CUSTOM,
        'foundPrintRCustom' => self::CUSTOM,
        'foundVarDumpCustom' => self::CUSTOM,
        'foundOutputBufferingCustom' => self::CUSTOM,
        // F
        'missingFile' => self::MANUAL,
        'md5Mismatch' => self::MANUAL,
        'sameModuleName' => self::MANUAL,
        'fieldTypeMissing' => self::MANUAL,
        'typeChange' => self::MANUAL,
        'thisUsage' => self::MANUAL,
        'badVardefs' => self::MANUAL,
        'inlineHtml' => self::MANUAL,
        'foundEcho' => self::MANUAL,
        'foundPrint' => self::MANUAL,
        'foundDieExit' => self::MANUAL,
        'foundPrintR' => self::MANUAL,
        'foundVarDump' => self::MANUAL,
        'foundOutputBuffering' => self::MANUAL,
    );

    
    protected $instance;
    /**
     * DB connection to Sugar
     * @var DBManager
     */
    protected $db;
    protected $logfile = "sortinghat.log";
    public $verbose = false;
    protected $exit_status = 0;
    protected $ping_url = 'http://sortinghat-sugarcrm.rhcloud.com/feedback';

    /**
     * @var array List of packages with compatible versions to check.
     */
    protected $packages = array(
        'Zendesk' => '2.8',
        'Act-On Integrated Marketing Automation for SugarCRM' => '*',
        'Admin Sandbox' => '*',
        'Pardot Marketing Automation for SugarCRM' => '*',
        'iNetMaps' => '*',
        'Sugar-Constant Contact Integration' => '*',
        'Adobe EchoSign e-Signatures for SugarCRM' => '*',
        'DocuSign for SugarCRM' => '*',
        'FBSG SugarCRM QuickBooks Integration' => '*',
        'JJWDesign_Google_Maps' => '*',
        'Dashboard Manager' => '*',
        'wSQL Admin' => '*',
    );

    /**
     * @var array List of unsupported modules.
     */
    protected $unsupportedModules = array(
        'Feeds',
        'iFrames'
    );
    /**
     * Number of fields on detail/editview to trigger class E
     */
    protected $fieldCountMax = 100;

    /**
     * Number of fields on detail/editview to trigger a warning
     */
    protected $fieldCountWarn = 50;
    
    /**
     * Instance status
     * @var int
     */
    public $status = self::VANILLA;
    
    protected $status_log = array();
    
    /**
     * Log message
     * @param string $msg
     */
    protected function log($msg, $tag = 'INFO')
    {
        $fmsg = sprintf("[%s] %s %s\n", date('c'), $tag, $msg);
        if($this->verbose > 1) {
            echo $fmsg;
        }
        if(empty($this->fp)) {
            $this->fp = @fopen($this->logfile, 'a+');
        }
        if(empty($this->fp)) {
            die("Cannot open logfile: $this->logfile");
        }
    
        fwrite($this->fp, $fmsg);
    }
    
    /**
     * Script failure
     * @param string $msg
     * @return false
     */
    public function fail($msg)
    {
        $this->exit_status = self::FAIL;
        $this->updateStatus(self::MANUAL, $msg);
        $this->log($msg, 'ERROR');
        echo "$msg\n";
        return false;
    }

    
    /**
     * Add reason to stats log
     * @param string $status
     * @param string $reason
     */
    protected function logReason($status, $key, $code, $reason)
    {
        $lines = explode("\n", $reason, 2);
        $this->status_log[$status][] = array(
            'key' => $key,
            'code' => $code,
            'reason' => $reason
        );
    }
    
    /**
     * If current status is lower that this, raise it
     * @param string $status
     */
    public function updateStatus()
    {
        global $mod_strings;
        $params = func_get_args();
        $key = array_shift($params);
        $status = $this->errorBuckets[$key];
        $code   = $this->errorCodes[$key];
        $reason = "[$key][$code] " . vsprintf($mod_strings[$key], $params);
        if($reason) {
            $this->log($reason, 'CHECK-'.$status);
            $this->logReason($status, $key, $code, $reason);
        }
        if($status > $this->status) {
            $this->log("===> Status changed to $status", 'STATUS');
            $this->status = $status;
        }
    }
    
    protected function parseArgs($argv)
    {
        for($i=0;$i<count($argv)-1;$i++) {
            if($argv[$i] == '-l') {
                $i++;
                $this->logfile = $argv[$i];
            }
            if($argv[$i] == '-v') {
                $this->verbose = true;
            }
            if($argv[$i] == '-vv') {
                $this->verbose = 2;
            }
        }
        $this->instance = $argv[count($argv)-1];          
    }
    
    public function scan($argv) 
    {
        $this->parseArgs($argv);
        set_error_handler(array($this, 'scriptErrorHandler'), E_ALL & ~E_STRICT & ~E_DEPRECATED);
        $this->log("Starting scanning $this->instance");
        if(!$this->init()) {
            return;
        }

        $sugar_version = '9.9.9';
        $sugar_flavor = 'unknown';
        include "sugar_version.php";
        $this->log("Instance version: $sugar_version");
        $this->log("Instance flavor: $sugar_flavor");

        if(version_compare($sugar_version, '7.0', '>')) {
            $this->updateStatus(self::UPGRADED, "Instance already upgraded to 7");
            $this->log("Instance already upgraded to 7");
            return;
        }

        if($GLOBALS['sugar_config']['site_url']) {
            $this->ping(array("instance" => $GLOBALS['sugar_config']['site_url'], "version" => $sugar_version));
        }

        $this->listUpgrades();
        $this->checkPackages();
        $this->checkLanguageFiles();
        $this->checkVendorFiles();
        if(!empty($this->filesToFix))
        {
            $files_to_fix = implode("\r\n", $this->filesToFix);
            $this->updateStatus("vendorFilesInclusion", $files_to_fix);
        }
        
        // check non-upgrade-safe customizations by verifying md5's
        $this->log("Comparing md5 sums");
        $skip_prefixes = "#^[.]/(custom/|cache/|tmp/|temp/|upload/|config|examples/|[.]htaccess|sugarcrm[.]log|/language/|)#";
        foreach($this->md5_files as $file => $sum) {
            if(preg_match($skip_prefixes, $file)) {
                continue; 
            }
            if(!file_exists($file)) {
                $this->updateStatus("missingFile", $file);
            }
            if(md5_file($file) !== $sum) {
                $this->updateStatus("md5Mismatch", $file, $sum);
            }
        }
        
        foreach($this->getModuleList() as $module) {
            $this->log("Checking module $module");
            $this->scanModule($module);
        }  
        
        // checking app_list_strings for weird entries
        // FIXME: can not do this check yet because we have entries like FAQ and Newsletter in moduleList
//         $app_list = return_app_list_strings_language('en_us');
//         foreach($app_list['moduleList'] as $module => $name) {
//             if(empty($this->beanList[$module]) && !file_exists("modules/$module")) {
//                 $this->log("Bad module $module - not in beanList and not in filesystem");
//                 $this->updateStatus(self::CUSTOM);
//             }
//         }
        
        
        // Check global hooks
        $this->log("Checking global hooks");
        $hook_files = array();
        $this->extractHooks("custom/modules/logic_hooks.php", $hook_files);
        $this->extractHooks("custom/application/Ext/LogicHooks/logichooks.ext.php", $hook_files);
        if(!empty($hook_files['after_ui_footer'])) {
            $this->updateStatus("logicHookAfterUIFooter");
        }
        if(!empty($hook_files['after_ui_frame'])) {
            $this->updateStatus("logicHookAfterUIFrame");
        }
        foreach($hook_files as $hookname => $hooks) {
            foreach($hooks as $hook_data) {
                $this->log("Checking global hook $hookname:{$hook_data[1]}");
                $this->checkFileForOutput($hook_data[2], self::CUSTOM);
            }
        }
        // TODO: custom dashlets
        $this->log("VERDICT: {$this->status}", 'STATUS');
        if($GLOBALS['sugar_config']['site_url']) {
            $this->ping(array("instance" => $GLOBALS['sugar_config']['site_url'], "verdict" => $this->status));
        }
        
        ksort($this->status_log);
        foreach($this->status_log as $status => $items) {
            $this->log("=> $status: ".count($items)." total", 'BUCKET');
            foreach($items as $item) {
                $this->log(sprintf("=> %s: [%s][%s] %s", $status, $item['key'], $item['code'], $item['reason']), 'BUCKET');
            }
        }

        return $this->status_log;
    }

    /**
     * Loads all language files with customizations and overrides
     *
     * @see CRYS-130
     */
    protected function checkLanguageFiles()
    {
        if (!empty($GLOBALS['sugar_config']['languages'])) {

            foreach ($GLOBALS['sugar_config']['languages'] as $key => $lang) {
                return_application_language($key);
            }
        }
    }

    /**
     * Checks for unsupported installed packages.
     */
    protected function checkPackages()
    {
        require_once 'ModuleInstall/PackageManager/PackageManager.php';

        $this->log("Checking packages");
        $pm = new PackageManager();
        $packages = $pm->getinstalledPackages(array('module'));
        foreach ($packages as $pack) {
            if($pack['enabled'] == 'DISABLED') {
                $this->log("Disabled package {$pack['name']} (version {$pack['version']}) detected");
                continue;
            }
            $this->log("Package {$pack['name']} (version {$pack['version']}) detected");
            if (array_key_exists($pack['name'], $this->packages) &&
                (
                    $this->packages[$pack['name']] == '*' ||
                    version_compare($pack['version'], $this->packages[$pack['name']], '<')
                )
            ) {
                $this->updateStatus("incompatIntegration", $pack['name'], $pack['version']);
            }
        }
    }
    
    /**
     * Log upgrades registered for the instance
     */
    protected function listUpgrades()
    {
        $uh = new UpgradeHistory();
        $ulist = $uh->getList("SELECT * FROM {$uh->table_name} WHERE type='patch'");
        if(empty($ulist)) return;
        foreach($ulist as $urecord) {
            $this->log("Detected patch: {$urecord->name} version {$urecord->version} status {$urecord->status}");
        }
    }

    /**
     * Dirs that are moved to vendor
     * @var array
     */
    protected $removed_directories = array(
            'include/HTMLPurifier',
            'include/HTTP_WebDAV_Server',
            'include/Pear',
            'include/Smarty',
            'XTemplate',
            'Zend',
            'include/lessphp',
            'log4php',
            'include/nusoap',
            'include/oauth2-php',
            'include/pclzip',
            'include/reCaptcha',
            'include/tcpdf',
            'include/ytree',
            'include/SugarSearchEngine/Elastic/Elastica',
    );
    
    protected $excludedScanDirectories = array(
            'backup',
            'tmp',
            'temp',
    );
    protected $filesToFix = array();
    
    /**
     * This method checks for directories that have been moved that are referenced
     * in custom code
     * @return bool
    */
    protected function checkVendorFiles()
    {
        $this->log("Checking for bad includes");
        $files = $this->getPhpFiles("custom/");
        foreach ($files as $name => $file) {
            // check for any occurrence of the directories and flag them
            $fileContents = file_get_contents($file);
            foreach ($this->removed_directories AS $directory) {
                if (preg_match("#(include|require|require_once|include_once)[\s('\"]*({$directory})#",$fileContents) > 0) {
                    $this->log("Found $directory in $file");
                    $this->filesToFix[] = $file;
                }
            }
        }
    }
    
    /**
     * Scan individual module
     * @param string $module
     * @return boolean Was it a real module?
     */
    protected function scanModule($module)
    {
        if(empty($this->beanList[$module])) {
            // absent from module list, not an actual module
            // TODO: we may still want to check for extensions here?
            // TODO: check for view defs for modules not in BeanList?
            $this->log("$module is not in Bean List, may be not a real module");
            return false;
        }

        if (in_array($module, $this->unsupportedModules)) {
            $this->updateStatus("incompatModule", $module);
            return;
        }
        // TODO: check if module table is OK
        
        if($this->isNewModule($module)) {
            $this->updateStatus("notStockModule", $module);
            // not a stock module, check if it's working at least with BWC
            $this->checkMBModule($module);
        } else {
            $this->checkStockModule($module);
        }
    }
    
    /**
     * Get name of the object
     * @param string $module
     * @return string|null
     */
    protected function getObjectName($module)
    {
        if(!empty($this->objectList[$module])) {
            return $this->objectList[$module];
        }
        if(!empty($this->beanList[$module])) {
            return $this->beanList[$module];
        }
        return null;
    }
    
    /**
     * Do checks for ModuleBuilder modules
     * @param string $module
     */
    protected function checkMBModule($module)
    {
        if(!empty($this->newModules[$module])) {
            // we have a name clash
            $this->updateStatus("sameModuleName", $module);
        }
        
        // Check if ModuleBuilder module needs to be run as BWC
        // Checks from 6_ScanModules
        if(!$this->isMBModule($module)) {
            $this->log("toBeRunAsBWC", $module);
        } else {
            $this->log("$module is upgradeable MB module");
        }
        
        $objectName = $this->getObjectName($module);
        // check for subpanels since BWC subpanels can be used in non-BWC modules
        $defs = $this->getPhpFiles("$module/metadata/subpanels");
        if(!empty($defs) && !empty($this->beanList[$module])) {
            foreach($defs as $deffile) {
                $this->checkListFields($deffile, "subpanel_layout", 'list_fields', $module, $objectName);
            }
        }
        
        $defs = $this->getPhpFiles("custom/$module/metadata/subpanels");
        if(!empty($defs) && !empty($this->beanList[$module])) {
            $this->log("$module has custom subpanels");
            foreach($defs as $deffile) {
                $this->checkCustomCode($deffile, "subpanel_layout", "modules/$module/metadata/".basename($deffile));
                $this->checkListFields($deffile, "subpanel_layout", 'list_fields', $module, $objectName);
            }
        }
        
        
        // check for output in logic hooks
        // if there is some, we'd need to put it to custom
        // since upgrader does not handle it, we have to manually BWC the module
        $this->checkHooks($module, self::CUSTOM);
    }
    
    /**
     * Check if stock module is a BWC module
     * @param string $module
     */
    protected function isStockBWCModule($module)
    {
        return isset($this->bwcModulesHash[$module]);
    }
    
    /**
     * Var names for various viewdefs
     * Isn't it fun that we use so many differen ones?
     * @var array
     */
    protected $vardefnames = array(
        'SearchFields.php' => 'searchFields',
        'dashletviewdefs.php' => 'dashletData',
        'listviewdefs.php' => 'listViewDefs',  
        'popupdefs.php' => 'popupMeta',
        'searchdefs.php' => 'searchdefs',
        'subpaneldefs.php' => 'layout_defs',
        'wireless.subpaneldefs.php' => 'layout_defs',
        
    );
    
    /**
     * Check stock module for customizations not compatible with 7
     * @param string $module
     */
    protected function checkStockModule($module)
    {
        $bwc = $this->isStockBWCModule($module);
        
        $history = $this->getPhpFiles("custom/history/modules/$module");
        if(!empty($history)) {
            $this->updateStatus("hasStudioHistory", $module);
        } 
        
        $objectName = $this->getObjectName($module);
        
        // check vardefs for HTML and bad names
        if(!$bwc && $objectName) {
            $this->checkVardefs($module, $objectName, true, self::CUSTOM);
        }
        
        // Check for extension files
        $extfiles = $this->getPhpFiles("custom/Extension/modules/$module/Ext");
        if(!empty($extfiles)) {
            $this->updateStatus("hasExtensions", $module, var_export($extfiles, true));
        }
        foreach($extfiles as $phpfile) {
            $this->checkFileForOutput($phpfile, $bwc?self::CUSTOM:self::MANUAL);
        }

        // Check custom vardefs
        $defs = $this->getPhpFiles("custom/Extension/modules/$module/Ext/Vardefs");
        if(!empty($defs)) {
            $this->updateStatus("hasCustomVardefs", $module);
            foreach($defs as $deffile) {
                $this->checkCustomCode($deffile, "dictionary", "modules/$module/vardefs.php");
            }
        }
        
        // check layout defs
        $defs = $this->getPhpFiles("custom/Extension/modules/$module/Ext/Layoutdefs");
        if(!empty($defs)) {
            $this->updateStatus("hasCustomLayoutdefs", $module);
            foreach($defs as $deffile) {
                $this->checkCustomCode($deffile, "layout_defs", "modules/$module/metadata/subpaneldefs.php");
                $this->checkSubpanelLayoutDefs($module, $objectName, $deffile);
            }
        }
        
        // check custom viewdefs
        $defs = $this->getPhpFiles("custom/modules/$module/metadata");
       
        if($module == "Connectors") {
            $pos = array_search("custom/modules/Connectors/metadata/connectors.php", $defs);
            if($pos !== false) {
                unset($defs[$pos]);
                // TODO: any checks for connectors.php?
            }
            $pos = array_search("custom/modules/Connectors/metadata/display_config.php", $defs);
            if($pos !== false) {
                unset($defs[$pos]);
                // TODO: any checks for display_config.php?
            }
        }
        
        // check viewdefs
        if(!empty($defs)) {
            $this->updateStatus("hasCustomViewdefs", $module);
            foreach($defs as $deffile) {
                if(strpos($deffile, "/subpanels/") !== false) {
                    // special case for subpanels, since subpanels are special
                    $base = basename(dirname($deffile))."/".basename($deffile);
                    $defsname = 'subpanel_layout';
                } else {
                    $base = basename($deffile);
                    if(!empty($this->vardefnames[$base])) {
                        $defsname = $this->vardefnames[$base];
                    } else {
                        $defsname = "viewdefs";
                    }
                }
                $this->checkCustomCode($deffile, $defsname, "modules/$module/metadata/$base");
                // For stock modules, check subpanels and also list views for non-bwc modules
                if($defsname == 'subpanel_layout') {
                    // checking also BWC since Sugar 7 module can have subpanel for BWC module 
                    $this->checkListFields($deffile, $defsname, 'list_fields', $module, $objectName);
                } 
            }
        }
        
        if(!$bwc) {
            // check for custom views
            $defs = array_filter($this->getPhpFiles("custom/modules/$module/views"), function($def) {
                // ENGRD-248 - exclude view.sidequickcreate.php
                return basename($def) != 'view.sidequickcreate.php';
            });
            if(!empty($defs)) {
                $this->updateStatus("hasCustomViews", $module);
            }
            $md5 = $this->md5_files; // work around 5.3 missing $this in closures
            $defs = array_filter($this->getPhpFiles("modules/$module/views"), function($def) use($md5) {
                // ENGRD-248 - exclude view.sidequickcreate.php
                return basename($def) != 'view.sidequickcreate.php' && !isset($md5["./".$def]);
            });
            if(!empty($defs)) {
                $this->updateStatus("hasCustomViewsModDir", $module);
            }
        }
        
        // Check custom extensions which aren't Studio
        $badExts = array("ActionViewMap", "ActionFileMap", "ActionReMap", "EntryPointRegistry",
                "FileAccessControlMap", "WirelessModuleRegistry", "JSGroupings");
        $badExts = array_flip($badExts);
        foreach(glob("custom/$module/Ext/*") as $extdir) {
            if(isset($badExts[basename($extdir)])) {
                $extfiles = glob("$extdir/*");
                if(!empty($extfiles)) {
                    $this->updateStatus("extensionDir", $extdir);
                    break;
                }
            }
        }
        
        // check logic hooks for module
        $this->checkHooks($module, $bwc?self::CUSTOM:self::MANUAL);
        
    }
    
    /**
     * Types that are BLOBs in the DB
     * @var array
     */
    protected $blob_types = array('text', 'longtext', 'multienum', 'html', 'blob', 'longblob');
    
    /**
     * Check if any original vardef changed type
     * @param string $module
     * @param string $object
     */
    protected function checkVardefTypeChange($module, $object)
    {
        if(!file_exists("modules/$module/vardefs.php")) {
            // can't find original vardefs, don't mess with it
            return;
        }
        $full_vardefs = $GLOBALS['dictionary'][$object];
        unset($GLOBALS['dictionary'][$object]);
        global $dictionary;
        include "modules/$module/vardefs.php";
        // load only original vardefs
        $original_vardefs = $GLOBALS['dictionary'][$object];
        // return vardefs back to old state
        $GLOBALS['dictionary'][$object] = $full_vardefs;
        foreach($original_vardefs['fields'] as $name => $def) {
            if(empty($def['type']) || empty($def['name'])) {
                continue;
            }
            if(!empty($def['source']) && $def['source'] != 'db') {
                continue;
            }
            $real_type = $this->db->getFieldType($full_vardefs['fields'][$name]);
            $original_type = $this->db->getFieldType($def);
            if(empty($real_type)) {
                // If we can't find the type, this is some serious breakage
                $this->updateStatus("fieldTypeMissing", $module, $name);
                continue;    
            }
            if(!in_array($real_type, $this->blob_types)) {
                // Per ENGRD-263, we are only interested in changes to blob type
                continue;
            }
            if(!in_array($original_type, $this->blob_types)) {
                // We have changed from non-blob type to blob type, not good
                $this->updateStatus("typeChange", $module, $name, $original_type, $real_type);
            }
        }
    }
    
    /**
     * Load definition of certain var from file
     * @param string $deffile
     * @param string $varname
     * @return array
     */
    protected function loadFromFile($deffile, $varname)
    {
        if(!file_exists($deffile)) {
            return array();
        }
        $l = new FileLoaderWrapper();
        $res = $l->loadFile($deffile, $varname);
        if(is_null($res)) {
            $this->log("Weird, loaded $deffile but no $varname there");
            return array();
        }
        if($res === false) {
            $this->updateStatus("thisUsage", $deffile);
        }
        return $res;
    }
    
    protected $knownCustomCode = array(
        '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
        '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
        '{$fields.currency_symbol.value}{$fields.deal_calc.value}',
        '{$EMAIL1_LINK}{$EMAIL1}</a>',
    );
    
    /**
     * Look for custom code in array of defs
     * @param array $path path through the defs so far
     * @param array $defs Defs to be checked
     */
    protected function lookupCustomCode($path, $defs, $codes)
    {
        foreach($defs as $key => $value) {
            if($key === 'customCode' && !empty($value)) {
                $codes[$value][] = $path;
            } elseif(is_array($value)) {
                $codes = $this->lookupCustomCode($path.$key.':', $value, $codes);
            }
        }
        return $codes;
    }
    
    /**
     * Check defs for customCode entries
     * @param string $deffile Filename for definitions file
     * @param string $varname Variable to get defs from
     * @param string $original Original defs file
     */
    protected function checkCustomCode($deffile, $varname, $original)
    {
        $this->log("Checking $deffile for custom code");
        $defs = $this->loadFromFile($deffile, $varname);
        if(empty($defs)) {
            return;
        }
        
        $origdefs = $this->loadFromFile($original, $varname);
        
        $defs_code = $this->lookupCustomCode('', $defs, array());
        $orig_code = $this->lookupCustomCode('', $defs, array());
        foreach($defs_code as $code => $places) {
            if(!isset($orig_code[$code])) {
                $this->updateStatus("foundCustomCode", $code, join(", ", $places));
            }    
        }

        // Perform viewdef field count - putting this logic here to avoid
        // additional file access to get the viewdefs as we already have
        // them here
        $this->checkViewFieldCount($deffile, $defs);
    }

    /**
     * Figure out if we need to perform a field count and for which view
     * @param string $deffile
     * @return array
     */
    protected function getFieldCountParams($deffile)
    {
        if (strpos($deffile, 'detailviewdefs')) {
            return array(true, 'DetailView');
        }

        if (strpos($deffile, 'editviewdefs')) {
            return array(true, 'EditView');
        }

        return array(false, null);
    }

    /**
     * Check field count on view defs
     * @param string $deffile Filename for definitions file
     * @param string $varname Variable to get defs from
     */
    protected function checkViewFieldCount($deffile, $defs)
    {
        list($doFieldCount, $defName) = $this->getFieldCountParams($deffile);
        if (!$doFieldCount) {
            return;
        }
        $this->log("Checking $deffile for field count");
        $count = 0;

        if(empty($defs) || !is_array($defs)) {
            $this->log("No defs found for $defName in $deffile");
            return;
        }

        // figure out module
        reset($defs);
        $module = key($defs);

        // sanity check base field defs array
        if (empty($defs[$module]) ||
            empty($defs[$module][$defName]) ||
            empty($defs[$module][$defName]['panels']))
        {
            $this->log("No valid panel defs found for $defName in $deffile for $module");
            return;
        }

        // start counting panels -> rows -> columns
        foreach ($defs[$module][$defName]['panels'] as $panel) {
            foreach ($panel as $row) {
                foreach ($row as $column) {
                    if (!empty($column)) {
                        $count++;
                    }
                }
            }
        }

        $this->log("Found $count fields for $defName in $deffile for $module");

        if ($count >= $this->fieldCountMax) {
            $this->updateStatus("maxFieldsView", $this->fieldCountMax, $count, $deffile);
        } elseif ($count >= $this->fieldCountWarn) {
            $this->log("Found more than {$this->fieldCountWarn} fields in $deffile", "WARN");
        }
    }

    /**
     * Check if the link name is valid
     * @param string $module
     * @param string $object
     * @param string $link Link name
     * @return boolean
     */
    protected function isValidLink($module, $object, $link)
    {
        if(empty($GLOBALS['dictionary'][$object]['fields'])) {
            VardefManager::loadVardef($module, $object);
        }
        if(empty($GLOBALS['dictionary'][$object]['fields'])) {
            // weird, we could not load vardefs for this link
            $this->log("Failed to load vardefs for $module:$object");
            return false;
        }
        if(empty($GLOBALS['dictionary'][$object]['fields'][$link]) || 
            empty($GLOBALS['dictionary'][$object]['fields'][$link]['type']) ||
            $GLOBALS['dictionary'][$object]['fields'][$link]['type'] != 'link') {
            return false;
        }
        return true;
    }
    
    /**
     * Check subpanel defs
     * @param string $module Module for subpanel
     * @param string $deffile Filename for definitions file
     */
    protected function checkSubpanelLayoutDefs($module, $object, $deffile)
    {
        $layoutDefs = $this->loadFromFile($deffile, 'layout_defs');
        // get defs regardless of the module_name since it can be plural or singular, but we don't care here
        $defs = $layoutDefs[key($layoutDefs)];
        if (empty($defs['subpanel_setup'])) {
            return;
        }
        $this->log("Checking subpanel file $deffile");
        // check 'get_subpanel_data' contains not applicable in Sidecar 'function:...' value
        foreach ($defs['subpanel_setup'] as $panel) {
            if(!empty($panel['module']) && ($panel['module'] == 'Activities' || $panel['module'] == 'History') 
                && isset($panel['collection_list'])) {
                // skip activities/history, upgrader will take care of them
                continue;
            }

            // check subpanel module. This param should refer to existing module
            if (!empty($panel['module']) && empty($this->beanList[$panel['module']])) {
                $this->updateStatus("subpanelLinkNonExistModule", $panel['module']);
            }

            if (!empty($panel['get_subpanel_data']) && strpos($panel['get_subpanel_data'], 'function:') !== false) {
                $this->updateStatus("subPanelWithFunction", $deffile);
            }
            if (!empty($panel['get_subpanel_data']) && !$this->isValidLink($module, $object, $panel['get_subpanel_data'])) {
                $this->updateStatus("badSubpanelLink", $panel['get_subpanel_data'], $deffile);
            }
        }
    }

    protected $knownWidgetClasses = array('SubPanelDetailViewLink', 'SubPanelEmailLink', 
        'SubPanelEditButton', 'SubPanelRemoveButton', 'SubPanelIcon', 'SubPanelDeleteButton',
    );
    
    /**
     * Check list view type metadata for bad fields 
     * @param string $deffile Filename for definitions file
     * @param string $varname Variable to get defs from
     * @param string $subvarname Section in defs where list fields are stored
     * @param string $module Module name
     * @param string $object Object name
     * @param string $status Status to set if something is wrong
     */
    protected function checkListFields($deffile, $varname, $subvarname, $module, $object)
    {
        if(!$object) {
            return true;
        }
        
        $this->log("Checking $deffile for bad list fields");
        
        if(empty($GLOBALS['dictionary'][$object])) {
            VardefManager::loadVardef($module, $object);
        }
        
        if(empty($GLOBALS['dictionary'][$object]['fields'])) {
            // weird module, no fields, skip
            return true;
        }
        $vardefs = $GLOBALS['dictionary'][$object]['fields'];
        
        $defs = $this->loadFromFile($deffile, $varname);
        if(empty($defs)) {
            return true;
        }
        if($subvarname) {
            if(empty($defs[$subvarname])) {
                return true;
            }
            $defs = $defs[$subvarname];
        }
        foreach($defs as $key => $data)
        {
            if(!empty($data['usage'])) {
                // it's a query field, skip it, converter will take care of them
                continue;
            }
            $key = strtolower($key);
            if(!empty($data['widget_class']) && !in_array($data['widget_class'], $this->knownWidgetClasses)) {
                if(!file_exists("include/generic/SugarWidgets/SugarWidget{$data['widget_class']}.php")) {
                    $this->updateStatus("unknownWidgetClass", $data['widget_class'], $key);
                }
            }
            // Unknown fields handled by CRYS-36, so no more checks here
        }
    }
    
    /**
     * Check logic hooks for module
     * @param string $module
     * @param bool $bwc
     */
    protected function checkHooks($module, $status = self::MANUAL)
    {
        $this->log("Checking hooks for $module");
        $hook_files = array();
        $this->extractHooks("custom/modules/$module/logic_hooks.php", $hook_files);
        $this->extractHooks("custom/modules/$module/Ext/LogicHooks/logichooks.ext.php", $hook_files);

        foreach($hook_files as $hookname => $hooks) {
            foreach($hooks as $hook_data) {
                $this->log("Checking module hook $hookname:{$hook_data[1]}");
                $this->checkFileForOutput($hook_data[2], $status);
            }
        }
    }
    
    /**
     * Get list of existing modules
     * @return array
     */
    protected function getModuleList()
    {
        $beanList = $beanFiles = $objectList = array();
        require 'include/modules.php';
        $this->beanList = $beanList;
        $this->beanFiles = $beanFiles;
        $this->objectList = $objectList;
        
        return array_map(function ($m) { return substr($m, 8); /* cut off modules/ */ }, glob("modules/*", GLOB_ONLYDIR)); 
    }
    
    /**
     * Initialize instance environment
     * @return bool False means this instance is messed up
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
        $this->db = DBManagerFactory::getInstance();
        $GLOBALS['current_user'] = new BlackHole();
        
        
        $md5_string = array();
        if(!file_exists('files.md5')) {
            return $this->fail("files.md5 not found");
        }
        require 'files.md5';
        $this->md5_files = $md5_string;
        $this->bwcModulesHash = array_flip($this->bwcModules);
        return true;
    }
    
    /**
     * Is $module a new module or standard Sugar module?
     * @param string $module
     * @return boolean $module is new?
     */
    protected function isNewModule($module)
    {
        $object = $this->beanList[$module];
        if(empty($this->beanFiles[$object])) {
            // no bean file - check directly
            foreach(glob("modules/$module/*") as $file) {
                // if any file from this dir mentioned in md5 - not a new module
                if(!empty($this->md5_files["./$file"])) {
                    return false;
                }
            }
            return true;
        }
    
        if(empty($this->md5_files["./".$this->beanFiles[$object]])) {
            // no mention of the bean in files.md5 - new module
            return true;
        }
    
        return false;
    }
    
    public function getResultCode()
    {
        if($this->exit_status == self::FAIL) {
            return self::FAIL;
        } 
        return ord($this->status)-ord(self::VANILLA);
    }

    /**
     * Scan directory and build the list of PHP files it contains
     * @param string $path
     * @return array Files data
     */
    protected function getPhpFiles($path)
    {
        $data = array();
        if(!is_dir($path)) {
            return array();
        }
        $path = rtrim($path, "/")."/";
        $iter = new DirectoryIterator("./" . $path);
        foreach ($iter as $item) {
            if ($item->isDot()) {
                continue;
            }

            $filename = $item->getFilename();
            if(strpos($filename, ".suback.php") !== false) {
                // we'll ignore .suback files, they are old upgrade backups
                continue;    
            }

            $extension = $item->getExtension();
            if ($item->isDir() && in_array($filename, $this->excludedScanDirectories)) {
                continue;
            } elseif ($item->isDir()) {
                if(strtolower($filename) == 'disable' || strtolower($filename) == 'disabled') {
                    // skip disable dirs
                    continue;
                }
                $data = array_merge($data, $this->getPhpFiles($path . $filename . "/"));
            } elseif ($extension != 'php') {
                continue;
            } else {
                $data[] = $path . $filename;
            }
        }
        return $data;
    }
    
    /**
     * Extract hook filenames from logic hook file and put them into hook files list
     * @param string $hookfile
     * @param array &$hook_files
     */
    protected function extractHooks($hookfile, &$hook_array)
    {
        $hook_array = array();
        if(!is_readable($hookfile)) {
            return;
        }
        ob_start();
        include $hookfile;
        ob_end_clean();
        if(empty($hook_array)) {
            return;
        }
        foreach($hook_array as $hooks) {
            foreach($hooks as $hook) {
                if(!file_exists($hook[2])) {
                    // putting it as custom since LogicHook checks file_exists
                    $this->updateStatus("badHookFile", $hookfile, $hook[2]);
                }
            }
        }
    }
    
    /**
     * Check logic hook file for by-ref parameters
     * NOTE: not currently used
     * @param string $filename
     */
    protected function checkHookByRef($filename)
    {
        $cont = file_get_contents($filename);
        $matches = array();
        if(preg_match('#function\s+(\w+)\s*\(\s*&\$bean\s*,#i', $cont, $matches)) {
            $this->updateStatus("byRefInHookFile", $filename, $matches[1]);
        }
    }
    
    /**
     * Check PHP file for output constructs. 
     * Set $status if it happens.
     * @param string $phpfile
     * @param string $status
     */
    protected function checkFileForOutput($phpfile, $status) 
    {
        $contents = file_get_contents($phpfile);
        if(!empty($this->md5_files["./".$phpfile]) && $this->md5_files["./".$phpfile] === md5($contents)) {
            // this is our file, no need to check
            return;
        }
        // remove sugarEntry check
        $sePattern = <<<ENDP
if\s*\(\s*!\s*defined\s*\(\s*'sugarEntry'\s*\)\s*(\|\|\s*!\s*sugarEntry\s*)?\)\s*{?\s*die\s*\(\s*'Not A Valid Entry Point'\s*\)\s*;\s*}?
ENDP;
        $contents = preg_replace("#$sePattern#i", '', $contents);
        $fileLines = explode(PHP_EOL, $contents);
        
        $tokens = token_get_all($contents);
        foreach ($tokens as $token) {
            if (is_array($token)) {
                $args = array();
                if ($token[0] == T_INLINE_HTML) {
                    $args = array('inlineHtml', $phpfile, $token[2]);
                } elseif ($token[0] == T_ECHO) {
                    $args = array('foundEcho', $phpfile, $token[2]);
                } elseif ($token[0] == T_PRINT) {
                    $args = array('foundPrint', $phpfile, $token[2]);
                } elseif ($token[0] == T_EXIT) {
                    $args = array('foundDieExit', $phpfile, $token[2]);
                } elseif ($token[0] == T_STRING && $token[1] == 'print_r') {
                    // Checks if print_r has the second parameter as 'true', according to:
                    // When this parameter is set to TRUE, print_r() will return the information rather than print it.
                    // Continue to scan, if has.
                    if (preg_match('#print_r\([^\)]+,\s*true\s*\)#is', $fileLines[$token[2] - 1]) > 0) {
                        continue;
                    }
                    $args = array('foundPrintR', $phpfile, $token[2]);
                } elseif ($token[0] == T_STRING && $token[1] == 'var_dump') {
                    $args = array('foundVarDump', $phpfile, $token[2]);
                } elseif ($token[0] == T_STRING && strpos($token[1], 'ob_') === 0) {
                    $args = array('inlineHtml', $token[1], $phpfile, $token[2]);
                } else {
                    continue;
                }
                if($status == self::CUSTOM) {
                    $args[0] = $args[0] . 'Custom';
                }
                call_user_func_array(array($this, 'updateStatus'), $args);
            }
        }
    }
    
    /**
     * PHP error handler, to log PHP errors
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @param array $errcontext
     */
    public function scriptErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $this->log("PHP: [$errno] $errstr in $errfile at $errline", 'ERROR');
    }

    public $names = array('Gryffindor', 'Hufflepuff', 'Ravenclaw', 'Slytherin', 'Death Eater', 'Voldemort', 'Dumbledore');

    /* Copypaste from 6_ScanModules */
    
    /**
     * Is this a pure ModuleBuilder module?
     * @param string $module_dir
     * @return boolean
     */
    protected function isMBModule($module_name)
    {
        $module_dir = "modules/$module_name";
        if(empty($this->beanList[$module_name])) {
            // if this is not a deployed one, don't bother
            return false;
        }
        $bean = $this->beanList[$module_name];
        if(empty($this->beanFiles[$bean])) {
            return false;
        }
        
        // bad vardefs means no conversion to Sugar 7
        $this->checkVardefs($module_name, $bean, false, self::STUDIO_MB_BWC);

        $mbFiles = array("Dashlets", "Menu.php", "language", "metadata", "vardefs.php", "clients", "workflow");
        $mbFiles[] = basename($this->beanFiles[$bean]);
        $mbFiles[] = pathinfo($this->beanFiles[$bean], PATHINFO_FILENAME)."_sugar.php";

        // to make checks faster
        $mbFiles = array_flip($mbFiles);
    
        $hook_files = array();
        $this->extractHooks("custom/$module_dir/logic_hooks.php", $hook_files);
        $this->extractHooks("custom/$module_dir/Ext/LogicHooks/logichooks.ext.php", $hook_files);
        $hook_files_list = array();
        foreach($hook_files as $hookname => $hooks) {
            foreach($hooks as $hook_data) {
                $hook_files_list[] = $hook_data[2];
            }
        }
        $hook_files = array_unique($hook_files_list);
    
        // For now, the check is just checking if we have any files
        // in the directory that we do not recognize. If we do, we
        // put the module in BC.
        foreach(glob("$module_dir/*") as $file) {
            if(isset($hook_files[$file])) {
                // logic hook files are OK
                continue;
            }
            if(basename($file) == "views") {
                // check views separately because of file template that has view.edit.php
                if(!$this->checkViewsDir("$module_dir/views")) {
                    $this->updateStatus("unknownFileViews", $module_name);
                    return false;
                } else {
                    continue;
                }
            }
            if (basename($file) == 'Forms.php') {
                if (filesize($file) > 0) {
                    $this->updateStatus("nonEmptyFormFile", $file, $module_name);
		            return false;
		        }
                continue;
            }
            if(!isset($mbFiles[basename($file)])) {
                // unknown file, not MB module
                $this->updateStatus("isNotMBModule", $file, $module_name);
                return false;
            }
        }
        // files that are OK for custom:
        $mbFiles['Ext'] = true;
        $mbFiles['logic_hooks.php'] = true;
    
        // now check custom/ for unknown files
        foreach(glob("custom/$module_dir/*") as $file) {
            if(isset($hook_files[$file])) {
                // logic hook files are OK
                continue;
            }
            if(!isset($mbFiles[basename($file)])) {
                // unknown file, not MB module
                $this->updateStatus("isNotMBModule", $file, $module_name);
                return false;
            }
        }
        $badExts = array("ActionViewMap", "ActionFileMap", "ActionReMap", "EntryPointRegistry",
                "FileAccessControlMap", "WirelessModuleRegistry");
        $badExts = array_flip($badExts);
        // Check Ext for any "dangerous" extentsions
        foreach(glob("custom/$module_dir/Ext/*") as $extdir) {
            if(isset($badExts[basename($extdir)])) {
                $extfiles = glob("$extdir/*");
                if(!empty($extfiles)) {
                    $this->updateStatus(self::STUDIO_MB_BWC, "Extension dir $extdir detected - $module_name is not MB module");
                    return false;
                }
            }
        }
        
        return $check === true;
    }
    
    /**
     * Check if views dir was created by file template
     * @param string $view_dir
     * @param string $status Status to assign if check fails
     * @return boolean
     */
    protected function checkViewsDir($view_dir)
    {
        foreach(glob("$view_dir/*") as $file) {
            // for now we allow only view.edit.php
            if(basename($file) != 'view.edit.php') {
                $this->updateStatus("unknownFile", $view_dir, $file);
                return false;
            }
            $data = file_get_contents($file);
            // start with first {
            $data= substr($data, strpos($data, '{'));
            // drop function names
            $data = preg_replace('/function\s[<>_\w]+/', '', $data);
            // drop whitespace
            $data = preg_replace('/\s+/', '', $data);
            /* File data is:
             * {(){parent::ViewEdit();}(){if(isset($this->bean->id)){$this->ss->assign("FILE_OR_HIDDEN","hidden");if(empty($_REQUEST['isDuplicate'])||$_REQUEST['isDuplicate']=='false'){$this->ss->assign("DISABLED","disabled");}}else{$this->ss->assign("FILE_OR_HIDDEN","file");}parent::display();}}?>
            * md5 is: c8251f6b50e3e814135c936f6b5292eb
            */
            if(md5($data) !== 'c8251f6b50e3e814135c936f6b5292eb') {
                $this->updateStatus("badMd5", $file);
                return false;
            }
        }
        return true;
    }
    
    /**
     * List of modules with messed-up vardefs
     * For our eternal shame, these vardefs are broken in existing installs
     * Only non-BWC modules here, since BWC ones aren't checked for vardefs
     * @var array
     */
    protected $bad_vardefs = array(
        'Forecasts' => array('closed_count'),
        'ForecastOpportunities' => array('description'),
        'Quotas' => array('assigned_user_id'), 
        'ProductTemplates' => array('assigned_user_link'),
    );
    
    /**
     * Check that all fields in array exist
     * @param string $key Origin field
     * @param array $fields List of fields to check
     * @param array $fieldDefs Vardefs
     * @param array $status Status array to store errors
     */
    protected function checkFields($key, $fields, $fieldDefs, &$status)
    {
        foreach ($fields as $subField) {
            if(empty($fieldDefs[$subField])) {
                $status[] = "Bad vardefs - $key refers to bad subfield $subField";
            }
        }
    }
    
    protected $templateFields = array(
        "email1" => true,
        "email2" => true, 
        "currency_id" => true,
        "currency_name" => true, 
        "currency_symbol" => true   
    );
    
    /**
     * Check vardefs for module
     * @param string $module
     * @param string $object
     * @param bool $stock Is this a stock module?
     * @return boolean|array true if vardefs OK, list of reasons if module needs to be BWCed
     */
    protected function checkVardefs($module, $object, $stock = false, $status)
    {
        $custom = '';
        if($status == HealthCheck::CUSTOM) {
            $custom = 'Custom';
        }

        if($module == 'DynamicFields') {
            // this one is an odd one
            return true;
        }
        $this->log("Checking vardefs for $module");
        VardefManager::loadVardef($module, $object);
        if(empty($GLOBALS['dictionary'][$object]['fields'])) {
            $this->log("Failed to load vardefs for $module:$object");
            return true;
        }
        $seed = BeanFactory::getBean($module);
        if(empty($seed)) {
            $this->log("Failed to instantiate bean for $module, not checking vardefs");
            return true;
        }
        $status = array();
        $fieldDefs = $GLOBALS['dictionary'][$object]['fields'];
        foreach($fieldDefs as $key => $value) {
            if(!empty($this->bad_vardefs[$module]) && in_array($key, $this->bad_vardefs[$module])) {
                continue;
            }
            if(empty($value['name']) || $key != $value['name']) {
                $this->updateStatus("badVardefsKey" . $custom, $key, $value['name']);
                continue;
            }
            
            if($key == 'team_name') {
                if (empty($value['module'])) {
                    $this->updateStatus("badVardefsRelate" . $custom, $key);
                }
                // this field is really weird, let's leave it alone for now
                continue;
            }
            
            if(!empty($value['function']['returns']) && $value['function']['returns'] == 'html' 
                    && (!$stock || substr($key, -2) == '_c') && !isset($this->templateFields[$key])) {
                // found html functional field in custom code
                $this->updateStatus("vardefHtmlFunction" . $custom, $key);
            }
            
            if(!empty($value['type'])) {
                switch($value['type']) {
                    case 'enum':
                    case 'multienum':
                        if(!empty($value['function']['returns']) && $value['function']['returns'] == 'html') {
                            // found html functional field
                            $this->updateStatus("vardefHtmlFunction" . $custom, $key);
                        }
                        break;
                    case 'link':
                        $seed->load_relationship($key);
                        if(empty($seed->$key)) {
                            $this->updateStatus("badVardefsLink" . $custom, $key);
                        } 
                        break;
                    case 'relate':
                        if(!empty($value['link'])) {
                            $lname = $value['link'];
                            if(empty($fieldDefs[$lname])) {;
                                $this->updateStatus("badVardefsKey" . $custom, $key, $lname);
                                break;
                            }
                            $seed->load_relationship($lname);
                            if(empty($seed->$lname)) {
                                $this->updateStatus("badVardefsRelate" . $custom, $key);
                                break;
                            }
                            $relatedModuleName = $seed->$lname->getRelatedModuleName();
                            if(empty($relatedModuleName)) {
                                break;
                            }
                            $relatedBean = BeanFactory::newBean($relatedModuleName);
                            if(empty($relatedBean)) {
                                break;
                            }
                        }
                        if ((empty($value['link_type']) || $value['link_type'] != 'relationship_info') &&
                            empty($value['module'])) {
                            $this->updateStatus("badVardefsRelate" . $custom, $key);
                        }
                        break;
                }
            }
            
            if(empty($value['source']) || $value['source'] == 'db' || $value['source'] == 'custom_fields') {
                // check fields
                if (isset($value['fields'])) {
                    $this->checkFields($key, $value['fields'], $fieldDefs, $status);
                }
                // check db_concat_fields
                if (isset($value['db_concat_fields'])) {
                    $this->checkFields($key, $value['db_concat_fields'], $fieldDefs, $status);
                }
                // check sort_on
                if(!empty($value['sort_on'])) {
                    if(is_array($value['sort_on'])) {
                        $sort = $value['sort_on'];
                    } else {
                        $sort = array($value['sort_on']);
                    }
                    $this->checkFields($key, $sort, $fieldDefs, $status);
                }
            }            
        }
        
        // check if we have any type changes for vardefs, BR-1427 
        $this->checkVardefTypeChange($module, $object);
    
        return $status?$status:true;
    }
    
    /* END of copypaste from 6_ScanModules */
    
    /**
     * Ping feedback url
     * @param array $data
     */
    protected function ping($data)
    {
        $url = $this->ping_url."?".http_build_query($data);
        @file_get_contents($url);
    }
    
    /**
     * List of standard BWC modules
     * @var array
     */
    protected $bwcModules = array('ACLFields','ACLRoles','ACLActions',
            'Administration','Audit','Calendar','Calls','CampaignLog','Campaigns',
            'CampaignTrackers','Charts','Configurator','Contracts','ContractTypes',
            'Connectors','Currencies','CustomQueries','DataSets','DocumentRevisions',
            'Documents','EAPM','EmailAddresses','EmailMarketing','EmailMan','Emails',
            'EmailTemplates','Employees','Exports','Expressions','Groups','History',
            'Holidays','iCals','Import','InboundEmail','KBContents','KBDocuments',
            'KBDocumentRevisions','KBTags','KBDocumentKBTags','KBContents',
            'Manufacturers','Meetings','MergeRecords','ModuleBuilder','MySettings',
            'OAuthKeys','OptimisticLock','OutboundEmailConfiguration','PdfManager',
            'ProductBundleNotes','ProductBundles','ProductTypes','Project',
            'ProjectResources','ProjectTask','Quotes','QueryBuilder','Relationships',
            'Releases','ReportMaker','Reports','Roles','SavedSearch','Schedulers',
            'SchedulersJobs','Shippers','SNIP','Studio','SugarFavorites','TaxRates',
            'Teams','TeamMemberships','TeamSets','TeamSetModules','TeamNotices',
            'TimePeriods','Trackers','TrackerSessions','TrackerPerfs',
            'TrackerQueries','UserPreferences','UserSignatures','Users','vCals',
            'vCards','Versions','WorkFlow','WorkFlowActions','WorkFlowActionShells',
            'WorkFlowAlerts','WorkFlowAlertShells','WorkFlowTriggerShells'
    );
    
    /**
     * List of modules we have added in Sugar7
     * @var array
     */
    protected $newModules = array(
                    'Comments' => 'Comments',
                    'Filters' => 'Filters',
                    'RevenueLineItems' => 'Revenue Line Items',
                    'Styleguide' => 'Styleguide',
                    'Subscriptions' => 'Subscriptions',
                    'UserSignatures' => 'User Signatures',
                    'WebLogicHooks' => 'Web Logic Hooks',
                    'Words' => 'Words',
    );
}

/**
 * Class that ignores everything, needs for loading 
 * metadata with code
 */
class BlackHole
{
    protected $called;
    public function __get($v) { $this->called = true; return null; }
    public function __call($n, $a) { $this->called = true; return null; }
}

/**
 * Stub class for loading files
 * Needed because we can not override $this but some data files use $this
 * @param string $deffile Definitions file
 * @param string $varname Variable to load
 * @return null if no variable, false on error, otherwise value of $varname in file
 */
class FileLoaderWrapper extends BlackHole
{
    public function loadFile($deffile, $varname) {
        ob_start();
        @include $deffile;
        ob_end_clean();
        if($this->called) {
            return false;
        }
        if(empty($$varname)) {
            return null;   
        }
        return $$varname;
    }    
}

