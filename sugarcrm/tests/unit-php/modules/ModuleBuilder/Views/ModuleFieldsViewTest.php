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

namespace Sugarcrm\SugarcrmTestsUnit\modules\ModuleBuilder\Views;

use PHPUnit\Framework\TestCase;

require_once 'include/utils.php';

/**
 * @coversDefaultClass \ViewModulefields
 */
class ModuleFieldsViewTest extends TestCase
{
    /**
     * @var \ViewModulefields
     */
    protected $view = null;

    /**
     * @var \SugarConfig
     */
    protected $sugarConfig = null;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->view = new \ViewModulefields();
        $this->sugarConfig = \SugarConfig::getInstance();
        $this->sugarConfig->clearCache();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->sugarConfig->clearCache();
    }

    /**
     * Provides data for testIsValidStudioField
     * @return array
     */
    public function isValidStudioFieldProvider()
    {
        return [
            'idmModeDisabledInSugarAndFieldIsIdmMode' => [
                'idmModeConfig' => [],
                'fieldDef' => [
                    'name' => 'user_name',
                    'vname' => 'LBL_USER_NAME',
                    'type' => 'username',
                    'dbType' => 'varchar',
                    'len' => '60',
                    'importable' => 'required',
                    'required' => true,
                    'studio' => [
                        'no_duplicate' => true,
                        'editview' => false,
                        'detailview' => true,
                        'quickcreate' => false,
                        'basic_search' => false,
                        'advanced_search' => false,
                        'wirelesseditview' => false,
                        'wirelessdetailview' => true,
                        'wirelesslistview' => false,
                        'wireless_basic_search' => false,
                        'wireless_advanced_search' => false,
                        'rollup' => false,
                    ],
                    'idm_mode_disabled' => true,
                ],
                'expectedResult' => true,
            ],
            'idmModeDisabledInSugarAndStudioFalse' => [
                'idmModeConfig' => [],
                'fieldDef' => [
                    'name' => 'user_name',
                    'vname' => 'LBL_USER_NAME',
                    'type' => 'username',
                    'dbType' => 'varchar',
                    'len' => '60',
                    'importable' => 'required',
                    'required' => true,
                    'studio' => false,
                ],
                'expectedResult' => false,
            ],
            'idmModeDisabledInSugarAndTypeId' => [
                'idmModeConfig' => [],
                'fieldDef' => [
                    'name' => 'user_name',
                    'vname' => 'LBL_USER_NAME',
                    'type' => 'id',
                    'dbType' => 'varchar',
                    'len' => '60',
                    'importable' => 'required',
                    'required' => true,
                ],
                'expectedResult' => false,
            ],
            'idmModeDisabledInSugarAndNoStudioDef' => [
                'idmModeConfig' => [],
                'fieldDef' => [
                    'name' => 'user_name',
                    'vname' => 'LBL_USER_NAME',
                    'type' => 'username',
                    'dbType' => 'varchar',
                    'len' => '60',
                    'importable' => 'required',
                    'required' => true,
                ],
                'expectedResult' => true,
            ],
            'idmModeDisabledInSugarAndStudioHidden' => [
                'idmModeConfig' => [],
                'fieldDef' => [
                    'name' => 'user_name',
                    'vname' => 'LBL_USER_NAME',
                    'type' => 'username',
                    'dbType' => 'varchar',
                    'len' => '60',
                    'importable' => 'required',
                    'required' => true,
                    'studio' => 'hidden',
                ],
                'expectedResult' => false,
            ],
            'idmModeDisabledInSugarAndStudioVisible' => [
                'idmModeConfig' => [],
                'fieldDef' => [
                    'name' => 'user_name',
                    'vname' => 'LBL_USER_NAME',
                    'type' => 'username',
                    'dbType' => 'varchar',
                    'len' => '60',
                    'importable' => 'required',
                    'required' => true,
                    'studio' => 'visible',
                ],
                'expectedResult' => true,
            ],
            'idmModeEnabledInSugarAndFieldIsIdmModeDisabled' => [
                'idmModeConfig' => [
                    'clientId' => 'testLocal',
                    'clientSecret' => 'testLocalSecret',
                    'stsUrl' => 'http://sts.sugarcrm.local',
                    'idpUrl' => 'http://login.sugarcrm.local',
                    'stsKeySetId' => 'KeySetName',
                    'tid' => 'srn:cluster:sugar:eu:0000000001:tenant',
                    'idpServiceName' => 'idm',
                    'cloudConsoleUrl' => 'http://sts.staging.arch.sugarcrm.io/',
                ],
                'fieldDef' => [
                    'name' => 'user_name',
                    'vname' => 'LBL_USER_NAME',
                    'type' => 'username',
                    'dbType' => 'varchar',
                    'len' => '60',
                    'importable' => 'required',
                    'required' => true,
                    'studio' => [
                        'no_duplicate' => true,
                        'editview' => false,
                        'detailview' => true,
                        'quickcreate' => false,
                        'basic_search' => false,
                        'advanced_search' => false,
                        'wirelesseditview' => false,
                        'wirelessdetailview' => true,
                        'wirelesslistview' => false,
                        'wireless_basic_search' => false,
                        'wireless_advanced_search' => false,
                        'rollup' => false,
                    ],
                    'idm_mode_disabled' => true,
                ],
                'expectedResult' => false,
            ],
            'idmModeEnabledInSugarAndFieldIsIdmModeAndStudioTrue' => [
                'idmModeConfig' => [
                    'clientId' => 'testLocal',
                    'clientSecret' => 'testLocalSecret',
                    'stsUrl' => 'http://sts.sugarcrm.local',
                    'idpUrl' => 'http://login.sugarcrm.local',
                    'stsKeySetId' => 'KeySetName',
                    'tid' => 'srn:cluster:sugar:eu:0000000001:tenant',
                    'idpServiceName' => 'idm',
                    'cloudConsoleUrl' => 'http://sts.staging.arch.sugarcrm.io/',
                ],
                'fieldDef' => [
                    'name' => 'user_name',
                    'vname' => 'LBL_USER_NAME',
                    'type' => 'username',
                    'dbType' => 'varchar',
                    'len' => '60',
                    'importable' => 'required',
                    'required' => true,
                    'studio' => true,
                    'idm_mode_disabled' => true,
                ],
                'expectedResult' => false,
            ],
        ];
    }

    /**
     * @param $idmModeConfig
     * @param $fieldDef
     * @param $expectedResult
     *
     * @dataProvider isValidStudioFieldProvider
     * @covers ::isValidStudioField
     */
    public function testIsValidStudioField($idmModeConfig, $fieldDef, $expectedResult)
    {
        $this->sugarConfig->_cached_values['idm_mode'] = $idmModeConfig;
        $this->assertEquals($expectedResult, $this->view->isValidStudioField($fieldDef));
    }
}
