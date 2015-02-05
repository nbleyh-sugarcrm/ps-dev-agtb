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

namespace Sugarcrm\Sugarcrm\SearchEngine;

/**
 *
 * Logic hook handler
 *
 */
class HookHandler
{
    /**
     * To be used from logic hooks to index a bean.
     *
     * @param \SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function indexBean($bean, $event, $arguments)
    {
        if (!$bean instanceof \SugarBean) {
            $this->getLogger()->fatal("Not bean ->" . var_export(get_class($bean), true));
            return;
        }

        $engine = $this->getSearchEngine()->indexBean($bean);
    }

    /**
     * Get search engine object
     * @return \Sugarcrm\Sugarcrm\SearchEngine\SearchEngine
     */
    protected function getSearchEngine()
    {
        return SearchEngine::getInstance();
    }

    /**
     * Get logger object
     * @return \LoggerManager
     */
    protected function getLogger()
    {
        return \LoggerManager::getLogger();
    }
}
