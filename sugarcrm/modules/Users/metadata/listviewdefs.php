<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
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
 * by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights Reserved.
 */

 // $Id: listviewdefs.php 16292 2006-08-22 20:57:23 +0000 (Tue, 22 Aug 2006) awu $

$listViewDefs['Users'] = array(
    'NAME' => array(
        'width' => '30', 
        'label' => 'LBL_LIST_NAME', 
        'link' => true,
        'related_fields' => array('last_name', 'first_name'),
        'orderBy' => 'last_name',
        'default' => true),
    'USER_NAME' => array(
        'width' => '5', 
        'label' => 'LBL_USER_NAME', 
        'link' => true,
        'default' => true),
    'TITLE' => array(
        'width' => '15', 
        'label' => 'LBL_TITLE', 
        'link' => true,
        'default' => true),        
    'DEPARTMENT' => array(
        'width' => '15', 
        'label' => 'LBL_DEPARTMENT', 
        'link' => true,
        'default' => true),
    'EMAIL1' => array(
        'width' => '30',
        'sortable' => false, 
        'label' => 'LBL_LIST_EMAIL', 
        'link' => true,
        'default' => true),
    'PHONE_WORK' => array(
        'width' => '25', 
        'label' => 'LBL_LIST_PHONE', 
        'link' => true,
        'default' => true),
    'STATUS' => array(
        'width' => '10', 
        'label' => 'LBL_STATUS', 
        'link' => false,
        'default' => true),
    'IS_ADMIN' => array(
        'width' => '10', 
        'label' => 'LBL_ADMIN', 
        'link' => false,
        'default' => true),
    'IS_GROUP' => array(
        'width' => '10', 
        'label' => 'LBL_LIST_GROUP', 
        'link' => true,
        'default' => false),
);
?>
