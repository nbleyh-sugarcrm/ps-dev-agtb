<?php
//FILE SUGARCRM flav=pro ONLY
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You may
 * not use this file except in compliance with the License. Under the terms of the
 * license, You shall not, among other things: 1) sublicense, resell, rent, lease,
 * redistribute, assign or otherwise transfer Your rights to the Software, and 2)
 * use the Software for timesharing or service bureau purposes such as hosting the
 * Software for commercial gain and/or for the benefit of a third party.  Use of
 * the Software may be subject to applicable fees and any use of the Software
 * without first paying applicable fees is strictly prohibited.  You do not have
 * the right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.  Your Warranty, Limitations of liability and Indemnity are
 * expressly stated in the License.  Please refer to the License for the specific
 * language governing these rights and limitations under the License.
 * Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.;
 * All Rights Reserved.
 ********************************************************************************/

require_once('modules/Forecasts/ForecastsDefaults.php');

class ForecastsDefaultsTest extends Sugar_PHPUnit_Framework_TestCase
{
    // holds any current config already set up in the DB for forecasts
    private static $currentConfig;

    public static function setUpBeforeClass() {
        // Save the current config to be put back later
        $admin = BeanFactory::getBean('Administration');
        self::$currentConfig = $admin->getConfigForModule('Forecasts');

        parent::setUpBeforeClass();
    }
    public function setUp()
    {
        parent::setUp();

        //Clear config table of Forecasts values before each test, so each test can setup it's own db
        $db = DBManagerFactory::getInstance();
        $db->query("DELETE FROM config WHERE category = 'Forecasts' ");
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public static function tearDownAfterClass() {
        // Clear config table of Forecasts values after the last test in case tests
        // set any values that the bean doesnt normally have
        $db = DBManagerFactory::getInstance();
        $db->query("DELETE FROM config WHERE category = 'Forecasts' ");

        $admin = BeanFactory::getBean('Administration');
        foreach(self::$currentConfig as $name => $value)
        {
            if(is_array($value))
            {
                $admin->saveSetting('Forecasts', $name, json_encode($value), 'base');
            } else {
                $admin->saveSetting('Forecasts', $name, $value, 'base');
            }
        }
        parent::tearDownAfterClass();
    }

    /**
     * Tests the setupForecastSettings for a fresh install where configs are not in the db
     *
     * @group forecasts
     */
    public function testSetupForecastSettingsFreshInstall() {

        ForecastsDefaults::setupForecastSettings();

        $admin = BeanFactory::getBean('Administration');
        $adminConfig = $admin->getConfigForModule('Forecasts');

        // On fresh install, is_setup should be 0 in the DB
        $this->assertEquals(0, $adminConfig['is_setup'], "On a fresh install, Forecasts config is_setup should be 0");
    }

    /**
     * Tests the setupForecastSettings for an upgrade where configs are already in the db
     * and is_setup == 0, should force defaults on the db
     *
     * @group forecasts
     */
    public function testSetupForecastSettingsUpgradeNotSetup() {
        $db = DBManagerFactory::getInstance();

        // set up config table with one test value and is_setup set to 0
        // test should show that if is_setup is 0, already existing values are overwritten
        // by any new defaults used in the ForecastsDefaults class
        $timeperiodType = 'previousVersionDefaultTimePeriod1';
        $setupConfig = array(
            'is_setup' => 0,
            'timeperiod_type' => $timeperiodType
        );

        $admin = BeanFactory::getBean('Administration');
        foreach($setupConfig as $name => $value)
        {
            if(is_array($value))
            {
                $admin->saveSetting('Forecasts', $name, json_encode($value), 'base');
            } else {
                $admin->saveSetting('Forecasts', $name, $value, 'base');
            }
        }

        ForecastsDefaults::setupForecastSettings(true);

        $admin = BeanFactory::getBean('Administration');
        $adminConfig = $admin->getConfigForModule('Forecasts');

        $defaultConfig = ForecastsDefaults::getDefaults();

        // Check value from ForecastDefaults and make sure they're in the db on upgrade
        $this->assertNotEquals($timeperiodType, $adminConfig['timeperiod_type'], "On an upgrade with config data existing but NOT set up, new default settings should override pre-existing settings in the config table");
    }

    /**
     * Tests the setupForecastSettings for an upgrade where configs are already in the db
     * and is_setup == 1
     *
     * @group forecasts
     */
    public function testSetupForecastSettingsUpgradeAlreadySetup() {
        $db = DBManagerFactory::getInstance();

        // set up config table with one test value and is_setup set to 1
        // test should show that if is_setup is 1, already existing values are preserved
        // while if the value doesnt exist, defaults are used
        $timeperiodType = 'testTimePeriod1';
        $setupConfig = array(
            'is_setup' => 1,
            'timeperiod_type' => $timeperiodType
        );

        $admin = BeanFactory::getBean('Administration');
        foreach($setupConfig as $name => $value)
        {
            if(is_array($value))
            {
                $admin->saveSetting('Forecasts', $name, json_encode($value), 'base');
            } else {
                $admin->saveSetting('Forecasts', $name, $value, 'base');
            }
        }

        ForecastsDefaults::setupForecastSettings(true);

        $admin = BeanFactory::getBean('Administration');
        $adminConfig = $admin->getConfigForModule('Forecasts');

        $this->assertEquals($timeperiodType, $adminConfig['timeperiod_type'], "On an upgrade with config data already set up, pre-existing settings should be preserved");

        // Check value from ForecastDefaults
        $this->assertEquals('Annual', $adminConfig['timeperiod_interval'], "On an upgrade with config data already set up, default settings that don't override pre-existing settings should be in the config table");
    }

}
