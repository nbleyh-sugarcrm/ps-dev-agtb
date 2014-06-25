<?php
//FILE SUGARCRM flav=pro ONLY

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

require_once('data/SugarBean.php');
require_once('modules/Contacts/Contact.php');
require_once('modules/SNIP/SugarSNIP.php');
require_once('include/TimeDate.php');

/*
 * Tests SNIP's object creation feature by setting up a createdefs.php file and importing emails.
 */

class CreateObjectTest extends Sugar_PHPUnit_Framework_TestCase {
	private $snip;
	private $orig_file = '';

	// store ids of generated objects so we can delete them in tearDown
	private $email_id = '';
	private $contact_id = '';
	private $case_id = '';
	private $opportunity_id = '';

	// create a Contacts object
	public function testCreateContactsObject () {
		// create email and import to snip
		$email['message']['message_id'] = '48812';
		$email['message']['from_name'] = 'Cathy Contacts <contacts@testsugar.info>';
		$email['message']['description'] = 'Testing SNIP to Contacts definition';
		$email['message']['description_html'] = 'Testing <u>SNIP</u> to Contacts definition';
		$email['message']['to_addrs'] = 'sugar.phone@example.name';
		$email['message']['cc_addrs'] = 'sugar.section.dev@example.net';
		$email['message']['bcc_addrs'] = 'qa.sugar@example.net';
		$email['message']['date_sent'] = '2010-01-11 01:30:00';
		$email['message']['subject'] = 'Contacts test subject';
		$email['user'] = 'Administrator';
		$this->snip->importEmail($email);

		// get email's ID
		$e = new Email();
		$e->retrieve_by_string_fields(array("message_id" => $email['message']['message_id']));
		$this->assertTrue(isset($e->id) && !empty($e->id));
		$this->email_id = $e->id;
		// retrieve contact that was generated by snip
		$contact = new Contact();
		$contact->retrieve_by_string_fields(array('department' => $e->id));
		$this->assertTrue(isset($contact->id) && !empty($contact->id));
		$this->contact_id = $contact->id;

		// validate object members
		$this->assertEquals('Cathy Contacts', $contact->last_name);
		$this->assertEquals($e->id, $contact->department);
		$this->assertEquals($email['message']['description'].' '.$e->id.' '.$email['message']['message_id'].' '.$email['message']['subject'].' '.htmlentities($email['message']['from_name']), $contact->description);
		$this->assertEquals('Email', $contact->lead_source);
		$this->assertEquals(gmdate($GLOBALS['timedate']->get_db_date_time_format(), strtotime($email['message']['date_sent'])), $GLOBALS['db']->fromConvert($contact->date_entered, 'datetime'));
	}

	// create a Cases object
	public function testCreateCasesObject () {
		// create email and import to snip
		$email['message']['message_id'] = '67070';
		$email['message']['from_name'] = 'Corey Cases <cases@testsugar.info>';
		$email['message']['description'] = 'Testing SNIP to Cases definition';
		$email['message']['description_html'] = 'Testing <u>SNIP</u> to Cases definition';
		$email['message']['to_addrs'] = 'Test To <sugar.phone@example.name>';
		$email['message']['cc_addrs'] = 'Test CC <sugar.section.dev@example.net>';
		$email['message']['bcc_addrs'] = 'Test Bcc <qa.sugar@example.net>';
		$email['message']['date_sent'] = '2010-01-11 01:30:00';
		$email['message']['subject'] = 'Cases test subject';
		$email['user'] = 'Administrator';
		$this->snip->importEmail($email);

		// get email's ID
		$e = new Email();
		$e->retrieve_by_string_fields(array("message_id" => $email['message']['message_id']));
		$this->assertTrue(isset($e->id) && !empty($e->id));
		$this->email_id = $e->id;

		// retrieve case that was generated by snip
		$case = new aCase();
		$case->retrieve_by_string_fields(array('status' => $e->id));
		$this->assertTrue(isset($case->id) && !empty($case->id));
		$this->case_id = $case->id;

		// validate object members
		$this->assertEquals('Corey Cases', $case->name);
		$this->assertEquals($e->id, $case->status);
		$this->assertEquals($email['message']['description'].' '.$e->id.' '.$email['message']['message_id'].' '.$email['message']['subject'].' '.htmlentities($email['message']['from_name']), $case->description);
		$this->assertEquals(gmdate($GLOBALS['timedate']->get_db_date_time_format(), strtotime($email['message']['date_sent'])), $GLOBALS['db']->fromConvert($case->date_entered, 'datetime'));
	}

	// create an Opportunity object
	public function testCreateOpportunitiesObject () {
		// create email and import to snip
		$email['message']['message_id'] = '79708';
		$email['message']['from_name'] = 'Oscar Opportunities <opp@testsugar.info>';
		$email['message']['description'] = 'Testing SNIP to Opportunities definition';
		$email['message']['description_html'] = 'Testing <u>SNIP</u> to Opportunities definition';
		$email['message']['to_addrs'] = 'Test To <sugar.phone@example.name>';
		$email['message']['cc_addrs'] = 'Test CC <sugar.section.dev@example.net>';
		$email['message']['bcc_addrs'] = 'Test Bcc <qa.sugar@example.net>';
		$email['message']['date_sent'] = '2010-01-11 01:30:00';
		$email['message']['subject'] = 'Cases test subject';
		$email['user'] = 'Administrator';
		$this->snip->importEmail($email);

		// get email's ID
		$e = new Email();
		$e->retrieve_by_string_fields(array("message_id" => $email['message']['message_id']));
		$this->assertEquals((isset($e->id) && !empty($e->id)), true);
		$this->email_id = $e->id;

		// retrieve opportunity that was generated by snip
		$opp = new Opportunity();
		$opp->retrieve_by_string_fields(array('sales_stage' => $e->id));
		$this->assertEquals((isset($opp->id) && !empty($opp->id)), true);
		$this->opportunity_id = $opp->id;

		// validate object members
		$this->assertEquals('Oscar Opportunities', $opp->name);
		$this->assertEquals($e->id, $opp->sales_stage);
		$this->assertEquals($email['message']['description'].' '.$e->id.' '.$email['message']['message_id'].' '.$email['message']['subject'].' '.htmlentities($email['message']['from_name']), $opp->description);
		$this->assertEquals(gmdate($GLOBALS['timedate']->get_db_date_time_format(), strtotime($email['message']['date_sent'])), $GLOBALS['db']->fromConvert($opp->date_entered, 'datetime'));
	}

	public function setUp () {
	    global $current_user;

	    // setup new anonymous user
	    $current_user = SugarTestUserUtilities::createAnonymousUser();
	    $GLOBALS['current_user'] = $current_user;

	    // copy over existing createdefs.php
	    if (file_exists('custom/modules/SNIP/createdefs.php')) {
	    	$this->orig_file = tempnam('custom/modules/SNIP', 'SNIP');
	    	rename ('custom/modules/SNIP/createdefs.php', $this->orig_file);
	    }

	    // create necessary folders before we copy our test createdefs.php file
	    if (!is_dir ('custom/modules/SNIP'))
	    	mkdir ('custom/modules/SNIP', 0755, true);

	    // copy our test data file
	    copy ('tests/modules/SNIP/createdefs.php', 'custom/modules/SNIP/createdefs.php');
        SugarAutoLoader::addToMap('custom/modules/SNIP/createdefs.php', false);
	    // initiate snip
		$this->snip = SugarSNIP::getInstance();
	}

	public function tearDown () {
		// delete emails that were imported
    	$GLOBALS['db']->query("DELETE FROM emails WHERE id = '{$this->email_id}'");
    	$GLOBALS['db']->query("DELETE FROM emails_text WHERE email_id = '{$this->email_id}'");

    	// delete other objects that were created
    	if (!empty($this->contact_id)) $GLOBALS['db']->query("DELETE FROM contacts WHERE id = '{$this->contact_id}'");
    	if (!empty($this->case_id)) $GLOBALS['db']->query("DELETE FROM cases WHERE id = '{$this->case_id}'");
    	if (!empty($this->opportunity_id)) {
	    	$GLOBALS['db']->query("DELETE FROM opportunities WHERE id = '{$this->opportunity_id}'");
    		$GLOBALS['db']->query("DELETE FROM opportunities_contacts WHERE opportunity_id = '{$this->opportunity_id}'");
    	}

		// remove anonymous user
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
		unset($GLOBALS['current_user']);

		// delete our test createdefs and restore original file
		if(!empty($this->orig_file)) {
		    unlink('custom/modules/SNIP/createdefs.php');
		    rename($this->orig_file, 'custom/modules/SNIP/createdefs.php');
		} else {
		    SugarAutoLoader::unlink('custom/modules/SNIP/createdefs.php');
		}

		unset($this->snip);
		unset($this->email_id);
		unset($this->contact_id);
		unset($this->case_id);
		unset($this->opportunity_id);
	}
}
?>