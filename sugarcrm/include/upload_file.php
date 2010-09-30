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
/*********************************************************************************
 * $Id: upload_file.php 55278 2010-03-15 13:45:13Z jmertic $
 * Description:
 ********************************************************************************/
require_once('modules/Documents/WebDocument.php');
require_once('modules/Documents/WebDocumentFactory.php');
require_once('modules/Documents/GoogleDocument.php');
require_once('modules/EAPM/EAPM.php');

class UploadFile 
{
	var $field_name;
	var $stored_file_name;
	var $original_file_name;
	var $temp_file_location;
	var $use_soap = false;
	var $file;
	var $file_ext;
	
	function UploadFile ($field_name) {
		// $field_name is the name of your passed file selector field in your form
		// i.e., for Emails, it is "email_attachmentX" where X is 0-9
		$this->field_name = $field_name;
        // Bug 28408 -  Add automatic creation of upload cache directory if it doesn't exist
		if ( !is_dir($GLOBALS['sugar_config']['upload_dir']) ) 
            create_cache_directory(str_replace($GLOBALS['sugar_config']['cache_dir'],'',$GLOBALS['sugar_config']['upload_dir']));
	}

	function set_for_soap($filename, $file) {
		$this->stored_file_name = $filename;
		$this->use_soap = true;
		$this->file = $file;
	}

	/**
	 * wrapper for this::get_file_path()
	 * @param string stored_file_name File name in filesystem
	 * @param string bean_id note bean ID
	 * @return string path with file name
	 */
	function get_url($stored_file_name,$bean_id) {
		global $sugar_config;
		return UploadFile::get_file_path($stored_file_name,$bean_id);
	}
	
	/**
	 * builds a URL path for an anchor tag 
	 * @param string stored_file_name File name in filesystem
	 * @param string bean_id note bean ID
	 * @return string path with file name
	 */
	function get_file_path($stored_file_name,$bean_id) {
		global $sugar_config;
		global $locale;
        
        // if the parameters are empty strings, just return back the upload_dir
		if ( empty($bean_id) && empty($stored_file_name) )
            return $sugar_config['upload_dir'];
            
		if (file_exists($sugar_config['upload_dir'] . $bean_id . rawurlencode($stored_file_name))){
			if (!rename($sugar_config['upload_dir'] . $bean_id . rawurlencode($stored_file_name),
				   $sugar_config['upload_dir'] . $bean_id)){
				$GLOBALS['log']->fatal("unable to rename file in {$sugar_config['upload_dir']}");
			}
		}
		else if (file_exists($sugar_config['upload_dir'] . $bean_id . urlencode($stored_file_name))){
			if (!rename($sugar_config['upload_dir'] . $bean_id . urlencode($stored_file_name),
				   $sugar_config['upload_dir'] . $bean_id)){
				$GLOBALS['log']->fatal("unable to rename file in {$sugar_config['upload_dir']}");
			}
		} 
		else if (file_exists($sugar_config['upload_dir'] . $bean_id . $stored_file_name)){
			if (!rename($sugar_config['upload_dir'] . $bean_id . $stored_file_name,
				   $sugar_config['upload_dir'] . $bean_id)){
				$GLOBALS['log']->fatal("unable to rename file in {$sugar_config['upload_dir']}");
			}
		}
		else if (file_exists($sugar_config['upload_dir'] . $bean_id . $locale->translateCharset( $stored_file_name, 'UTF-8', $locale->getExportCharset() ))){
			if (!rename($sugar_config['upload_dir'] . $bean_id . $locale->translateCharset( $stored_file_name, 'UTF-8', $locale->getExportCharset() ), 
						$sugar_config['upload_dir'] . $bean_id)){
				$GLOBALS['log']->fatal("unable to rename file in {$sugar_config['upload_dir']}");
			}
		}		
				
		return $sugar_config['upload_dir'] . $bean_id;
	}

	/**
	 * duplicates an already uploaded file in the filesystem.
	 * @param string old_id ID of original note
	 * @param string new_id ID of new (copied) note
	 * @param string filename Filename of file (deprecated)
	 */
	function duplicate_file($old_id, $new_id, $file_name) {
		global $sugar_config;

		// current file system (GUID)
		$source = $sugar_config['upload_dir'] . $old_id;
		
		if(!file_exists($source)) {
			// old-style file system (GUID.filename.extension)
			$oldStyleSource = $source.$file_name;
			if(file_exists($oldStyleSource)) {
				// change to new style
				if(copy($oldStyleSource, $source)) {
					// delete the old
					if(!unlink($oldStyleSource)) {
						$GLOBALS['log']->warn("upload_file could not unlink [ {$oldStyleSource} ]");
					}
				} else {
					$GLOBALS['log']->warn("upload_file could not copy [ {$oldStyleSource} ] to [ {$source} ]");
				}
			}
		}
		
		$destination = $sugar_config['upload_dir'] . $new_id;
		if(!copy($source, $destination)) {
			$GLOBALS['log']->warn("upload_file could not copy [ {$source} ] to [ {$destination} ]");
		}
	}

	/**
	 * standard PHP file-upload security measures. all variables accessed in a global context
	 * @return bool True on success
	 */
	function confirm_upload() {
		global $sugar_config;

		if(!is_uploaded_file($_FILES[$this->field_name]['tmp_name'])) {
			return false;
		} elseif($_FILES[$this->field_name]['size'] > $sugar_config['upload_maxsize']) {
			die("ERROR: uploaded file was too big: max filesize: {$sugar_config['upload_maxsize']}");
		}

		if(!is_writable($sugar_config['upload_dir'])) {
			die("ERROR: cannot write to directory: {$sugar_config['upload_dir']} for uploads");
		}

		$this->mime_type =$this->getMime($_FILES[$this->field_name]);
		$this->stored_file_name = $this->create_stored_filename();
		$this->temp_file_location = $_FILES[$this->field_name]['tmp_name'];

		return true;
	}

	function getMimeSoap($filename){

		if( function_exists( 'ext2mime' ) )
		{
			$mime = ext2mime($filename);
		}
		else
		{
			$mime = ' application/octet-stream';
		}
		return $mime;

	}
	function getMime(&$_FILES_element)
	{

		$filename = $_FILES_element['name'];

		if( $_FILES_element['type'] )
		{
			$mime = $_FILES_element['type'];
		}
		elseif( function_exists( 'mime_content_type' ) )
		{
			$mime = mime_content_type( $_FILES_element['tmp_name'] );
		}
		elseif( function_exists( 'ext2mime' ) )
		{
			$mime = ext2mime( $_FILES_element['name'] );
		}
		else
		{
			$mime = ' application/octet-stream';
		}
		return $mime;
	}

	/**
	 * gets note's filename
	 * @return string
	 */
	function get_stored_file_name() {
		return $this->stored_file_name;
	}

	/**
	 * creates a file's name for preparation for saving
	 * @return string
	 */
	function create_stored_filename() {
		global $sugar_config;
		
		if(!$this->use_soap) {
			$stored_file_name = $_FILES[$this->field_name]['name'];
			$this->original_file_name = $stored_file_name;
			
			/**
			 * cn: bug 8056 - windows filesystems and IIS do not like utf8.  we are forced to urlencode() to ensure that
			 * the file is linkable from the browser.  this will stay broken until we move to a db-storage system
			 */
			if(is_windows()) {
				// create a non UTF-8 name encoding
				// 176 + 36 char guid = windows' maximum filename length
				$end = (strlen($stored_file_name) > 176) ? 176 : strlen($stored_file_name);
				$stored_file_name = substr($stored_file_name, 0, $end);
				$this->original_file_name = $_FILES[$this->field_name]['name'];
			}
		} else {
			$stored_file_name = $this->stored_file_name;
			$this->original_file_name = $stored_file_name;
		}
		
        $ext_pos = strrpos($stored_file_name, ".");
        if($ext_pos !== false)
			$this->file_ext = substr($stored_file_name, $ext_pos + 1);
        // cn: bug 6347 - fix file extension detection 
        foreach($sugar_config['upload_badext'] as $badExt) {
            if(strtolower($this->file_ext) == strtolower($badExt)) {
                $stored_file_name .= ".txt";
                $this->file_ext="txt";
                break; // no need to look for more
            }
        }
		return $stored_file_name;
	}

	/**
	 * moves uploaded temp file to permanent save location
	 * @param string bean_id ID of parent bean
	 * @return bool True on success
	 */
	function final_move($bean_id) {
		global $sugar_config;

        $destination = clean_path($this->get_upload_path($bean_id));
        if($this->use_soap) {
        	$fp = sugar_fopen($destination, 'wb');
        	if(!fwrite($fp, $this->file)){
        		die("ERROR: can't save file to $destination");
        	}
        	fclose($fp);
		} else {
			if(!move_uploaded_file($_FILES[$this->field_name]['tmp_name'], $destination)) {
				die("ERROR: can't move_uploaded_file to $destination. You should try making the directory writable by the webserver");
			}
		}
		return true;
	}
	
	function upload_doc(&$bean, $bean_id, $doc_type, $file_name, $mime_type){
		$document_classname = WebDocumentFactory::getDocClass($doc_type);
		if($document_classname!='Sugar') {
			global $sugar_config;
	        $destination = clean_path($this->get_upload_path($bean_id));
	        sugar_rename($destination, str_replace($bean_id, $bean_id.'_'.$file_name, $destination));
	        $new_destination = clean_path($this->get_upload_path($bean_id.'_'.$file_name));
	        
	        $eapmType = WebDocumentFactory::getEapmType($doc_type);
	        $row = EAPM::getLoginInfo($eapmType);
            $url = $row['url'];
            if ($url[strlen($url)-1] == "/") {
      	      $url = substr($url, 0, -1);
            }
            
		    try{
                
                $this->document = WebDocumentFactory::getInstance(
                    $document_classname, 
                    $url, 
                    $row['name'],
                    $row['password']
                    );
                
                $doc_id = '';
                
                $doc_id = $this->document->uploadDoc(
                    $new_destination,
                    $file_name,
                    $mime_type
                    );
                $bean->doc_id = $doc_id;
                unlink($new_destination);
                $bean->save();
            }catch(Exception $e){
                sugar_rename($new_destination, str_replace($bean_id.'_'.$file_name, $bean_id, $new_destination));
                // FIXME: Translate
                $_SESSION['administrator_error'] = 'Error during plugin save: '.$e->getMessage();
                $GLOBALS['log']->fatal("Caught exception:   $e->getMessage() ");
            }
        }
	}

	/**
	 * returns the path with file name to save an uploaded file
	 * @param string bean_id ID of the parent bean
	 * @return string
	 */
	function get_upload_path($bean_id) {
		global $sugar_config;
		$file_name = $bean_id;
		
		// cn: bug 8056 - mbcs filename in urlencoding > 212 chars in Windows fails
		$end = (strlen($file_name) > 212) ? 212 : strlen($file_name);
		$ret_file_name = substr($file_name, 0, $end);
		
		return $sugar_config['upload_dir'].$ret_file_name;
	}

	/**
	 * deletes a file
	 * @param string bean_id ID of the parent bean
	 * @param string file_name File's name
	 */
	function unlink_file($bean_id,$file_name) {
		global $sugar_config;
        return unlink($sugar_config['upload_dir'].$bean_id.$file_name);
    }
}
?>
