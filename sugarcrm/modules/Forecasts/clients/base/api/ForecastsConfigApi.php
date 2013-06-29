<?php
/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (“MSA”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */

require_once('clients/base/api/ConfigModuleApi.php');

class ForecastsConfigApi extends ConfigModuleApi
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function registerApiRest()
    {
        return
            array(
                'forecastsConfigGet' => array(
                    'reqType' => 'GET',
                    'path' => array('Forecasts', 'config'),
                    'pathVars' => array('module', ''),
                    'method' => 'config',
                    'shortHelp' => 'Retrieves the config settings for a given module',
                    'longHelp' => 'include/api/help/config_get_help.html',
                ),
                'forecastsConfigCreate' => array(
                    'reqType' => 'POST',
                    'path' => array('Forecasts', 'config'),
                    'pathVars' => array('module', ''),
                    'method' => 'forecastsConfigSave',
                    'shortHelp' => 'Creates the config entries for the Forecasts module.',
                    'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastsConfigPut.html',
                ),
                'forecastsConfigUpdate' => array(
                    'reqType' => 'PUT',
                    'path' => array('Forecasts', 'config'),
                    'pathVars' => array('module', ''),
                    'method' => 'forecastsConfigSave',
                    'shortHelp' => 'Updates the config entries for the Forecasts module',
                    'longHelp' => 'modules/Forecasts/clients/base/api/help/ForecastsConfigPut.html',
                ),
            );
    }

    /**
     * Forecast Override since we have custom logic that needs to be ran
     *
     * {@inheritdoc}
     */
    public function forecastsConfigSave(ServiceBase $api, array $args)
    {
        //acl check, only allow if they are module admin
        if (!$api->user->isAdminForModule("Forecasts")) {
            // No create access so we construct an error message and throw the exception
            $failed_module_strings = return_module_language($GLOBALS['current_language'], 'forecasts');
            $moduleName = $failed_module_strings['LBL_MODULE_NAME'];
            $args = null;
            if (!empty($moduleName)) {
                $args = array('moduleName' => $moduleName);
            }
            throw new SugarApiExceptionNotAuthorized(
                $GLOBALS['app_strings']['EXCEPTION_CHANGE_MODULE_CONFIG_NOT_AUTHORIZED'],
                $args
            );
        }

        $admin = BeanFactory::getBean('Administration');
        //track what settings have changed to determine if timeperiods need rebuilt
        $prior_forecasts_settings = $admin->getConfigForModule('Forecasts', $api->platform);

        //If this is a first time setup, default prior settings for timeperiods to 0 so we may correctly recalculate
        //how many timeperiods to build forward and backward.  If we don't do this we would need the defaults to be 0
        if (empty($prior_forecasts_settings['is_setup'])) {
            $prior_forecasts_settings['timeperiod_shown_forward'] = 0;
            $prior_forecasts_settings['timeperiod_shown_backward'] = 0;
        }

        $upgraded = 0;
        if (!empty($prior_forecasts_settings['is_upgrade'])) {
            $db = DBManagerFactory::getInstance();
            // check if we need to upgrade opportunities when coming from version below 6.7.x.
            $upgraded = $db->getOne(
                "SELECT count(id) as total FROM upgrade_history
                    WHERE type = 'patch' AND status = 'installed' AND version LIKE '6.7.%';"
            );
            if ($upgraded == 1) {
                //TODO-sfa remove this once the ability to map buckets when they get changed is implemented (SFA-215).
                $args['has_commits'] = true;
            }
        }

        //BEGIN SUGARCRM flav=ent ONLY
        if (isset($args['show_custom_buckets_options'])) {
            $json = getJSONobj();
            $_args = array(
                'dropdown_lang' => isset($_SESSION['authenticated_user_language']) ?
                        $_SESSION['authenticated_user_language'] : $GLOBALS['current_language'],
                'dropdown_name' => 'commit_stage_custom_dom',
                'view_package' => 'studio',
                'list_value' => $json->encode($args['show_custom_buckets_options'])
            );
            $_REQUEST['view_package'] = 'studio';
            require_once 'modules/ModuleBuilder/parsers/parser.dropdown.php';
            $parser = new ParserDropDown();
            $parser->saveDropDown($_args);
            unset($args['show_custom_buckets_options']);
        }
        //END SUGARCRM flav=ent ONLY

        if ($upgraded || empty($prior_forecasts_settings['is_setup'])) {
            require_once('modules/UpgradeWizard/uw_utils.php');
            updateOpportunitiesForForecasting();
        }

        //reload the settings to get the current settings
        $current_forecasts_settings = parent::configSave($api, $args);

        //if primary settings for timeperiods have changed, then rebuild them
        if ($this->timePeriodSettingsChanged($prior_forecasts_settings, $current_forecasts_settings)) {
            $timePeriod = TimePeriod::getByType($current_forecasts_settings['timeperiod_interval']);
            $timePeriod->rebuildForecastingTimePeriods($prior_forecasts_settings, $current_forecasts_settings);
        }
        return $current_forecasts_settings;
    }

    /**
     * Compares two sets of forecasting settings to see if the primary timeperiods settings are the same
     *
     * @param array $priorSettings              The Prior Settings
     * @param array $currentSettings            The New Settings Coming from the Save
     *
     * @return boolean
     */
    public function timePeriodSettingsChanged($priorSettings, $currentSettings)
    {
        if (!isset($priorSettings['timeperiod_shown_backward']) ||
            (isset($currentSettings['timeperiod_shown_backward']) &&
                ($currentSettings['timeperiod_shown_backward'] != $priorSettings['timeperiod_shown_backward'])
            )
        ) {
            return true;
        }
        if (!isset($priorSettings['timeperiod_shown_forward']) ||
            (isset($currentSettings['timeperiod_shown_forward']) &&
                ($currentSettings['timeperiod_shown_forward'] != $priorSettings['timeperiod_shown_forward'])
            )
        ) {
            return true;
        }
        if (!isset($priorSettings['timeperiod_interval']) ||
            (isset($currentSettings['timeperiod_interval']) &&
                ($currentSettings['timeperiod_interval'] != $priorSettings['timeperiod_interval'])
            )
        ) {
            return true;
        }
        if (!isset($priorSettings['timeperiod_type']) ||
            (isset($currentSettings['timeperiod_type']) &&
                ($currentSettings['timeperiod_type'] != $priorSettings['timeperiod_type'])
            )
        ) {
            return true;
        }
        if (!isset($priorSettings['timeperiod_start_date']) ||
            (isset($currentSettings['timeperiod_start_date']) &&
                ($currentSettings['timeperiod_start_date'] != $priorSettings['timeperiod_start_date'])
            )
        ) {
            return true;
        }
        if (!isset($priorSettings['timeperiod_leaf_interval']) ||
            (isset($currentSettings['timeperiod_leaf_interval']) &&
                ($currentSettings['timeperiod_leaf_interval'] != $priorSettings['timeperiod_leaf_interval'])
            )
        ) {
            return true;
        }

        return false;
    }
}
