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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;

/**
 * Discovery API
 */
class DiscoveryApi extends SugarApi
{
    /**
     * @return array
     */
    public function registerApiRest()
    {
        return array(
            'discovery' => array(
                'reqType' => 'GET',
                'path' => array('discovery'),
                'pathVars' => array(''),
                'method' => 'discovery',
                'shortHelp' => 'Returns publicly availble configuration for authentication',
                'longHelp' => 'include/api/help/discovery_help.html',
                'noLoginRequired' => true,
                'ignoreMetaHash' => true,
                'ignoreSystemStatusError' => true,
            ),
        );
    }

    /**
     * Discovery endpoint
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function discovery(ServiceBase $api, array $args)
    {
        $config = $this->getIDMModeConfig();
        if (!empty($config)) {
            $data = [
                'idmMode' => true,
                'stsUrl' => $config['stsUrl'],
                'tenant' => $config['tid'],
            ];
        } else {
            $data = [
                'idmMode' => false,
            ];
        }
        return $data;
    }

    /**
     * @return array
     */
    protected function getIDMModeConfig()
    {
        return (new Config(SugarConfig::getInstance()))->getIDMModeConfig();
    }
}
