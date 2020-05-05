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

/**
 * Bug 58560 - Custom address don't have group property in vardefs
 */
class Bug58560Test extends RestTestBase
{
    /**
     * Module Builder Controller
     *
     * @var ModuleBuilderController
     */
    protected static $_mb;

    /**
     * Holder for the current request array
     *
     * @var array
     */
    protected static $_request = [];

    /**
     * Mock request for creating a field
     *
     * @var array
     */
    protected static $_createFieldRequestVars = [
        "action" => "saveField",
        "comments" => "",
        "default" => "",
        "dependency" => "",
        "dependency_display" => "",
        "duplicate_merge" => "0",
        "enforced" => "false",
        "formula" => "",
        "formula_display" => "",
        "help" => "",
        "importable" => "true",
        "is_update" => "true",
        "labelValue" => "Test Address",
        "label" => "LBL_TEST_ADDRESS",
        "new_dropdown" => "",
        "reportableCheckbox" => "1",
        "reportable" => "1",
        "to_pdf" => "true",
        "type" => "address",
        "name" => "test_address",
        "module" => "ModuleBuilder",
        "view_module" => "Accounts",
    ];

    /**
     * Mock request for deleting a field
     *
     * @var array
     */
    protected static $_deleteFieldRequestVars = [
        "action" => "DeleteField",
        "labelValue" => "Test Address",
        "label" => "LBL_TEST_ADDRESS",
        "to_pdf" => "true",
        "type" => "varchar",
        "name" => "test_address_c",
        "module" => "ModuleBuilder",
        "view_module" => "Accounts",
    ];
    
    public static function setUpBeforeClass() : void
    {
        // Basic setup of the environment
        SugarTestHelper::setUp('current_user', [true, true]);
        SugarTestHelper::setUp('beanList');
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('app_list_strings');
        SugarTestHelper::setUp('mod_strings', ['ModuleBuilder']);
        
        // Back up and reset the REQUEST
        self::$_request = $_REQUEST;
        $_REQUEST = self::$_createFieldRequestVars;
        
        // Build up the controller to save the new field
        self::$_mb = new ModuleBuilderController();
        self::$_mb->metadataApiCacheCleared = false;
        self::$_mb->action_SaveField();
    }
    
    public static function tearDownAfterClass(): void
    {
        // Set the request to delete the test field
        $_REQUEST = self::$_deleteFieldRequestVars;
        
        // Loop through the created fields and wipe them out
        $suffixes = ['street', 'city', 'state', 'postalcode', 'country'];
        foreach ($suffixes as $suffix) {
            $_REQUEST['name'] = self::_getFieldName($suffix);
            self::$_mb->metadataApiCacheCleared = false;
            self::$_mb->action_DeleteField();
        }
        
        // Clean up the environment
        SugarTestHelper::tearDown();
        
        // Reset the request
        $_REQUEST = self::$_request;
    }

    /**
     * Since our setup is needed before class, but we still need the rest utils,
     * we will simply override the rest base setup method, setting up only the
     * current user.
     */
    protected function setUp() : void
    {
        // Copied from RestTestBase and modified for our use here
        // Create an anonymous user for login purposes
        $this->_user = $GLOBALS['current_user'];
    }

    /**
     * @group 58560
     */
    public function testCustomFieldMetaDataFilesSaved()
    {
        $field = self::$_deleteFieldRequestVars['name'];
        
        // Eliminating the repetitive rebuilding of metadata cache in action_saveField
        // phpUnit should NOT be running setUpBeforeClass and tearDownAfterClass
        // for each test case, but it appears it is doing just that so this test
        // is being pulled out of the data provider and hit in a loop. Not ideal
        // but necessary.
        foreach ($this->_testFieldFileProvider() as $params) {
            $suffix = $params['suffix'];
            $name = self::_getFieldName($suffix);
            $file = 'custom/Extension/modules/Accounts/Ext/Vardefs/sugarfield_' . $name . '.php';
            $this->assertFileExists($file, "Custom field vardefs file not found");
            
            require $file;
            
            $this->assertNotEmpty($dictionary['Account']['fields'][$name]['group'], "The group setting was not saved");
            $this->assertEquals($dictionary['Account']['fields'][$name]['group'], $field, "Field group was not saved correctly");
        }
    }
    
    /**
     * @group rest
     * @group 58560
     */
    public function testGroupSetForAddressInMetaData()
    {
        $field = self::$_deleteFieldRequestVars['name'];
        $reply = $this->_restCall("metadata?module_filter=Accounts&type_filter=modules");
        $this->assertNotEmpty($reply['reply']['modules']['Accounts']['fields'], "Fields metadata array is empty");
        
        // Break it down a bit
        $fields = $reply['reply']['modules']['Accounts']['fields'];
        
        // This is kinda dirty, but it saves us from making 5 rest calls
        foreach ($this->_testFieldFileProvider() as $params) {
            $name = self::_getFieldName($params['suffix']);
            $this->assertArrayHasKey($name, $fields, "The field $name is missing");
            $this->assertNotEmpty($fields[$name]['group'], "Group index of the fields metadata for $name is not set");
            $this->assertEquals($fields[$name]['group'], $field, "Field group {$fields[$name]['group']} did not match the known field name $field");
        }
    }
    
    public function _testFieldFileProvider()
    {
        return [
            ['suffix' => 'street'],
            ['suffix' => 'city'],
            ['suffix' => 'state'],
            ['suffix' => 'postalcode'],
            ['suffix' => 'country'],
        ];
    }
    
    protected static function _getFieldName($suffix)
    {
        $field = self::$_createFieldRequestVars['name'];
        $name = $field . '_' . $suffix . '_c';
        return $name;
    }
}
