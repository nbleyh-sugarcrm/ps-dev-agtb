<?php
 if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
/**
 * Merge viewdefs files between old and new code
 */
class SugarUpgradeMergeTemplates extends UpgradeScript
{
    public $order = 400;
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if(empty($this->context['new_source_dir'])) {
            $this->log("**** Merge skipped - no new source dir");
            return;
        }
        $this->log("**** Merge started ");
        require_once('modules/UpgradeWizard/SugarMerge/SugarMerge.php');
        if (file_exists($this->context['new_source_dir'].'/modules/UpgradeWizard/SugarMerge/SugarMerge7.php')) {
            require_once($this->context['new_source_dir'].'/modules/UpgradeWizard/SugarMerge/SugarMerge7.php');
        } else {
            if (file_exists('modules/UpgradeWizard/SugarMerge/SugarMerge7.php')) {
                require_once('modules/UpgradeWizard/SugarMerge/SugarMerge7.php');
            } else {
                return $this->error('SugarMerge7.php not found, this file is required for Sugar7 Upgrades', true);
            }
        }
        $merger = new SugarMerge7($this->context['new_source_dir']);
        $merger->setUpgrader($this->upgrader);
        $merger->mergeAll();
        $this->log("**** Merge finished ");
    }
}
