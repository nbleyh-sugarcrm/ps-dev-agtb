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

class ViewFtsTest extends TestCase
{
    public function testTranslateModulesList()
    {
        $view = new ViewFtsStub();
        $modules = ['Accounts', 'Bugs'];
        $results = $view->translateModulesList($modules);
        $match = [0=>['module'=>'Accounts', 'label'=>'Accounts'],
                                           1=>['module'=>'Bugs', 'label'=>'Bugs']];
        // Don't use array_diff, it doesn't compare in depth
        $this->assertEquals($match, $results, 'unexpected results');
    }

    public function testSendOutput()
    {
        $view = new ViewFtsStub();
        $testString = 'test string';
        $result = $view->sendOutput($testString, true, true);
        $expected = json_encode(['results' => $testString]);
        $this->assertEquals($expected, $result, "string not encoded correctly");
    }
}

class ViewFtsStub extends ViewFts
{
    public function translateModulesList($module)
    {
        return parent::translateModulesList($module);
    }

    public function sendOutput($contents, $return = false, $encode = false)
    {
        return parent::sendOutput($contents, $return, $encode);
    }
}
