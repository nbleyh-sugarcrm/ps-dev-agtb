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
namespace Sugarcrm\SugarcrmTestsUnit\Security\Subject;

use Sugarcrm\Sugarcrm\Security\Subject\Installer;

/**
 * @coversDefaultClass \Sugarcrm\Sugarcrm\Security\Subject\Installer
 */
class InstallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::jsonSerialize
     */
    public function jsonSerialize()
    {
        $client = new Installer();
        $this->assertSame(['_type' => 'installer'], $client->jsonSerialize());
    }
}
