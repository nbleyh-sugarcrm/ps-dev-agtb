<?php
 if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/**
 * Merge viewdefs files between old and new code
 */
class SugarUpgradeMergeTemplates extends UpgradeScript
{
    public $order = 200;
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
                $this->error('SugarMerge7.php not found, this file is required for Sugar7 Upgrades', true);
            }
        }
        $merger = new SugarMerge7($this->context['new_source_dir']);
        $merger->mergeAll();
        $this->log("**** Merge finished ");
    }
}
