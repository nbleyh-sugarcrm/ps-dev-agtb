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

use PHPUnit\Framework\TestCase;

class UsersLastImportTest extends TestCase
{
    private $_importModule = 'Notes';
    private $_importObject = 'Note';
    private $_importRecordCount = 3;
    private $_importIds = [];
    private $_usersLastImport;
    private $_usersLastImportIds;
    private $cstmTableExistBefore;
    private $oldCustomFields;
    
    protected function setUp() : void
    {
        SugarTestHelper::setUp("current_user");
        $this->_usersLastImport = new UsersLastImport();
        $this->_addImportedRecords();
    }
    
    protected function tearDown() : void
    {
        $focus = $this->_loadBean($this->_importModule);
        
        $sql = "DELETE FROM {$focus->table_name} WHERE id IN ('" . implode("','", $this->_importIds) . "')";
        $GLOBALS['db']->query($sql);
        
        $sql = 'DELETE FROM users_last_import WHERE id IN (\'' . implode("','", $this->_usersLastImportIds) . '\')';
        $GLOBALS['db']->query($sql);

        if (!$this->cstmTableExistBefore && $GLOBALS['db']->tableExists('notes_cstm')) {
            $GLOBALS['db']->dropTableName('notes_cstm');
        }

        if (!empty($this->oldCustomFields)) {
            $GLOBALS['dictionary']['Note']['custom_fields'] = $this->oldCustomFields;
        } else {
            unset($GLOBALS['dictionary']['Note']['custom_fields']);
        }

        SugarTestHelper::tearDown();
    }
    
    private function _loadBean($module)
    {
        return BeanFactory::newBean($module);
    }
    
    private function _addImportedRecords()
    {
        for ($i = 0; $i < $this->_importRecordCount; $i++) {
            $focus = $this->_loadBean($this->_importModule);
            $focus->name = "record $i";
            $focus->save();
            $this->_importIds[$i] = $focus->id;
            
            $last_import = new UsersLastImport();
            $last_import->assigned_user_id = $GLOBALS['current_user']->id;
            $last_import->import_module = $this->_importModule;
            $last_import->bean_type = $this->_importObject;
            $last_import->bean_id = $this->_importIds[$i];
            $this->_usersLastImportIds[] = $last_import->save();
        }
    }
    
    public function testMarkDeletedByUserId()
    {
        $this->_usersLastImport->mark_deleted_by_user_id($GLOBALS['current_user']->id);
        
        $query = "SELECT * FROM users_last_import 
                    WHERE assigned_user_id = '{$GLOBALS['current_user']->id}'";
        
        $result = $GLOBALS['db']->query($query);
       
        $this->assertFalse($GLOBALS['db']->fetchByAssoc($result), 'There should not be any records in the table now');
    }
    
    public function testUndo()
    {
        $this->_usersLastImport->undo(
            $this->_importModule
        );

        $focus = $this->_loadBean($this->_importModule);
        
        $query = "SELECT * FROM {$focus->table_name}
                    WHERE id IN ('" .
                        implode("','", $this->_importIds) . "')";
        
        $result = $GLOBALS['db']->query($query);
        
        $this->assertFalse($GLOBALS['db']->fetchByAssoc($result), 'There should not be any records in the table now');
    }

    /**
     * Test undo() to remove the imported custom fields
     */
    public function testUndoLastImportedCustomFields()
    {
        $this->cstmTableExistBefore = false;
        if (!$GLOBALS['db']->tableExists('notes_cstm')) {
            $GLOBALS['db']->createTableParams(
                'notes_cstm',
                [
                    'id_c' =>  [
                        'name' => 'id_c',
                        'type' => 'id',
                    ],
                ],
                [],
            );
        } else {
            $this->cstmTableExistBefore = true;
        }
        $this->oldCustomFields = $GLOBALS['dictionary']['Note']['custom_fields'] ?? null;
        $GLOBALS['dictionary']['Note']['custom_fields'] = ['customField'];

        $focus = SugarTestNoteUtilities::createNote();

        $GLOBALS['db']->query("INSERT INTO notes_cstm (id_c) VALUES ('{$focus->id}')");

        $last_import = new UsersLastImport();
        $last_import->assigned_user_id = $GLOBALS['current_user']->id;
        $last_import->import_module = 'Notes';
        $last_import->bean_type = 'Note';
        $last_import->bean_id = $focus->id;
        $this->_usersLastImportIds[] = $last_import->save();

        $last_import->undo(
            $last_import->import_module
        );

        $result = $GLOBALS['db']->query("SELECT * FROM notes_cstm where id_c = '{$focus->id}'");
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse($row, 'There should not be any records in the table now');
    }
    
    /**
     * @ticket 21828
     */
    public function testUndoRemovedAddedEmailAddresses()
    {
        $time = date('Y-m-d H:i:s');
        $unid = uniqid();
        
        $focus = new Account();
        $focus->id = "Account_".$unid;
        
        $last_import = new UsersLastImport();
        $last_import->assigned_user_id = $GLOBALS['current_user']->id;
        $last_import->import_module = 'Accounts';
        $last_import->bean_type = 'Account';
        $last_import->bean_id = $focus->id;
        $last_import->save();
        
        $this->email_addr_bean_rel_id = 'email_addr_bean_rel_'.$unid;
        $this->email_address_id = 'email_address_id_'.$unid;
        $GLOBALS['db']->query("insert into email_addr_bean_rel (id , email_address_id, bean_id, bean_module, primary_address, date_created , date_modified) values ('{$this->email_addr_bean_rel_id}', '{$this->email_address_id}', '{$focus->id}', 'Accounts', 1, '$time', '$time')");
                
        $GLOBALS['db']->query("insert into email_addresses (id , email_address, email_address_caps, date_created, date_modified) values ('{$this->email_address_id}', 'test@g.com', 'TEST@G.COM', '$time', '$time')");

        // setup
        require 'include/modules.php';
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;

        $last_import->undo(
            $last_import->import_module
        );

        $result = $GLOBALS['db']->query("SELECT * FROM email_addr_bean_rel where id = '{$this->email_addr_bean_rel_id}'");
        $rows = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse($rows);
        
        $result = $GLOBALS['db']->query("SELECT * FROM email_addresses where id = '{$this->email_address_id}'");
        $rows = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse($rows);
        
        $GLOBALS['db']->query("DELETE FROM users_last_import WHERE id = '{$last_import->id}'");
    }
    
    public function testUndoById()
    {
        $this->_usersLastImport->undoById(
            $this->_usersLastImportIds[0]
        );

        $focus = $this->_loadBean($this->_importModule);
        
        $query = "SELECT * FROM {$focus->table_name}
                    WHERE id = '{$this->_importIds[0]}'";
        
        $result = $GLOBALS['db']->query($query);
        
        $this->assertFalse($GLOBALS['db']->fetchByAssoc($result), 'There should not be any records in the table now');
    }
    
    public function testGetBeansByImport()
    {
        foreach (UsersLastImport::getBeansByImport('Notes') as $objectName) {
            $this->assertEquals($objectName, 'Note');
        }
    }
}
