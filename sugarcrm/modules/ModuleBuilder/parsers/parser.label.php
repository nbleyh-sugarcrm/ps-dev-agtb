<?php
if (! defined ( 'sugarEntry' ) || ! sugarEntry)
    die ( 'Not A Valid Entry Point' ) ;
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

require_once ('modules/ModuleBuilder/parsers/ModuleBuilderParser.php') ;

class ParserLabel extends ModuleBuilderParser
{
    /**
     * A ModuleInstaller instance
     * 
     * @var ModuleInstaller
     */
    protected static $moduleInstaller;

    function ParserLabel ($moduleName, $packageName = '' )
    {
        $this->moduleName = $moduleName;
        if (!empty($packageName))
            $this->packageName = $packageName ;
    }

    /**
     * Takes in the request params from a save request and processes
     * them for the save.
     * @param REQUEST $params       Labels as "label_".System label => Display label pairs
     * @param string $language      Language key, for example 'en_us'
     */
    function handleSave ($params , $language)
    {
        $labels = array ( ) ;
        foreach ( $params as $key => $value )
        {
            if (preg_match ( '/^label_/', $key ) && strcmp ( $value, 'no_change' ) != 0)
            {
                $labels [ strtoupper(substr ( $key, 6 )) ] = SugarCleaner::cleanHtml(from_html($value),false);
            }
        }

        // Set a basepath if we are in ModuleBuilder
        $basepath = null;
        if (!empty($this->packageName)) {
            $basepath = "custom/modulebuilder/packages/{$this->packageName}/modules/{$this->moduleName}/language";
        }

        return self::addLabels($language, $labels, $this->moduleName, $basepath);
    }

    /**
     * Gets custom strings for this module. If $ext is true, will look in the
     * extension language file. Otherwise will look in the custom lang file.
     * 
     * @param string $language The language to get the strings for
     * @param boolean $ext Whether to use the extension file
     * @return array
     */
    protected function getCustomModStrings($language, $ext = false)
    {
        if ($ext) {
            $file = "custom/modules/".$this->moduleName."/Ext/Language/".$language.".lang.ext.php";
        } else {
            $file = "custom/modules/".$this->moduleName."/language/".$language.".lang.php";
        }

        if (is_file($file)) {
            include $file;
        }

        return isset($mod_strings) ? $mod_strings : array();
    }

    /**
     * Remove a label from the language pack for a module
     * 
     * @param string $language      Language key, for example 'en_us'
     * @param string $label         The label to remove
     * @param string $labelvalue    The value of the label to remove
     * @param string $moduleName    Name of the module to which to add these labels
     * @param string $basepath      base path of the language file
     */
    static function removeLabel($language, $label, $labelvalue, $moduleName, $basepath = null) {
        $GLOBALS['log']->debug("ParserLabel->removeLabels($language, \$label, \$labelvalue, $moduleName, $basepath );");
        if (is_null($basepath)) {
            $deployedModule = true ;
            $basepath = "custom/Extension/modules/$moduleName/Ext/Language";
            if (!SugarAutoLoader::fileExists($basepath)) {
                $GLOBALS['log']->debug("$basepath is not a directory.");
                return false;
            }
        }

        $filename = "$basepath/$language.lang.php";
        $mod_strings = array();

        if (SugarAutoLoader::fileExists($basepath)) {
            if (SugarAutoLoader::fileExists($filename)) {
                // Get current $mod_strings
                include $filename;
            } else {
                $GLOBALS['log']->debug("file $filename does not exist.");
                return false;
            }
        } else {
            $GLOBALS['log']->debug("directory $basepath does not exist.");
            return false ;
        }

        $changed = false;
        if (isset($mod_strings[$label]) && $mod_strings[$label]==$labelvalue) {
            unset($mod_strings[$label]);
            $changed = true;
        }

        if ($changed) {
            if (!write_array_to_file("mod_strings", $mod_strings, $filename)) {
                $GLOBALS['log']->fatal("Could not write $filename");
            } else {
                // if we have a cache to worry about, then clear it now
                if ($deployedModule) {
                    $GLOBALS['log']->debug("PaserLabel->addLabels: clearing language cache");
                    self::rebuildLanguageExtensions($language, $moduleName);
                    $cache_key = "module_language." . $language . $moduleName;
                    sugar_cache_clear($cache_key);
                    LanguageManager::clearLanguageCache($moduleName, $language);
                }
            }
        }

        return true ;
    }

    /**
     * Add a set of labels to the language pack for a module, deployed or undeployed
     * 
     * @param string $language Language key, for example 'en_us'
     * @param array $labels The labels to add in the form of an array of System label => Display label pairs
     * @param string $moduleName Name of the module to which to add these labels
     * @param string $basepath Basepath to the file to be written to
     */
    public static function addLabels($language, $labels, $moduleName, $basepath = null)
    {
        $GLOBALS['log']->debug("ParserLabel->addLabels($language, \$labels, $moduleName, $basepath);");
        $GLOBALS['log']->debug("\$labels:" . print_r($labels, true));

        $deployedModule = false ;
        if (is_null($basepath)) {
            $deployedModule = true ;
            $basepath = "custom/Extension/modules/$moduleName/Ext/Language";
            if (!SugarAutoLoader::fileExists($basepath)) {
                mkdir_recursive($basepath);
            }
        }

        $filename = "$basepath/$language.lang.php";
        $mod_strings = array();
        $changed = false;
        
        if (SugarAutoLoader::fileExists($basepath)) {
            if (SugarAutoLoader::fileExists($filename)) {
                // Get the current $mod_strings
                include $filename;
            }

            foreach($labels as $key => $value) {
                if (!isset($mod_strings[$key]) || strcmp($value, $mod_strings[$key]) != 0) {
                    // Must match encoding used in view.labels.php
                    $mod_strings[$key] = to_html(strip_tags(from_html($value)));
                    $changed = true;
                }
            }
        } else {
            $changed = true;
        }

        if ($changed) {
            $GLOBALS['log']->debug("ParserLabel->addLabels: writing new mod_strings to $filename");
            $GLOBALS['log']->debug("ParserLabel->addLabels: mod_strings=".print_r($mod_strings, true));
            
            $write  = "<?php\n// WARNING: The contents of this file are auto-generated.\n";
            // We can't use normal array writing here since multiple files can be
            // structured differently. This is dirty, yes, but necessary.
            foreach ($mod_strings as $k => $v) {
                $write .= "\$mod_strings['$k'] = " . var_export($v, 1) . ";\n";
            }

            if (!SugarAutoLoader::put($filename, $write, true)) {
                $GLOBALS['log']->fatal("Could not write $filename");
            } else {
                // if we have a cache to worry about, then clear it now
                if ($deployedModule) {
                    SugarCache::cleanOpcodes();
                    $GLOBALS['log']->debug("PaserLabel->addLabels: clearing language cache");
                    self::rebuildLanguageExtensions($language, $moduleName);
                    $cache_key = "module_language." . $language . $moduleName;
                    sugar_cache_clear($cache_key);
                    LanguageManager::clearLanguageCache($moduleName, $language);
                    MetaDataManager::refreshLanguagesCache($language);
                }
            }
        }

        return true ;
    }

    /**
     * Takes in the request params from a save request and processes
     * them for the save.
     * @param $metadata
     * @param string $language      Language key, for example 'en_us'
     */
    function handleSaveRelationshipLabels ($metadata , $language)
        {
        foreach ( $metadata as $definition )
            {
        	$labels = array();
        	$labels[$definition [ 'system_label' ]] = $definition [ 'display_label' ];
        	self::addLabels ( $language, $labels, $definition [ 'module' ],null,true );
            }
        }

    function addLabelsToAllLanguages($labels)
            {
    	$langs = get_languages();
    	foreach($langs as $lang_key => $lang_display)
        {
    		self::addLabels($lang_key, $labels, $this->moduleName);
        }
    }

    /**
     * Rebuilds extensions and language files for this language and module
     * 
     * @param string $language The language to rebuild extensions for
     * @param string $moduleName The name of the module whose extensions are being rebuilt
     */
    protected static function rebuildLanguageExtensions($language, $moduleName)
    {
        if (empty(self::$moduleInstaller)) {
            self::$moduleInstaller = new ModuleInstaller();
            self::$moduleInstaller->silent = true;
        }

        self::$moduleInstaller->rebuild_extensions();
        
        // While this *is* called from rebuild_extensions, it doesn't do anything
        // there because there is no language or module provided to it. This fixes
        // that.
        self::$moduleInstaller->rebuild_languages(array($language => $language), array($moduleName));
    }
}

