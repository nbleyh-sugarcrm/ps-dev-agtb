<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once('include/MVC/View/SugarView.php');
require_once('include/connectors/sources/SourceFactory.php');

class ViewDisplayProperties extends SugarView
{
 	/**
	 * @see SugarView::_getModuleTitleParams()
	 */
	protected function _getModuleTitleParams()
	{
	    global $mod_strings;
        $iconPath = $this->getModuleTitleIconPath($this->module);
    	return array(
           "<a href='index.php?module=Administration&action=index'>".translate('LBL_MODULE_NAME','Administration')."</a>",
    	   $mod_strings['LBL_MODULE_TITLE']
    	   );
    }

    /**
	 * @see SugarView::display()
	 */
	public function display()
	{
		global $mod_strings, $app_strings;
        
        $apiList = ExternalAPIFactory::getModuleDropDown('',true, false);
        $enabledModules = array();
        foreach($apiList as $key=>$value){
            $enabledModules[] = array('name' => $key, 'label' => $value);
        }

        $disabledModules = array();
         if (file_exists(ExternalAPIFactory::$disabledApiFileName)) {
            require(ExternalAPIFactory::$disabledApiFileName);
            foreach($disabledAPIList as $disabledAPI){
                 $disabledModules[] = array('name' => $disabledAPI, 'label' => $disabledAPI);
            }
         }
        echo $this->getModuleTitle();
        $this->ss->assign('enabled_modules', json_encode($enabledModules));
        $this->ss->assign('disabled_modules', json_encode($disabledModules));
		$this->ss->assign('mod', $mod_strings);
		$this->ss->assign('APP', $app_strings);
		$this->ss->display('modules/EAPM/tpls/display_properties.tpl');
    }
}