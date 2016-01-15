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

namespace Sugarcrm\Sugarcrm\JobQueue\Runner;

/**
 * Class Standard
 * @package JobQueue
 */
class Standard extends AbstractRunner
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->acquireLock();
        $this->startWorker();
    }

    /**
     * Added check on lock lifetime.
     * {@inheritdoc}
     */
    public function isWorkProcessActual()
    {
        if (!parent::isWorkProcessActual()) {
            return (time() - $this->lock->getLock()) > $this->lockLifetime;
        }
        return true;
    }
}
