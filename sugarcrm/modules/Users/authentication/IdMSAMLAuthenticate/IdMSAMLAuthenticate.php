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

use Sugarcrm\IdentityProvider\Authentication\Token\SAML\InitiateToken;
use Sugarcrm\IdentityProvider\Authentication\Token\SAML\AcsToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\AuthProviderBasicManagerBuilder;

class IdMSAMLAuthenticate extends SAMLAuthenticate
{
    /**
     * Get URL to follow to get logged in
     *
     * @param array $returnQueryVars Query variables that should be added to the return URL
     *
     * @return string
     * @throws AuthenticationException
     */
    public function getLoginUrl($returnQueryVars = array())
    {
        $initToken = new InitiateToken();

        $config = $this->getConfig();
        $sameWindow = $config->get('SAML_SAME_WINDOW');

        $relayStateData = [
            'dataOnly' => 1,
        ];
        foreach ($returnQueryVars as $key => $value) {
            if (!is_null($value)) {
                $relayStateData[$key] = $value;
            }
        }
        if (!empty($returnQueryVars['platform']) && $returnQueryVars['platform'] == 'base' && !empty($sameWindow)) {
            unset($relayStateData['dataOnly']);
        }

        if ($relayStateData) {
            $initToken->setAttribute('returnTo', base64_encode(json_encode($relayStateData)));
        }

        $authManager = $this->getAuthProviderBasicBuilder($config)->buildAuthProviders();

        $token = $authManager->authenticate($initToken);

        $url = $token->getAttribute('ssoUrl');

        // @todo This should be moved into IdM, see BR-5052
        if ($config->get('saml.validate_request_id')) {
            // try to generate cryptographically secure request ID
            // and replace the one generated by onelogin/php-saml
            $requestId = $this->generateRequestId();
            if ($requestId) {
                $url = $this->patchLoginUrl($url, $requestId);
            } else {
                $requestId = $this->getRequestId($url);
            }

            $GLOBALS['log']->info('Registering SAML request ' . $requestId);
            $this->getRequestRegistry()->registerRequest($requestId);
        }

        return $url;
    }

    public function loginAuthenticate($username, $password, $fallback = false, $params = [])
    {
        if (empty($_POST['SAMLResponse'])) {
            return parent::loginAuthenticate($username, $password, $fallback, $params);
        }

        $acsToken = new AcsToken($_POST['SAMLResponse']);
        $authManager = $this->getAuthProviderBuilder($this->getConfig())->buildAuthProviders();
        $token = $authManager->authenticate($acsToken);

        if ($token->isAuthenticated()) {
            // @todo validate replay protection (in IdM), see BR-5052
            // @todo update custom fields
            // @todo JIT user creation
            return true;
        }
        return false;
    }

    /**
     * Get idm configuration instance.
     *
     * @return Config
     */
    protected function getConfig()
    {
        return new Config(\SugarConfig::getInstance());
    }

    /**
     * @param Config $config
     *
     * @return AuthProviderBasicManagerBuilder
     */
    protected function getAuthProviderBasicBuilder(Config $config)
    {
        return new AuthProviderBasicManagerBuilder($config);
    }
}
