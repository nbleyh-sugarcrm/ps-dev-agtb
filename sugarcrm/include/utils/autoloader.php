<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
require_once 'include/utils/file_utils.php';
/**
 * File and class loader
 * @api
 */
class SugarAutoLoader
{
    const CACHE_FILE = "file_map.php";
    const CLASS_CACHE_FILE = "class_map.php";
    const COMPOSER_AUTOLOAD = "vendor/autoload.php";

    /**
     * Direct class mapping
     * @var array name => path
     */
	public static $classMap = array(
	);
    public static $classMapDirty = false;

	/**
	 * Classes not to be loaded
	 * @var array name => boolean
	 */
	public static $noAutoLoad = array(
		'Tracker'=>true,
	// this one is generated by ViewFactory for classic view, but if never actually exists
	    'UsersViewClassic'=>true,
	);

    /**
     * @var array
     */
    public static $moduleMap = array();

	/**
	 * Class prefixes
	 * Classes are loaded by prefix:
	 * SugarAclFoo.php => data/acl/SugarACLFoo.php
	 * @var array prefix => directory
	 */
	public static $prefixMap = array(
	    'SugarACL' => "data/acl/",
	    'SugarWidget' => "include/generic/SugarWidgets/",
	    'Zend_' => 'vendor/',
	    'SugarJob' => 'include/SugarQueue/jobs/',
	);

    /**
     * Namespace directory mapping
     * - Prefix should include trailing \
     * - Directory should NOT contain trailing /
     *
     * Order is important on overlapping prefixes, first match wins !
     *   'Sugarcrm\\lib\\' => 'include'
     *   'Sugarcrm\\' => ''
     *
     * To add namespaces dynamically it's advised to use self::addNamespace
     * as this method will ensure a correct order from more to less
     * specific namespace prefixes.
     *
     * @var array nsPrefix => directory
     */
    public static $namespaceMap = array(
    );

    /**
     * List of namespaces and directories which are provisioned
     * using composer. This list is added to the autoloader if
     * composer autoloader is not enabled.
     */
    public static $composerNamespaceMap = array(
        'Elastica\\' => 'vendor/ruflin/elastica/lib/Elastica',
    );

	/**
	 * Class loading directories
	 * Classes in these dirs are loaded by class name:
	 * Foo -> $dir/Foo.php
	 * @var array paths
	 */
	public static $dirMap = array(
	    "data/visibility/",
        "data/Relationships/",
	    "data/duplicatecheck/",
	    "include/SugarSearchEngine/",
	    "include/",
        "modules/Mailer/",
	);

    /**
     * Directories to exclude form mapping
     * @var array
     */
    public static $exclude = array(
        'cache/',
        'custom/history/',
        '.idea/',
        'custom/blowfish/',
        'custom/Extension/',
        'custom/backup/',
        'custom/modulebuilder/',
        'tests/',
        'examples/',
        'docs/',
        'vendor/log4php/',
        'upload/',
        'portal/',
        'vendor/HTMLPurifier/',
        'vendor/PHPMailer/',
        'vendor/reCaptcha/',
        'vendor/ytree/',
        'vendor/pclzip/',
        'vendor/nusoap/',
        'vendor/composer/',
    );

	/**
	 * Extensions to include in mapping
	 * @var string
	 */
    public static $exts = array("php", "tpl", "html", "js", "override", 'gif', 'png', 'jpg', 'tif', 'bmp', 'ico', 'css', 'xml', 'hbs', 'less');

    /**
     * File map
     * @var array
     */
    public static $filemap = array();
    public static $memmap = array();

    /**
     * Copy of extension map
     * @var array
     */
    public static $extensions = array();

    /**
     * Initialize the loader
     */
    public static function init()
    {
        $config = SugarConfig::getInstance();

        // Extensions included from config
        $exts = $config->get('autoloader.exts');
        if (is_array($exts)) {
            self::$exts += $exts;
        }

        // Excludes from config
        $exclude = $config->get('autoloader.exclude');
        if (is_array($exclude)) {
            self::$exclude += $exclude;
        }

        /*
         * Composer autoloader integration:
         * Use our own autoloader if disabled, otherwise exclude dir.
         */
        $useComposer = $config->get('autoloader.composer', false);
        foreach (self::$composerNamespaceMap as $ns => $dir) {
            if ($useComposer) {
                self::$exclude[] = $dir . '/';
            } else {
                self::addNamespace($ns, $dir);
            }
        }

        self::loadFileMap();
        self::loadClassMap();

        // register ourself
        spl_autoload_register(array('SugarAutoLoader', 'autoload'));

        /*
         * Register composer autoloader - By default the composer autoloader 
         * is appended to the autoload stack. If required this behavior can
         * be changed directly in composer.json (see prepend-autoloader).
         */
        if ($useComposer) {
            self::load(self::COMPOSER_AUTOLOAD);
        }

        self::loadExts();
    }

	/**
	 * Load a class
	 * @param string $class Class name
	 * @return boolean Success?
	 */
    public static function autoload($class)
	{
        // work around for PHP 5.3.0 - 5.3.2 https://bugs.php.net/50731
        $class = ltrim($class, '\\');

		$uclass = ucfirst($class);
		if(!empty(self::$noAutoLoad[$class])){
			return false;
		}

		// try known classes
		if(isset(self::$classMap[$uclass])){
			if(self::$classMap[$uclass]){
				// No need for a file_exists, if it is in the map we have found it before
				require_once self::$classMap[$uclass];
				return true;
			}
			return false;
		}

        // try namespaces
        if (false !== strpos($class, '\\')) {
            if ($file = self::getFilenameForFQCN($class)) {
                if ($file = self::requireWithCustom($file)) {
                    self::$classMap[$class] = $file;
                    self::$classMapDirty = true;
                    return true;
                }
            }
            self::$classMap[$class] = false;
            self::$classMapDirty = true;
            return false;
        }

		if(empty(self::$moduleMap)){
			if(isset($GLOBALS['beanFiles'])){
				self::$moduleMap = $GLOBALS['beanFiles'];
			}else{
				include('include/modules.php');
				self::$moduleMap = $beanFiles;
			}
		}

		// Try known modules
		if(!empty(self::$moduleMap[$class])){
			require_once(self::$moduleMap[$class]);
			return true;
		}

		if (strncmp('HTMLPurifier', $class, 12) == 0) {
			return HTMLPurifier_Bootstrap::autoload($class);
		}

	    // Split on _, capitalize elements and make a path
	    // foo_bar -> Foo/Bar.
	    $class_file = join('/', array_map('ucfirst', explode('_', $class)));

		// Try known prefixes
		foreach(self::$prefixMap as $prefix => $dir) {
		    if(strncasecmp($prefix, $class, strlen($prefix)) === 0) {
		        if($file = self::requireWithCustom("{$dir}$class_file.php")) {
		            self::$classMap[$uclass] = $file;
		            self::$classMapDirty = true;
		            return true;
		        } else {
		            break;
		        }
		    }
		}

		// Special cases
		// Special case because lookup goes to $_REQUEST['module']
		if($file = self::getFilenameForViewClass($class)) {
			self::$classMap[$uclass] = $file;
			self::$classMapDirty = true;
			return true;
		}
		// Special case because widget name can be lowercased
		if($file = self::getFilenameForSugarWidget($class)) {
			self::$classMap[$uclass] = $file;
			self::$classMapDirty = true;
			return true;
		}
		// Special case because it checks by ending in Layout
		if($file = self::getFilenameForLayoutClass($class)) {
			self::$classMap[$uclass] = $file;
			self::$classMapDirty = true;
			return true;
		}
        //BEGIN SUGARCRM flav=pro ONLY
        if($file = self::getFilenameForExpressionClass($class)) {
            self::$classMap[$uclass] = $file;
            self::$classMapDirty = true;
            return true;
        }
        //END SUGARCRM flav=pro ONLY

		// Try known dirs
		foreach(self::$dirMap as $dir) {
		    // include/Class.php
		    if($file = self::requireWithCustom("{$dir}$class_file.php")) {
		        self::$classMap[$uclass] = $file;
		        self::$classMapDirty = true;
		        return true;
		    }
		    // include/Class/Class.php
		    // Note here we don't use $class_file since using path twice would not make sense:
		    // Foo/Bar/Foo/Bar.php vs. Foo_Bar/Foo_Bar.php
			if($file = self::requireWithCustom("{$dir}$class/$class.php")) {
		        self::$classMap[$uclass] = $file;
		        self::$classMapDirty = true;
		        return true;
		    }
		    // try include/Foo_Bar.php as a last resort
			if($file = self::requireWithCustom("{$dir}$class.php")) {
		        self::$classMap[$uclass] = $file;
		        self::$classMapDirty = true;
		        return true;
		    }
		}

        self::$classMap[$uclass] = false;
        self::$classMapDirty = true;
        return false;
	}

    /**
     * PSR-0 autoloader interoperability
     * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
     *
     * Return filename for given Fully Qualified Class Name
     *
     * @param string $class FQCN without leading backslash
     * @return mixed(string|boolean)
     */
    public static function getFilenameForFQCN($class)
    {
        foreach (self::$namespaceMap as $prefix => $path) {
            if (strpos($class, $prefix) === 0) {
                $path = empty($path) ? '' : $path . DIRECTORY_SEPARATOR;
                $suffix = str_replace($prefix, '', $class);
                if (false !== $pos = strrpos($suffix, '\\')) {
                    $path .= str_replace('\\', DIRECTORY_SEPARATOR, substr($suffix, 0, $pos)) . DIRECTORY_SEPARATOR;
                    $path .= str_replace('_', DIRECTORY_SEPARATOR, substr($suffix, $pos + 1)) . '.php';
                } else {
                    $path .= str_replace('_', DIRECTORY_SEPARATOR, $suffix) . '.php';
                }
                return $path;
            }
        }
        return false;
    }

	/**
	 * Load layout class from include/MetaDataManager/layouts
	 * @param string $class
	 * @return string|false
	 */
    protected static function getFilenameForLayoutClass($class)
    {
        if(substr($class, -6) == "Layout") {
            return self::requireWithCustom("include/MetaDataManager/layouts/$class.php");
        }
        return false;
    }

    /**
	 * Add directory for loading classes
	 * Directory should include trailing /
	 * @param string $dir
	 */
	public static function addDirectory($dir)
	{
	    self::$dirMap[] = $dir;
	}

    /**
     * Add namespace prefix directory mapping
     * @param string $prefix
     * @param string $dir
     */
    public static function addNamespace($prefix, $dir)
    {
        $prefix = rtrim($prefix, '\\') . '\\';  // enforce trailing \
        $dir = rtrim($dir, '/');                // remove trailing /
        self::$namespaceMap[$prefix] = $dir;

        // The order of self::$namespace is important because the first match
        // will win. When registering new namespace dynamically we need to make
        // sure this array is ordered from more to less specific.

        uksort(self::$namespaceMap, function ($val1, $val2) {
            $level1 = substr_count($val1, '\\');
            $level2 = substr_count($val2, '\\');
            if ($level1 > $level2) {
                return -1;
            } elseif ($level1 < $level2) {
                return 1;
            } else {
                // if levels are the same, sort alphabetically for predictable result
                return strcasecmp($val1, $val2);
            }
        });
    }

	/**
	 * Add directory for loading classes by prefix
	 * Directory should include trailing /
	 * @param string $prefix
	 * @param string $dir
	 */
	public static function addPrefixDirectory($prefix, $dir)
	{
	    self::$prefixMap[$prefix] = $dir;
	}

	protected static function getFilenameForViewClass($class)
    {
        $module = false;
        if (!empty($_REQUEST['module']) && substr($class, 0, strlen($_REQUEST['module'])) === $_REQUEST['module'])
        {
            //This is a module view
            $module = $_REQUEST['module'];
            $class = substr($class, strlen($module));
        }

        if (substr($class, 0, 4) == "View")
        {
            $view = strtolower(substr($class, 4));
            if ($module)
            {
                return self::requireWithCustom("modules/$module/views/view.$view.php");
            } else {
                return self::requireWithCustom("include/MVC/View/views/view.$view.php");
            }
        }
        return false;
    }

    /**
     * getFilenameForSugarWidget
     *
     * This method attempts to autoload classes starting with name "SugarWidget".  It first checks for the file
     * in custom/include/generic/SugarWidgets directory and if not found defaults to include/generic/SugarWidgets.
     * This method is used so that we can easily customize and extend these SugarWidget classes.
     *
     * Can not be served by prefixMap because of the lowercasing in class names.
     *
     * @static
     * @param $class String name of the class to load
     * @return String file of the SugarWidget class; false if none found
     */
    protected static function getFilenameForSugarWidget($class)
    {
        //Only bother to check if the class name starts with SugarWidget
        if(strpos($class, 'SugarWidgetField') !== false) {
            //We need to lowercase the portion after SugarWidgetField
            $name = substr($class, 16);
            if(empty($name)) {
                return false;
            }
            $class = 'SugarWidgetField' . strtolower($name);
            return self::requireWithCustom("include/generic/SugarWidgets/{$class}.php");
        }
        return false;
    }

    /**
     * Load file if exists
     * @param string $file
     * @return boolean True if file was loaded
     */
    public static function load($file)
    {
        if(self::fileExists($file)) {
            require_once $file;
            return true;
        }
        return false;
    }

    /**
     * Load file either from custom, if exists, or from core
     * @param string $file filename
     * @param bool $both Do we want both?
     * @return was any file loaded?
     */
    public static function requireWithCustom($file, $both = false)
    {
        if(self::fileExists("custom/$file")) {
            if($both) {
                // when loading both, core file goes first so custom can override it
                // however we check for custom first and if $both not set load only it
                if(self::fileExists($file)) {
                    require_once($file);
                }
            }
            require_once "custom/$file";
            return "custom/$file";
        } else {
            if(self::fileExists($file)) {
                require_once($file);
                return $file;
            }
        }
        return false;
    }

    /**
     * Get list of existing files and their customizations.
     * @param ... $files
     * @return array Existing files and customizations. Customizations go after files.
     */
    public static function existing()
    {
        $files = func_get_args();
        $out = array();
        foreach($files as $file) {
            if(empty($file)) continue;
            if(is_array($file)) {
                $out += call_user_func_array(array("SugarAutoLoader", "existing"), $file);
                continue;
            }
            if(self::fileExists($file)) {
                $out[] = $file;
            }
        }
        return $out;
    }

    /**
     * Get list of existing files and their customizations.
     * @param ... $files
     * @return array Existing files and customizations. Customizations go after files.
     */
    public static function existingCustom()
    {
        $files = func_get_args();
        $out = array();
        foreach($files as $file) {
            if(empty($file)) continue;
            if(is_array($file)) {
                $out += call_user_func_array(array("SugarAutoLoader", "existingCustom"), $file);
                continue;
            }
            if(self::fileExists($file)) {
                $out[] = $file;
            }
            if(substr($file, 0, 7) != 'custom/' && self::fileExists("custom/$file")) {
                $out[] = "custom/$file";
            }
        }
        return $out;
    }

    /**
     * Get customized file or core file.
     * Returns only the last existing variant, custom if exists
     * @param ... $files
     * @return string|null Last existing file out of given arguments
     */
    public static function existingCustomOne()
    {
        $files = func_get_args();
        $out = call_user_func_array(array("SugarAutoLoader", "existingCustom"), $files);
        if(empty($out)) {
            return null;
        } else {
            return array_pop($out);
        }
    }

    /**
     * Lookup filename in a list of paths. Paths are checked with and without custom/
     * @param array $paths
     * @param string $file
     * @return string|bool Filename found or false
     */
    public static function lookupFile($paths, $file)
    {
        foreach($paths as $path) {
            $fullname = "$path/$file";
            if(self::fileExists("custom/$fullname")) {
                return "custom/$fullname";
            }
            if(self::fileExists($fullname)) {
                return $fullname;
            }
        }
        return false;
    }

    //BEGIN SUGARCRM flav=pro ONLY
    /**
     * getFilenameForExpressionClass
     *
     * Used to autoload classes that end in "Expression". It will check in all directories found in
     * custom/include/Expressions/Expression and include/Expressions/Expression .
     * This method is allows for easy loading of arbitrary expression classes by the SugarLogic Expression parser.
     *
     * @static
     * @param $class String name of the class to load
     * @return String file of the Expression class; false if none found
     */
    protected static function getFilenameForExpressionClass($class)
    {
        if(substr($class, -10) == 'Expression') {
            if($file = self::requireWithCustom("include/Expressions/Expression/{$class}.php")) {
                return $file;
            }

            $types = array("Boolean", "Date", "Enum", "Generic", "Numeric", "Relationship", "String", "Time");

            foreach($types as $type) {
                if($file = self::requireWithCustom("include/Expressions/Expression/{$type}/{$class}.php")) {
                    return $file;
                }
            }
        }
        return false;
    }
    //END SUGARCRM flav=pro ONLY


    /**
     * Load all classes in self::$classMap
     */
	public static function loadAll()
	{
		foreach(self::$classMap as $class=>$file){
			require_once($file);
		}
		if(isset($GLOBALS['beanFiles'])){
			$files = $GLOBALS['beanFiles'];
		}else{
			include('include/modules.php');
			$files = $beanList;
		}
		foreach($files as $class=>$file){
			require_once($file);
		}
	}

	/**
	 * Get viewdefs file name using the following logic:
	 * 1. Check custom/module/metadata/$varname.php
	 * 2. If not there, check metafiles.php
	 * 3. If still not found, use module/metadata/$varname.php
	 * This is used for Studio-enabled definitions. Only one file is loaded
	 * because Studio should be able to delete fields.
	 * @param string $module
	 * @param string $varname Name of the vardef file (listviewdef, etc.) - no .php
	 * @return string|null Suitable metadata file or null
	 */
	public static function loadWithMetafiles($module, $varname)
	{
	    $vardef = self::existingCustomOne("modules/{$module}/metadata/{$varname}.php");
	    if(!empty($vardef) && substr($vardef, 0, 7) == "custom/") {
	        // custom goes first, because this is how Studio overrides defaults
	        return $vardef;
	    }
	    // otherwise check metadata
	    global $metafiles;
	    if(!isset($metafiles[$module])) {
	        $meta = self::existingCustomOne('modules/'.$module.'/metadata/metafiles.php');
    	    if($meta) {
    	    	require $meta;
    	    }
	    }
	    if(!empty($metafiles[$module][$varname])) {
	        $defs = self::existing($metafiles[$module][$varname], $vardef);
	    } else {
	        $defs = self::existing($vardef);
	    }
	    if(!$defs) {
	        return null;
	    } else {
	        return $defs[0];
	    }
	}

	/**
     * Load search fields
     * Search fields are loaded differently since they are not Studio metadata file,
     * so they are combined instead of being overloaded.
     * NOTE: unlike generic loadWithMetafiles, this one returns defs, not filenames
     * Also note that even though $module is given, the defs are not in $searchFields but in $searchFields[$module]
     * for BC reasons.
	 * @param string $module
	 * @return array searchFields def
	 */
	public static function loadSearchFields($module)
	{
		// load metadata first
		global $metafiles;
		if(!isset($metafiles[$module])) {
			$meta = self::existingCustomOne('modules/'.$module.'/metadata/metafiles.php');
			if($meta) {
				require $meta;
			}
		}
        // Then get all files that are revevant
		if(!empty($metafiles[$module]['searchfields'])) {
			$defs = $metafiles[$module]['searchfields'];
		} else {
			$defs = "modules/$module/metadata/SearchFields.php";
		}

		foreach(self::existingCustom($defs) as $file) {
		    require $file;
		}
		if(empty($searchFields)) {
		    return array();
		}
		return $searchFields;
	}

	/**
	 * Load popupdefs metadata file
	 * Allows to override 'popupdefs' with $metadata variable
     * NOTE: unlike generic loadWithMetafiles, this one returns defs, not filenames
	 * @param string $module
	 * @param string $metadata metadata name override
	 * @return array popup defs data or NULL
	 */
	public static function loadPopupMeta($module, $metadata = null)
	{
	    $defs = null;
	    if($metadata == 'undefined' || strpos($metadata, "..") !== false) {
	        $metadata = null;
	    }
	    if(!empty($metadata)) {
	    	$defs = SugarAutoLoader::loadWithMetafiles($module, $metadata);
	    }

	    if(!$defs) {
	    	$defs = SugarAutoLoader::loadWithMetafiles($module, 'popupdefs');
	    }
        if($defs) {
            require $defs;
            return $popupMeta;
        }
        return array();
	}

	/**
	 * Get metadata file for an extension
	 * see extensions.php for the list
	 * @param string $extname Extension name
	 * @param string $module Module to apply to
	 * @return boolean|string File to load, false if none
	 */
	public static function loadExtension($extname, $module = "application")
	{
	    if(empty(self::$extensions[$extname])) return false;
	    $ext = self::$extensions[$extname];
	    if(empty($ext['file']) || empty($ext['extdir'])) {
	        // custom rebuilds, can't handle
	        return false;
	    }
	    if(isset($ext["module"])) {
	        $module = $ext["module"];
	    }
	    if($module == "application") {
	        $file = "custom/application/Ext/{$ext["extdir"]}/{$ext["file"]}";
	    } else {
	        $file = "custom/modules/{$module}/Ext/{$ext["extdir"]}/{$ext["file"]}";
	    }
	    if(self::fileExists($file)) {
	        return $file;
	    }
        return false;
	}

    /**
     * Check if file exists in the cache
     * @param string $filename
     * @return boolean
     */
    public static function fileExists($filename)
    {
        $filename = self::normalizeFilePath($filename);
        
        // See if this filename would have been skipped by the cache creator. This
        // addresses situations like module loader that call sugar_* file functions
        // that use the autoloader on files that are in cache, etc.
        $excluded = false;
        foreach (self::$exclude as $path) {
            if (strpos($filename, $path) === 0) {
                $excluded = true;
                break;
            }
        }
        if ($excluded) {
            // This is a filename that would have been skipped, so check the file
            // system for existence
            return file_exists($filename);
        }
        
        if(isset(self::$memmap[$filename])) {
            return (bool)self::$memmap[$filename];
        }

        $parts = explode('/', $filename);
        $data = self::$filemap;
        foreach($parts as $part) {
            if(empty($part)) continue; // allow sequences of /s
            if(!isset($data[$part])) {
                self::$memmap[$filename] = false;
                return false;
            }
            $data = $data[$part];
        }
        if($data || $data == array()) {
            self::$memmap[$filename] = true;
            return true;
        }
        self::$memmap[$filename] = false;
        return false;
    }

    /**
     * Get all files in directory from cache
     * @param string $dir
     * @param bool $get_dirs Get directories and not files
     * @param string $extension Get only files with given extension
     * @return array List of files
     */
    public static function getDirFiles($dir, $get_dirs = false, $extension = null)
    {
        if(empty(self::$filemap)) {
            self::init();
        }

        // remove leading . if present
        $extension = ltrim($extension, ".");
        $dir = rtrim($dir, "/");
        $parts = explode('/', $dir);
        $data = self::$filemap;
        foreach($parts as $part) {
            if(empty($part)) continue; // allow sequences of /s
            if(!isset($data[$part])) {
        		return array();
        	}
        	$data = $data[$part];
        }
        $result = array();
        if(!is_array($data)) {
            return $result;
        }
        foreach($data as $file => $data) {
            // check extension if given
            if(!empty($extension) && pathinfo($file, PATHINFO_EXTENSION) != $extension) continue;
            // get dirs or files depending on $get_dirs
            if(is_array($data) == $get_dirs) {
                $result[] = "$dir/$file";
            }
        }
        return $result;
    }

    /**
     * Get list of files in this dir and custom duplicate of it
     * @param string $dir
     * @param bool $get_dirs Get directories and not files
     * @return array
     */
    public static function getFilesCustom($dir, $get_dirs = false, $extension = null)
    {
        return array_merge(self::getDirFiles($dir, $get_dirs, $extension), self::getDirFiles("custom/$dir", $get_dirs, $extension));
    }


    /**
     * Build file cache
     */
	public static function buildCache()
	{
        $data = self::scanDir("");
        write_array_to_file("existing_files", $data, sugar_cached(self::CACHE_FILE));
        self::$filemap = $data;
        self::$memmap = array();
        // Rebuild the class cache so that it can find any new classes in the file map
        self::buildClassCache();
	}

	/**
	 * Load cached file map
	 */
	public static function loadFileMap()
	{
	    $existing_files = null;
	    @include sugar_cached(self::CACHE_FILE);
	    if(empty($existing_files)) {
	        // oops, something happened to cache
	        // try to rebuild
	        self::buildCache();
	        @include sugar_cached(self::CACHE_FILE);
	    }
        self::$filemap = $existing_files;
        self::$memmap = array();
	}

    /**
     * Build class map cache
     */
    public static function buildClassCache()
    {
        $class_map = null;
        foreach(self::existingCustom('include/utils/class_map.php') as $file) {
            require $file;
        }
        write_array_to_file("class_map", $class_map, sugar_cached(self::CLASS_CACHE_FILE));

        self::$classMap = $class_map;
        self::$classMapDirty = false;
    }

    /**
     * Load cached class map
     */
    public static function loadClassMap()
    {
        $class_map = null;
        @include sugar_cached(self::CLASS_CACHE_FILE);
        if(empty($class_map)) {
            // oops, something happened to cache
            // try to rebuild
            self::buildClassCache();
        } else {
            self::$classMap = $class_map;
            self::$classMapDirty = false;
        }
    }

	/**
	 * Load extensions map
	 */
	protected static function loadExts()
	{
	    include "ModuleInstall/extensions.php";
	    self::$extensions = $extensions;
	}

	/**
	 * Add filename to list of existing files
	 * @param string $filename
	 * @param bool $save should we save it to file?
	 * @param bool $dir should it be empty directory?
	 */
	public static function addToMap($filename, $save = true, $dir = false)
	{
        // Normalize filename
        $filename = self::normalizeFilePath($filename);

	    if(self::fileExists($filename))
	        return;
        foreach(self::$exclude as $exclude_pattern) {
            if(substr($filename, 0, strlen($exclude_pattern)) == $exclude_pattern) {
                return;
            }
        }

        self::$memmap[$filename] = 1;

        $parts = explode('/', $filename);
	    $filename = array_pop($parts);
	    $data =& self::$filemap;
	    foreach($parts as $part) {
            if(empty($part)) continue; // allow sequences of /s
	        if(!isset($data[$part])) {
                $data[$part] = array();
	        }
	        $data =& $data[$part];
	    }
	    if(!is_array($data)) {
	        $data = array();
	    }
	    $data[$filename] = $dir?array():1;
	    if($save) {
	        write_array_to_file("existing_files", self::$filemap, sugar_cached(self::CACHE_FILE));
	    }
	}

	/**
	 * Delete file from the map
	 * Mainly for use in tests
	 * @param string $filename
	 * @param bool $save should we save it to file?
	 */
	public static function delFromMap($filename, $save = true)
	{
	    // Normalize directory separators
        $filename = self::normalizeFilePath($filename);

	    // we have to reset here since we could delete a directory
        // and memmap is not hierarchical. It may be a performance hit
        //
	    self::$memmap = array();
        $parts = explode('/', $filename);
	    $filename = array_pop($parts);
	    $data =& self::$filemap;
	    foreach($parts as $part) {
            if(empty($part)) continue; // allow sequences of /s
	        if(!isset($data[$part])) {
	    	  return;
	    	}
	    	$data =& $data[$part];
	    }
	    unset($data[$filename]);
	    if($save) {
	        write_array_to_file("existing_files", self::$filemap, sugar_cached(self::CACHE_FILE));
	    }
	}

	/**
	 * Scan directory and build the list of files it contains
	 * @param string $path
	 * @return array Files data
	 */
	public static function scanDir($path)
	{
	    $data = array();
	    if(in_array($path, self::$exclude)) {
	    	return array();
	    }
	    $iter = new DirectoryIterator("./".$path);
	    foreach($iter as $item) {
	    	if($item->isDot()) continue;
	    	$filename = $item->getFilename();
	    	if($item->isDir()) {
	    		$data[$filename] = self::scanDir($path.$filename."/");
	    	} else {
	    		if(!in_array(pathinfo($filename, PATHINFO_EXTENSION), self::$exts)) continue;
	    		$data[$filename] = 1;
	    	}
	    }
	    return $data;
	}

	/**
	 * Get custom class name if that exists or original one if not
	 * @param string $classname
	 * @return string Classname
	 */
	public static function customClass($classname, $autoload = false)
	{
	    $customClass = 'Custom'.$classname;
	    if(class_exists($customClass, $autoload)) {
	        return $customClass;
	    }
	    return $classname;
	}

	/**
	 * Unlink and delete from map
	 * To use mainly for tests
	 * @param string $filename
	 * @param bool $save Save map to file?
	 * @return bool Success?
	 */
	public static function unlink($filename, $save = false)
	{
	    self::delFromMap($filename, $save);
	    unlink($filename);
	}

	/**
	 * Create empty file and add to map
	 * To use mainly for tests
	 * @param string $filename
	 * @param bool $save Save map to file?
	 * @return bool Success?
	 */
	public static function touch($filename, $save = false)
	{
	    if(sugar_touch($filename)) {
	        self::addToMap($filename, $save);
	        return true;
	    }
	    return false;
	}

	/**
	 * Put data to file and add to map
	 * To use mainly for tests
	 * @param string $filename
	 * @param bool $save Save map to file?
	 * @return bool Success?
	 */
	public static function put($filename, $data, $save = false)
	{
	    if(file_put_contents($filename, $data) !== false) {
	        self::addToMap($filename, $save);
	        return true;
	    }
	    return false;
	}

	/**
	 * Ensure the directory exists
	 * @param string $dir
	 * @return boolean
	 */
	public static function ensureDir($dir)
	{
	    if(self::fileExists($dir)) {
	        return true;
	    }
	    if(sugar_mkdir($dir, null, true)) {
	        self::addToMap($dir, true, true);
	        return true;
	    }
	    return false;
	}

	/**
	 * Save the file map to disk
	 */
	public static function saveMap()
	{
	    write_array_to_file("existing_files", self::$filemap, sugar_cached(self::CACHE_FILE));
	}

    /**
     * Save the file map to disk
     */
    public static function saveClassMap()
    {
        if ( self::$classMapDirty && !empty(self::$classMap) ) {
            write_array_to_file("class_map", self::$classMap, sugar_cached(self::CLASS_CACHE_FILE));
            self::$classMapDirty = false;
        }
    }


    /**
     * Cleans up a filepath, normalizing path separators and removing extras
     *
     * @param string $filename The name of the file to work on
     * @return string
     */
    public static function normalizeFilePath($filename) {
        // Normalize directory separators
        if(DIRECTORY_SEPARATOR != '/') {
            $filename = str_replace(DIRECTORY_SEPARATOR, "/", $filename);
        }

        // Remove repeated separators
        $filename = preg_replace('#(/)(\1+)#', '/', $filename);

        return $filename;
    }
}
