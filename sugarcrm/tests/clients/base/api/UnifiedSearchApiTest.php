<?php
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



require_once ('include/api/RestService.php');
require_once ("clients/base/api/UnifiedSearchApi.php");
require_once ("clients/base/api/ModuleApi.php");


/**
 * @group ApiTests
 */
class UnifiedSearchApiTest extends Sugar_PHPUnit_Framework_TestCase {

    public $accounts;
    public $roles;
    public $unifiedSearchApi;
    public $moduleApi;

    public function setUp() {
        SugarTestHelper::setUp("current_user");        
        // create a bunch of accounts
        for($x=0; $x<10; $x++) {
            $acc = BeanFactory::newBean('Accounts');
            $acc->name = 'UnifiedSearchApiTest Account ' . create_guid();
            $acc->assigned_user_id = $GLOBALS['current_user']->id;
            $acc->save();
            $this->accounts[] = $acc;
        }
        // load up the unifiedSearchApi for good times ahead
        $this->unifiedSearchApi = new UnifiedSearchApi();
        $this->moduleApi = new ModuleApi();
    }

    public function tearDown() {
        $GLOBALS['current_user']->is_admin = 1;        
        // delete the bunch of accounts crated
        foreach($this->accounts AS $account) {
            $account->mark_deleted($account->id);
        }
        // unset unifiedSearchApi
        unset($this->unifiedSearchApi);
        unset($this->moduleApi);
        // clean up all roles created
        foreach($this->roles AS $role) {
            $role->mark_deleted($role->id);
            $role->mark_relationships_deleted($role->id);
        }
        unset($_SESSION['ACL']);
        SugarTestHelper::tearDown();
        parent::tearDown();        
    }

    // test that when read only is set for every field you can still retrieve
    // @Bug 60225
    public function testReadOnlyFields() {
        // create role that is all fields read only
        $this->roles[] = $role = $this->createRole('UNIFIEDSEARCHAPI - UNIT TEST ' . create_guid(), array('Accounts'), array('access', 'view', 'list', 'export'));

        if (!($GLOBALS['current_user']->check_role_membership($role->name))) {
            $GLOBALS['current_user']->load_relationship('aclroles');
            $GLOBALS['current_user']->aclroles->add($role);
            $GLOBALS['current_user']->save();
        }

        // get all the accounts fields and set them readonly
        foreach($this->accounts[0]->field_defs AS $fieldName => $params) {
            $aclField = new ACLField();
            $aclField->setAccessControl('Accounts', $role->id, $fieldName, 50);
        }
        ACLField::loadUserFields('Accounts', 'Account', $GLOBALS['current_user']->id, true );

        $id = $GLOBALS['current_user']->id;
        $GLOBALS['current_user'] = BeanFactory::getBean('Users', $id);
        unset($_SESSION['ACL']);        
        // test I can retreive accounts
        $args = array('module_list' => 'Accounts',);
        $list = $this->unifiedSearchApi->globalSearch(new UnifiedSearchApiServiceMockUp(), $args);
        $this->assertNotEmpty($list['records'], "Should have some accounts: " . print_r($list, true));        
    }

    // if you have view only you shouldn't be able to create, but you should be able to retrieve records
    public function testViewOnly() {
        // create a role that is view only
        $this->roles[] = $role = $this->createRole('UNIFIEDSEARCHAPI - UNIT TEST ' . create_guid(), array('Accounts', ), array('access', 'view', 'list', ));

        if (!($GLOBALS['current_user']->check_role_membership($role->name))) {
            $GLOBALS['current_user']->load_relationship('aclroles');
            $GLOBALS['current_user']->aclroles->add($role);
            $GLOBALS['current_user']->save();
        }
        $id = $GLOBALS['current_user']->id;
        $GLOBALS['current_user'] = BeanFactory::getBean('Users', $id);
        unset($_SESSION['ACL']);
        // test I can retrieve accounts
        $args = array('module_list' => 'Accounts',);
        $list = $this->unifiedSearchApi->globalSearch(new UnifiedSearchApiServiceMockUp(), $args);
        $this->assertNotEmpty($list['records'], "Should have some accounts: " . print_r($list, true));
        // test I can't create
        $this->setExpectedException(
          'SugarApiExceptionNotAuthorized', 'You are not authorized to create Accounts. Contact your administrator if you need access.'
        );        
        $result = $this->moduleApi->createRecord(new UnifiedSearchApiServiceMockUp, array('module' => 'Accounts', 'name' => 'UnifiedSearchApi Create Denied - ' . create_guid()));
    }
    
    protected function createRole($name, $allowedModules, $allowedActions, $ownerActions = array()) {
        $role = new ACLRole();
        $role->name = $name;
        $role->description = $name;
        $role->save();
        $GLOBALS['db']->commit();

        $roleActions = $role->getRoleActions($role->id);

        foreach ($roleActions as $moduleName => $actions) {
            // enable allowed modules
            if (isset($actions['module']['access']['id']) && !in_array($moduleName, $allowedModules)) {
                $role->setAction($role->id, $actions['module']['access']['id'], ACL_ALLOW_DISABLED);
            } elseif (isset($actions['module']['access']['id']) && in_array($moduleName, $allowedModules)) {
                $role->setAction($role->id, $actions['module']['access']['id'], ACL_ALLOW_ENABLED);
            } else {
                foreach ($actions as $action => $actionName) {
                    if (isset($actions[$action]['access']['id'])) {
                        $role->setAction($role->id, $actions[$action]['access']['id'], ACL_ALLOW_DISABLED);
                    }
                }
            }

            if (in_array($moduleName, $allowedModules)) {
                foreach ($actions['module'] as $actionName => $action) {

                    if(in_array($actionName, $ownerActions)) {
                        $aclAllow = ACL_ALLOW_OWNER;
                    }
                    elseif (in_array($actionName, $allowedActions)) {
                        $aclAllow = ACL_ALLOW_ALL;
                    } else {
                        $aclAllow = ACL_ALLOW_NONE;
                    }
                    $role->setAction($role->id, $action['id'], $aclAllow);
                }
            }

        }
        return $role;
    }

}

class UnifiedSearchApiServiceMockUp extends RestService
{
    public function execute() {}
    protected function handleException(Exception $exception) {}
}
