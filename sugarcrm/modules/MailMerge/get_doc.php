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
/*
 * Created on Oct 4, 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$local_location = $_SESSION['mail_merge_file_location'];
$name = $_SESSION['mail_merge_file_name'];
$download_location= $_SESSION['mail_merge_file_location'];

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-type: application/force-download");
header("Content-Length: " . filesize($local_location));
		header("Content-disposition: attachment; filename=\"".$name."\";");

		header("Expires: 0");
		set_time_limit(0);

		@ob_end_clean();
		ob_start();

		
	        echo file_get_contents($download_location);
	   
		@ob_flush();
?>
