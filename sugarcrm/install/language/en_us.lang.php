<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/*********************************************************************************

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 * *******************************************************************************/

$mod_strings = array(
	'LBL_BASIC_SEARCH'					=> 'Basic Search',
	'LBL_ADVANCED_SEARCH'				=> 'Advanced Search',
	'LBL_BASIC_TYPE'					=> 'Basic Type',
	'LBL_ADVANCED_TYPE'					=> 'Advanced Type',
	'LBL_SYSOPTS_1'						=> 'Select from the following system configuration options below.',
    'LBL_SYSOPTS_2'                     => 'What type of database will be used for the Sugar instance you are about to install?',
	'LBL_SYSOPTS_CONFIG'				=> 'System Configuration',
	'LBL_SYSOPTS_DB_TYPE'				=> '',
	'LBL_SYSOPTS_DB'					=> 'Specify Database Type',
    'LBL_SYSOPTS_DB_TITLE'              => 'Database Type',
	'LBL_SYSOPTS_ERRS_TITLE'			=> 'Please fix the following errors before proceeding:',
	'LBL_MAKE_DIRECTORY_WRITABLE'      => 'Please make the following directory writable:',


	//BEGIN SUGARCRM flav=ent ONLY
    'ERR_DB_LOGIN_FAILURE_IBM_DB2'		=> 'The provided database host, username, and/or password is invalid, and a connection to the database could not be established.  Please enter a valid host, username and password',
    'ERR_DB_IBM_DB2_CONNECT'			=> 'The provided database host, username, and/or password is invalid, and a connection to the database could not be established.  Please enter a valid host, username and password',
    'ERR_DB_IBM_DB2_VERSION'			=> 'Your version of DB2 (%s) is not supported by Sugar.  You will need to install a version that is compatible with the Sugar application.  Please consult the Compatibility Matrix in the Release Notes for supported DB2 Versions.',

	'LBL_SYSOPTS_DB_DIRECTIONS'			=> 'You must have an Oracle client installed and configured if you select Oracle.',
	'ERR_DB_LOGIN_FAILURE_OCI8'			=> 'The provided database host, username, and/or password is invalid, and a connection to the database could not be established.  Please enter a valid host, username and password',
	'ERR_DB_OCI8_CONNECT'				=> 'The provided database host, username, and/or password is invalid, and a connection to the database could not be established.  Please enter a valid host, username and password',
	'ERR_DB_OCI8_VERSION'				=> 'Your version of Oracle (%s) is not supported by Sugar.  You will need to install a version that is compatible with the Sugar application.  Please consult the Compatibility Matrix in the Release Notes for supported Oracle Versions.',
    'LBL_DBCONFIG_ORACLE'               => 'Please provide the name of your database.  This will be the default table space that is assigned to your user ((SID from tnsnames.ora).',
	// seed Ent Reports
	'LBL_Q'								=> 'Opportunity Query ',
	'LBL_Q1_DESC'						=> 'Opportunities by Type',
	'LBL_Q2_DESC'						=> 'Opportunities by Account',
	'LBL_R1'							=> '6 month Sales Pipeline Report',
	'LBL_R1_DESC'						=> 'Opportunities over the next 6 months broken down by month and type',
	'LBL_OPP'							=> 'Opportunity Data Set ',
	'LBL_OPP1_DESC'						=> 'This is where you can change the look and feel of the custom query',
	'LBL_OPP2_DESC'						=> 'This query will be stacked below the first query in the report',
	//END SUGARCRM flav=ent ONLY
    'ERR_DB_VERSION_FAILURE'			=> 'Unable to check database version.',

	'DEFAULT_CHARSET'					=> 'UTF-8',
    'ERR_ADMIN_USER_NAME_BLANK'         => 'Provide the user name for the Sugar admin user. ',
	'ERR_ADMIN_PASS_BLANK'				=> 'Provide the password for the Sugar admin user. ',

    'ERR_CHECKSYS'                      => 'Errors have been detected during compatibility check.  In order for your SugarCRM Installation to function properly, please take the proper steps to address the issues listed below and either press the recheck button, or try installing again.',
    'ERR_CHECKSYS_CALL_TIME'            => 'Allow Call Time Pass Reference is On (this should be set to Off in php.ini)',

	'ERR_CHECKSYS_CURL'					=> 'Not found: Sugar Scheduler will run with limited functionality. The Email Archiving service will not run.',
    'ERR_CHECKSYS_IMAP'					=> 'Not found: InboundEmail and Campaigns (Email) require the IMAP libraries. Neither will be functional.',
	'ERR_CHECKSYS_MSSQL_MQGPC'			=> 'Magic Quotes GPC cannot be turned "On" when using MS SQL Server.',
	'ERR_CHECKSYS_MEM_LIMIT_0'			=> 'Warning: ',
	'ERR_CHECKSYS_MEM_LIMIT_1'			=> ' (Set this to ',
	'ERR_CHECKSYS_MEM_LIMIT_2'			=> 'M or larger in your php.ini file)',
	'ERR_CHECKSYS_MYSQL_VERSION'		=> 'Minimum Version 4.1.2 - Found: ',
	'ERR_CHECKSYS_NO_SESSIONS'			=> 'Failed to write and read session variables.  Unable to proceed with the installation.',
	'ERR_CHECKSYS_NOT_VALID_DIR'		=> 'Not A Valid Directory',
	'ERR_CHECKSYS_NOT_WRITABLE'			=> 'Warning: Not Writable',
	'ERR_CHECKSYS_PHP_INVALID_VER'		=> 'Your version of PHP is not supported by Sugar.  You will need to install a version that is compatible with the Sugar application.  Please consult the Compatibility Matrix in the Release Notes for supported PHP Versions. Your version is ',
	'ERR_CHECKSYS_IIS_INVALID_VER'      => 'Your version of IIS is not supported by Sugar.  You will need to install a version that is compatible with the Sugar application.  Please consult the Compatibility Matrix in the Release Notes for supported IIS Versions. Your version is ',
	'ERR_CHECKSYS_FASTCGI'              => 'We detect that you are not using a FastCGI handler mapping for PHP. You will need to install/configure a version that is compatible with the Sugar application.  Please consult the Compatibility Matrix in the Release Notes for supported Versions. Please see <a href="http://www.iis.net/php/" target="_blank">http://www.iis.net/php/</a> for details ',
	'ERR_CHECKSYS_FASTCGI_LOGGING'      => 'For optimal experience using IIS/FastCGI sapi, set fastcgi.logging to 0 in your php.ini file.',
    'ERR_CHECKSYS_PHP_UNSUPPORTED'		=> 'Unsupported PHP Version Installed: ( ver',
    'LBL_DB_UNAVAILABLE'                => 'Database unavailable',
//BEGIN SUGARCRM flav!=ent && flav!=ult ONLY
    'LBL_CHECKSYS_DB_SUPPORT_NOT_AVAILABLE' => 'Database Support was not found.  Please make sure you have the necessary drivers for one of the following supported Database Types: MySQL or MS SQLServer.  You might need to uncomment the extension in the php.ini file, or recompile with the right binary file, depending on your version of PHP.  Please refer to your PHP Manual for more information on how to enable Database Support.<br><br>Oracle and DB2 are not supported databases for SugarCRM PRO. For a list of supported databases and other platform information, please visit <a href="http://support.sugarcrm.com/05_Resources/03_Supported_Platforms/">http://support.sugarcrm.com/05_Resources/03_Supported_Platforms/</a>.',
//END SUGARCRM flav!=ent && flav!=ult ONLY
//BEGIN SUGARCRM flav=ent ONLY
    'LBL_CHECKSYS_DB_SUPPORT_NOT_AVAILABLE' => 'Database Support was not found.  Please make sure you have the necessary drivers for one of the following supported Database Types: MySQL, MS SQLServer, Oracle, or DB2.  You might need to uncomment the extension in the php.ini file, or recompile with the right binary file, depending on your version of PHP.  Please refer to your PHP Manual for more information on how to enable Database Support.',
//END SUGARCRM flav=ent ONLY
    'LBL_CHECKSYS_XML_NOT_AVAILABLE'        => 'Functions associated with XML Parser Libraries that are needed by the Sugar application were not found.  You might need to uncomment the extension in the  php.ini file, or recompile with the right binary file, depending on your version of PHP.  Please refer to your PHP Manual for more information.',
    'LBL_CHECKSYS_CSPRNG' => 'Random number generator',
    'ERR_CHECKSYS_MBSTRING'             => 'Functions associated with the Multibyte Strings PHP extension (mbstring) that are needed by the Sugar application were not found. <br/><br/>Generally, the mbstring module is not enabled by default in PHP and must be activated with --enable-mbstring when the PHP binary is built. Please refer to your PHP Manual for more information on how to enable mbstring support.',
    'ERR_CHECKSYS_SESSION_SAVE_PATH_NOT_SET'       => 'The session.save_path setting in your php configuration file (php.ini) is not set or is set to a folder which did not exist. You might need to set the save_path setting in php.ini or verify that the folder sets in save_path exist.',
    'ERR_CHECKSYS_SESSION_SAVE_PATH_NOT_WRITABLE'  => 'The session.save_path setting in your php configuration file (php.ini) is set to a folder which is not writeable.  Please take the necessary steps to make the folder writeable.  <br>Depending on your Operating system, this might require you to change the permissions by running chmod 766, or to right click on the filename to access the properties and uncheck the read only option.',
    'ERR_CHECKSYS_CONFIG_NOT_WRITABLE'  => 'The config file exists but is not writeable.  Please take the necessary steps to make the file writeable.  Depending on your Operating system, this might require you to change the permissions by running chmod 766, or to right click on the filename to access the properties and uncheck the read only option.',
    'ERR_CHECKSYS_CONFIG_OVERRIDE_NOT_WRITABLE'  => 'The config override file exists but is not writeable.  Please take the necessary steps to make the file writeable.  Depending on your Operating system, this might require you to change the permissions by running chmod 766, or to right click on the filename to access the properties and uncheck the read only option.',
    'ERR_CHECKSYS_CUSTOM_NOT_WRITABLE'  => 'The Custom Directory exists but is not writeable.  You may have to change permissions on it (chmod 766) or right click on it and uncheck the read only option, depending on your Operating System.  Please take the needed steps to make the file writeable.',
    'ERR_CHECKSYS_FILES_NOT_WRITABLE'   => "The files or directories listed below are not writeable or are missing and cannot be created.  Depending on your Operating System, correcting this may require you to change permissions on the files or parent directory (chmod 755), or to right click on the parent directory and uncheck the 'read only' option and apply it to all subfolders.",
	'ERR_CHECKSYS_SAFE_MODE'			=> 'Safe Mode is On (you may wish to disable in php.ini)',
    'ERR_CHECKSYS_ZLIB'					=> 'ZLib support not found: SugarCRM reaps enormous performance benefits with zlib compression.',
    'ERR_CHECKSYS_ZIP'					=> 'ZIP support not found: SugarCRM needs ZIP support in order to process compressed files.',
    'ERR_CHECKSYS_BCMATH'				=> 'BCMATH support not found: SugarCRM needs BCMATH support for arbitrary precision math.',
    'ERR_CHECKSYS_HTACCESS'             => 'Test for .htaccess rewrites failed. This usually means you do not have AllowOverride set up for Sugar directory.',
    'ERR_CHECKSYS_CSPRNG' => 'CSPRNG Exception',
	'ERR_DB_ADMIN'						=> 'The provided database administrator username and/or password is invalid, and a connection to the database could not be established.  Please enter a valid user name and password.  (Error: ',
    'ERR_DB_ADMIN_MSSQL'                => 'The provided database administrator username and/or password is invalid, and a connection to the database could not be established.  Please enter a valid user name and password.',
	'ERR_DB_EXISTS_NOT'					=> 'The specified database does not exist.',
	'ERR_DB_EXISTS_WITH_CONFIG'			=> 'Database already exists with config data.  To run an install with the chosen database, please re-run the install and choose: "Drop and recreate existing SugarCRM tables?"  To upgrade, use the Upgrade Wizard in the Admin Console.  Please read the upgrade documentation located <a href="http://www.sugarforge.org/content/downloads/" target="_new">here</a>.',
	'ERR_DB_EXISTS'						=> 'The provided Database Name already exists -- cannot create another one with the same name.',
    'ERR_DB_EXISTS_PROCEED'             => 'The provided Database Name already exists.  You can<br>1.  hit the back button and choose a new database name <br>2.  click next and continue but all existing tables on this database will be dropped.  <strong>This means your tables and data will be blown away.</strong>',
	'ERR_DB_HOSTNAME'					=> 'Host name cannot be blank.',
	'ERR_DB_INVALID'					=> 'Invalid database type selected.',
	'ERR_DB_LOGIN_FAILURE'				=> 'The provided database host, username, and/or password is invalid, and a connection to the database could not be established.  Please enter a valid host, username and password',
	'ERR_DB_LOGIN_FAILURE_MYSQL'		=> 'The provided database host, username, and/or password is invalid, and a connection to the database could not be established.  Please enter a valid host, username and password',
	'ERR_DB_LOGIN_FAILURE_MSSQL'		=> 'The provided database host, username, and/or password is invalid, and a connection to the database could not be established.  Please enter a valid host, username and password',
	'ERR_DB_MYSQL_VERSION'				=> 'Your MySQL version (%s) is not supported by Sugar.  You will need to install a version that is compatible with the Sugar application.  Please consult the Compatibility Matrix in the Release Notes for supported MySQL versions.',
	'ERR_DB_NAME'						=> 'Database name cannot be blank.',
	'ERR_DB_NAME2'						=> "Database name cannot contain a '\\', '/', or '.'",
    'ERR_DB_MYSQL_DB_NAME_INVALID'      => "Database name cannot contain a '\\', '/', or '.'",
    'ERR_DB_MSSQL_DB_NAME_INVALID'      => "Database name cannot begin with a number, '#', or '@' and cannot contain a space, '\"', \"'\", '*', '/', '\', '?', ':', '<', '>', '&', '!', or '-'",
    'ERR_DB_OCI8_DB_NAME_INVALID'       => "Database name can only consist of alphanumeric characters and the symbols '#', '_', '-', ':', '.', '/' or '$'",
	'ERR_DB_PASSWORD'					=> 'The passwords provided for the Sugar database administrator do not match.  Please re-enter the same passwords in the password fields.',
	'ERR_DB_PRIV_USER'					=> 'Provide a database administrator user name.  The user is required for the initial connection to the database.',
	'ERR_DB_USER_EXISTS'				=> 'User name for Sugar database user already exists -- cannot create another one with the same name. Please enter a new user name.',
	'ERR_DB_USER'						=> 'Enter a user name for the Sugar database administrator.',
	'ERR_DBCONF_VALIDATION'				=> 'Please fix the following errors before proceeding:',
    'ERR_DBCONF_PASSWORD_MISMATCH'      => 'The passwords provided for the Sugar database user do not match. Please re-enter the same passwords in the password fields.',
	'ERR_ERROR_GENERAL'					=> 'The following errors were encountered:',
	'ERR_LANG_CANNOT_DELETE_FILE'		=> 'Cannot delete file: ',
	'ERR_LANG_MISSING_FILE'				=> 'Cannot find file: ',
	'ERR_LANG_NO_LANG_FILE'			 	=> 'No language pack file found at include/language inside: ',
	'ERR_LANG_UPLOAD_1'					=> 'There was a problem with your upload.  Please try again.',
	'ERR_LANG_UPLOAD_2'					=> 'Language Packs must be ZIP archives.',
	'ERR_LANG_UPLOAD_3'					=> 'PHP could not move the temp file to the upgrade directory.',
	'ERR_LICENSE_MISSING'				=> 'Missing Required Fields',
	'ERR_LICENSE_NOT_FOUND'				=> 'License file not found!',
	'ERR_LOG_DIRECTORY_NOT_EXISTS'		=> 'Log directory provided is not a valid directory.',
	'ERR_LOG_DIRECTORY_NOT_WRITABLE'	=> 'Log directory provided is not a writable directory.',
	'ERR_LOG_DIRECTORY_REQUIRED'		=> 'Log directory is required if you wish to specify your own.',
	'ERR_NO_DIRECT_SCRIPT'				=> 'Unable to process script directly.',
	'ERR_NO_SINGLE_QUOTE'				=> 'Cannot use the single quotation mark for ',
	'ERR_PASSWORD_MISMATCH'				=> 'The passwords provided for the Sugar admin user do not match.  Please re-enter the same passwords in the password fields.',
	'ERR_PERFORM_CONFIG_PHP_1'			=> 'Cannot write to the <span class=stop>config.php</span> file.',
	'ERR_PERFORM_CONFIG_PHP_2'			=> 'You can continue this installation by manually creating the config.php file and pasting the configuration information below into the config.php file.  However, you <strong>must </strong>create the config.php file before you continue to the next step.',
	'ERR_PERFORM_CONFIG_PHP_3'			=> 'Did you remember to create the config.php file?',
	'ERR_PERFORM_CONFIG_PHP_4'			=> 'Warning: Could not write to config.php file.  Please ensure it exists.',
	'ERR_PERFORM_HTACCESS_1'			=> 'Cannot write to the ',
	'ERR_PERFORM_HTACCESS_2'			=> ' file.',
	'ERR_PERFORM_HTACCESS_3'			=> 'If you want to secure your log file from being accessible via browser, create an .htaccess file in your log directory with the line:',
	'ERR_PERFORM_NO_TCPIP'				=> '<b>We could not detect an Internet connection.</b> When you do have a connection, please visit <a href="http://www.sugarcrm.com/home/index.php?option=com_extended_registration&task=register">http://www.sugarcrm.com/home/index.php?option=com_extended_registration&task=register</a> to register with SugarCRM. By letting us know a little bit about how your company plans to use SugarCRM, we can ensure we are always delivering the right application for your business needs.',
	'ERR_SESSION_DIRECTORY_NOT_EXISTS'	=> 'Session directory provided is not a valid directory.',
	'ERR_SESSION_DIRECTORY'				=> 'Session directory provided is not a writable directory.',
	'ERR_SESSION_PATH'					=> 'Session path is required if you wish to specify your own.',
	'ERR_SI_NO_CONFIG'					=> 'You did not include config_si.php in the document root, or you did not define $sugar_config_si in config.php',
	'ERR_SITE_GUID'						=> 'Application ID is required if you wish to specify your own.',
    'ERROR_SPRITE_SUPPORT'              => "Currently we are not able to locate the GD library, as a result you will not be able to use the CSS Sprite functionality.",
	'ERR_UPLOAD_MAX_FILESIZE'			=> 'Warning: Your PHP configuration should be changed to allow files of at least 6MB to be uploaded.',
    'LBL_UPLOAD_MAX_FILESIZE_TITLE'     => 'Upload File Size',
	'ERR_URL_BLANK'						=> 'Provide the base URL for the Sugar instance.',
	'ERR_UW_NO_UPDATE_RECORD'			=> 'Could not locate installation record of',
    'ERROR_FLAVOR_INCOMPATIBLE'         => 'The uploaded file is not compatible with this flavor (Professional, Enterprise or Ultimate edition) of Sugar: ',
	'ERROR_LICENSE_EXPIRED'				=> "Error: Your license expired ",
	'ERROR_LICENSE_EXPIRED2'			=> " day(s) ago.   Please go to the <a href='index.php?action=LicenseSettings&module=Administration'>'\"License Management\"</a>  in the Admin screen to enter your new license key.  If you do not enter a new license key within 30 days of your license key expiration, you will no longer be able to log in to this application.",
	'ERROR_MANIFEST_TYPE'				=> 'Manifest file must specify the package type.',
	'ERROR_PACKAGE_TYPE'				=> 'Manifest file specifies an unrecognized package type',
	'ERROR_VALIDATION_EXPIRED'			=> "Error: Your validation key expired ",
	'ERROR_VALIDATION_EXPIRED2'			=> " day(s) ago.   Please go to the <a href='index.php?action=LicenseSettings&module=Administration'>'\"License Management\"</a> in the Admin screen to enter your new validation key.  If you do not enter a new validation key within 30 days of your validation key expiration, you will no longer be able to log in to this application.",
	'ERROR_VERSION_INCOMPATIBLE'		=> 'The uploaded file is not compatible with this version of Sugar: ',

	'LBL_BACK'							=> 'Back',
    'LBL_CANCEL'                        => 'Cancel',
    'LBL_ACCEPT'                        => 'I Accept',
	'LBL_CHECKSYS_1'					=> 'In order for your SugarCRM installation to function properly, please ensure all of the system check items listed below are green. If any are red, please take the necessary steps to fix them.<BR><BR> For help on these system checks, please visit the <a href="http://www.sugarcrm.com/crm/installation" target="_blank">Sugar Wiki</a>.',
	'LBL_CHECKSYS_CACHE'				=> 'Writable Cache Sub-Directories',
    'LBL_DROP_DB_CONFIRM'               => 'The provided Database Name already exists.<br>You can either:<br>1.  Click on the Cancel button and choose a new database name, or <br>2.  Click the Accept button and continue.  All existing tables in the database will be dropped. <strong>This means that all of the tables and pre-existing data will be blown away.</strong>',
	'LBL_CHECKSYS_CALL_TIME'			=> 'PHP Allow Call Time Pass Reference Turned Off',
    'LBL_CHECKSYS_COMPONENT'			=> 'Component',
	'LBL_CHECKSYS_COMPONENT_OPTIONAL'	=> 'Optional Components',
	'LBL_CHECKSYS_CONFIG'				=> 'Writable SugarCRM Configuration File (config.php)',
	'LBL_CHECKSYS_CONFIG_OVERRIDE'		=> 'Writable SugarCRM Configuration File (config_override.php)',
	'LBL_CHECKSYS_CURL'					=> 'cURL Module',
    'LBL_CHECKSYS_SESSION_SAVE_PATH'    => 'Session Save Path Setting',
	'LBL_CHECKSYS_CUSTOM'				=> 'Writeable Custom Directory',
	'LBL_CHECKSYS_DATA'					=> 'Writable Data Sub-Directories',
	'LBL_CHECKSYS_IMAP'					=> 'IMAP Module',
	'LBL_CHECKSYS_MQGPC'				=> 'Magic Quotes GPC',
	'LBL_CHECKSYS_MBSTRING'				=> 'MB Strings Module',
	'LBL_CHECKSYS_MEM_OK'				=> 'OK (No Limit)',
	'LBL_CHECKSYS_MEM_UNLIMITED'		=> 'OK (Unlimited)',
	'LBL_CHECKSYS_MEM'					=> 'PHP Memory Limit',
	'LBL_CHECKSYS_MODULE'				=> 'Writable Modules Sub-Directories and Files',
	'LBL_CHECKSYS_MYSQL_VERSION'		=> 'MySQL Version',
	'LBL_CHECKSYS_NOT_AVAILABLE'		=> 'Not Available',
	'LBL_CHECKSYS_OK'					=> 'OK',
	'LBL_CHECKSYS_PHP_INI'				=> 'Location of your PHP configuration file (php.ini):',
	'LBL_CHECKSYS_PHP_OK'				=> 'OK (ver ',
	'LBL_CHECKSYS_PHPVER'				=> 'PHP Version',
    'LBL_CHECKSYS_IISVER'               => 'IIS Version',
    'LBL_CHECKSYS_FASTCGI'              => 'FastCGI',
	'LBL_CHECKSYS_RECHECK'				=> 'Re-check',
	'LBL_CHECKSYS_SAFE_MODE'			=> 'PHP Safe Mode Turned Off',
	'LBL_CHECKSYS_SESSION'				=> 'Writable Session Save Path (',
	'LBL_CHECKSYS_STATUS'				=> 'Status',
	'LBL_CHECKSYS_TITLE'				=> 'System Check Acceptance',
	'LBL_CHECKSYS_VER'					=> 'Found: ( ver ',
	'LBL_CHECKSYS_XML'					=> 'XML Parsing',
	'LBL_CHECKSYS_ZLIB'					=> 'ZLIB Compression Module',
	'LBL_CHECKSYS_ZIP'					=> 'ZIP Handling Module',
    'LBL_CHECKSYS_BCMATH'				=> 'Arbitrary Precision Math Module',
    'LBL_CHECKSYS_HTACCESS'				=> 'AllowOverride setup for .htaccess',
    'LBL_CHECKSYS_FIX_FILES'            => 'Please fix the following files or directories before proceeding:',
    'LBL_CHECKSYS_FIX_MODULE_FILES'     => 'Please fix the following module directories and the files under them before proceeding:',
    'LBL_CHECKSYS_UPLOAD'               => 'Writable Upload Directory',
    'LBL_CLOSE'							=> 'Close',
    'LBL_THREE'                         => '3',
	'LBL_CONFIRM_BE_CREATED'			=> 'be created',
	'LBL_CONFIRM_DB_TYPE'				=> 'Database Type',
	'LBL_CONFIRM_DIRECTIONS'			=> 'Please confirm the settings below.  If you would like to change any of the values, click "Back" to edit.  Otherwise, click "Next" to start the installation.',
	'LBL_CONFIRM_LICENSE_TITLE'			=> 'License Information',
	'LBL_CONFIRM_NOT'					=> 'not',
	'LBL_CONFIRM_TITLE'					=> 'Confirm Settings',
	'LBL_CONFIRM_WILL'					=> 'will',
	'LBL_DBCONF_CREATE_DB'				=> 'Create Database',
	'LBL_DBCONF_CREATE_USER'			=> 'Create User',
	'LBL_DBCONF_DB_DROP_CREATE_WARN'	=> 'Caution: All Sugar data will be erased<br>if this box is checked.',
	'LBL_DBCONF_DB_DROP_CREATE'			=> 'Drop and Recreate Existing Sugar tables?',
    'LBL_DBCONF_DB_DROP'                => 'Drop Tables',
    'LBL_DBCONF_DB_NAME'				=> 'Database Name',
	'LBL_DBCONF_DB_PASSWORD'			=> 'Sugar Database User Password',
	'LBL_DBCONF_DB_PASSWORD2'			=> 'Re-enter Sugar Database User Password',
	'LBL_DBCONF_DB_USER'				=> 'Sugar Database Username',
    'LBL_DBCONF_SUGAR_DB_USER'          => 'Sugar Database Username',
    'LBL_DBCONF_DB_ADMIN_USER'          => 'Database Administrator Username',
    'LBL_DBCONF_DB_ADMIN_PASSWORD'      => 'Database Admin Password',
	'LBL_DBCONF_DEMO_DATA'				=> 'Populate Database with Demo Data?',
    'LBL_DBCONF_DEMO_DATA_TITLE'        => 'Choose Demo Data',
	'LBL_DBCONF_HOST_NAME'				=> 'Host Name',
	'LBL_DBCONF_HOST_INSTANCE'			=> 'Host Instance',
	'LBL_DBCONF_HOST_PORT'				=> 'Port',
    'LBL_DBCONF_SSL_ENABLED'            => 'Enable SSL connection',
	'LBL_DBCONF_INSTRUCTIONS'			=> 'Please enter your database configuration information below. If you are unsure of what to fill in, we suggest that you use the default values.',
	'LBL_DBCONF_MB_DEMO_DATA'			=> 'Use multi-byte text in demo data?',
    'LBL_DBCONFIG_MSG2'                 => 'Name of web server or machine (host) on which the database is located ( such as localhost or www.mydomain.com ):',
    'LBL_DBCONFIG_MSG3'                 => 'Name of the database that will contain the data for the Sugar instance you are about to install:',
    'LBL_DBCONFIG_B_MSG1'               => 'The username and password of a database administrator who can create database tables and users and who can write to the database is necessary in order to set up the Sugar database.',
    'LBL_DBCONFIG_SECURITY'             => 'For security purposes, you can specify an exclusive database user to connect to the Sugar database.  This user must be able to write, update and retrieve data on the Sugar database that will be created for this instance.  This user can be the database administrator specified above, or you can provide new or existing database user information.',
    'LBL_DBCONFIG_AUTO_DD'              => 'Do it for me',
    'LBL_DBCONFIG_PROVIDE_DD'           => 'Provide existing user',
    'LBL_DBCONFIG_CREATE_DD'            => 'Define user to create',
    'LBL_DBCONFIG_SAME_DD'              => 'Same as Admin User',
	//'LBL_DBCONF_I18NFIX'              => 'Apply database column expansion for varchar and char types (up to 255) for multi-byte data?',
    'LBL_FTS'                           => 'Full Text Search',
    'LBL_FTS_INSTALLED'                 => 'Installed',
    'LBL_FTS_INSTALLED_ERR1'            => 'Full Text Search capability is not installed.',
    'LBL_FTS_INSTALLED_ERR2'            => 'You can still install but will not be able to use Full Text Search functionality.  Please refer to your database server install guide on how to do this, or contact your Administrator.',
	'LBL_DBCONF_PRIV_PASS'				=> 'Privileged Database User Password',
	'LBL_DBCONF_PRIV_USER_2'			=> 'Database Account Above Is a Privileged User?',
	'LBL_DBCONF_PRIV_USER_DIRECTIONS'	=> 'This privileged database user must have the proper permissions to create a database, drop/create tables, and create a user.  This privileged database user will only be used to perform these tasks as needed during the installation process.  You may also use the same database user as above if that user has sufficient privileges.',
	'LBL_DBCONF_PRIV_USER'				=> 'Privileged Database User Name',
	'LBL_DBCONF_TITLE'					=> 'Database Configuration',
    'LBL_DBCONF_TITLE_NAME'             => 'Provide Database Name',
    'LBL_DBCONF_TITLE_USER_INFO'        => 'Provide Database User Information',
	'LBL_DISABLED_DESCRIPTION_2'		=> 'After this change has been made, you may click the "Start" button below to begin your installation.  <i>After the installation is complete, you will want to change the value for \'installer_locked\' to \'true\'.</i>',
	'LBL_DISABLED_DESCRIPTION'			=> 'The installer has already been run once.  As a safety measure, it has been disabled from running a second time.  If you are absolutely sure you want to run it again, please go to your config.php file and locate (or add) a variable called \'installer_locked\' and set it to \'false\'.  The line should look like this:',
	'LBL_DISABLED_HELP_1'				=> 'For installation help, please visit the SugarCRM',
    'LBL_DISABLED_HELP_LNK'               => 'http://www.sugarcrm.com/forums/',
	'LBL_DISABLED_HELP_2'				=> 'support forums',
	'LBL_DISABLED_TITLE_2'				=> 'SugarCRM Installation has been Disabled',
	'LBL_DISABLED_TITLE'				=> 'SugarCRM Installation Disabled',
	'LBL_EMAIL_CHARSET_DESC'			=> 'Character Set most commonly used in your locale',
	'LBL_EMAIL_CHARSET_TITLE'			=> 'Outbound Email Settings',
    'LBL_EMAIL_CHARSET_CONF'            => 'Character Set for Outbound Email ',
	'LBL_HELP'							=> 'Help',
    'LBL_INSTALL'                       => 'Install',
    'LBL_INSTALL_TYPE_TITLE'            => 'Installation Options',
    'LBL_INSTALL_TYPE_SUBTITLE'         => 'Choose Install Type',
    'LBL_INSTALL_TYPE_TYPICAL'          => ' <b>Typical Install</b>',
    'LBL_INSTALL_TYPE_CUSTOM'           => ' <b>Custom Install</b>',
    'LBL_INSTALL_TYPE_MSG1'             => 'The key is required for general application functionality, but it is not required for installation. You do not need to enter the key at this time, but you will need to provide the key after you have installed the application.',
    'LBL_INSTALL_TYPE_MSG2'             => 'Requires minimum information for the installation. Recommended for new users.',
    'LBL_INSTALL_TYPE_MSG3'             => 'Provides additional options to set during the installation. Most of these options are also available after installation in the admin screens. Recommended for advanced users.',
	'LBL_LANG_1'						=> 'To use a language in Sugar other than the default language (US-English), you can upload and install the language pack at this time. You will be able to upload and install language packs from within the Sugar application as well.  If you would like to skip this step, click Next.',
	'LBL_LANG_BUTTON_COMMIT'			=> 'Install',
	'LBL_LANG_BUTTON_REMOVE'			=> 'Remove',
	'LBL_LANG_BUTTON_UNINSTALL'			=> 'Uninstall',
	'LBL_LANG_BUTTON_UPLOAD'			=> 'Upload',
	'LBL_LANG_NO_PACKS'					=> 'none',
	'LBL_LANG_PACK_INSTALLED'			=> 'The following language packs have been installed: ',
	'LBL_LANG_PACK_READY'				=> 'The following language packs are ready to be installed: ',
	'LBL_LANG_SUCCESS'					=> 'The language pack was successfully uploaded.',
	'LBL_LANG_TITLE'			   		=> 'Language Pack',
    'LBL_LAUNCHING_SILENT_INSTALL'     => 'Installing Sugar now.  This may take up to a few minutes.',
	'LBL_LANG_UPLOAD'					=> 'Upload a Language Pack',
	'LBL_LICENSE_ACCEPTANCE'			=> 'License Acceptance',
    'LBL_LICENSE_CHECKING'              => 'Checking system for compatibility.',
    'LBL_LICENSE_CHKENV_HEADER'         => 'Checking Environment',
    'LBL_LICENSE_CHKDB_HEADER'          => 'Verifying DB, FTS Credentials.',
    'LBL_LICENSE_CHECK_PASSED'          => 'System passed check for compatibility.',
    'LBL_LICENSE_REDIRECT'              => 'Redirecting in ',
	'LBL_LICENSE_DIRECTIONS'			=> 'If you have your license information, please enter it in the fields below.',
	'LBL_LICENSE_DOWNLOAD_KEY'			=> 'Enter Download Key',
	'LBL_LICENSE_EXPIRY'				=> 'Expiration Date',
	'LBL_LICENSE_I_ACCEPT'				=> 'I Accept',
	'LBL_LICENSE_NUM_USERS'				=> 'Number of Users',
	'LBL_LICENSE_PRINTABLE'				=> ' Printable View ',
    'LBL_PRINT_SUMM'                    => 'Print Summary',
	'LBL_LICENSE_TITLE_2'				=> 'SugarCRM License',
	'LBL_LICENSE_TITLE'					=> 'License Information',
	'LBL_LICENSE_USERS'					=> 'Licensed Users',

	'LBL_LOCALE_CURRENCY'				=> 'Currency Settings',
	'LBL_LOCALE_CURR_DEFAULT'			=> 'Default Currency',
	'LBL_LOCALE_CURR_SYMBOL'			=> 'Currency Symbol',
	'LBL_LOCALE_CURR_ISO'				=> 'Currency Code (ISO 4217)',
	'LBL_LOCALE_CURR_1000S'				=> '1000s Separator',
	'LBL_LOCALE_CURR_DECIMAL'			=> 'Decimal Separator',
	'LBL_LOCALE_CURR_EXAMPLE'			=> 'Example',
	'LBL_LOCALE_CURR_SIG_DIGITS'		=> 'Significant Digits',
	'LBL_LOCALE_DATEF'					=> 'Default Date Format',
	'LBL_LOCALE_DESC'					=> 'The specified locale settings will be reflected globally within the Sugar instance.',
	'LBL_LOCALE_EXPORT'					=> 'Character Set for Import/Export<br> <i>(Email, .csv, vCard, PDF, data import)</i>',
	'LBL_LOCALE_EXPORT_DELIMITER'		=> 'Export (.csv) Delimiter',
	'LBL_LOCALE_EXPORT_TITLE'			=> 'Import/Export Settings',
	'LBL_LOCALE_LANG'					=> 'Default Language',
	'LBL_LOCALE_NAMEF'					=> 'Default Name Format',
	'LBL_LOCALE_NAMEF_DESC'				=> 's = salutation<br />f = first name<br />l = last name',
	'LBL_LOCALE_NAME_FIRST'				=> 'David',
	'LBL_LOCALE_NAME_LAST'				=> 'Livingstone',
	'LBL_LOCALE_NAME_SALUTATION'		=> 'Dr.',
	'LBL_LOCALE_TIMEF'					=> 'Default Time Format',
	'LBL_LOCALE_TITLE'					=> 'Locale Settings',
    'LBL_CUSTOMIZE_LOCALE'              => 'Customize Locale Settings',
	'LBL_LOCALE_UI'						=> 'User Interface',

	'LBL_ML_ACTION'						=> 'Action',
	'LBL_ML_DESCRIPTION'				=> 'Description',
	'LBL_ML_INSTALLED'					=> 'Date Installed',
	'LBL_ML_NAME'						=> 'Name',
	'LBL_ML_PUBLISHED'					=> 'Date Published',
	'LBL_ML_TYPE'						=> 'Type',
	'LBL_ML_UNINSTALLABLE'				=> 'Uninstallable',
	'LBL_ML_VERSION'					=> 'Version',
	'LBL_MSSQL'							=> 'SQL Server',
	'LBL_MSSQL_SQLSRV'				    => 'SQL Server (Microsoft SQL Server Driver for PHP)',
	'LBL_MYSQL'							=> 'MySQL',
    'LBL_MYSQLI'						=> 'MySQL (mysqli extension)',
	'LBL_IBM_DB2'						=> 'IBM DB2',
	'LBL_NEXT'							=> 'Next',
	'LBL_NO'							=> 'No',
    'LBL_ORACLE'						=> 'Oracle',
	'LBL_PERFORM_ADMIN_PASSWORD'		=> 'Setting site admin password',
	'LBL_PERFORM_AUDIT_TABLE'			=> 'audit table / ',
	'LBL_PERFORM_CONFIG_PHP'			=> 'Creating Sugar configuration file',
	'LBL_PERFORM_CREATE_DB_1'			=> '<b>Creating the database</b> ',
	'LBL_PERFORM_CREATE_DB_2'			=> ' <b>on</b> ',
	'LBL_PERFORM_CREATE_DB_USER'		=> 'Creating the Database username and password...',
	'LBL_PERFORM_CREATE_DEFAULT'		=> 'Creating default Sugar data',
	'LBL_PERFORM_CREATE_LOCALHOST'		=> 'Creating the Database username and password for localhost...',
	'LBL_PERFORM_CREATE_RELATIONSHIPS'	=> 'Creating Sugar relationship tables',
	'LBL_PERFORM_CREATING'				=> 'creating / ',
	'LBL_PERFORM_DEFAULT_REPORTS'		=> 'Creating default reports',
	'LBL_PERFORM_DEFAULT_SCHEDULER'		=> 'Creating default scheduler jobs',
	'LBL_PERFORM_DEFAULT_SETTINGS'		=> 'Inserting default settings',
	'LBL_PERFORM_DEFAULT_USERS'			=> 'Creating default users',
	'LBL_PERFORM_DEMO_DATA'				=> 'Populating the database tables with demo data (this may take a little while)',
	'LBL_PERFORM_DONE'					=> 'done<br>',
	'LBL_PERFORM_DROPPING'				=> 'dropping / ',
	'LBL_PERFORM_FINISH'				=> 'Finish',
	'LBL_PERFORM_LICENSE_SETTINGS'		=> 'Updating license information',
	'LBL_PERFORM_OUTRO_1'				=> 'The setup of Sugar ',
	'LBL_PERFORM_OUTRO_2'				=> ' is now complete!',
	'LBL_PERFORM_OUTRO_3'				=> 'Total time: ',
	'LBL_PERFORM_OUTRO_4'				=> ' seconds.',
	'LBL_PERFORM_OUTRO_5'				=> 'Approximate memory used: ',
	'LBL_PERFORM_OUTRO_6'				=> ' bytes.',
	'LBL_PERFORM_OUTRO_7'				=> 'Your system is now installed and configured for use.',
	'LBL_PERFORM_REL_META'				=> 'relationship meta ... ',
	'LBL_PERFORM_SUCCESS'				=> 'Success!',
	'LBL_PERFORM_TABLES'				=> 'Creating Sugar application tables, audit tables and relationship metadata',
	'LBL_PERFORM_TITLE'					=> 'Perform Setup',
	'LBL_PRINT'							=> 'Print',
	'LBL_REG_CONF_1'					=> 'Please complete the short form below to receive product announcements, training news, special offers and special event invitations from SugarCRM. We do not sell, rent, share or otherwise distribute the information collected here to third parties.',
	'LBL_REG_CONF_2'					=> 'Your name and email address are the only required fields for registration. All other fields are optional, but very helpful. We do not sell, rent, share, or otherwise distribute the information collected here to third parties.',
	'LBL_REG_CONF_3'					=> 'Thank you for registering. Click on the Finish button to login to SugarCRM. You will need to log in for the first time using the username "admin" and the password you entered in step 2.',
	'LBL_REG_TITLE'						=> 'Registration',
    'LBL_REG_NO_THANKS'                 => 'No Thanks',
    'LBL_REG_SKIP_THIS_STEP'            => 'Skip this Step',
	'LBL_REQUIRED'						=> '* Required field',

    'LBL_SITECFG_ADMIN_Name'            => 'Sugar Application Admin Name',
	'LBL_SITECFG_ADMIN_PASS_2'			=> 'Re-enter Sugar Admin User Password',
	'LBL_SITECFG_ADMIN_PASS_WARN'		=> 'Caution: This will override the admin password of any previous installation.',
	'LBL_SITECFG_ADMIN_PASS'			=> 'Sugar Admin User Password',
	'LBL_SITECFG_APP_ID'				=> 'Application ID',
	'LBL_SITECFG_CUSTOM_ID_DIRECTIONS'	=> 'If selected, you must provide an application ID to override the auto-generated ID. The ID ensures that sessions of one Sugar instance are not used by other instances.  If you have a cluster of Sugar installations, they all must share the same application ID.',
	'LBL_SITECFG_CUSTOM_ID'				=> 'Provide Your Own Application ID',
	'LBL_SITECFG_CUSTOM_LOG_DIRECTIONS'	=> 'If selected, you must specify a log directory to override the default directory for the Sugar log. Regardless of where the log file is located, access to it through a web browser will be restricted via an .htaccess redirect.',
	'LBL_SITECFG_CUSTOM_LOG'			=> 'Use a Custom Log Directory',
	'LBL_SITECFG_CUSTOM_SESSION_DIRECTIONS'	=> 'If selected, you must provide a secure folder for storing Sugar session information. This can be done to prevent session data from being vulnerable on shared servers.',
	'LBL_SITECFG_CUSTOM_SESSION'		=> 'Use a Custom Session Directory for Sugar',
	'LBL_SITECFG_DIRECTIONS'			=> 'Please enter your site configuration information below. If you are unsure of the fields, we suggest that you use the default values.',
	'LBL_SITECFG_FIX_ERRORS'			=> '<b>Please fix the following errors before proceeding:</b>',
	'LBL_SITECFG_LOG_DIR'				=> 'Log Directory',
	'LBL_SITECFG_SESSION_PATH'			=> 'Path to Session Directory<br>(must be writable)',
	'LBL_SITECFG_SITE_SECURITY'			=> 'Select Security Options',
	'LBL_SITECFG_SUGAR_UP_DIRECTIONS'	=> 'If selected, the system will periodically check for updated versions of the application.',
	'LBL_SITECFG_SUGAR_UP'				=> 'Automatically Check For Updates?',
	'LBL_SITECFG_SUGAR_UPDATES'			=> 'Sugar Updates Config',
	'LBL_SITECFG_TITLE'					=> 'Site Configuration',
    'LBL_SITECFG_TITLE2'                => 'Identify Administration User',
    'LBL_SITECFG_SECURITY_TITLE'        => 'Site Security',
	'LBL_SITECFG_URL'					=> 'URL of Sugar Instance',
	'LBL_SITECFG_USE_DEFAULTS'			=> 'Use Defaults?',
	'LBL_SITECFG_ANONSTATS'             => 'Send Anonymous Usage Statistics?',
	'LBL_SITECFG_ANONSTATS_DIRECTIONS'  => 'If selected, Sugar will send <b>anonymous</b> statistics about your installation to SugarCRM Inc. every time your system checks for new versions. This information will help us better understand how the application is used and guide improvements to the product.',
    'LBL_SITECFG_URL_MSG'               => 'Enter the URL that will be used to access the Sugar instance after installation. The URL will also be used as a base for the URLs in the Sugar application pages. The URL should include the web server or machine name or IP address.',
    'LBL_SITECFG_SYS_NAME_MSG'          => 'Enter a name for your system.  This name will be displayed in the browser title bar when users visit the Sugar application.',
    'LBL_SITECFG_PASSWORD_MSG'          => 'After installation, you will need to use the Sugar admin user (default username = admin) to log in to the Sugar instance.  Enter a password for this administrator user. This password can be changed after the initial login.  You may also enter another admin username to use besides the default value provided.',
    'LBL_SITECFG_COLLATION_MSG'         => 'Select collation (sorting) settings for your system. This settings will create the tables with the specific language you use. In case your language doesn\'t require special settings please use default value.',
    'LBL_SPRITE_SUPPORT'                => 'Sprite Support',
	'LBL_SYSTEM_CREDS'                  => 'System Credentials',
    'LBL_SYSTEM_ENV'                    => 'System Environment',
	'LBL_START'							=> 'Start',
    'LBL_SHOW_PASS'                     => 'Show Passwords',
    'LBL_HIDE_PASS'                     => 'Hide Passwords',
    'LBL_HIDDEN'                        => '<i>(hidden)</i>',
//	'LBL_NO_THANKS'						=> 'Continue to installer',
	'LBL_CHOOSE_LANG'					=> '<b>Choose your language</b>',
	'LBL_STEP'							=> 'Step',
	'LBL_TITLE_WELCOME'					=> 'Welcome to the SugarCRM ',
	'LBL_WELCOME_1'						=> 'This installer creates the SugarCRM database tables and sets the configuration variables that you need to start. The entire process should take about ten minutes.',
    //welcome page variables
    'LBL_TITLE_ARE_YOU_READY'            => 'Are you ready to install?',
    'REQUIRED_SYS_COMP' => 'Required System Components',
    'REQUIRED_SYS_COMP_MSG' =>
                    'Before you begin, please be sure that you have the supported versions of the following system
                      components:<br>
                      <ul>
                      <li> Database/Database Management System (Examples: MySQL, SQL Server, Oracle, DB2)</li>
                      <li> Web Server (Apache, IIS)</li>
                      <li> Elasticsearch</li>
                      </ul>
                      Consult the Compatibility Matrix in the Release Notes for
                      compatible system components for the Sugar version that you are installing.<br>',
    'REQUIRED_SYS_CHK' => 'Initial System Check',
    'REQUIRED_SYS_CHK_MSG' =>
                    'When you begin the installation process, a system check will be performed on the web server on which the Sugar files are located in order to
                      make sure the system is configured properly and has all of the necessary components
                      to successfully complete the installation. <br><br>
                      The system checks all of the following:<br>
                      <ul>
                      <li><b>PHP version</b> &#8211; must be compatible
                      with the application</li>
                                        <li><b>Session Variables</b> &#8211; must be working properly</li>
                                            <li> <b>MB Strings</b> &#8211; must be installed and enabled in php.ini</li>

                      <li> <b>Database Support</b> &#8211; must exist for MySQL, SQL
                      Server, Oracle, or DB2</li>

                      <li> <b>Config.php</b> &#8211; must exist and must have the appropriate
                                  permissions to make it writeable</li>
					  <li>The following Sugar files must be writeable:<ul><li><b>/custom</li>
<li>/cache</li>
<li>/modules</li>
<li>/upload</b></li></ul></li></ul>
                                  If the check fails, you will not be able to proceed with the installation. An error message will be displayed, explaining why your system
                                  did not pass the check.
                                  After making any necessary changes, you can undergo the system
                                  check again to continue the installation.<br>',
    'REQUIRED_INSTALLTYPE' => 'Typical or Custom install',
    'REQUIRED_INSTALLTYPE_MSG' =>
                    "After the system check is performed, you can choose either
                      the Typical or the Custom installation.<br><br>
                      For both <b>Typical</b> and <b>Custom</b> installations, you will need to know the following:<br>
                      <ul>
                      <li> <b>Type of database</b> that will house the Sugar data <ul><li>Compatible database
                      types: MySQL, MS SQL Server, Oracle, DB2.<br><br></li></ul></li>
                      <li> <b>Name of the web server</b> or machine (host) on which the database is located
                      <ul><li>This may be <i>localhost</i> if the database is on your local computer or is on the same web server or machine as your Sugar files.<br><br></li></ul></li>
                      <li><b>Name of the database</b> that you would like to use to house the Sugar data</li>
                        <ul>
                          <li> You might already have an existing database that you would like to use. If
                          you provide the name of an existing database, the tables in the database will
                          be dropped during installation when the schema for the Sugar database is defined.</li>
                          <li> If you do not already have a database, the name you provide will be used for
                          the new database that is created for the instance during installation.<br><br></li>
                        </ul>
                      <li><b>Database administrator user name and password</b> <ul><li>The database administrator should be able to create tables and users and write to the database.</li><li>You might need to
                      contact your database administrator for this information if the database is
                      not located on your local computer and/or if you are not the database administrator.<br><br></ul></li></li>
                      <li> <b>Sugar database user name and password</b>
                      </li>
                        <ul>
                          <li> The user may be the database administrator, or you may provide the name of
                          another existing database user. </li>
                          <li> If you would like to create a new database user for this purpose, you will
                          be able to provide a new username and password during the installation process,
                          and the user will be created during installation. </li>
                        </ul>
                    <li> <b>Elasticsearch host and port</b>
                      </li>
                        <ul>
                          <li> Elasticsearch host is the host the search engine is running on. This defaults to localhost assuming you are running the search engine on the same server as Sugar.</li>
                          <li> Elasticsearch port is the port number for Sugar to connect to the search engine. This defaults to 9200, which is elasticsearch's default. </li>
                        </ul>
                        </ul><p>

                      For the <b>Custom</b> setup, you might also need to know the following:<br>
                      <ul>
                      <li> <b>URL that will be used to access the Sugar instance</b> after it is installed.
                      This URL should include the web server or machine name or IP address.<br><br></li>
                                  <li> [Optional] <b>Path to the session directory</b> if you wish to use a custom
                                  session directory for Sugar information in order to prevent session data from
                                  being vulnerable on shared servers.<br><br></li>
                                  <li> [Optional] <b>Path to a custom log directory</b> if you wish to override the default directory for the Sugar log.<br><br></li>
                                  <li> [Optional] <b>Application ID</b> if you wish to override the auto-generated
                                  ID that ensures that sessions of one Sugar instance are not used by other instances.<br><br></li>
                                  <li><b>Character Set</b> most commonly used in your locale.<br><br></li></ul>
                                  For more detailed information, please consult the Installation Guide.
                                ",
    'LBL_WELCOME_PLEASE_READ_BELOW' => 'Please read the following important information before proceeding with the installation.  The information will help you determine whether or not you are ready to install the application at this time.',


	'LBL_WELCOME_2'						=> 'For installation documentation, please visit the <a href="http://www.sugarcrm.com/crm/installation" target="_blank">Sugar Wiki</a>.  <BR><BR> To contact a SugarCRM support engineer for installation help, please log in to the <a target="_blank" href="http://support.sugarcrm.com">SugarCRM Support Portal</a> and submit a support case.',
	'LBL_WELCOME_CHOOSE_LANGUAGE'		=> '<b>Choose your language</b>',
	'LBL_WELCOME_SETUP_WIZARD'			=> 'Setup Wizard',
	'LBL_WELCOME_TITLE_WELCOME'			=> 'Welcome to the SugarCRM ',
	'LBL_WELCOME_TITLE'					=> 'SugarCRM Setup Wizard',
	'LBL_WIZARD_TITLE'					=> 'Sugar Setup Wizard: ',
	'LBL_YES'							=> 'Yes',
    'LBL_YES_MULTI'                     => 'Yes - Multibyte',
	// OOTB Scheduler Job Names:
	'LBL_OOTB_WORKFLOW'		=> 'Process Workflow Tasks',
	'LBL_OOTB_REPORTS'		=> 'Run Report Generation Scheduled Tasks',
	'LBL_OOTB_IE'			=> 'Check Inbound Mailboxes',
	'LBL_OOTB_BOUNCE'		=> 'Run Nightly Process Bounced Campaign Emails',
    'LBL_OOTB_CAMPAIGN'		=> 'Run Nightly Mass Email Campaigns',
	'LBL_OOTB_PRUNE'		=> 'Prune Database on 1st of Month',
    'LBL_OOTB_TRACKER'		=> 'Prune tracker tables',
    'LBL_OOTB_SEND_EMAIL_REMINDERS' => 'Run Email Reminder Notifications',
    'LBL_UPDATE_TRACKER_SESSIONS' => 'Update tracker_sessions table',
    'LBL_OOTB_CLEANUP_QUEUE' => 'Clean Jobs Queue',


    'LBL_FTS_TABLE_TITLE'     => 'Provide Full-Text Search Settings',
    'LBL_FTS_HOST'     => 'Host',
    'LBL_FTS_PORT'     => 'Port',
    'LBL_FTS_TYPE'     => 'Search Engine Type',
    'LBL_FTS_HELP'      => 'To enable full-text searching, enter the Host and Port where the search engine is hosted. Sugar includes built-in support for the elasticsearch engine.',
    'LBL_FTS_REQUIRED'    => 'Elastic Search is required.',
    'LBL_FTS_CONN_ERROR'    => 'Unable to connect to Full Text Search server, please verify your settings.',
    'LBL_FTS_NO_VERSION_AVAILABLE'    => 'No full text search server version available, please verify your settings.',
    'LBL_FTS_UNSUPPORTED_VERSION'    => 'Unsupported version of Elastic search detected. Please use versions: %s',

    'LBL_PATCHES_TITLE'     => 'Install Latest Patches',
    'LBL_MODULE_TITLE'      => 'Install Language Packs',
    'LBL_PATCH_1'           => 'If you would like to skip this step, click Next.',
    'LBL_PATCH_TITLE'       => 'System Patch',
    'LBL_PATCH_READY'       => 'The following patch(es) are ready to be installed:',
	'LBL_SESSION_ERR_DESCRIPTION'		=> "SugarCRM relies upon PHP sessions to store important information while connected to this web server.  Your PHP installation does not have the Session information correctly configured.
											<br><br>A common misconfiguration is that the <b>'session.save_path'</b> directive is not pointing to a valid directory.  <br>
											<br> Please correct your <a target=_new href='http://us2.php.net/manual/en/ref.session.php'>PHP configuration</a> in the php.ini file located here below.",
	'LBL_SESSION_ERR_TITLE'				=> 'PHP Sessions Configuration Error',
	'LBL_SYSTEM_NAME'=>'System Name',
    'LBL_COLLATION' => 'Collation Settings',
	'LBL_REQUIRED_SYSTEM_NAME'=>'Provide a System Name for the Sugar instance.',
	'LBL_PATCH_UPLOAD' => 'Select a patch file from your local computer',
	'LBL_BACKWARD_COMPATIBILITY_ON' => 'PHP Backward Compatibility mode is turned on. Set zend.ze1_compatibility_mode to Off for proceeding further',

    'meeting_notification_email' => array(
        'name' => 'Meeting Notifications Emails',
        'subject' => 'SugarCRM Meeting - $event_name ',
        'description' => 'This template is used when the System sends a meeting notifications to a user.',
        'body' => '<div>
	<p>To: $assigned_user</p>

	<p>$assigned_by_user has invited you to a Meeting</p>

	<p>Subject: $event_name<br/>
	Start Date: $start_date<br/>
	End Date: $end_date</p>

	<p>Description: $description</p>

	<p>Accept this meeting:<br/>
	<<a href="$accept_link">$accept_link</a>></p>
	<p>Tentatively Accept this meeting:<br/>
	<<a href="$tentative_link">$tentative_link</a>></p>
	<p>Decline this meeting:<br/>
	<<a href="$decline_link">$decline_link</a>></p>
</div>',
        'txt_body' =>
            'To: $assigned_user

$assigned_by_user has invited you to a Meeting

Subject: $event_name
Start Date: $start_date
End Date: $end_date

Description: $description

Accept this meeting:
<$accept_link>

Tentatively Accept this meeting
<$tentative_link>

Decline this meeting
<$decline_link>',
    ),

    'call_notification_email' => array(
        'name' => 'Call Notifications Emails',
        'subject' => 'SugarCRM Call - $event_name ',
        'description' => 'This template is used when the System sends a call notifications to a user.',
        'body' => '<div>
	<p>To: $assigned_user</p>

	<p>$assigned_by_user has invited you to a Call</p>

	<p>Subject: $event_name<br/>
	Start Date: $start_date<br/>
	Duration: $hoursh, $minutesm</p>

	<p>Description: $description</p>

	<p>Accept this call:<br/>
	<<a href="$accept_link">$accept_link</a>></p>
	<p>Tentatively Accept this call:<br/>
	<<a href="$tentative_link">$tentative_link</a>></p>
	<p>Decline this call:<br/>
	<<a href="$decline_link">$decline_link</a>></p>
</div>',
        'txt_body' =>
            'To: $assigned_user

$assigned_by_user has invited you to a Call

Subject: $event_name
Start Date: $start_date
Duration: $hoursh, $minutesm

Description: $description

Accept this call:
<$accept_link>

Tentatively Accept this call
<$tentative_link>

Decline this call
<$decline_link>',
    ),

    'assigned_notification_email' => array(
        'name' => 'Assignment Notification Emails',
        'subject' => 'SugarCRM - Assigned $module_name ',
        'description' => 'This template is used when the System sends a task assignment to a user.',
        'body' => '<div>
<p>$assigned_by_user has assigned a&nbsp;$module_name to&nbsp;$assigned_user.</p>

<p>You may review this&nbsp;$module_name at:<br/>
	<<a href="$module_link">$module_link</a>></p>
</div>',
        'txt_body' =>
            '$assigned_by_user has assigned a $module_name to $assigned_user.

You may review this $module_name at:
<$module_link>',
    ),

    'scheduled_report_email' => array(
        'name' => 'Scheduled Report Emails',
        'subject' => 'Scheduled Report: $report_name as of $report_time',
        'description' => 'This template is used when the System sends a scheduled report to a user.',
        'body' => '<div>
<p>Hello $assigned_user,</p>
<p>Attached is an auto generated report that has been scheduled for you.</p>
<p>Report Name: $report_name</p>
<p>Report Run Date and Time: $report_time</p>
</div>',
        'txt_body' =>
            'Hello $assigned_user,

Attached is an auto generated report that has been scheduled for you.

Report Name: $report_name

Report Run Date and Time: $report_time',
    ),

    'advanced_password_new_account_email' => array(
        'subject' => 'New account information',
        'description' => 'This template is used when the System Administrator sends a new password to a user.',
        'body' => '<div><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width="550" align=\"\&quot;\&quot;center\&quot;\&quot;\"><tbody><tr><td colspan=\"2\"><p>Here is your account username and temporary password:</p><p>Username : $contact_user_user_name </p><p>Password : $contact_user_user_hash </p><br><p><a href="$config_site_url">$config_site_url</a></p><br><p>After you log in using the above password, you may be required to reset the password to one of your own choice.</p> </td> </tr><tr><td colspan=\"2\"></td> </tr> </tbody></table> </div>',
        'txt_body' =>
'
Here is your account username and temporary password:
Username : $contact_user_user_name
Password : $contact_user_user_hash

$config_site_url

After you log in using the above password, you may be required to reset the password to one of your own choice.',
        'name' => 'System-generated password email',
        ),
    'advanced_password_forgot_password_email' => array(
        'subject' => 'Reset your account password',
        'description' => "This template is used to send a user a link to click to reset the user's account password.",
        'body' => '<div><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width="550" align=\"\&quot;\&quot;center\&quot;\&quot;\"><tbody><tr><td colspan=\"2\"><p>You recently requested on $contact_user_pwd_last_changed to be able to reset your account password. </p><p>Click on the link below to reset your password:</p><p> <a href="$contact_user_link_guid">$contact_user_link_guid</a> </p> </td> </tr><tr><td colspan=\"2\"></td> </tr> </tbody></table> </div>',
        'txt_body' =>
'
You recently requested on $contact_user_pwd_last_changed to be able to reset your account password.

Click on the link below to reset your password:

$contact_user_link_guid',
        'name' => 'Forgot Password email',
        ),

'portal_forgot_password_email_link' => [
    'name' => 'Portal Forgot Password Email',
    'subject' => 'Reset your account password',
    'description' => 'This template is used to send a user a link to click to reset the Portal user\'s account password.',
    'body' => '<div><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width="550" align=\"\&quot;\&quot;center\&quot;\&quot;\"><tbody><tr><td colspan=\"2\"><p>You recently requested to reset your account password. </p><p>Click on the link below to reset your password:</p><p> <a href="$portal_user_link_guid">$portal_user_link_guid</a> </p> </td> </tr><tr><td colspan=\"2\"></td> </tr> </tbody></table> </div>',
    'txt_body' =>
'
    You recently requested to reset your account password.

    Click on the link below to reset your password:

    $portal_user_link_guid',
],

    'portal_password_reset_confirmation_email' => [
        'name' => 'Portal Password Reset Confirmation Email',
        'subject' => 'Your account password has been reset',
        'description' => 'This template is used to send a confirmation to a Portal user that their accout password has been reset.',
        'body' => '<div><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width="550" align=\"\&quot;\&quot;center\&quot;\&quot;\"><tbody><tr><td colspan=\"2\"><p>This email is to confirm that your Portal account password has been reset. </p><p>Use the link below to log in to the Portal:</p><p> <a href="$portal_login_url">$portal_login_url</a> </p> </td> </tr><tr><td colspan=\"2\"></td> </tr> </tbody></table> </div>',
        'txt_body' =>
            '
    This email is to confirm that your Portal account password has been reset.

    Use the link below to log in to the Portal:

    $portal_login_url',
    ],

);
