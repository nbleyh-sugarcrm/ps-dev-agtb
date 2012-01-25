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
 * $Id: additionalDetails.php 13782 2006-06-06 17:58:55Z majed $
 *********************************************************************************/

 require_once ('modules/ModuleBuilder/parsers/views/ListLayoutMetaDataParser.php') ;
 require_once ('modules/ModuleBuilder/parsers/views/SearchViewMetaDataParser.php') ;
 require_once 'modules/ModuleBuilder/parsers/constants.php' ;

 class PopupMetaDataParser extends ListLayoutMetaDataParser
 {

 	// Columns is used by the view to construct the listview - each column is built by calling the named function
 	public $columns = array ( 'LBL_DEFAULT' => 'getDefaultFields' , 'LBL_AVAILABLE' => 'getAdditionalFields' , 'LBL_HIDDEN' => 'getAvailableFields' ) ;
 	
 	public static $reserveProperties = array('moduleMain', 'varName' , 'orderBy', 'whereClauses', 'searchInputs', 'create');
 	
 	public static $defsMap = array(MB_POPUPSEARCH => 'searchdefs' , MB_POPUPLIST => 'listviewdefs');

 	/*
 	 * Constructor
 	 * Must set:
 	 * $this->columns   Array of 'Column LBL'=>function_to_retrieve_fields_for_this_column() - expected by the view
 	 *
 	 * @param string moduleName     The name of the module to which this listview belongs
 	 * @param string packageName    If not empty, the name of the package to which this listview belongs
 	 */
 	 function __construct ($view, $moduleName , $packageName = '')
 	 {
 	 	$this->search = ($view == MB_POPUPSEARCH) ? true : false;
 	 	$this->_moduleName = $moduleName;
 	 	$this->_packageName = $packageName;
 	 	$this->_view = $view ;
 	 	$this->columns = array ( 'LBL_DEFAULT' => 'getDefaultFields' , 'LBL_HIDDEN' => 'getAvailableFields' ) ;
 	 	
		if ($this->search)
 	 	{
 	 		$this->columns = array ( 'LBL_DEFAULT' => 'getSearchFields' , 'LBL_HIDDEN' => 'getAvailableFields' ) ;
 	 		parent::__construct ( MB_POPUPSEARCH, $moduleName, $packageName ) ;
 	 	} else
 	 	{
 	 		parent::__construct ( MB_POPUPLIST, $moduleName, $packageName ) ;
 	 	}
 	 	
 	 	$this->_viewdefs = $this->mergeFieldDefinitions($this->_viewdefs, $this->_fielddefs);
 	 }

 	 /**
 	  * Dashlets contain both a searchview and list view definition, therefore we need to merge only the relevant info
 	  */
    function mergeFieldDefinitions ( $viewdefs, $fielddefs ) {
        $viewdefs = $this->_viewdefs = array_change_key_case($viewdefs );
		$viewdefs = $this->_viewdefs = $this->convertSearchToListDefs($viewdefs);
    	return $viewdefs;
    }
	
    function convertSearchToListDefs($defs) {
    	$temp = array();
    	foreach($defs as $key=>$value) {
    			if(!is_array($value)){
    				$temp[$value] = array('name'=>$value);
    			}else{
    				$temp[$key] = $value;
    				if(isset($value['name']) && $value['name'] != $key){
    					$temp[$value['name']] = $value;
    					unset($temp[$key] );
    				}else if( !isset($value['name']) ){
    					$temp[$key]['name'] = $key;
    				}
    			}
    	}
    	return $temp;
    }
	
	function getOriginalViewDefs(){
		$defs = parent::getOriginalViewDefs();
		return $this->convertSearchToListDefs($defs);
	}
	
	public function getSearchFields()
	{
		$searchFields = array ( ) ;
        foreach ( $this->_viewdefs as $key => $def )
        {
            if (isset($this->_fielddefs [ $key ] )) {
				$searchFields [ $key ] = self::_trimFieldDefs ( $this->_fielddefs [ $key ] ) ;
				if (!empty($def['label']))
				   $searchFields [ $key ]['label'] = $def['label'];
            }
			else {
				$searchFields [ $key ] = $def;
			}
        }

        return $searchFields ;
	}

    function handleSave ($populate = true)
   {
    	if (empty (  $this->_packageName ))
        {
        	foreach(array(MB_CUSTOMMETADATALOCATION , MB_BASEMETADATALOCATION) as $value){
        		$file = $this->implementation->getFileName(MB_POPUPLIST, $this->_moduleName, $value);
        		if(file_exists($file)){
	        		break;
	        	}
        	}
        	$writeFile = $this->implementation->getFileName(MB_POPUPLIST, $this->_moduleName);
        	if(!file_exists($writeFile)){
        		mkdir_recursive ( dirname ( $writeFile ) ) ;
    		}
    	}
    	else{
    		$writeFile = $file = $this->implementation->getFileName(MB_POPUPLIST, $this->_moduleName, $this->_packageName);
    	}
    	$this->implementation->_history->append ( $file ) ;
    	if ($populate)
    	   $this->_populateFromRequest() ;
    	$out = "<?php\n" ;

		//Load current module languages
		global $mod_strings , $current_language;
		$oldModStrings = $mod_strings;
		$GLOBALS['mod_strings'] = return_module_language($current_language , $this->_moduleName);
    	require($file);
    	if (!isset($popupMeta)) {
    		sugar_die ("unable to load Module Popup Definition");
    	}
    	
    	if ($this->_view == MB_POPUPSEARCH)
    	{
    		foreach($this->_viewdefs as $k => $v){
    			if(isset($this->_viewdefs[$k]) && isset($this->_viewdefs[$k]['default'])){
    				unset($this->_viewdefs[$k]['default']);
    			}
    		}
    		$this->_viewdefs = $this->convertSearchToListDefs($this->_viewdefs);
    		$popupMeta['searchdefs'] = $this->_viewdefs;
    		$this->addNewSearchDef($this->_viewdefs , $popupMeta);
    	} else
    	{
    		$popupMeta['listviewdefs'] = array_change_key_case($this->_viewdefs , CASE_UPPER );
    	}
    	$allProperties = array_merge(self::$reserveProperties , array('searchdefs', 'listviewdefs'));
    	
    	$out .= "\$popupMeta = array (\n";
    	foreach( $allProperties as $p){
    		if(isset($popupMeta[$p])){
    			$out .= "    '$p' => ". var_export_helper ($popupMeta[$p]) . ",\n";
    		}
    	}
    	$out .= ");\n";
    	sugar_file_put_contents($writeFile, $out);
    	
    	//return back mod strings
    	$GLOBALS['mod_strings'] = $oldModStrings;
    }
    
    public function addNewSearchDef($searchDefs, &$popupMeta){
    	if(!empty($searchDefs)){
			$this->__diffAndUpdate( $searchDefs , $popupMeta['whereClauses'] , true);
			$this->__diffAndUpdate( $searchDefs , $popupMeta['searchInputs'] );
    	}
    }
    
    private function __diffAndUpdate($newDefs , &$targetDefs , $forWhere = false){
    	if(!is_array($targetDefs)){
    		$targetDefs = array();
    	}
    	foreach($newDefs as $key =>$def){
    		if(!isset($targetDefs[$key]) && $forWhere){
    			$targetDefs[$key] = $this->__getTargetModuleName($def).'.'.$key;
    		}else if( !in_array($key , $targetDefs ) && !$forWhere){
				array_push($targetDefs , $key);
    		}
    	}
    	
    	if($forWhere){
    		foreach(array_diff(  array_keys($targetDefs) , array_keys($newDefs) ) as $key ){
	    		unset($targetDefs[$key]);
	    	}
    	}else{
    		foreach($targetDefs as $key =>$value){
    			if(!isset($newDefs[$value])) 
	    			unset($targetDefs[$key]);
	    	}
    	}
    	
    }
    
    private function __getTargetModuleName($def){
    	$dir = strtolower($this->implementation->getModuleDir());
    	if(isset($this->_fielddefs[$def['name']]) && isset($this->_fielddefs[$def['name']]['source']) && $this->_fielddefs[$def['name']]['source'] == 'custom_fields'){
    		return $dir.'_cstm';
    	}
    	
    	return $dir;
    }
    
 }
 ?>
