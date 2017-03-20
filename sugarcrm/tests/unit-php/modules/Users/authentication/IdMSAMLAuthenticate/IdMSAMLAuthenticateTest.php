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
namespace Sugarcrm\SugarcrmTestUnit\modules\Users\authentication\IdMSAMLAuthenticate;

use Sugarcrm\IdentityProvider\Authentication\Token\SAML\ResultToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\AuthProviderBasicManagerBuilder;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;

/**
 * Class IdMSAMLAuthenticateTest
 *
 * @coversDefaultClass \IdMSAMLAuthenticate
 */
class IdMSAMLAuthenticateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Config | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $config = null;

    /**
     * @var \IdMSAMLAuthenticate | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $auth = null;

    /**
     * @var AuthProviderBasicManagerBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authProviderBuilder;

    /**
     * @var AuthenticationProviderManager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authProviderManager = null;

    /**
     * @var ResultToken | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $token = null;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->auth = $this->getMockBuilder(\IdMSAMLAuthenticate::class)
                           ->setMethods(['getConfig', 'getAuthProviderBasicBuilder', 'getAuthProviderBuilder'])
                           ->getMock();
        $this->config = $this->createMock(Config::class);
        $this->authProviderBuilder = $this->createMock(AuthProviderBasicManagerBuilder::class);
        $this->authProviderManager = $this->createMock(AuthenticationProviderManager::class);
        $this->token = $this->createMock(ResultToken::class);

        $this->auth->method('getConfig')->willReturn($this->config);
        $this->auth->method('getAuthProviderBasicBuilder')->willReturn($this->authProviderBuilder);
        $this->auth->method('getAuthProviderBuilder')->willReturn($this->authProviderBuilder);
        $this->authProviderBuilder->method('buildAuthProviders')->willReturn($this->authProviderManager);
    }

    /**
     * Provides set of data for testGetLoginUrlRelayState
     * @return array
     */
    public function getLoginUrlRelayStateProvider()
    {
        return [
            'platformBaseSameWindow' => [
                'platform' => 'base',
                'sameWindow' => true,
                'expectedRelayState' => 'eyJwbGF0Zm9ybSI6ImJhc2UifQ==',
            ],
            'noPlatformSameWindow' => [
                'platform' => null,
                'sameWindow' => true,
                'expectedRelayState' => 'eyJkYXRhT25seSI6MX0=',
            ],
            'platformBaseNewWindow' => [
                'platform' => 'base',
                'sameWindow' => false,
                'expectedRelayState' => 'eyJkYXRhT25seSI6MSwicGxhdGZvcm0iOiJiYXNlIn0=',
            ],
            'noPlatformNewWindow' => [
                'platform' => null,
                'sameWindow' => false,
                'expectedRelayState' => 'eyJkYXRhT25seSI6MX0=',
            ],
        ];
    }

    /**
     * @param string $platform
     * @param bool $sameWindow
     * @param string expectedRelayState
     *
     * @dataProvider getLoginUrlRelayStateProvider
     * @covers ::getLoginUrl
     */
    public function testGetLoginUrlRelayState($platform, $sameWindow, $expectedRelayState)
    {
        $this->config->method('get')->withConsecutive(
            ['SAML_SAME_WINDOW', null],
            ['saml.validate_request_id', null]
        )->willReturnOnConsecutiveCalls($sameWindow, false);
        $this->authProviderManager->expects($this->once())
                                  ->method('authenticate')
                                  ->with(
                                      $this->callback(function ($token) use ($expectedRelayState) {
                                          $this->assertEquals($expectedRelayState, $token->getAttribute('returnTo'));
                                          return true;
                                      })
                                  )->willReturn($this->token);
        $this->token->expects($this->once())->method('getAttribute')->with('url');
        $this->auth->getLoginUrl(['platform' => $platform]);
    }

    public function loginAuthenticateDataProvider()
    {
        return [
            [false, false],
            [true, true],
        ];
    }

    /**
     * @dataProvider loginAuthenticateDataProvider
     * @covers ::loginAuthenticate()
     */
    public function testLoginAuthenticate($expected, $tokenAuthenticated)
    {
        $_POST['SAMLResponse'] = '<SAMLResponse>';

        $this->token->expects($this->once())
            ->method('isAuthenticated')
            ->willReturn($tokenAuthenticated);
        $this->authProviderManager->expects($this->once())
            ->method('authenticate')
            ->willReturn($this->token);
        $this->assertEquals($expected, $this->auth->loginAuthenticate('', ''));

        unset($_POST['SAMLResponse']);
    }

    public function getLogoutUrlDataProvider()
    {
        return [
            'redirect binding' => [
                'http://test.com/saml/logout',
                [
                    ['url', 'http://test.com/saml/logout'],
                    ['method', 'GET'],
                ],
            ],
            'post binding' => [
                [
                    'url' => 'http://test.com/saml/logout',
                    'method' => 'POST',
                    'params' => ['SAMLRequest' => 'some-saml-request'],
                ],
                [
                    ['url', 'http://test.com/saml/logout'],
                    ['method', 'POST'],
                    ['parameters', ['SAMLRequest' => 'some-saml-request']],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getLogoutUrlDataProvider
     * @covers ::getLogoutUrl()
     */
    public function testGetLogoutUrl($expected, $attributesMap)
    {
        $this->token->expects($this->any())
            ->method('getAttribute')
            ->willReturnMap($attributesMap);
        $this->authProviderManager->expects($this->once())
            ->method('authenticate')
            ->willReturn($this->token);
        $this->assertEquals($expected, $this->auth->getLogoutUrl());
    }
}
