<?php
//FILE SUGARCRM flav=pro ONLY

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


require_once('tests/rest/RestTestBase.php');

/***
 * Used to test Forecast Module endpoints from ForecastModuleApi.php
 *
 * @group forecastapi
 * @group forecasts
 */
class ForecastsWorksheetManagerApiTest extends RestTestBase
{
    /**
     * @var array
     */
    private static $reportee;

    /**
     * @var array
     */
    private static $reportee2;

    /**
     * @var array
     */
    protected static $manager;

    /**
     * @var array
     */
    protected static $manager2;

    /**
     * @var TimePeriod
     */
    protected static $timeperiod;

    /**
     * @var array
     */
    protected static $managerData;

    /**
     * @var array
     */
    protected static $managerData2;

    /**
     * @var array
     */
    protected static $repData;

    /**
     * @var Administration
     */
    protected static $admin;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        SugarTestHelper::setUp('app_strings');
        SugarTestHelper::setUp('app_list_strings');
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('beanList');

        self::$manager = SugarTestForecastUtilities::createForecastUser(array(
            'opportunities' => array(
                'total' => 5,
                'include_in_forecast' => 5
            ),
        ));
        
        //set up another manager, and assign him to the first manager manually so his data is generated
        //correctly.
        self::$manager2 = SugarTestForecastUtilities::createForecastUser(array(
            'opportunities' => array(
                'total' => 5,
                'include_in_forecast' => 5
            ),
        ));
        
        self::$manager2["user"]->reports_to_id = self::$manager['user']->id;
        self::$manager2["user"]->save();

        self::$reportee = SugarTestForecastUtilities::createForecastUser(array(
            'user' => array(
                'reports_to' => self::$manager['user']->id
            ),
            'opportunities' => array(
                'total' => 5,
                'include_in_forecast' => 5
            )
        ));
        self::$reportee2 = SugarTestForecastUtilities::createForecastUser(array(
            'user' => array(
                'reports_to' => self::$manager2['user']->id
            ),
            'opportunities' => array(
                'total' => 5,
                'include_in_forecast' => 5
            )
        ));

        self::$timeperiod = SugarTestForecastUtilities::getCreatedTimePeriod();

        self::$managerData = array("amount" => self::$manager['opportunities_total'],
            "quota" => self::$manager['quota']->amount,
            "quota_id" => self::$manager['quota']->id,
            "best_case" => self::$manager['forecast']->best_case,
            "likely_case" => self::$manager['forecast']->likely_case,
            "worst_case" => self::$manager['forecast']->worst_case,
            "best_adjusted" => self::$manager['worksheet']->best_case,
            "likely_adjusted" => self::$manager['worksheet']->likely_case,
            "worst_adjusted" => self::$manager['worksheet']->worst_case,
            "commit_stage" => self::$manager['worksheet']->commit_stage,
            "forecast_id" => self::$manager['forecast']->id,
            "worksheet_id" => self::$manager['worksheet']->id,
            "show_opps" => true,
            "id" => self::$manager['user']->id,
            "name" => 'Opportunities (' . self::$manager['user']->first_name . ' ' . self::$manager['user']->last_name . ')',
            "user_id" => self::$manager['user']->id,

        );

        self::$managerData2 = array("amount" => self::$manager2['opportunities_total'],
            "quota" => self::$manager2['quota']->amount,
            "quota_id" => self::$manager2['quota']->id,
            "best_case" => self::$manager2['forecast']->best_case,
            "likely_case" => self::$manager2['forecast']->likely_case,
            "worst_case" => self::$manager2['forecast']->worst_case,
            "best_adjusted" => self::$manager2['worksheet']->best_case,
            "likely_adjusted" => self::$manager2['worksheet']->likely_case,
            "worst_adjusted" => self::$manager2['worksheet']->worst_case,
            "commit_stage" =>self::$manager2['worksheet']->commit_stage,
            "forecast_id" => self::$manager2['forecast']->id,
            "worksheet_id" => self::$manager2['worksheet']->id,
            "show_opps" => true,
            "id" => self::$manager2['user']->id,
            "name" => 'Opportunities (' . self::$manager2['user']->first_name . ' ' . self::$manager2['user']->last_name . ')',
            "user_id" => self::$manager2['user']->id,

        );

        self::$repData = array("amount" => self::$reportee['opportunities_total'],
            "quota" => self::$reportee['quota']->amount,
            "quota_id" => self::$reportee['quota']->id,
            "best_case" => self::$reportee['forecast']->best_case,
            "likely_case" => self::$reportee['forecast']->likely_case,
            "worst_case" => self::$reportee['forecast']->worst_case,
            "best_adjusted" => self::$reportee['worksheet']->best_case,
            "likely_adjusted" => self::$reportee['worksheet']->likely_case,
            "worst_adjusted" => self::$reportee['worksheet']->worst_case,
            "commit_stage" => self::$reportee['worksheet']->commit_stage,
            "forecast_id" => self::$reportee['forecast']->id,
            "worksheet_id" => self::$reportee['worksheet']->id,
            "show_opps" => true,
            "id" => self::$reportee['user']->id,
            "name" => self::$reportee['user']->first_name . ' ' . self::$reportee['user']->last_name,
            "user_id" => self::$reportee['user']->id,

        );

        // get current settings
        self::$admin = BeanFactory::getBean('Administration');
    }

    public function setUp()
    {
        //Create an anonymous user for login purposes/
        $this->_user = self::$manager['user'];
        $this->_oldUser = $GLOBALS['current_user'];
        $GLOBALS['current_user'] = $this->_user;
        //Reset all columns to be shown
        self::$admin->saveSetting('Forecasts', 'show_worksheet_likely', 1, 'base');
        self::$admin->saveSetting('Forecasts', 'show_worksheet_best', 1, 'base');
        self::$admin->saveSetting('Forecasts', 'show_worksheet_worst', 1, 'base');
    }

    public static function tearDownAfterClass()
    {
        SugarTestForecastUtilities::cleanUpCreatedForecastUsers();
        SugarTestForecastUtilities::removeAllCreatedForecasts();
        parent::tearDown();
    }

    //Override tearDown so we don't lose the current user
    public function tearDown()
    {
        $GLOBALS['current_user'] = $this->_oldUser;
    }


    /**
     * This test asserts that we get back data.
     *
     * @group forecastapi
     * @group forecasts
     */
    public function testPassedInUserIsManager()
    {
        $restReply = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager['user']->id . '&timeperiod_id=' . self::$timeperiod->id);
        $this->assertNotEmpty($restReply['reply'], "Reply empty, user not a manager");
    }

    /**
     * @group forecastapi
     * @group forecasts
     */
    public function testPassedInUserIsNotManagerReturnsEmpty()
    {
        $restReply = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$reportee['user']->id . '&timeperiod_id=' . self::$timeperiod->id);
        $this->assertEmpty($restReply['reply'], "rest reply is not empty");
    }

    /**
     * @group forecastapi
     * @group forecasts
     */
    public function testCurrentUserIsNotManagerReturnsEmpty()
    {
        // save the current user
        $_old_current_user = $GLOBALS['current_user'];

        // set the current user to the reportee
        $this->_user = self::$reportee['user'];
        $GLOBALS['current_user'] = $this->_user;

        // run the test
        $restReply = $this->_restCall("ForecastManagerWorksheets?timeperiod_id=" . self::$timeperiod->id);

        $this->assertEmpty($restReply['reply'], "rest reply is not empty");

        // reset current user;
        $GLOBALS['current_user'] = $_old_current_user;
        $this->_user = $_old_current_user;
    }

    /**
     * @bug 54619
     * @group 54619
     * @group forecastapi
     * @group forecasts
     */
    public function testAdjustedNumbersShouldBeSameAsNonAdjustedColumns()
    {
        $rep_worksheet = BeanFactory::getBean('Worksheet', self::$repData['worksheet_id']);
        $rep_worksheet->deleted = 1;
        $rep_worksheet->save();
        $GLOBALS['db']->commit();

        $localRepData = self::$repData;

        $localRepData['best_adjusted'] = SugarTestForecastUtilities::formatTestNumber($localRepData['best_case']);
        $localRepData['likely_adjusted'] = SugarTestForecastUtilities::formatTestNumber($localRepData['likely_case']);
        $localRepData['worst_adjusted'] = SugarTestForecastUtilities::formatTestNumber($localRepData['worst_case']);
        $localRepData['forecast'] = SugarTestForecastUtilities::formatTestNumber(0);
        $localRepData['worksheet_id'] = '';

        $restReply = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager['user']->id . '&timeperiod_id=' . self::$timeperiod->id);
        foreach ($restReply['reply'] as $record) {
            if ($record["user_id"] == $localRepData["user_id"]) {
                $this->assertEquals($localRepData['best_adjusted'], $record['best_adjusted'], "best_adjusted numbers should be the same");
                $this->assertEquals($localRepData['likely_adjusted'], $record['likely_adjusted'], "likely_adjusted numbers should be the same");
                $this->assertEquals($localRepData['worst_adjusted'], $record['worst_adjusted'], "worst_adjusted numbers should be the same");
                $this->assertEquals($localRepData['forecast'], $record['forecast'], "forecast numbers should be the same");
                break;
            }
        }

        $rep_worksheet->deleted = 0;
        $rep_worksheet->save();
        $GLOBALS['db']->commit();
    }

    /**
     * @bug 54655
     * @group forecastapi
     * @group forecasts
     */
    public function testBlankLineInWorksheetAfterDeletingASalesRep()
    {
        // temp reportee
        $tmp = SugarTestUserUtilities::createAnonymousUser();
        $tmp->reports_to_id = self::$manager['user']->id;
        $tmp->deleted = 1;
        $tmp->save();

        $restReply = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager['user']->id . '&timeperiod_id=' . self::$timeperiod->id);

        // we should only have one row returned
        $this->assertEquals(3, count($restReply['reply']), "deleted user's data should not be listed in worksheet table");
    }

    /**
     * @bug 55172
     * @group forecastapi
     * @group forecasts
     */
    public function testAmountIsZeroWhenReporteeHasNoCommittedForecast()
    {
        $rep_forecast = BeanFactory::getBean('Forecasts', self::$repData['forecast_id']);
        $rep_forecast->deleted = 1;
        $rep_forecast->save();

        $restReply = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager['user']->id . '&timeperiod_id=' . self::$timeperiod->id);

        $replyAmount = 0;
		//check to see if the Quota was auto calculated
		foreach($restReply["reply"] as $record)
        {
        	if($record["user_id"] == self::$repData['user_id'])
        	{
                $replyAmount = $record["amount"];
                break;
        	}
        }
        $this->assertSame(0, $replyAmount);

        $rep_forecast->deleted = 0;
        $rep_forecast->save();
    }

    /**
     * @bug 55181
     * @group forecastapi
     * @group forecasts
     */
    public function testManagerAndReporteeWithNoDataReturnsAllZeros()
    {
        global $current_user;

        $tmp1 = SugarTestUserUtilities::createAnonymousUser();

        $_current_user = $current_user;

        $current_user = $tmp1;

        $tmp2 = SugarTestUserUtilities::createAnonymousUser();
        $tmp2->reports_to_id = $tmp1->id;
        $tmp2->save();

        $restReply = $this->_restCall("ForecastManagerWorksheets?user_id=" . $tmp1->id . '&timeperiod_id=' . self::$timeperiod->id);

        $expected = array(
            0 =>
            array(
                'amount' => 0,
                'quota' => 0,
                'quota_id' => '',
                'best_case' => 0,
                'likely_case' => 0,
                'worst_case' => 0,
                'best_adjusted' => 0,
                'likely_adjusted' => 0,
                'worst_adjusted' => 0,
                'forecast' => 0,
                'forecast_id' => '',
                'worksheet_id' => '',
                'show_opps' => true,
                'id' => $tmp1->id,
                'name' => 'Opportunities (' . $tmp1->first_name . ' ' . $tmp1->last_name . ')',
                'user_id' => $tmp1->id,
            ),
            1 =>
            array(
                'amount' => 0,
                'quota' => 0,
                'quota_id' => '',
                'best_case' => 0,
                'likely_case' => 0,
                'worst_case' => 0,
                'best_adjusted' => 0,
                'likely_adjusted' => 0,
                'worst_adjusted' => 0,
                'forecast' => 0,
                'forecast_id' => '',
                'worksheet_id' => '',
                'show_opps' => true,
                'id' => $tmp2->id,
                'name' => $tmp2->first_name . ' ' . $tmp2->last_name,
                'user_id' => $tmp2->id,
            ),
        );

        $this->assertEquals($expected[0]['amount'], $restReply['reply'][0]['amount']);
        $this->assertEquals($expected[0]['quota'], $restReply['reply'][0]['quota']);
        $this->assertEquals($expected[0]['best_case'], $restReply['reply'][0]['best_case']);
        $this->assertEquals($expected[0]['likely_case'], $restReply['reply'][0]['likely_case']);
        $this->assertEquals($expected[0]['worst_case'], $restReply['reply'][0]['worst_case']);
        $this->assertEquals($expected[0]['best_adjusted'], $restReply['reply'][0]['best_adjusted']);
        $this->assertEquals($expected[0]['likely_adjusted'], $restReply['reply'][0]['likely_adjusted']);
        $this->assertEquals($expected[0]['worst_adjusted'], $restReply['reply'][0]['worst_adjusted']);

        $this->assertEquals($expected[1]['amount'], $restReply['reply'][1]['amount']);
        $this->assertEquals($expected[1]['quota'], $restReply['reply'][1]['quota']);
        $this->assertEquals($expected[1]['best_case'], $restReply['reply'][1]['best_case']);
        $this->assertEquals($expected[1]['likely_case'], $restReply['reply'][1]['likely_case']);
        $this->assertEquals($expected[1]['worst_case'], $restReply['reply'][1]['worst_case']);
        $this->assertEquals($expected[1]['best_adjusted'], $restReply['reply'][1]['best_adjusted']);
        $this->assertEquals($expected[1]['likely_adjusted'], $restReply['reply'][1]['likely_adjusted']);
        $this->assertEquals($expected[1]['worst_adjusted'], $restReply['reply'][1]['worst_adjusted']);

        $current_user = $_current_user;
    }

    /**
     * @group forecastapi
     * @group forecasts
     */
    public function testManagerReporteeManagerReturnesProperValues()
    {
        // create extra reps
        $rep1 = SugarTestForecastUtilities::createForecastUser(array('user' => array('reports_to' => self::$reportee['user']->id)));
        $rep2 = SugarTestForecastUtilities::createForecastUser(array('user' => array('reports_to' => self::$reportee['user']->id)));

        // create a rollup forecast for the new manager
        $tmpForecast = SugarTestForecastUtilities::createManagerRollupForecast(self::$reportee, $rep1, $rep2);

        // create a worksheet for the new managers user
        $tmpWorksheet = SugarTestWorksheetUtilities::createWorksheet();
        $tmpWorksheet->related_id = self::$reportee['user']->id;
        $tmpWorksheet->user_id = self::$reportee['user']->reports_to_id;
        $tmpWorksheet->forecast_type = "Rollup";
        $tmpWorksheet->related_forecast_type = "Direct";
        $tmpWorksheet->timeperiod_id = self::$timeperiod->id;
        $tmpWorksheet->best_case = $tmpForecast->best_case+100;
        $tmpWorksheet->likely_case = $tmpForecast->likely_case+100;
        $tmpWorksheet->worst_case = $tmpForecast->worst_case-100;
        $tmpWorksheet->save();

        $restReply = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager['user']->id . '&timeperiod_id=' . self::$timeperiod->id);

        $expected = array(
            "amount" => self::$reportee['opportunities_total'] + $rep1['opportunities_total'] + $rep2['opportunities_total'],
            "best_adjusted" => SugarTestForecastUtilities::formatTestNumber($tmpWorksheet->best_case),
            "best_case" => SugarTestForecastUtilities::formatTestNumber($tmpForecast->best_case),
            "forecast_id" => $tmpForecast->id,
            "id" => self::$reportee["user"]->id,
            "likely_adjusted" => SugarTestForecastUtilities::formatTestNumber($tmpWorksheet->likely_case),
            "likely_case" => SugarTestForecastUtilities::formatTestNumber($tmpForecast->likely_case),
            "name" => self::$reportee["user"]->first_name . " " . self::$reportee["user"]->last_name,
            "quota" => SugarTestForecastUtilities::formatTestNumber(self::$reportee['quota']->amount),
            "quota_id" => self::$reportee['quota']->id,
            "show_opps" => false,
            "user_id" => self::$reportee["user"]->id,
            "worksheet_id" => $tmpWorksheet->id,
            "worst_adjusted" => SugarTestForecastUtilities::formatTestNumber($tmpWorksheet->worst_case),
            "worst_case" => SugarTestForecastUtilities::formatTestNumber($tmpForecast->worst_case),
            "timeperiod_id" => self::$timeperiod->id
        );

        foreach ($restReply['reply'] as $record) {
            if ($record["user_id"] == self::$reportee["user"]->id) {
                $this->assertEquals($expected["amount"], $record["amount"], 'Failed retrieving correct amount value');
                $this->assertEquals($expected["best_adjusted"], $record["best_adjusted"], 'Failed retrieving correct best_adjusted value');
                $this->assertEquals($expected["best_case"], $record["best_case"], 'Failed retrieving correct best_case value');
                $this->assertEquals($expected["forecast_id"], $record["forecast_id"], 'Failed retrieving correct forecast_id value');
                $this->assertEquals($expected["id"], $record["id"], 'Failed retrieving correct id value');
                $this->assertEquals($expected["likely_adjusted"], $record["likely_adjusted"], 'Failed retrieving correct likely_adjusted value');
                $this->assertEquals($expected["likely_case"], $record["likely_case"], 'Failed retrieving correct likely_case value');
                $this->assertEquals($expected["name"], $record["name"]);
                $this->assertEquals($expected["quota"], $record["quota"]);
                $this->assertEquals($expected["quota_id"], $record["quota_id"]);
                $this->assertEquals($expected["show_opps"], $record["show_opps"]);
                $this->assertEquals($expected["user_id"], $record["user_id"]);
                $this->assertEquals($expected["worksheet_id"], $record["worksheet_id"]);
                $this->assertEquals($expected["worst_adjusted"], $record["worst_adjusted"]);
                $this->assertEquals($expected["worst_case"], $record["worst_case"]);
                $this->assertEquals($expected["timeperiod_id"], $record["timeperiod_id"]);
                break;
            }
        }
    }


    /**
     * This test is to see that the data returned for the name field is set correctly when locale name format changes
     *
     * @group testGetLocaleFormattedName
     * @group forecastapi
     * @group forecasts
     */
    public function testGetLocaleFormattedName()
    {
        global $locale, $current_language;
        $defaultPreference = $this->_user->getPreference('default_locale_name_format');
        $this->_user->setPreference('default_locale_name_format', 'l, f', 0, 'global');
        $this->_user->savePreferencesToDB();
        $this->_user->reloadPreferences();

        $restReply = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager['user']->id . '&timeperiod_id=' . self::$timeperiod->id);
        $current_module_strings = return_module_language($current_language, 'Forecasts');
        $expectedName = string_format($current_module_strings['LBL_MY_OPPORTUNITIES'],
            array($locale->getLocaleFormattedName(self::$manager['user']->first_name, self::$manager['user']->last_name))
        );
        $restUserName = '';
        foreach($restReply['reply'] as $record)
        {
            if($record['user_id'] == self::$manager['user']->id) {
                $restUserName = $record['name'];
                break;
            }
        }
        $this->assertEquals($expectedName, $restUserName);
        $this->_user->setPreference('default_locale_name_format', $defaultPreference, 0, 'global');
        $this->_user->savePreferencesToDB();
        $this->_user->reloadPreferences();
    }

    /**
     * @group forecastapi
     * @group forecasts
     */
    public function testWorksheetVersionSave()
    {
    	$version = "";
    	
        $postData = array("amount" => self::$managerData["amount"],
            "quota" => self::$managerData["quota"],
            "quota_id" => self::$managerData["quota_id"],
            "best_case" => self::$managerData["best_case"],
            "likely_case" => self::$managerData["likely_case"],
            "worst_case" => self::$managerData["worst_case"],
            "best_adjusted" => self::$managerData["best_adjusted"],
            "likely_adjusted" => self::$managerData["likely_adjusted"],
            "worst_adjusted" => self::$managerData["worst_adjusted"],
            "commit_stage" => self::$managerData["commit_stage"],
            "forecast_id" => self::$managerData["forecast_id"],
            "id" => self::$managerData["id"],
            "worksheet_id" => self::$managerData["worksheet_id"],
            "show_opps" => self::$managerData["show_opps"],
            "name" => self::$managerData["name"],
            "user_id" => self::$managerData["user_id"],
            "current_user" => self::$managerData["user_id"],
            "timeperiod_id" => self::$timeperiod->id,
            "draft" => 1
        );

        //save draft version
        $response = $this->_restCall("ForecastManagerWorksheets/" . self::$managerData["user_id"], json_encode($postData), "PUT");

        $db = DBManagerFactory::getInstance();
        $db->commit();

        //see if draft version comes back
        $response = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager['user']->id . '&timeperiod_id=' . self::$timeperiod->id);
		
		foreach($response["reply"] as $record)
        {
        	if($record["id"] == $postData["id"])
        	{
        		$version = $record["version"];
                break;
        	}
        }
	
        $this->assertEquals("0", $version, "Draft version was not returned.");

        sleep(1);

        //Now, save as a regular version so things will be reset.
        $postData["draft"] = 0;
        $response = $this->_restCall("ForecastManagerWorksheets/" . self::$managerData["user_id"], json_encode($postData), "PUT");

        $db->commit();

        //now, see if the regular version comes back.
        $response = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager['user']->id . '&timeperiod_id=' . self::$timeperiod->id);
        foreach($response["reply"] as $record)
        {
        	if($record["id"] == $postData["id"])
        	{
        		$version = $record["version"];
                break;
        	}
        }
        $this->assertEquals("1", $version, "Comitted version was not returned.");

    }

    /**
     * @group forecastapi
     * @group forecasts
     */
    public function testWorksheetDraftVisibility()
    {
    	$oldUser = $GLOBALS["current_user"];
    	
        self::$managerData2["best_adjusted"] = self::$managerData2["best_adjusted"] + 100;
		$best_adjusted = "";
		
        $postData = array("amount" => self::$managerData2["amount"],
            "quota" => self::$managerData2["quota"],
            "quota_id" => self::$managerData2["quota_id"],
            "best_case" => self::$managerData2["best_case"],
            "likely_case" => self::$managerData2["likely_case"],
            "worst_case" => self::$managerData2["worst_case"],
            "best_adjusted" => self::$managerData2["best_adjusted"],
            "likely_adjusted" => self::$managerData2["likely_adjusted"],
            "worst_adjusted" => self::$managerData2["worst_adjusted"],
            "commit_stage" => self::$managerData2["commit_stage"],
            "forecast_id" => self::$managerData2["forecast_id"],
            "id" => self::$managerData2["id"],
            "worksheet_id" => self::$managerData2["worksheet_id"],
            "show_opps" => self::$managerData2["show_opps"],
            "name" => self::$managerData2["name"],
            "user_id" => self::$managerData2["user_id"],
            "current_user" => self::$managerData2["user_id"],
            "timeperiod_id" => self::$timeperiod->id,
            "draft" => 1
        );
        // set the current user to manager2
        $this->_user = self::$manager2['user'];
        $GLOBALS['current_user'] = $this->_user;
        $this->authToken = "";

        //save draft version for manager2
        $response = $this->_restCall("ForecastManagerWorksheets/" . self::$managerData2["user_id"], json_encode($postData), "PUT");

        $db = DBManagerFactory::getInstance();
        $db->commit();

        // reset current user to manager1
        $this->_user = self::$manager['user'];
        $GLOBALS['current_user'] = $this->_user;
        $this->authToken = "";

        //Check the table as a manager1 to see if the draft version is hidden
        $response = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager2['user']->id . '&timeperiod_id=' . self::$timeperiod->id);
        foreach($response["reply"] as $record)
        {
        	if($record["id"] == $postData["id"])
        	{
        		$best_adjusted = $record["best_adjusted"];
                break;
        	}
        }
        
        $this->assertEquals(self::$managerData2["best_adjusted"] - 100, $best_adjusted, "Draft version was returned");
        
        //Now, save as a regular version so things will be reset.
        $postData["draft"] = 0;
        $response = $this->_restCall("ForecastManagerWorksheets/" . self::$managerData2["user_id"], json_encode($postData), "PUT");

        $db->commit();
        
        // set the current user to original user
        $this->_user = $oldUser;
        $GLOBALS["current_user"] = $oldUser;
        $this->authToken = "";
    }
    
     /**
     * @group forecastapi
     * @group forecasts
     */
     public function testForecastWorksheetQuotaRecalc()
     {
     	$oldUser = $GLOBALS["current_user"];
     	$quota = "";
     	$index = 0;
     	// reset current user to manager1
        $this->_user = self::$manager['user'];
        $GLOBALS['current_user'] = $this->_user;
        $this->authToken = "";
        
        $response = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager["user"]->id . "&timeperiod_id=" . self::$timeperiod->id);
            	
    	foreach($response["reply"] as $record)
        {
        	if($record["user_id"] == self::$manager2["user"]->id)
        	{        		
        		break;
        	}
        	$index++;
        }
    	  
    	$postData = array(	 "amount" => $response["reply"][$index]["amount"],
							 "quota" => 5000,
                             "quota_id" => $response["reply"][$index]["quota_id"],
                             "best_case" => $response["reply"][$index]["best_case"],
                             "likely_case" => $response["reply"][$index]["amount"],
                             "worst_case" => $response["reply"][$index]["worst_case"],
                             "best_adjusted" => $response["reply"][$index]["best_adjusted"],
                             "likely_adjusted" => $response["reply"][$index]["likely_adjusted"],
                             "worst_adjusted" => $response["reply"][$index]["worst_adjusted"],
                             "forecast" => intval($response["reply"][$index]["forecast"]),
                             "forecast_id" => $response["reply"][$index]["forecast_id"],
                             "id" => $response["reply"][$index]["id"],
                             "worksheet_id" => $response["reply"][$index]["worksheet_id"],
                             "show_opps" => $response["reply"][$index]["show_opps"],
                             "name" => $response["reply"][$index]["name"],
                             "user_id" => $response["reply"][$index]["user_id"],
                             "current_user" => $this->_user->id,
                             "timeperiod_id" =>$response["reply"][$index]["timeperiod_id"],
                             "draft" => 0
                        );
        $response = $this->_restCall("ForecastManagerWorksheets/" .  $response["reply"][$index]["user_id"], json_encode($postData), "PUT");
						
		// now get the data back to see if it was saved to all the proper tables.
		$response = $this->_restCall("ForecastManagerWorksheets?user_id=". self::$manager["user"]->id . "&timeperiod_id=" . self::$timeperiod->id);	
		
		//check to see if the Quota was auto calculated
		foreach($response["reply"] as $record)
        {
        	if($record["user_id"] == $this->_user->id)
        	{
        		$quota = $record["quota"];
                break;
        	}
        }
                
        //Since the manager has no overall quota assigned to him from an uber_manager, his total should be recalculated
        //to zero on updating a reportee.
		$this->assertEquals(0, $quota, "Quota data was not auto calculated.");
		
		// set the current user to original user
        $this->_user = $oldUser;
        $GLOBALS["current_user"] = $oldUser;
        $this->authToken = "";
     }
     
     /**
      * @depends testForecastWorksheetQuotaRecalc
      * @group forecastapi
      * @group forecasts
     */
     public function testForecastWorksheetQuotaRecalcReps()
     {
    	$oldUser = $GLOBALS["current_user"];
     	$quota = "";
     
     	// reset current user to manager1
        $this->_user = self::$manager2['user'];
        $GLOBALS['current_user'] = $this->_user;
        $this->authToken = "";
        
        $response = $this->_restCall("ForecastManagerWorksheets?user_id=" . self::$manager2["user"]->id . "&timeperiod_id=" . self::$timeperiod->id);

        //get the data for the rep
         foreach($response["reply"] as $record)
         {
            if($record["user_id"] != $this->_user->id)
            {
                $repData = $record;
                break;
            }
         }
         
         
        $newQuota = 4000;
        //alter the rep
        $postData = array(	 "amount" => $repData["amount"],
							 "quota" => $newQuota,
                             "quota_id" => $repData["quota_id"],
                             "best_case" => $repData["best_case"],
                             "likely_case" => $repData["amount"],
                             "worst_case" => $repData["worst_case"],
                             "best_adjusted" => $repData["best_adjusted"],
                             "likely_adjusted" => $repData["likely_adjusted"],
                             "worst_adjusted" => $repData["worst_adjusted"],
                             "forecast" => intval($repData["forecast"]),
                             "forecast_id" => $repData["forecast_id"],
                             "id" => $repData["id"],
                             "worksheet_id" => $repData["worksheet_id"],
                             "show_opps" => $repData["show_opps"],
                             "name" => $repData["name"],
                             "user_id" => $repData["user_id"],
                             "current_user" => $this->_user->id,
                             "timeperiod_id" =>$repData["timeperiod_id"],
                             "draft" => 0
                        );
        
        $response = $this->_restCall("ForecastManagerWorksheets/" .  $repData["user_id"], json_encode($postData), "PUT");

        // now get the data back to see if it was saved to all the proper tables.
		$response = $this->_restCall("ForecastManagerWorksheets?user_id=". self::$manager2["user"]->id . "&timeperiod_id=" . self::$timeperiod->id);
		
		$GLOBALS['current_user'] = $this->_user;
		//check to see if the Quota was auto calculated
		foreach($response["reply"] as $record)
        {
        	if($record["user_id"] == $this->_user->id)
        	{
        		$quota = $record["quota"];
                break;
        	}
        }
        
        //Since we set Manager2 to have a overall quota of 5000 in the testForecastWorksheetQuotaRecalc test, the recalc
        //should subtract the rep value of 4000 from 5000, giving us 1000 for manager2's direct
		$this->assertEquals(5000 - $newQuota, $quota, "Quota data was not auto calculated.");
        
		// set the current user to original user
        $this->_user = $oldUser;
        $GLOBALS["current_user"] = $oldUser;
        $this->authToken = "";
     }	

}