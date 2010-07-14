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

 // $Id: MyContactsDashlet.data.php 56115 2010-04-26 17:08:09Z kjing $

global $current_user;
$dashletData['MyContactsDashlet']['searchFields'] = array('date_entered'     => array('default' => ''),
														  'title'			 => array('default' => ''),
														  'primary_address_country'  => array('default' => ''),
                                                          //BEGIN SUGARCRM flav=pro ONLY
                                                          'team_id'          => array('default' => '', 'label'=>'LBL_TEAMS'),
                                                          //END SUGARCRM flav=pro ONLY
                                                          'assigned_user_id' => array('type'    => 'assigned_user_name', 
                                                                                      'default' => $current_user->name,
																					  'label' => 'LBL_ASSIGNED_TO')); 
$dashletData['MyContactsDashlet']['columns'] = array('name' => array('width'   => '30', 
                                                                     'label'   => 'LBL_NAME',
                                                                     'link'    => true,
                                                                     'default' => true,
                                                                     'related_fields' => array('first_name', 'last_name', 'salutation')),
                                                     'account_name' => array('width' => '20',
                                                                             'label' => 'LBL_ACCOUNT_NAME',
                                                                             'sortable' => false,
                                                                             'link' => true,
                                                                             'module' => 'Accounts',
                                                                             'id' => 'ACCOUNT_ID',
                                                                             'ACLTag' => 'ACCOUNT'),
                                                     'title' => array('width' => '20s',
                                                                      'label' => 'LBL_TITLE',
																	  'default' => true),
                                                     'email1' => array('width' => '10',
                                                                    'label' => 'LBL_EMAIL_ADDRESS',
                                                                    'sortable' => false,
                                                                    'customCode' => '{$EMAIL1_LINK}{$EMAIL1}</a>',),
                                                     'phone_work' => array('width'   => '15',
                                                                           'label'   => 'LBL_OFFICE_PHONE',
                                                                           'default' => true),
                                                     'phone_home' => array('width' => '10',
                                                                           'label' => 'LBL_HOME_PHONE'),
                                                     'phone_mobile' => array('width' => '10',
                                                                             'label' => 'LBL_MOBILE_PHONE'),
                                                     'phone_other' => array('width' => '10',
                                                                            'label' => 'LBL_OTHER_PHONE'),
                                                     'date_entered' => array('width'   => '15', 
                                                                             'label'   => 'LBL_DATE_ENTERED',
                                                                             'default' => true),
                                                     'date_modified' => array('width'   => '15', 
                                                                              'label'   => 'LBL_DATE_MODIFIED'),    
                                                     'created_by' => array('width'   => '8', 
                                                                           'label'   => 'LBL_CREATED'),
                                                     'assigned_user_name' => array('width'   => '15', 
                                                                                   'label'   => 'LBL_LIST_ASSIGNED_USER',
                                                                                   'default' => true),
                                                   //BEGIN SUGARCRM flav=pro ONLY
                                                     'team_name' => array('width'   => '15', 
                                                                          'label'   => 'LBL_LIST_TEAM'),
                                                   //END SUGARCRM flav=pro ONLY
                                                                             );
?>
