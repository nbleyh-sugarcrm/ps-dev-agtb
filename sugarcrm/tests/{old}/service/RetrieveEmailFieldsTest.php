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

require_once 'vendor/nusoap//nusoap.php';


class RetrieveEmailFieldsTest extends SOAPTestCase
{
    public $acc;
    public $email_id;

    protected function setUp() : void
    {
        $this->soapURL = $GLOBALS['sugar_config']['site_url'].'/soap.php';
        parent::setUp();
        $this->login();
    }

    public function testGetEmailAddressFields()
    {
        $this->acc = SugarTestAccountUtilities::createAccount();
        $result = $this->soapClient->call('set_entry', ['session'=>$this->sessionId,"module_name" => 'Emails', 'name_value_list' => [['name'=>'assigned_user_id' , 'value'=>$GLOBALS['current_user']->id],['name'=>'from_addr_name' , 'value'=>'test@test.com'],['name'=>'parent_type' , 'value'=>'Accounts'],['name'=>'parent_id' , 'value'=>$this->acc->id],['name'=>'description' , 'value'=>"test"],['name'=>'name' , 'value'=>"Test Subject"]]]);
        $this->email_id = $result['id'];

        $result = $this->soapClient->call('get_entry_list', ['session'=> $this->sessionId,'module_name'=>'Emails', 'query' => "emails.id='".$this->email_id."'", 'order_by' => '', 'offset' => 0, 'select_fields' => ['id', 'from_addr_name', 'to_addrs_names'],'max_results'=>10,'deleted'=>0]);

        $this->assertEquals('from_addr_name', $result['entry_list'][0]['name_value_list'][1]['name']);
        $this->assertEquals('test@test.com', $result['entry_list'][0]['name_value_list'][1]['value']);
    }

    public function testGetEmailModuleFields()
    {
        $result = $this->soapClient->call('get_module_fields', ['session'=>$this->sessionId,"module_name" => 'Emails']);
        $foundFromAddrsName = false;
        foreach ($result['module_fields'] as $field) {
            if ($field['name'] == 'from_addr_name') {
                $foundFromAddrsName = true;
            }
        }
        $this->assertTrue($foundFromAddrsName, "Did not find from_addr_name");
    }
}
