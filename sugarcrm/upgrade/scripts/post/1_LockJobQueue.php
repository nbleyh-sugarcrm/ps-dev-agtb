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

use Sugarcrm\Sugarcrm\JobQueue\Helper\ProcessControl;

/**
 * Stop JobQueue and lock the JQ entry point.
 */
class SugarUpgradeLockJobQueue extends UpgradeScript
{
    public $order = 999; // Before the scheme update.
    public $type = self::UPGRADE_CORE; // To not be used by Shadow.

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // JobQueue has been added in 7.7.
        if (version_compare($this->from_version, '7.7', '<')) {
            $this->log('Skipping script, pre 7.7 version.');
            return true;
        }
        $JobQueueEPHelper = new ProcessControl('queueManager');
        $JobQueueEPHelper->lockService();
        $JobQueueEPHelper->stopServiceProcesses();
    }
}
