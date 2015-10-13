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

namespace Sugarcrm\Sugarcrm\Dav\Cal\Handler\JobQueue;

use Sugarcrm\Sugarcrm\JobQueue\Handler\RunnableInterface;
use Sugarcrm\Sugarcrm\Dav\Cal\Adapter\Factory as CalDavAdapterFactory;
use Sugarcrm\Sugarcrm\Dav\Cal\Handler as CalDavHandler;
use Sugarcrm\Sugarcrm\JobQueue\Exception\LogicException as JQLogicException;
use Sugarcrm\Sugarcrm\JobQueue\Exception\InvalidArgumentException as JQInvalidArgumentException;

/**
 * Class Import
 * @package Sugarcrm\Sugarcrm\Dav\Cal\Handler\JobQueue
 * Class for import process initialization
 */
class Import implements RunnableInterface
{
    /**
     * @var string
     */
    protected $moduleName;
    /**
     * @var string
     */
    protected $fetchedRow;


    /**
     * @param array $fetchedRow
     * @param string $moduleName
     */
    public function __construct(array $fetchedRow, $moduleName)
    {
        $this->moduleName = $moduleName;
        $this->fetchedRow = $fetchedRow;
    }

    /**
     * start imports process for current CalDavEvent object
     * @throws \Sugarcrm\Sugarcrm\JobQueue\Exception\InvalidArgumentException if bean not instance of CalDavEvent
     * @throws \Sugarcrm\Sugarcrm\JobQueue\Exception\LogicException if related bean doesn't have adapter
     * @return string
     */
    public function run()
    {
        /** @var \CalDavEvent $bean */
        $bean = $this->getBean();
        if (!($bean instanceof \CalDavEvent)) {
            throw new JQInvalidArgumentException('Bean must be an instance of CalDavEvent. Instance of ' . get_class($bean) . ' given');
        }

        $handler = $this->getHandler();
        $handler->import($bean);
        return \SchedulersJob::JOB_SUCCESS;
    }

    /**
     * get bean for import process
     * @return null|\SugarBean
     */
    protected function getBean()
    {
        $bean = \BeanFactory::getBean($this->moduleName);
        $bean->populateFromRow($this->fetchedRow);
        return $bean;
    }

    /**
     * @return \Sugarcrm\Sugarcrm\Dav\Cal\Adapter\Factory
     */
    protected function getAdapterFactory()
    {
        return CalDavAdapterFactory::getInstance();
    }

    /**
     * return CalDav handler for import processing
     * @return Handler
     */
    protected function getHandler()
    {
        return new CalDavHandler();
    }
}
