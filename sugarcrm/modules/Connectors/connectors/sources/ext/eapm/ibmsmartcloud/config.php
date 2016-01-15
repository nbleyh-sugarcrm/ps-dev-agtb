<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

$config = array (
  'name' => 'IBM SmartCloud',
  'eapm' => array(
    'enabled' => true,
    'only' => true,
  ),
  'order' => 14,
  'properties' => array (
      'oauth_consumer_key' => '',
      'oauth_consumer_secret' => '',
  ),
  'encrypt_properties' => array (
      'oauth_consumer_secret',
  ),
);
//BEGIN SUGARCRM flav=int ONLY
$config['properties']['oauth_consumer_key'] = '9399cf0ce6e4ca4d30d56a76b21da89';
$config['properties']['oauth_consumer_secret'] = '7704b27829c5715445e14637415b67c1';
//END SUGARCRM flav=int ONLY
