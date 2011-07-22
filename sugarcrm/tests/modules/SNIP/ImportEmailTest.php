<?php
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

require_once('data/SugarBean.php');
require_once('modules/SNIP/SugarSNIP.php');
require_once('modules/Emails/Email.php');
require_once('include/TimeDate.php');

class ImportEmailTest extends Sugar_PHPUnit_Framework_TestCase {
	private $snip;
	private $date_time_format;

	public function testNewEmailWithEvent () {
		// import email through snip
		$file_path = 'tests/modules/SNIP/SampleEvent.ics';

		$email['message']['message_id'] = '10011';
		$email['message']['from_name'] = 'Test Emailer <temailer@sugarcrm.com>';
		$email['message']['description'] = 'Email with event attachment';
		$email['message']['description_html'] = 'Email with <b>event</b> attachment';
		$email['message']['to_addrs'] = 'kid.dev.info@example.de';
		$email['message']['cc_addrs'] = 'sugar.section.dev@example.net';
		$email['message']['bcc_addrs'] = 'qa.sugar@example.net';
		$email['message']['date_sent'] = '2010-01-01 12:30:00';
		$email['message']['subject'] = 'PHPUnit Test Email with iCal';
		$email['message']['attachments'][] = array('filename' => $file_path, 'content' => base64_encode(file_get_contents($file_path)));
		$email['user'] = 'Administrator';
		$this->snip->importEmail($email);

		// get the email object if it imported correctly
		$e = new Email();
		$e->retrieve_by_string_fields(array("message_id" => $email['message']['message_id']));
		$this->assertEquals((isset($e->id) && !empty($e->id)), true);

		// populate the whole bean
		if (isset ($e->id) && !empty ($e->id))
			$e->retrieve($e->id);

		// get the meeting
		$meeting = new Meeting();
		$meeting->retrieve_by_string_fields(array('assigned_user_id' => $e->assigned_user_id, 'team_set_id' => $e->team_set_id, 
											'team_id' => $e->team_id, 'parent_id' => $e->id, 'parent_type' => $e->module_dir));
		$this->assertEquals((isset($meeting->id) && !empty($meeting->id)), true);

		// check if the values match with the iCal event
		$this->assertEquals($meeting->name, 'Coffee with Jason');
		$this->assertEquals($meeting->location, 'Conference Room - F123, Bldg. 002');
		$this->assertEquals($meeting->status, 'Planned');
		$this->assertEquals($meeting->date_start, '2002-10-28 22:00:00');
		$this->assertEquals($meeting->parent_type, 'Emails');
		$this->assertEquals($meeting->parent_name, $email['message']['subject']);

		// delete
		$e->delete($e->id);
		$meeting->mark_deleted($meeting->id);
	}

	public function testNewEmail () {
		global $current_user;

		// import email through snip
		$email['message']['message_id'] = '12345';
		$email['message']['from_name'] = 'Test Emailer <temailer@sugarcrm.com>';
		$email['message']['description'] = 'This is a test email';
		$email['message']['description_html'] = 'This is a <b>test</b> <u>email</u>';
		$email['message']['to_addrs'] = 'sugar.phone@example.name';
		$email['message']['cc_addrs'] = 'sugar.section.dev@example.net';
		$email['message']['bcc_addrs'] = 'qa.sugar@example.net';
		$email['message']['date_sent'] = '2010-01-01 12:30:00';
		$email['message']['subject'] = 'PHPUnit Test Email';
		$email['user'] = 'Administrator';
		$this->snip->importEmail($email);

		// get the email object if it imported correctly
		$e = new Email();
		$e->retrieve_by_string_fields(array("message_id" => $email['message']['message_id']));
		$this->assertEquals((isset($e->id) && !empty($e->id)), true);

		// populate the whole bean
		if (isset ($e->id) && !empty ($e->id))
			$e->retrieve($e->id);

        // validate if everything was saved correctly
		$this->assertEquals($e->message_id,	$email['message']['message_id']);
		$this->assertEquals($e->from_addr_name, $email['message']['from_name']);
		$this->assertEquals($e->description, $email['message']['description']);
		$this->assertEquals($e->description_html, $email['message']['description_html']);
		$this->assertEquals($e->to_addrs, $email['message']['to_addrs']);
		$this->assertEquals($e->cc_addrs, $email['message']['cc_addrs']);
		$this->assertEquals($e->bcc_addrs, $email['message']['bcc_addrs']);		
		$this->assertEquals($e->name, $email['message']['subject']);
		$this->assertEquals($e->date_sent, gmdate($this->date_time_format,strtotime($email['message']['date_sent'])));

		// delete
		$e->delete($e->id);
	}

	public function testExistingEmail () {
		// import email through snip
		$email['message']['message_id'] = '2002';
		$email['message']['from_name'] = 'Test Emailer <temailer@sugarcrm.com>';
		$email['message']['description'] = 'Existing email test';
		$email['message']['description_html'] = 'Existing <b>email</b> test';
		$email['message']['to_addrs'] = 'sales.support@example.biz';
		$email['message']['cc_addrs'] = 'sugar.info.the@example.info';
		$email['message']['bcc_addrs'] = '';
		$email['message']['date_sent'] = '2011-06-09 00:01:00';
		$email['message']['subject'] = 'PHPUnit Test Existing Email';
		$email['user'] = 'Administrator';
		$this->snip->importEmail($email);

		// now, create another email with the same message id
		$a_email['message']['message_id'] = '2002';
		$a_email['message']['from_name'] = 'Test Emailer <temailer@sugarcrm.com>';
		$a_email['message']['description'] = 'Another existing email test';
		$a_email['message']['description_html'] = 'Another existing <b>email</b> test';
		$a_email['message']['to_addrs'] = 'support.sugar@example.co.jp';
		$a_email['message']['cc_addrs'] = '';
		$a_email['message']['bcc_addrs'] = 'dev.support@example.tw';
		$a_email['message']['date_sent'] = '2011-09-06 01:13:00';
		$a_email['message']['subject'] = 'PHPUnit Test Another Existing Email';
		$a_email['user'] = 'Administrator';
		$this->snip->importEmail($a_email);

		// now, get the email with the mesage id '2002'
		$e = new Email();
		$e->retrieve_by_string_fields(array("message_id" => $email['message']['message_id']));
		$this->assertEquals((isset($e->id) && !empty($e->id)), true);

		// populate the whole bean
		if (isset ($e->id) && !empty ($e->id))
			$e->retrieve($e->id);
			
        // everything should match the content of the first email because the second email should've been rejected
		$this->assertEquals($e->message_id,	$email['message']['message_id']);
		$this->assertEquals($e->from_addr_name, $email['message']['from_name']);
		$this->assertEquals($e->description, $email['message']['description']);
		$this->assertEquals($e->description_html, $email['message']['description_html']);
		$this->assertEquals($e->to_addrs, $email['message']['to_addrs']);
		$this->assertEquals($e->cc_addrs, $email['message']['cc_addrs']);
		$this->assertEquals($e->bcc_addrs, $email['message']['bcc_addrs']);		
		$this->assertEquals($e->name, $email['message']['subject']);
		$this->assertEquals($e->date_sent, gmdate($this->date_time_format,strtotime($email['message']['date_sent'])));

		// delete
		$e->delete($e->id);		
	}

	public function setUp () {
	    global $current_user;

	    // setup test user and initiate snip
	    $current_user = SugarTestUserUtilities::createAnonymousUser();
	    $GLOBALS['current_user'] = $current_user;
		$this->snip = SugarSNIP::getInstance();

		// get configured date format
		$timedate = new TimeDate();
		$this->date_time_format = $timedate->get_date_time_format();
	}

	public function tearDown () {
		// remove test user
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
		unset($GLOBALS['current_user']);
		unset ($this->snip);
	}
}
?>