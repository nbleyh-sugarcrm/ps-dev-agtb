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

require_once "tests/{old}/modules/OutboundEmailConfiguration/OutboundEmailConfigurationTestHelper.php";

/**
 * Test cases for Bug 30591
 *
 * @coversDefaultClass Email
 */
class EmailTest extends Sugar_PHPUnit_Framework_TestCase
{
	private $email;

	public function setUp()
	{
	    global $current_user;

	    $current_user = BeanFactory::newBean("Users");
        $current_user->getSystemUser();
	    $this->email = new Email();
	    $this->email->email2init();
	}

	public function tearDown()
	{
        // Clean up any dangling beans that need to be resaved.
        SugarRelationship::resaveRelatedBeans(false);

        SugarTestContactUtilities::removeAllCreatedContacts();
        SugarTestLeadUtilities::removeAllCreatedLeads();
        SugarTestEmailAddressUtilities::removeAllCreatedAddresses();
        SugarTestEmailUtilities::removeAllCreatedEmails();
		unset($this->email);
		// SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
		unset($GLOBALS['current_user']);
	}

    public function saveAndSetDateSentProvider()
    {
        return array(
            array(Email::EMAIL_STATE_DRAFT, null, null),
            array(Email::EMAIL_STATE_ARCHIVED, null, null),
            array(Email::EMAIL_STATE_ARCHIVED, null, null),
            array(Email::EMAIL_STATE_ARCHIVED, '2014-06-22', '10:44'),
        );
    }

    /**
     * @covers ::save
     * @dataProvider saveAndSetDateSentProvider
     * @param string $state
     * @param null|string $dateStart
     * @param null|string $timeStart
     */
    public function testSave_DateSentIsSet($state, $dateStart, $timeStart)
    {
        $this->email->state = $state;
        $this->email->date_start = $dateStart;
        $this->email->time_start = $timeStart;
        $this->email->save();
        SugarTestEmailUtilities::setCreatedEmail($this->email->id);

        $this->assertNotEmpty($this->email->date_sent);
    }

    public function testSaveArchivedEmail_EmptyTeamId_TeamAndTeamsetAreSetToGlobal()
    {
        $this->email->state = Email::EMAIL_STATE_ARCHIVED;
        $this->email->team_id = null;
        $this->email->save();
        SugarTestEmailUtilities::setCreatedEmail($this->email->id);

        $this->assertEquals('1', $this->email->team_id, "Actual team_id doesn't match Expected team_id");
        $this->assertEquals('1', $this->email->team_set_id, "Actual team_set_id doesn't match Expected team_set_id");
    }

	public function testSafeAttachmentName ()
	{
		$extArray[] = '0.py';
		$extArray[] = '1.php';
		$extArray[] = '2.php3';
		$extArray[] = '3.php4';
		$extArray[] = '4.php5';
		$extArray[] = '5.js';
		$extArray[] = '6.htm';
		$extArray[] = '7.html';
		$extArray[] = '8.txt';
		$extArray[] = '9.doc';
		$extArray[] = '10.xls';
		$extArray[] = '11.pdf';
		$extArray[] = '12';

		for ($i = 0; $i < count($extArray); $i++) {
			$result = $this->email->safeAttachmentName($extArray[$i]);
			if ($i < 8) {
				$this->assertEquals($result, true);
			} else {
				$this->assertEquals($result, false);
			}
		}
	}

	public function testEmail2ParseAddresses()
	{
		$emailDisplayName[] = '';
		$emailDisplayName[] = 'Shine Ye';
		$emailDisplayName[] = 'Roger,Smith';
		$emailAddress[] = 'masonhu@sugarcrm.com';
		$emailAddress[] = 'xye@sugarcrm.com';
		$emailAddress[] = 'roger@sugarcrm.com';
		for ($j = 0; $j < count($emailDisplayName); $j++)
		{
			if ($j < 1)
				$emailString[] = $emailDisplayName[$j].$emailAddress[$j];
			else
				$emailString[] = $emailDisplayName[$j].'<'.$emailAddress[$j].'>';

		}
		$emailAddressString = implode(', ', $emailString);
		$result = $this->email->email2ParseAddresses($emailAddressString);
		$onlyEmailResult = $this->email->email2ParseAddressesForAddressesOnly($emailAddressString);
		for ($v = 0; $v < count($result); $v++)
		{
			$this->assertEquals($result[$v]['display'], $emailDisplayName[$v]);
			$this->assertEquals($result[$v]['email'], $emailAddress[$v]);
			$this->asserteQuals($onlyEmailResult[$v], $emailAddress[$v]);
		}
	}

    public function testEmail2ParseAddresses_ParameterIsEmpty_EmptyArrayIsReturned()
    {
        $actual = $this->email->email2ParseAddresses('');
        $this->assertCount(0, $actual, 'An empty array should have been returned.');
    }

    /**
     * @dataProvider dataProviderEmailAddressParsing
     * @param string $fullEmailAddress
     * @param string $expDisplayName
     * @param string $expEmailAddress
     */
    public function testEmail2ParseEmailAddresses($fullEmailAddress, $expDisplayName, $expEmailAddress)
    {
        $result = $this->email->email2ParseAddresses($fullEmailAddress);
        $this->assertEquals($expDisplayName, $result[0]['display'], 'Unexpected Email Display Name');
        $this->assertEquals($expEmailAddress, $result[0]['email'], 'Unexpected Email Address');
    }

    /**
     * @dataProvider dataProviderEmailAddressParsing
     * @param string $fullEmailAddress
     * @param string $expDisplayName Not used in this test.
     * @param string $expEmailAddress
     */
    public function testEmail2ParseEmailAddressesAddressOnly($fullEmailAddress, $expDisplayName, $expEmailAddress)
    {
        $result = $this->email->email2ParseAddressesForAddressesOnly($fullEmailAddress);
        $this->assertEquals($expEmailAddress, $result[0], 'Unexpected Email Address');
    }

    public function dataProviderEmailAddressParsing()
    {
        return array(
            array(htmlspecialchars('John Doe<john@doe.com>'), 'John Doe', 'john@doe.com'),
            array(htmlspecialchars('Jo<hn Doe<john@doe.com>'), 'Jo<hn Doe', 'john@doe.com'),
            array(htmlspecialchars('Jo>hn Doe<john@doe.com>'), 'Jo>hn Doe', 'john@doe.com'),
            array(htmlspecialchars('Jo>h<n Doe<john@doe.com>'), 'Jo>h<n Doe', 'john@doe.com'),
            array(htmlspecialchars('Jo>h<n Doe  <john@doe.com>'), 'Jo>h<n Doe', 'john@doe.com'),
            array(htmlspecialchars("Jo'h<n D\"oe  <john@doe.com>"), "Jo'h<n D\"oe", 'john@doe.com'),
        );
    }

	public function testDecodeDuringSend()
	{
		$testString = 'Replace sugarLessThan and sugarGreaterThan with &lt; and &gt;';
		$expectedResult = 'Replace &lt; and &gt; with &lt; and &gt;';
		$resultString = $this->email->decodeDuringSend($testString);
		$this->asserteQuals($resultString, $expectedResult);
	}

    public function configParamProvider()
    {
        $address_array =  array(
            'id1' => 'test1@example.com',
            'id2' => 'test2@example.com',
            'id3' => 'test3@example.com'
        );

        return array(
            array(',',$address_array,'test1@example.com,test2@example.com,test3@example.com'), // default and correct delimiter for email addresses
            array(';',$address_array,'test1@example.com;test2@example.com;test3@example.com'), // outlook's delimiter for email addresses
        );
    }

    /**
     * @group email
     * @group mailer
     */
    public function testEmailSend_Success()
    {
        OutboundEmailConfigurationTestHelper::setUp();
        $config = OutboundEmailConfigurationPeer::getSystemMailConfiguration($GLOBALS['current_user']);
        $mockMailer = new MockMailer($config);
        MockMailerFactory::setMailer($mockMailer);

        $em = new Email();
        $em->email2init();
        $em->_setMailerFactoryClassName('MockMailerFactory');

        $em->name = "This is the Subject";
        $em->description_html = "This is the HTML Description";
        $em->description      = "This is the Text Description";

        $from       = new EmailIdentity("twolf@sugarcrm.com" , "Tim Wolf");
        $replyto    = $from;
        $to         = new EmailIdentity("twolf@sugarcrm.com" , "Tim Wolf");
        $cc         = new EmailIdentity("twolf@sugarcrm.com" , "Tim Wolf");

        $em->from_addr = $from->getEmail();
        $em->from_name = $from->getName();

        $em->reply_to_addr = $replyto->getEmail();
        $em->reply_to_name = $replyto->getName();

        $em->to_addrs_arr = array(
            array(
                'email'     => $to->getEmail(),
                'display'   => $to->getName(),
            )
        );
        $em->cc_addrs_arr = array(
            array(
                'email'     => $cc->getEmail(),
                'display'   => $cc->getName(),
            )
        );

        $em->send();

        $data = $mockMailer->toArray();
        $this->assertEquals($em->description_html, $data['htmlBody']);
        $this->assertEquals($em->description, $data['textBody']);

        $headers = $mockMailer->getHeaders();
        $this->assertEquals($em->name, $headers['Subject']);
        $this->assertEquals($from->getEmail(), $headers['From'][0]);
        $this->assertEquals($from->getName(),  $headers['From'][1]);
        $this->assertEquals($replyto->getEmail(), $headers['Reply-To'][0]);
        $this->assertEquals($replyto->getName(),  $headers['Reply-To'][1]);

        $recipients = $mockMailer->getRecipients();

        $actual_to=array_values($recipients['to']);
        $this->assertEquals($to->getEmail(), $actual_to[0]->getEmail(), "TO Email Address Incorrect");
        $this->assertEquals($to->getName(),  $actual_to[0]->getName(),  "TO Name Incorrect");

        $actual_cc=array_values($recipients['cc']);
        $this->assertEquals($to->getEmail(), $actual_cc[0]->getEmail(), "CC Email Address Incorrect");
        $this->assertEquals($to->getName(),  $actual_cc[0]->getName(),  "CC Name Incorrect");

        $this->assertEquals(true,$mockMailer->wasSent());
        OutboundEmailConfigurationTestHelper::tearDown();
    }

    /**
     * @group bug51804
     * @dataProvider configParamProvider
     * @param string $config_param
     * @param array $address_array
     * @param string $expected
     */
    public function testArrayToDelimitedString($config_param, $address_array, $expected)
    {
        $GLOBALS['sugar_config']['email_address_separator'] = $config_param;

        $this->assertEquals($expected,$this->email->_arrayToDelimitedString($address_array), 'Array should be delimited with correct delimiter');

    }

    /**
     * @covers ::getOutboundEmailDropdown
     */
    public function testGetOutboundEmailDropdown()
    {
        OutboundEmailConfigurationTestHelper::setUp();
        OutboundEmailConfigurationTestHelper::setAllowDefaultOutbound(2);

        SugarTestHelper::setUp('current_user');
        $systemConfig = OutboundEmailConfigurationTestHelper::getSystemConfiguration();
        $userConfigs = OutboundEmailConfigurationTestHelper::createUserOutboundEmailConfigurations(2);

        $email = new Email();
        $options = $email->getOutboundEmailDropdown();
        $optionKeys = array_keys($options);

        $this->assertCount(3, $options, 'There should be three options');
        $this->assertSame($systemConfig->id, $optionKeys[0], 'The system configuration should be the first option');

        OutboundEmailConfigurationTestHelper::tearDown();
    }

    /**
     * @covers ::save
     * @covers ::updateAttachmentsVisibility
     * @covers ::updateAttachmentVisibility
     * @covers Note::save
     */
    public function testUpdateAttachmentsVisibility()
    {
        $email = SugarTestEmailUtilities::createEmail();
        $data = array(
            'email_type' => 'Emails',
            'email_id' => $email->id,
        );
        $note1 = SugarTestNoteUtilities::createNote('', $data);
        $note2 = SugarTestNoteUtilities::createNote('', $data);

        // Change the teams on the email.
        $teams = BeanFactory::getBean('TeamSets');
        $email->state = Email::EMAIL_STATE_ARCHIVED;
        $email->assigned_user_id = $GLOBALS['current_user']->id;
        $email->team_id = 'East';
        $email->team_set_id = $teams->addTeams(array('East', 'West'));
        //BEGIN SUGARCRM flav=ent ONLY
        $email->team_set_selected_id = 'East';
        //END SUGARCRM flav=ent ONLY
        $email->save();

        $this->assertEquals(
            $email->assigned_user_id,
            $note1->assigned_user_id,
            'note1.assigned_user_id does not match'
        );
        $this->assertEquals(
            $email->assigned_user_id,
            $note2->assigned_user_id,
            'note2.assigned_user_id does not match'
        );
        $this->assertEquals($email->team_set_id, $note1->team_set_id, 'note1.team_set_id does not match');
        $this->assertEquals($email->team_set_id, $note2->team_set_id, 'note2.team_set_id does not match');
        $this->assertEquals($email->team_id, $note1->team_id, 'note1.team_id does not match');
        $this->assertEquals($email->team_id, $note2->team_id, 'note2.team_id does not match');
        //BEGIN SUGARCRM flav=ent ONLY
        $this->assertEquals(
            $email->team_set_selected_id,
            $note1->team_set_selected_id,
            'note1.team_set_selected_id does not match'
        );
        $this->assertEquals(
            $email->team_set_selected_id,
            $note2->team_set_selected_id,
            'note2.team_set_selected_id does not match'
        );
        //END SUGARCRM flav=ent ONLY
    }

    /**
     * @covers ::updateAttachmentVisibility
     */
    public function testUpdateAttachmentVisibility_EmailIsADraft_TeamIsPrivateTeamOfAssignedUser()
    {
        $note = $this->getMockBuilder('Note')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();
        $note->expects($this->once())->method('save');

        $email = BeanFactory::newBean('Emails');
        $email->state = Email::EMAIL_STATE_DRAFT;
        $email->assigned_user_id = $GLOBALS['current_user']->id;
        $email->team_id = 'East';
        $email->team_set_id = create_guid();
        //BEGIN SUGARCRM flav=ent ONLY
        $email->team_set_selected_id = 'East';
        //END SUGARCRM flav=ent ONLY
        $email->updateAttachmentVisibility($note);

        $this->assertEquals($email->assigned_user_id, $note->assigned_user_id, 'assigned_user_id does not match');
        $expected = $GLOBALS['current_user']->getPrivateTeam();
        $this->assertEquals($expected, $note->team_set_id, 'team_set_id does not match');
        $this->assertEquals($expected, $note->team_id, 'team_id does not match');
        //BEGIN SUGARCRM flav=ent ONLY
        $this->assertEquals($expected, $note->team_set_selected_id, 'team_set_selected_id does not match');
        //END SUGARCRM flav=ent ONLY
    }

    /**
     * @covers ::updateAttachmentVisibility
     */
    public function testUpdateAttachmentVisibility_EmailIsADraft_NoAssignedUser()
    {
        $assignedUserId = create_guid();
        $teamSetId = create_guid();
        $note = $this->getMockBuilder('Note')
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();
        $note->expects($this->never())->method('save');
        $note->assigned_user_id = $assignedUserId;
        $note->team_id = 'West';
        $note->team_set_id = $teamSetId;
        //BEGIN SUGARCRM flav=ent ONLY
        $note->team_set_selected_id = 'West';
        //END SUGARCRM flav=ent ONLY

        $email = BeanFactory::newBean('Emails');
        $email->state = Email::EMAIL_STATE_DRAFT;
        $email->assigned_user_id = null;
        $email->team_id = 'East';
        $email->team_set_id = create_guid();
        //BEGIN SUGARCRM flav=ent ONLY
        $email->team_set_selected_id = 'East';
        //END SUGARCRM flav=ent ONLY
        $email->updateAttachmentVisibility($note);

        $this->assertEquals($assignedUserId, $note->assigned_user_id, 'assigned_user_id should not have changed');
        $this->assertEquals($teamSetId, $note->team_set_id, 'team_set_id should not have changed');
        $this->assertEquals('West', $note->team_id, 'team_id should not have changed');
        //BEGIN SUGARCRM flav=ent ONLY
        $this->assertEquals('West', $note->team_set_selected_id, 'team_set_selected_id should not have changed');
    }

    /**
     * @covers ::save
     * @covers ::updateAttachmentsVisibility
     * @covers ::updateAttachmentVisibility
     * @covers Note::save
     */
    public function testUpdateAttachmentsVisibility_ArchivingADraftSynchronizesTeams()
    {
        $email = SugarTestEmailUtilities::createEmail();
        $data = array(
            'email_type' => 'Emails',
            'email_id' => $email->id,
        );
        $note = SugarTestNoteUtilities::createNote('', $data);

        // Change the teams on the email.
        $teams = BeanFactory::getBean('TeamSets');
        $email->state = Email::EMAIL_STATE_DRAFT;
        $email->assigned_user_id = $GLOBALS['current_user']->id;
        $email->team_id = 'East';
        $email->team_set_id = $teams->addTeams(array('East', 'West'));
        //BEGIN SUGARCRM flav=ent ONLY
        $email->team_set_selected_id = 'East';
        //END SUGARCRM flav=ent ONLY
        $email->save();

        $expected = $GLOBALS['current_user']->getPrivateTeam();
        $this->assertEquals($expected, $note->team_set_id, 'team_set_id should be the private team');
        $this->assertEquals($expected, $note->team_id, 'team_id should be the private team');
        //BEGIN SUGARCRM flav=ent ONLY
        $this->assertEquals($expected, $note->team_set_selected_id, 'team_set_selected_id should be the private team');
        //END SUGARCRM flav=ent ONLY

        // Archive the email.
        $email->state = Email::EMAIL_STATE_ARCHIVED;
        $email->save();

        $this->assertEquals($email->assigned_user_id, $note->assigned_user_id, 'assigned_user_id does not match');
        $this->assertEquals($email->team_set_id, $note->team_set_id, 'team_set_id does not match');
        $this->assertEquals($email->team_id, $note->team_id, 'team_id does not match');
        //BEGIN SUGARCRM flav=ent ONLY
        $this->assertEquals(
            $email->team_set_selected_id,
            $note->team_set_selected_id,
            'team_set_selected_id does not match'
        );
        //END SUGARCRM flav=ent ONLY
    }

    /**
     * @covers ::getAllEmailRecipients
     * @covers ::hasMoreRecipients
     */
    public function testGetAllEmailRecipients_LessRecipientsThanMaxBatch_RetrievesAllRecipientsOneCall()
    {
        $numberRecipients = 6;
        $mockRecords = array();

        for ($i = 1; $i <= $numberRecipients; $i++) {
            $mockRecords[] = array('id' => $i);
        }
        $mockResult = array(
            'records' => $mockRecords,
            'next_offset' => array(
                'foo' => -1,
                'bar' => -1,
            ),
        );

        $mockEmail = $this->getMock('Email', array('getEmailRecipients'));
        $mockEmail->expects($this->once())
            ->method('getEmailRecipients')
            ->will($this->returnValue($mockResult));

        $result = SugarTestReflection::callProtectedMethod($mockEmail, 'getAllEmailRecipients', array('to'));
        $this->assertEquals($result['records'], $mockRecords, 'records returned are not correct');
    }

    /**
     * @covers ::getAllEmailRecipients
     * @covers ::hasMoreRecipients
     */
    public function testGetAllEmailRecipients_MoreRecipientsThanMaxBatch_RetrievesAllRecipientsOneCall()
    {
        $numberRecipients = 26;
        $mockRecords = array();

        for ($i = 1; $i <= $numberRecipients; $i++) {
            $mockRecords[] = array('id' => $i);
        }
        $mockResult1 = array(
            'records' => array_slice($mockRecords, 0, 20),
            'next_offset' => array(
                'foo' => -1,
                'bar' => 20,
            ),
        );
        $mockResult2 = array(
            'records' => array_slice($mockRecords, 20),
            'next_offset' => array(
                'foo' => -1,
                'bar' => -1,
            ),
        );

        $mockEmail = $this->getMock('Email', array('getEmailRecipients'));
        $mockEmail->expects($this->exactly(2))
            ->method('getEmailRecipients')
            ->will($this->onConsecutiveCalls($mockResult1, $mockResult2));

        $result = SugarTestReflection::callProtectedMethod($mockEmail, 'getAllEmailRecipients', array('to'));
        $this->assertEquals($result['records'], $mockRecords, 'number of records returned is not correct');
    }

    /**
     * This test proves that the `total_attachments` field increases and decreases when attachments are linked and
     * unlinked, respectively.
     */
    public function testTotalAttachments()
    {
        $this->email->save();
        SugarTestEmailUtilities::setCreatedEmail($this->email->id);

        $this->assertSame(0, $this->email->total_attachments, 'Should not have any attachments yet');

        $note = SugarTestNoteUtilities::createNote();
        $this->email->load_relationship('attachments');
        $this->email->attachments->add($note);

        $this->assertSame(1, $this->email->total_attachments, 'Should have incremented the count');

        // While unlinking an attachment, `One2MBeanRelationship::remove` triggers the `after_relationship_delete`
        // event. `SugarBean::call_custom_logic` is ultimately called, which calls `SugarBean::updateRelatedCalcFields`
        // because the event is `after_relationship_delete`. The `attachments` link is associated with a calculated
        // field, so the email is added to `SugarRelationship::$resaveQueue`. So the email is not saved and therefore
        // the `total_attachments` count is not updated when an attachment is unlinked until
        // `SugarRelationship::resaveRelatedBeans` is called, which `RelateRecordApi::deleteRelatedLink` does already.
        $this->email->attachments->delete($this->email->id, $note);
        SugarRelationship::resaveRelatedBeans();

        $this->assertSame(0, $this->email->total_attachments, 'Should have decremented the count');
    }

    /**
     * @covers ::save
     */
    public function testSaveDraftEmail_EmailStatusAndTypeAreSetCorrectlyForCompatibility()
    {
        $data = array(
            'state' => Email::EMAIL_STATE_DRAFT,
        );
        $email = SugarTestEmailUtilities::createEmail('', $data);

        $this->assertEquals('draft', $email->type, 'Draft Email does not have draft type');
        $this->assertEquals('draft', $email->status, 'Draft Email does not have draft status');
    }

    public function willCalculateHtmlBodyProvider()
    {
        return array(
            array(null),
            array(''),
        );
    }

    /**
     * @dataProvider willCalculateHtmlBodyProvider
     * @param null|string $html
     */
    public function testSave_WillCalculateHtmlBody($html)
    {
        $this->email->state = 'Archived';
        $this->email->description = "This is a text body\nWith more
 than...\r\n\r\n... one line";
        $this->email->description_html = $html;
        $this->email->save();
        SugarTestEmailUtilities::setCreatedEmail($this->email->id);

        $this->assertSame('This is a text body&lt;br /&gt;With more&lt;br /&gt;' .
            ' than...&lt;br /&gt;&lt;br /&gt;... one line', $this->email->description_html);
    }

    public function willNotCalculateHtmlBodyProvider()
    {
        return array(
            array(
                'Archived',
                'This is a text body',
                'This is an &lt;b&gt;html&lt;/b&gt; body',
            ),
            array(
                'Archived',
                '',
                null,
            ),
            array(
                'Archived',
                '',
                'This is an &lt;b&gt;html&lt;/b&gt; body',
            ),
            array(
                'Archived',
                null,
                'This is an &lt;b&gt;html&lt;/b&gt; body',
            ),
            array(
                'Draft',
                'This is a text body',
                null,
            ),
        );
    }

    /**
     * @dataProvider willNotCalculateHtmlBodyProvider
     * @param string $state
     * @param null|string $plain
     * @param null|string $html
     */
    public function testSave_WillNotCalculateHtmlBody($state, $plain, $html)
    {
        $this->email->state = $state;
        $this->email->description = $plain;
        $this->email->description_html = $html;
        $this->email->save();
        SugarTestEmailUtilities::setCreatedEmail($this->email->id);

        $this->assertSame($html, $this->email->description_html);
    }

    public function willSetDescriptionProvider()
    {
        return array(
            array(
                'Hello World!',
                '<p>Hello World!</p>',
            ),
            array(
                'Example email message',
                '<div><div>Example</div> email <span>message</span></div>',
            ),
            array(
                "Example email\n\n message with\n line breaks",
                '<div>Example email<br /><br> <span>message</span> with<br /> line breaks</div>',
            ),
        );
    }
    /**
     * @dataProvider willSetDescriptionProvider
     * @covers ::save
     */
    public function testSave_WillSetDescription($text, $html)
    {
        $this->email->state = 'Archived';
        $this->email->description_html = $html;
        $this->email->save();
        SugarTestEmailUtilities::setCreatedEmail($this->email->id);

        $this->assertSame($text, $this->email->description);
    }

    public function willNotSetDescriptionProvider()
    {
        return array(
            array(
                'Archived',
                'Test email message',
                'Test <b>email</b> message',
            ),
            array(
                'Draft',
                null,
                '<div>Test email</div>',
            ),
            array(
                'Archived',
                'Test email message',
                null,
            ),
            array(
                'Archived',
                'One message',
                '<p>Entirely different message</p>',
            ),
            // The description field should not change if it was already set, even if it includes HTML tags
            array(
                'Archived',
                'Allow <b>HTML</b> if sent for text',
                'Allow <b>HTML</b> if sent for text',
            ),
        );
    }

    /**
     * @dataProvider willNotSetDescriptionProvider
     * @covers ::save
     */
    public function testSave_WillNotSetDescription($state, $text, $html)
    {
        $this->email->state = $state;
        $this->email->description = $text;
        $this->email->description_html = $html;
        $this->email->save();
        SugarTestEmailUtilities::setCreatedEmail($this->email->id);

        $this->assertSame($text, $this->email->description);
    }

    /**
     * @covers ::sendEmail
     */
    public function testSendEmail()
    {
        // Don't use the cached NOW in order to verify that Email::date_sent is changed after sending.
        $td = TimeDate::getInstance();
        $tdCache = $td->allow_cache;
        $td->allow_cache = false;

        // Need a configuration to use to send the email, even when faking the send.
        OutboundEmailConfigurationTestHelper::backupExistingConfigurations();
        OutboundEmailConfigurationTestHelper::createSystemOutboundEmailConfiguration();
        $config = OutboundEmailConfigurationPeer::getSystemMailConfiguration($GLOBALS['current_user']);

        // Create a draft that will be sent.
        $data = array(
            'state' => Email::EMAIL_STATE_DRAFT,
            'outbound_email_id' => $config->getConfigId(),
            'name' => 'foo',
            'description_html' => '<b>bar</b>',
            'description' => 'bar',
        );
        $email = SugarTestEmailUtilities::createEmail('', $data);
        $draftDate = $email->date_sent;

        // The current user is the sender.
        $email->load_relationship('users_from');
        $email->users_from->add($GLOBALS['current_user']);

        // Send to an arbitrary email address.
        $email->load_relationship('email_addresses_to');
        $address = SugarTestEmailAddressUtilities::createEmailAddress();
        $email->email_addresses_to->add($address);

        // Send the email.
        $email->sendEmail($config);

        $this->assertSame(Email::EMAIL_STATE_ARCHIVED, $email->state, 'Should be archived');
        //FIXME: Even with TimeDate::allow_cache disabled, the following assertion would fail if the test executes in
        // less than one second. For now, the best we can do is to verify that Email::date_sent is not empty.
        //$this->assertNotEquals($draftDate, $email->date_sent, 'Should reflect the date/time that the email was sent');
        $this->assertNotEmpty($email->date_sent, 'Should reflect the date/time that the email was sent');
        $this->assertSame('out', $email->type, 'Should be out');
        $this->assertSame('sent', $email->status, 'Should be sent');
        $this->assertNotEmpty($email->message_id, 'Should have a Message-ID');

        // Restore the environment.
        OutboundEmailConfigurationTestHelper::restoreExistingConfigurations();
        SugarTestEmailAddressUtilities::removeAllCreatedAddresses();
        $td->allow_cache = $tdCache;
    }

    /**
     * @covers ::save
     * @covers ::saveEmailAddresses
     * @covers ::linkEmailToAddress
     * @covers ::saveEmailText
     * @covers ::retrieveEmailText
     */
    public function testSave_WillLinkEmailAddressesAndRecalculateEmailsText()
    {
        $data = array(
            'from_addr' => 'sam@example.com',
            'from_addr_name' => '"Sam Rooker" <sam@example.com>',
            'to_addrs' => 'tom@example.com,wendy@example.com',
            'to_addrs_names' => 'Tom Hammond <tom@example.com>, "Wendy Towns" <wendy@example.com>',
            'cc_addrs' => 'randy@example.com',
            'cc_addrs_names' => '"Randy Ulman" <randy@example.com>',
            'bcc_addrs' => 'bonnie@example.com;tara@example.com,bill@example.com',
            'bcc_addrs_names' => '"Bonnie Vickers" <bonnie@example.com>;tara@example.com,bill@example.com',
        );
        $email = SugarTestEmailUtilities::createEmail('', $data);

        SugarTestEmailAddressUtilities::setCreatedEmailAddressByAddress('sam@example.com');
        SugarTestEmailAddressUtilities::setCreatedEmailAddressByAddress('tom@example.com');
        SugarTestEmailAddressUtilities::setCreatedEmailAddressByAddress('wendy@example.com');
        SugarTestEmailAddressUtilities::setCreatedEmailAddressByAddress('randy@example.com');
        SugarTestEmailAddressUtilities::setCreatedEmailAddressByAddress('bonnie@example.com');
        SugarTestEmailAddressUtilities::setCreatedEmailAddressByAddress('tara@example.com');
        SugarTestEmailAddressUtilities::setCreatedEmailAddressByAddress('bill@example.com');

        $email->retrieveEmailText();
        $this->assertEquals('sam@example.com', $email->from_addr_name);
        $this->assertEquals('tom@example.com, wendy@example.com', $email->to_addrs_names);
        $this->assertEquals('randy@example.com', $email->cc_addrs_names);
        $this->assertEquals('bill@example.com, bonnie@example.com, tara@example.com', $email->bcc_addrs_names);
    }

    /**
     * @covers ::linkEmailToAddress
     */
    public function testLinkEmailToAddress()
    {
        $email = SugarTestEmailUtilities::createEmail();
        $address1 = SugarTestEmailAddressUtilities::createEmailAddress();
        $address2 = SugarTestEmailAddressUtilities::createEmailAddress();

        // Link a new email address.
        $rowId = $email->linkEmailToAddress($address1->id, 'to');
        $this->assertNotEmpty(
            $rowId,
            "Should have returned the ID of the new row for address_type=to and email_address_id={$address1->id}"
        );

        // Link the same email address with the same address_type.
        $this->assertSame(
            $rowId,
            $email->linkEmailToAddress($address1->id, 'to'),
            "Should have returned the ID of the existing row for address_type=to and email_address_id={$address1->id}"
        );

        // Link the same email address with a different address_type.
        $this->assertNotSame(
            $rowId,
            $email->linkEmailToAddress($address1->id, 'cc'),
            "Should have returned the ID of the new row for address_type=cc and email_address_id={$address1->id}"
        );

        // Link a different email address.
        $this->assertNotEquals(
            $rowId,
            $email->linkEmailToAddress($address2->id, 'to'),
            "Should have returned the ID of the new row for address_type=to and email_address_id={$address2->id}"
        );

        // The email address is invalid.
        $this->assertEmpty(
            $email->linkEmailToAddress(create_guid(), 'to'),
            'Should not have been able to load the email address'
        );

        // Link an email address that is already linked via multiple records.
        $contact = SugarTestContactUtilities::createContact();
        SugarTestEmailAddressUtilities::addAddressToPerson($contact, $address2);
        $email->load_relationship('contacts_bcc');
        $email->contacts_bcc->add($contact, array('email_address_id' => $address2->id));

        $lead = SugarTestLeadUtilities::createLead();
        SugarTestEmailAddressUtilities::addAddressToPerson($lead, $address2);
        $email->load_relationship('leads_bcc');
        $email->leads_bcc->add($lead, array('email_address_id' => $address2->id));

        $this->assertNotEmpty(
            $email->linkEmailToAddress($address2->id, 'bcc'),
            "Should have returned the ID of one of the rows for address_type=bcc and email_address_id={$address1->id}"
        );

        // Fails to link the email address and there are no existing rows for that email address and address_type.
        $link = $this->getMockBuilder('Link2')
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();
        $link->method('add')->willReturn(array($address1->id));
        $email->email_addresses_from = $link;
        $this->assertEmpty($email->linkEmailToAddress($address1->id, 'from'), 'Should have returned nothing');

        // Need to remove the mock to avoid failures when SugarRelationship::resaveRelatedBeans() is called.
        unset($email->email_addresses_from);
    }
}


class MockMailer extends SmtpMailer
{
    var $_sent;

    function __construct(OutboundEmailConfiguration $config) {
        $this->_sent = false;
        $this->config = $config;
        $headers = new EmailHeaders();
        $headers->setHeader(EmailHeaders::From,   $config->getFrom());
        $headers->setHeader(EmailHeaders::Sender, $config->getFrom());
        $this->headers = $headers;
        $this->recipients = new RecipientsCollection();
    }

    public function getHeaders() {
        return($this->headers->packageHeaders());
    }

    public function getRecipients() {
        return $this->recipients->getAll();
    }

    public function send() {
        $this->_sent = true;
    }

    public function wasSent() {
        return $this->_sent;
    }

    public function toArray() {
        return $this->asArray($this);
    }

    private function asArray($d) {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }
        if (is_array($d)) {
            return array_map(__METHOD__, $d);
        }
        return $d;
    }
}

class MockMailerFactory extends MailerFactory
{
    private static $mailer;

    public static function setMailer(BaseMailer $mailer)
    {
        static::$mailer = $mailer;
    }

    public static function getMailer(OutboundEmailConfiguration $config)
    {
        return static::$mailer;
    }
}
