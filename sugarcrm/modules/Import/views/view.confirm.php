<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-enterprise-eula.html
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
 * by SugarCRM are Copyright (C) 2004-2007 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: view.step1.php 31561 2008-02-04 18:41:10Z jmertic $
 * Description: view handler for step 1 of the import process
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/
require_once('modules/Import/views/ImportView.php');
require_once('modules/Import/sources/ImportFile.php');
require_once('modules/Import/ImportFileSplitter.php');
require_once('modules/Import/CsvAutoDetect.php');

require_once('include/upload_file.php');

class ImportViewConfirm extends ImportView
{
    const SAMPLE_ROW_SIZE = 3;
 	protected $pageTitleKey = 'LBL_CONFIRM_TITLE';
    
 	/** 
     * @see SugarView::display()
     */
 	public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        global $sugar_config, $locale;

        //echo "<pre>";print_r($_REQUEST);die();
        $this->ss->assign("IMPORT_MODULE", $_REQUEST['import_module']);
        $this->ss->assign("TYPE",( !empty($_REQUEST['type']) ? $_REQUEST['type'] : "import" ));
        $this->ss->assign("SOURCE_ID", $_REQUEST['source_id']);
        $this->ss->assign("MODULE_TITLE", $this->getModuleTitle());
        $this->ss->assign("CURRENT_STEP", $this->currentStep);
        $sugar_config['import_max_records_per_file'] = ( empty($sugar_config['import_max_records_per_file']) ? 1000 : $sugar_config['import_max_records_per_file'] );
        $importSource = isset($_REQUEST['source']) ? $_REQUEST['source'] : 'csv' ;

        // Clear out this user's last import
        $seedUsersLastImport = new UsersLastImport();
        $seedUsersLastImport->mark_deleted_by_user_id($current_user->id);
        ImportCacheFiles::clearCacheFiles();

        // handle uploaded file
        $uploadFile = new UploadFile('userfile');
        if (isset($_FILES['userfile']) && $uploadFile->confirm_upload())
        {
            $uploadFile->final_move('IMPORT_'.$this->bean->object_name.'_'.$current_user->id);
            $uploadFileName = $uploadFile->get_upload_path('IMPORT_'.$this->bean->object_name.'_'.$current_user->id);
        }
        elseif( !empty($_REQUEST['tmp_file']) )
        {
            $uploadFileName = $_REQUEST['tmp_file'];
        }
        else
        {
            $this->_showImportError($mod_strings['LBL_IMPORT_MODULE_ERROR_NO_UPLOAD'],$_REQUEST['import_module'],'Step2');
            return;
        }

        $this->ss->assign("FILE_NAME", $uploadFileName);

        // Now parse the file and look for errors
        $importFile = new ImportFile( $uploadFileName, $_REQUEST['custom_delimiter'], html_entity_decode($_REQUEST['custom_enclosure'],ENT_QUOTES), FALSE);

        if( $this->shouldAutoDetectProperties($importSource) )
        {
            $GLOBALS['log']->fatal("Auto detecing csv properties...");
            $autoDetectOk = $importFile->autoDetectCSVProperties();
            $importFileMap = array();
            $this->ss->assign("SOURCE", 'csv');
            if($autoDetectOk === FALSE)
            {
                $this->ss->assign("AUTO_DETECT_ERROR",  $mod_strings['LBL_AUTO_DETECT_ERROR']);
            }
            else
            {
                $dateFormat = $importFile->getDateFormat();
                $timeFormat = $importFile->getTimeFormat();
                if ($dateFormat) {
                    $importFileMap['importlocale_dateformat'] = $dateFormat;
                }
                if ($timeFormat) {
                    $importFileMap['importlocale_timeformat'] = $timeFormat;
                }
            }
        }
        else
        {
            $impotMapSeed = $this->getImportMap($importSource);
            $importFile->setImportFileMap($impotMapSeed);
            $importFileMap = $impotMapSeed->getMapping();
        }
        
        $delimeter = $importFile->getFieldDelimeter();
        $enclosure = $importFile->getFieldEnclosure();
        $hasHeader = $importFile->hasHeaderRow();

        $this->ss->assign("IMPORT_ENCLOSURE_OPTIONS",  get_select_options_with_id( $GLOBALS['app_list_strings']['import_enclosure_options'], $enclosure));
        $this->ss->assign("CUSTOM_DELIMITER",  $delimeter);
        $this->ss->assign("CUSTOM_ENCLOSURE",  htmlentities($enclosure));
        $hasHeaderFlag = $hasHeader ? " CHECKED" : "";
        $this->ss->assign("HAS_HEADER_CHECKED", $hasHeaderFlag);

        if ( !$importFile->fileExists() ) {
            $this->_showImportError($mod_strings['LBL_CANNOT_OPEN'],$_REQUEST['import_module'],'Step2');
            return;
        }

         //Check if we will exceed the maximum number of records allowed per import.
         $maxRecordsExceeded = FALSE;
         $maxRecordsWarningMessg = "";
         $lineCount = $importFile->getNumberOfLinesInfile();
         $maxLineCount = isset($sugar_config['import_max_records_total_limit'] ) ? $sugar_config['import_max_records_total_limit'] : 5000;
         if( !empty($maxLineCount) && ($lineCount > $maxLineCount) )
         {
             $maxRecordsExceeded = TRUE;
             $maxRecordsWarningMessg = string_format($mod_strings['LBL_IMPORT_ERROR_MAX_REC_LIMIT_REACHED'], array($lineCount, $maxLineCount) );
         }

        //Retrieve a sample set of data
        $rows = $this->getSampleSet($importFile);


        $this->ss->assign('getNumberJs', $locale->getNumberJs());
        $this->setImportFileCharacterSet($importFile);
        $this->setDateTimeProperties($importFileMap);
        $this->setCurrencyOptions($importFileMap);
        $this->setNumberFormatOptions($importFileMap);
        $this->setNameFormatProperties($importFileMap);
        
        $importMappingJS = $this->getImportMappingJS();
        
        $this->ss->assign("SAMPLE_ROWS",$rows);
        $this->ss->assign("JAVASCRIPT", $this->_getJS($maxRecordsExceeded, $maxRecordsWarningMessg, $importMappingJS ));
        $this->ss->display('modules/Import/tpls/confirm.tpl');
    }

    private function shouldAutoDetectProperties($importSource)
    {
        if(empty($importSource) || $importSource == 'csv' )
            return TRUE;
        else
            return FALSE;
    }

    private function getImportMap($importSource)
    {
        if ( strncasecmp("custom:",$importSource,7) == 0)
        {
            $id = substr($importSource,7);
            $import_map_seed = new ImportMap();
            $import_map_seed->retrieve($id, false);

            $this->ss->assign("SOURCE_ID", $import_map_seed->id);
            $this->ss->assign("SOURCE_NAME", $import_map_seed->name);
            $this->ss->assign("SOURCE", $import_map_seed->source);
        }
        else
        {
            $classname = 'ImportMap' . ucfirst($importSource);
            if ( file_exists("modules/Import/maps/{$classname}.php") )
                require_once("modules/Import/maps/{$classname}.php");
            elseif ( file_exists("custom/modules/Import/maps/{$classname}.php") )
                require_once("custom/modules/Import/maps/{$classname}.php");
            else
            {
                require_once("custom/modules/Import/maps/ImportMapOther.php");
                $classname = 'ImportMapOther';
                $importSource = 'other';
            }
            if ( class_exists($classname) )
            {
                $import_map_seed = new $classname;
                $this->ss->assign("SOURCE", $importSource);
            }
        }

        return $import_map_seed;
    }

    private function setNameFormatProperties($field_map = array())
    {
        global $locale, $current_user;
        
        $localized_name_format = isset($field_map['importlocale_default_locale_name_format'])? $field_map['importlocale_default_locale_name_format'] : $locale->getLocaleFormatMacro($current_user);
        $this->ss->assign('default_locale_name_format', $localized_name_format);
        $this->ss->assign('getNameJs', $locale->getNameJs());

    }

    private function setNumberFormatOptions($field_map = array())
    {
        global $locale, $current_user, $sugar_config;

        $num_grp_sep = isset($field_map['importlocale_num_grp_sep'])? $field_map['importlocale_num_grp_sep'] : $current_user->getPreference('num_grp_sep');
        $dec_sep = isset($field_map['importlocale_dec_sep'])? $field_map['importlocale_dec_sep'] : $current_user->getPreference('dec_sep');

        $this->ss->assign("NUM_GRP_SEP",( empty($num_grp_sep) ? $sugar_config['default_number_grouping_seperator'] : $num_grp_sep ));
        $this->ss->assign("DEC_SEP",( empty($dec_sep)? $sugar_config['default_decimal_seperator'] : $dec_sep ));


        $significantDigits = isset($field_map['importlocale_default_currency_significant_digits']) ? $field_map['importlocale_default_currency_significant_digits']
                                :  $locale->getPrecedentPreference('default_currency_significant_digits', $current_user);

        $sigDigits = '';
        for($i=0; $i<=6; $i++)
        {
            if($significantDigits == $i)
            {
                $sigDigits .= '<option value="'.$i.'" selected="true">'.$i.'</option>';
            } else
            {
                $sigDigits .= '<option value="'.$i.'">'.$i.'</option>';
            }
        }

        $this->ss->assign('sigDigits', $sigDigits);
    }


    private function setCurrencyOptions($field_map = array() )
    {
        global $locale, $current_user;
        $cur_id = isset($field_map['importlocale_currency'])? $field_map['importlocale_currency'] : $locale->getPrecedentPreference('currency', $current_user);
        // get currency preference
        require_once('modules/Currencies/ListCurrency.php');
        $currency = new ListCurrency();
        if($cur_id)
            $selectCurrency = $currency->getSelectOptions($cur_id);
        else
            $selectCurrency = $currency->getSelectOptions();

        $this->ss->assign("CURRENCY", $selectCurrency);

        $currenciesVars = "";
        $i=0;
        foreach($locale->currencies as $id => $arrVal)
        {
            $currenciesVars .= "currencies[{$i}] = '{$arrVal['symbol']}';\n";
            $i++;
        }
        $currencySymbolsJs = <<<eoq
var currencies = new Object;
{$currenciesVars}
function setSymbolValue(id) {
    document.getElementById('symbol').value = currencies[id];
}
eoq;
        $this->ss->assign('currencySymbolJs', $currencySymbolsJs);

    }


    private function setDateTimeProperties( $field_map = array() )
    {
        global $current_user, $sugar_config;

        $time_strings = CsvAutoDetect::getTimeStrings();
        $timeFormat = $current_user->getUserDateTimePreferences();
        $defaultTimeOption = isset($field_map['importlocale_timeformat'])? $field_map['importlocale_timeformat'] : $timeFormat['time'];
        $defaultDateOption = isset($field_map['importlocale_dateformat'])? $field_map['importlocale_dateformat'] : $timeFormat['date'];

        $timeOptions = get_select_options_with_id($time_strings, $defaultTimeOption);
        $dateOptions = get_select_options_with_id($sugar_config['date_formats'], $defaultDateOption);

        // get list of valid timezones
        $userTZ = isset($field_map['importlocale_timezone'])? $field_map['importlocale_timezone'] : $current_user->getPreference('timezone');
        if(empty($userTZ))
            $userTZ = TimeDate::userTimezone();

        $this->ss->assign('TIMEZONE_CURRENT', $userTZ);
        $this->ss->assign('TIMEOPTIONS', $timeOptions);
        $this->ss->assign('DATEOPTIONS', $dateOptions);
        $this->ss->assign('TIMEZONEOPTIONS', TimeDate::getTimezoneList());
    }

    private function setImportFileCharacterSet($importFile)
    {
        global $locale;
        $charset_for_import = $importFile->autoDetectCharacterSet();
        $charsetOptions = get_select_options_with_id( $locale->getCharsetSelect(), $charset_for_import);//wdong,  bug 25927, here we should use the charset testing results from above.
        $this->ss->assign('CHARSETOPTIONS', $charsetOptions);
    }

    protected function getImportMappingJS()
    {
        $results = array();
        $importMappings = array('ImportMapSalesforce', 'ImportMapOutlook');
        foreach($importMappings as $importMap)
        {
            $tmpFile = "modules/Import/$importMap.php";
            if( file_exists($tmpFile) )
            {
                require_once($tmpFile);
                $t = new $importMap();
                $results[$t->name] = array('delim' => $t->delimiter, 'enclos' => $t->enclosure, 'has_header' => $t->has_header);
            }
        }
        return $results;
    }


    public function getSampleSet($importFile)
    {
        $rows = array();
        for($i=0; $i < self::SAMPLE_ROW_SIZE; $i++)
        {
            $rows[] = $importFile->getNextRow();
        }

        if( ! $importFile->hasHeaderRow(FALSE) )
        {
            array_unshift($rows, array_fill(0, count($rows[0]),'') );
        }

        return $rows;
    }

    /**
     * Returns JS used in this view
     */
    private function _getJS($maxRecordsExceeded, $maxRecordsWarningMessg, $importMappingJS)
    {
        global $mod_strings;
        $maxRecordsExceededJS = $maxRecordsExceeded?"true":"false";
        $importMappingJS = json_encode($importMappingJS);
        return <<<EOJAVASCRIPT
<script type="text/javascript">

var import_mapping_js = $importMappingJS;
document.getElementById('goback').onclick = function(){
    document.getElementById('importconfirm').action.value = 'Step2';
    return true;
}

document.getElementById('gonext').onclick = function(){
    document.getElementById('importconfirm').action.value = 'Step3';
    return true;
}

document.getElementById('custom_enclosure').onchange = function()
{
    document.getElementById('importconfirm').custom_enclosure_other.style.display = ( this.value == 'other' ? '' : 'none' );
}

document.getElementById('toggleImportOptions').onclick = function() {
    if (document.getElementById('importOptions').style.display == 'none'){
        document.getElementById('importOptions').style.display = '';
        document.getElementById('toggleImportOptions').value='  {$mod_strings['LBL_HIDE_ADVANCED_OPTIONS']}  ';
        document.getElementById('toggleImportOptions').title='{$mod_strings['LBL_HIDE_ADVANCED_OPTIONS']}';
    }
    else {
        document.getElementById('importOptions').style.display = 'none';
        document.getElementById('toggleImportOptions').value='  {$mod_strings['LBL_SHOW_ADVANCED_OPTIONS']}  ';
        document.getElementById('toggleImportOptions').title='{$mod_strings['LBL_SHOW_ADVANCED_OPTIONS']}';
    }
}

YAHOO.util.Event.onDOMReady(function(){
    if($maxRecordsExceededJS)
    {
        var contImport = confirm('$maxRecordsWarningMessg');
        if(!contImport)
        {
            var module = document.getElementById('importconfirm').import_module.value;
            var source = document.getElementById('importconfirm').source.value;
            var returnUrl = "index.php?module=Import&action=Step2&import_module=" + module + "&source=" + source;
            document.location.href = returnUrl;
        }
    }

    function refreshDataTable(e)
    {
        var callback = {
          success: function(o) {
            document.getElementById('confirm_table').innerHTML = o.responseText;
          },
          failure: function(o) {},
        };

        var importFile = document.getElementById('importconfirm').file_name.value;
        var fieldDelimeter = document.getElementById('custom_delimiter').value;
        var fieldQualifier = document.getElementById('custom_enclosure').value;
        var hasHeader = document.getElementById('importconfirm').has_header.checked ? 'true' : '';

        if(fieldQualifier == 'other' && this.id == 'custom_enclosure')
        {
            return;
        }
        else if( fieldQualifier == 'other' )
        {
            fieldQualifier = document.getElementById('custom_enclosure_other').value;
        }

        var url = 'index.php?action=RefreshMapping&module=Import&importFile=' + importFile
                    + '&delim=' + fieldDelimeter + '&qualif=' + fieldQualifier + "&header=" + hasHeader;

        YAHOO.util.Connect.asyncRequest('GET', url, callback);
    }
    var subscribers = ["custom_delimiter", "custom_enclosure", "custom_enclosure_other", "has_header", "importlocale_charset"];
    YAHOO.util.Event.addListener(subscribers, "change", refreshDataTable);

    function setMappingProperties(el)
    {
       var sourceEl = document.getElementById('source');
       if(sourceEl.value != '' && sourceEl.value != 'csv' && sourceEl.value != 'salesforce' && sourceEl.value != 'outlook')
       {
           if( !confirm(SUGAR.language.get('Import','LBL_CONFIRM_MAP_OVERRIDE')) )
           {
                deSelectExternalSources();
                return;
           }
        }
        var selectedMap = this.value;
        if( typeof(import_mapping_js[selectedMap]) == 'undefined')
            return;

        sourceEl.value = selectedMap;
        document.getElementById('custom_delimiter').value = import_mapping_js[selectedMap].delim;
        document.getElementById('custom_enclosure').value = import_mapping_js[selectedMap].enclos;
        document.getElementById('has_header').checked = import_mapping_js[selectedMap].has_header;
        
        refreshDataTable();
    }

    function deSelectExternalSources()
    {
        var els = document.getElementsByName('external_source');
        for(i=0;i<els.length;i++)
        {
            els[i].checked = false;
        }
    }
    YAHOO.util.Event.addListener(['sf_map', 'outlook_map'], "click", setMappingProperties);
});
</script>

EOJAVASCRIPT;
    }

    /**
     * Displays the Smarty template for an error
     *
     * @param string $message error message to show
     * @param string $module what module we were importing into
     * @param string $action what page we should go back to
     */
    protected function _showImportError(
        $message,
        $module,
        $action = 'Step1'
        )
    {
        $ss = new Sugar_Smarty();

        $ss->assign("MESSAGE",$message);
        $ss->assign("ACTION",$action);
        $ss->assign("IMPORT_MODULE",$module);
        $ss->assign("MOD", $GLOBALS['mod_strings']);
        $ss->assign("SOURCE","");
        if ( isset($_REQUEST['source']) )
            $ss->assign("SOURCE", $_REQUEST['source']);

        echo $ss->fetch('modules/Import/tpls/error.tpl');
    }
}

?>
