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

use Sugarcrm\Sugarcrm\AccessControl\AdminWork;

/**
 * Install OOB Reports that were added in post-7 releases.
 */
class SugarUpgradeUpdateOOBReports extends UpgradeScript
{
    public $order = 4500;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // install the new Reports either when upgrading from pre-9.1.0 or on pro-to-ENT flavor conversion
        if ($this->shouldInstallReports()) {
            $this->installReports($this->getReportsToInstall());
            // For the 9.1 release, we don't want these, but we may in the future.
            //$this->newOOBReportsNotifications();
        } else {
            $this->log('Not installing new Serve Reports');
        }
    }

    /**
     * Determine if we should install any new Reports this upgrade.
     *
     * @return bool true if we should install new Reports this upgrade. false
     *   otherwise.
     */
    public function shouldInstallReports(): bool
    {
        $isFlavorConversion = !$this->fromFlavor('ent') && $this->toFlavor('ent');
        $isBelow910Ent = $this->toFlavor('ent') && version_compare($this->from_version, '9.1.0', '<');
        return $isFlavorConversion || $isBelow910Ent;
    }

    /**
     * Install the OOB Reports with the given names.
     *
     * @param array $reportNames List of report names to install.
     *   These should be actual Report names, not translatable labels.
     */
    public function installReports(array $reportNames)
    {
        $this->log('Temporarily enabling admin work for Report installation');
        $adminWork = new AdminWork();
        $adminWork->startAdminWork();
        require_once 'modules/Reports/SavedReport.php';
        require_once 'modules/Reports/SeedReports.php';
        $this->log('Installing new Serve Reports');
        create_default_reports(true, $reportNames);
    }

    /**
     * Get the Reports to install for this upgrade.
     *
     * @return array A list of Report names.
     */
    public function getReportsToInstall(): array
    {
        return [
            // 9.1 new Serve Reports
            'New Cases by Business Center by Week',
            'Recently Created Cases',
            'New Cases by Customer Tier by Week',
            'Open Cases by Customer Tier and Priority',
            'Total Cases Resolved this Month by Business Center',
            'Total Cases Resolved this Month by Agent',
            'List of Recently Resolved Cases',
            'My Cases Resolved this Month by Week',
            'My Cases Due Today and Overdue',
            'All Cases Due Today and Overdue',
            'My Open Cases by Followup Date',
            'All Open Cases by Followup Date',
            'My Open Cases by Status',
            'My Cases in the Last Week by Status',
            'Status of Open Tasks Assigned by Me',
        ];
    }

    /**
     * A notification is created to all users informing them new OOB reports are available.
     */
    public function newOOBReportsNotifications()
    {
        $app_strings = return_application_language($GLOBALS['current_language']);

        $reports_module_url = "<a href='index.php#Reports'>" .
            $app_strings['LBL_NEW_OOB_REPORTS_NOTIFICATION_DESC_2'] . "</a>";
        $link = "http://www.sugarcrm.com/crm/product_doc.php?edition={$GLOBALS['sugar_flavor']}" .
            "&version={$GLOBALS['sugar_version']}&lang=en_us&module=Notify&route=stockReports";
        $documentation_url = "<a href='{$link}'>" . $app_strings['LBL_NEW_OOB_REPORTS_NOTIFICATION_DESC_4'] . "</a>";
        $description = $app_strings['LBL_NEW_OOB_REPORTS_NOTIFICATION_DESC_1'] . $reports_module_url . ". " .
            $app_strings['LBL_NEW_OOB_REPORTS_NOTIFICATION_DESC_3'] . $documentation_url . ".";

        $result = $this->db->query("SELECT id FROM users where deleted = 0 AND status = 'Active'");
        while ($row = $this->db->fetchByAssoc($result)) {
            $notification = BeanFactory::newBean('Notifications');
            $notification->name = $app_strings['LBL_NEW_OOB_REPORTS_NOTIFICATION_SUBJECT'];
            $notification->description = $description;
            $notification->severity = 'info';
            $notification->assigned_user_id = $row['id'];
            $notification->save();
        }
    }
}
