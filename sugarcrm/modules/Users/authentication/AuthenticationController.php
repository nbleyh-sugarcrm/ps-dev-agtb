<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
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
 * by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights Reserved.
 */

 // $Id: AuthenticationController.php 56522 2010-05-17 20:22:41Z jmertic $
class AuthenticationController
{
	public $loggedIn = false; //if a user has attempted to login
	public $authenticated = false;
	public $loginSuccess = false;// if a user has successfully logged in

	protected static $authcontrollerinstance = null;

    /**
     * @var SugarAuthenticate
     */
    public $authController;

	/**
	 * Creates an instance of the authentication controller and loads it
	 *
	 * @param STRING $type - the authentication Controller - default to SugarAuthenticate
	 * @return AuthenticationController -
	 */
	public function __construct($type = 'SugarAuthenticate')
	{
	    if ($type == 'SugarAuthenticate' && !empty($GLOBALS['system_config']->settings['system_ldap_enabled']) && empty($_SESSION['sugar_user'])){
			$type = 'LDAPAuthenticate';
        }

        // check in custom dir first, in case someone want's to override an auth controller
        if(!SugarAutoLoader::requireWithCustom('modules/Users/authentication/'.$type.'/' . $type . '.php')) {
            require_once('modules/Users/authentication/SugarAuthenticate/SugarAuthenticate.php');
            $type = 'SugarAuthenticate';
        }

        $this->authController = new $type();
	}


	/**
	 * Returns an instance of the authentication controller
	 *
	 * @param string $type this is the type of authetnication you want to use default is SugarAuthenticate
	 * @return an instance of the authetnciation controller
	 */
	public static function getInstance($type = null)
	{
	    global $sugar_config;
	    if(empty($type)) {
	        $type = !empty($sugar_config['authenticationClass'])? $sugar_config['authenticationClass'] : 'SugarAuthenticate';
	    }
		if (empty(self::$authcontrollerinstance)) {
			self::$authcontrollerinstance = new AuthenticationController($type);
		}

		return self::$authcontrollerinstance;
	}

	/**
	 * This function is called when a user initially tries to login.
	 *
	 * @param string $username
	 * @param string $password
	 * @param array $params Login parameters:
	 * - noHooks - don't run logic hooks
	 * - noRedirect - don't redirect if not logged in
	 * - passwordEncrypted - is password plaintext (false) or md5 (true)?
	 * @return boolean true if the user successfully logs in or false otherwise.
	 */
	public function login($username, $password, $params = array())
	{
		//kbrill bug #13225
		$_SESSION['loginAttempts'] = (isset($_SESSION['loginAttempts']))? $_SESSION['loginAttempts'] + 1: 1;
		unset($GLOBALS['login_error']);

		if($this->loggedIn)return $this->loginSuccess;
		if(empty($params['noHooks'])) {
		    LogicHook::initialize()->call_custom_logic('Users', 'before_login');
		}

		$this->loginSuccess = $this->authController->loginAuthenticate($username, $password, false, $params);
		$this->loggedIn = true;

		if($this->loginSuccess){
			//Ensure the user is authorized
			checkAuthUserStatus();

			loginLicense();
			if(!empty($GLOBALS['login_error'])){
				unset($_SESSION['authenticated_user_id']);
				$GLOBALS['log']->fatal('FAILED LOGIN: potential hack attempt:'.$GLOBALS['login_error']);
				$this->loginSuccess = false;
				return false;
			}

			//call business logic hook
			if(isset($GLOBALS['current_user']) && empty($params['noHooks']))
				$GLOBALS['current_user']->call_custom_logic('after_login');

			// Check for running Admin Wizard
            $config = Administration::getSettings();
		    if ( is_admin($GLOBALS['current_user']) && empty($config->settings['system_adminwizard']) && isset($_REQUEST['action']) && $_REQUEST['action'] != 'AdminWizard' ) {

                if ( isset($params['noRedirect']) && $params['noRedirect'] == true ) {
                    $this->nextStep = array('module'=>'Configurator','action'=>'AdminWizard');
                } else {
                    ob_clean();
                    $GLOBALS['module'] = 'Configurator';
                    $GLOBALS['action'] = 'AdminWizard';
                    header("Location: index.php?module=Configurator&action=AdminWizard");
                    sugar_cleanup(true);
                }
			}

			$ut = $GLOBALS['current_user']->getPreference('ut');
			$checkTimeZone = true;
			if (is_array($params) && !empty($params) && isset($params['passwordEncrypted'])) {
				$checkTimeZone = false;
			} // if
			if(empty($ut) && $checkTimeZone && isset($_REQUEST['action']) && $_REQUEST['action'] != 'SetTimezone' && $_REQUEST['action'] != 'SaveTimezone' ) {
			    if ( isset($params['noRedirect']) && $params['noRedirect'] == true && empty($this->nextStep) ) {
                    $this->nextStep = array('module'=>'Users','action'=>'Wizard');
                } else {
                    $GLOBALS['module'] = 'Users';
                    $GLOBALS['action'] = 'Wizard';
                    ob_clean();
                    header("Location: index.php?module=Users&action=Wizard");
                    sugar_cleanup(true);
                }
			}
		}else{
			//kbrill bug #13225
			if(empty($params['noHooks'])) {
			    LogicHook::initialize();
			    $GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
			}
			$GLOBALS['log']->fatal('FAILED LOGIN:attempts[' .$_SESSION['loginAttempts'] .'] - '. $username);
		}
		// if password has expired, set a session variable

		return $this->loginSuccess;
	}

	/**
	 * This is called on every page hit.
	 * It returns true if the current session is authenticated or false otherwise
	 *
	 * @return bool
	 */
	public function sessionAuthenticate()
	{
		if(!$this->authenticated){
			$this->authenticated = $this->authController->sessionAuthenticate();
		}
		if($this->authenticated){
			if(!isset($_SESSION['userStats']['pages'])){
			    $_SESSION['userStats']['loginTime'] = time();
			    $_SESSION['userStats']['pages'] = 0;
			}
			$_SESSION['userStats']['lastTime'] = time();
			$_SESSION['userStats']['pages']++;

		}
		return $this->authenticated;
	}

	/**
	 * Called when a user requests to logout. Should invalidate the session and redirect
	 * to the login page.
	 */
	public function logout()
	{
		$GLOBALS['current_user']->call_custom_logic('before_logout');
		$this->authController->logout();
		LogicHook::initialize();
		$GLOBALS['logic_hook']->call_custom_logic('Users', 'after_logout');
	}

	/**
	 * Does this controller require external authentication?
	 * @return boolean
	 */
	public function isExternal()
	{
	    return $this->authController instanceof SugarAuthenticateExternal;
	}

	/**
	 * Get URL for external login
	 * @return string
	 */
	public function getLoginUrl()
	{
	    if($this->isExternal()) {
	        return $this->authController->getLoginUrl();
	    }
	    return false;
	}
}
