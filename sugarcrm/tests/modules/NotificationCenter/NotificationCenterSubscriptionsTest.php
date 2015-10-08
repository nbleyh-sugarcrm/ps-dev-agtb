<?php

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

/**
 * Class NotificationCenterSubscriptionsTest
 * @coversDefaultClass \NotificationCenterSubscription
 */
class NotificationCenterSubscriptionsTest extends Sugar_PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        SugarTestHelper::setUp('beanList');
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('moduleList');
    }

    protected function tearDown()
    {
        SugarTestHelper::tearDown();
        parent::tearDown();
    }

    public function testBeanByModule()
    {
        $bean  = BeanFactory::getBean('NotificationCenterSubscriptions');
        $this->assertInstanceOf('NotificationCenterSubscription', $bean);
    }
}
