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


$mod_strings = array (
//  'LBL_TEAM' => 'Teams',
//  'LBL_TEAMS' => 'Teams',
//  'LBL_TEAM_ID' => 'Team Id',
//  'LBL_ASSIGNED_TO_ID' => 'Assigned User Id',
//  'LBL_ASSIGNED_TO_NAME' => 'Assigned to',
//  'LBL_ID' => 'ID',
//  'LBL_DATE_ENTERED' => 'Date Created',
//  'LBL_DATE_MODIFIED' => 'Date Modified',
//  'LBL_MODIFIED' => 'Modified By',
//  'LBL_MODIFIED_ID' => 'Modified By Id',
//  'LBL_MODIFIED_NAME' => 'Modified By Name',
//  'LBL_CREATED' => 'Created By',
//  'LBL_CREATED_ID' => 'Created By Id',
//  'LBL_DESCRIPTION' => 'Description',
//  'LBL_DELETED' => 'Deleted',
//  'LBL_NAME' => 'Name',
//  'LBL_CREATED_USER' => 'Created by User',
//  'LBL_MODIFIED_USER' => 'Modified by User',
//  'LBL_LIST_NAME' => 'Name',
//  'LBL_EDIT_BUTTON' => 'Edit',
//  'LBL_REMOVE' => 'Remove',
//  'LBL_LIST_FORM_TITLE' => 'Processes List',
  'LBL_MODULE_NAME' => 'Processes',
  'LBL_MODULE_TITLE' => 'Processes',
  'LBL_MODULE_NAME_SINGULAR' => 'Processes',
//  'LBL_HOMEPAGE_TITLE' => 'My Processes',
//  'LNK_NEW_RECORD' => 'Create Processes',
  'LNK_LIST' => 'View Processes',
//  'LNK_IMPORT_PMSE_INBOX' => 'Import Processes',
//  'LBL_SEARCH_FORM_TITLE' => 'Search Processes',
//  'LBL_HISTORY_SUBPANEL_TITLE' => 'View History',
//  'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'Activity Stream',
//  'LBL_PMSE_INBOX_SUBPANEL_TITLE' => 'Processes',
//  'LBL_NEW_FORM_TITLE' => 'New Processes',
//  'LNK_IMPORT_VCARD' => 'Import Processes vCard',
//  'LBL_IMPORT' => 'Import Processes',
//  'LBL_IMPORT_VCARDTEXT' => 'Automatically create a new Processes record by importing a vCard from your file system.',
//  'LBL_CAS_PARENT' => 'Process Parent',
//  'LBL_CAS_STATUS' => 'Process Status',
//  'LBL_CAS_TITLE' => 'Process Title',
  'LBL_CAS_ID' => 'Process Id',
//  'LBL_PRO_ID' => 'Process Id',
//  'LBL_PRO_TITLE' => 'Process Title',
//  'LBL_CAS_CUSTOM_STATUS' => 'Process Custom Status',
//  'LBL_CAS_INIT_USER' => 'Process Initialize User',
//  'LBL_CAS_CREATE_DATE' => 'Process Create Date',
//  'LBL_CAS_UPDATE_DATE' => 'Process Update Date',
//  'LBL_CAS_FINISH_DATE' => 'Process Finish Date',
//  'LBL_CAS_PIN' => 'Process Pin',
//  'LBL_CAS_ASSIGNED_STATUS' => 'Process Assigned Status',
//
//    'LBL_PROCESSES_DASHLET' => 'Processes',
//    'LBL_PROCESSES_DASHLET_DESCRIPTION' => 'The Processes dashlet displays due now, upcoming and to do Processes.',

    'LBL_PMSE_HISTORY_LOG_NOTFOUND_USER' => "Unknown (according UserId:'%s')",
    'LBL_PMSE_HISTORY_LOG_TASK_HAS_BEEN' => "task has been ",
    'LBL_PMSE_HISTORY_LOG_TASK_WAS' => "task was ",
    'LBL_PMSE_HISTORY_LOG_EDITED' => "edited",
    'LBL_PMSE_HISTORY_LOG_CREATED' => "created",
    'LBL_PMSE_HISTORY_LOG_ROUTED' => "routed",
    'LBL_PMSE_HISTORY_LOG_DONE_UNKNOWN' => "Done an unknown task",
    'LBL_PMSE_HISTORY_LOG_CREATED_CASE' => "has created the Process # %s ",
    'LBL_PMSE_HISTORY_LOG_DERIVATED_CASE' => "has derivated to the TaskId %s ",
    'LBL_PMSE_HISTORY_LOG_CURRENTLY_HAS_CASE' => "currently has the TaskId %s ",
    'LBL_PMSE_HISTORY_LOG_ACTIVITY_NAME' => " named: '%s' ",
    'LBL_PMSE_HISTORY_LOG_ACTION_PERFORMED'  => " The action performed was: <span style=\"font-weight: bold\">[%s]</span>",
    'LBL_PMSE_HISTORY_LOG_ACTION_STILL_ASSIGNED' => " The task is still assigned.",
    'LBL_PMSE_HISTORY_LOG_MODULE_ACTION'  => " of Module %s %s , ",
    'LBL_PMSE_HISTORY_LOG_WITH_EVENT'  => " with the Event %s ",
    'LBL_PMSE_HISTORY_LOG_WITH_GATEWAY'  => " with the Gateway %s was evaluated and routed to the next task ",
    'LBL_PMSE_HISTORY_LOG_NOT_REGISTED_ACTION'  => "not registed action",

    'LBL_PMSE_LABEL_APPROVE' => 'Approve',
    'LBL_PMSE_LABEL_REJECT' => 'Reject',
    'LBL_PMSE_LABEL_ROUTE' => 'Route',
    'LBL_PMSE_LABEL_CLAIM' => 'Claim',
    'LBL_PMSE_LABEL_STATUS' => 'Status',
    'LBL_PMSE_LABEL_REASSIGN' => 'Reassign',
    'LBL_PMSE_LABEL_CHANGE_OWNER' => 'Change Owner',
    'LBL_PMSE_LABEL_EXECUTE' => 'Execute',
    'LBL_PMSE_LABEL_CANCEL' => 'Cancel',
    'LBL_PMSE_LABEL_HISTORY' => 'History',
    'LBL_PMSE_LABEL_NOTES' => 'Notes',
//    'LBL_PMSE_LABEL_UNASSIGNED' => 'Unassigned',
//    'LBL_PMSE_LABEL_ERROR_EXPECTED_DUE_DATE' => 'Due date',
//    'LBL_PMSE_LABEL_ERROR_EXPECTED_OVERDUE' => 'Overdue',
//    'LBL_PMSE_LABEL_ERROR_EXPECTED_DUE_DATE_NO_SET' => ' < no set >',

    'LBL_PMSE_FORM_OPTION_SELECT' => 'Select...',
    'LBL_PMSE_FORM_LABEL_USER' => 'User',
    'LBL_PMSE_FORM_LABEL_TYPE' => 'Type',
    'LBL_PMSE_FORM_LABEL_NOTE' => 'Note',

    'LBL_PMSE_BUTTON_SAVE' => 'Save',
    'LBL_PMSE_BUTTON_CLOSE' => 'Close',
    'LBL_PMSE_BUTTON_PROCESS_AUTHOR_LOG' => 'Process Author Log',
    'LBL_PMSE_BUTTON_SUGARCRM_LOG' => 'SugarCRM Log',
    'LBL_PMSE_BUTTON_REFRESH' => 'Refresh',

    'LBL_PMSE_FORM_TOOLTIP_SELECT_USER' => 'Select the user to assign the case',


    'LBL_PMSE_LABEL_CURRENT_ACTIVITY' => 'Current Activity',
    'LBL_PMSE_LABEL_ACTIVITY_DELEGATE_DATE' => 'Activity Delegate Data',
    'LBL_PMSE_LABEL_EXPECTED_TIME' => 'Expected Time',
    'LBL_PMSE_LABEL_DUE_DATE' => 'Due Date',
    'LBL_PMSE_LABEL_IN_TIME' => 'In Time',
    'LBL_PMSE_LABEL_OVERDUE' => 'Overdue',

//    'LBL_CASE_ID'  => "Process Id",
    'LBL_CASE_TITLE'  => "Process Title",
    'LBL_PROCESS_NAME'  => "Process Name",
//    'LBL_STATUS'  => "Status",
//    'LBL_DATE_CREATED' => "Date Created",
    'LBL_OWNER' => 'Owner',

//    'LBL_CASES_LIST_TITLE'  => "Search Process Title",
    'LBL_PMSE_TITLE_PROCESSESS_LIST'  => 'Processes List',
    'LBL_PMSE_TITLE_UNATTENDED_CASES' => 'Unattended Processes',
    'LBL_PMSE_TITLE_REASSIGN' => 'User can change Record Owner',
    'LBL_PMSE_TITLE_AD_HOC' => 'Reassign',
    'LBL_PMSE_TITLE_ACTIVITY_TO_REASSIGN' => "Activity to Reassign",
    'LBL_PMSE_TITLE_HISTORY' => 'Process History',
    'LBL_PMSE_TITLE_IMAGE_GENERATOR' => 'Process #%s: Current Status',
    'LBL_PMSE_TITLE_LOG_VIEWER' => 'Process Author Log Viewer',

//    'LBL_BASIC_SEARCH' => 'Search by Process Title',
//
//    'LBL_LOG_VIEW_PMSE'  => "Process Author Log Viewer",
//    'LBL_PMSE_LOG'  => "Processes Log",
//    'LBL_SUGAR_CRM_LOG'  => "SugarCRM Log",
//    'LBL_CRON_LOG'  => "Cron Log",
//    'LBL_CANCEL_CASE'  => "Cancel Process",
//    'LBL_EXECUTE_CASE'  => "Execute Process",
//    "LBL_SHOW_MORE_CASES"  => "More Process...",
//
//    'LBL_CLOSE_BUTTON_LABEL'=>"Close",
//    'LBL_SAVE_BUTTON_LABEL'=>"Save",
//

    'LBL_PMSE_MY_PROCESSES' => 'My Processes',
    'LBL_PMSE_SELF_SERVICE_PROCESSES' => 'Self Service Processes',

    'LBL_PMSE_ACTIVITY_STREAM_APPROVE'=>"&0 on <strong>%s</strong> Approved ",
    'LBL_PMSE_ACTIVITY_STREAM_REJECT'=>"&0 on <strong>%s</strong> Rejected ",
    'LBL_PMSE_ACTIVITY_STREAM_ROUTE'=>'&0 on <strong>%s</strong> Routed ',
    'LBL_PMSE_ACTIVITY_STREAM_CLAIM'=>"&0 on <strong>%s</strong> Claimed ",
    'LBL_PMSE_ACTIVITY_STREAM_REASSIGN'=>"&0 on <strong>%s</strong> assigned to user &1 ",
//
//    'LBL_NSC_MESSAGE'=>"This process was ",
//    'LBL_NSC_CANCELED'=>" cancelled due that the record related to this Process has been removed",
//    'LBL_NSC_CLAIM'=>" claimed by other user",
//    'LBL_CANCEL_MESSAGE'=>"Are you sure to want cancel the process: [] with Process Id: {}",
//
    'LBL_ASSIGNED_USER'=>"User Assigned",
//
//    'LBL_PMSE_SETTING_LOG_LEVEL' => 'Error Log Level',
    'LBL_PMSE_SETTING_NUMBER_CYCLES' => "Error Number of Cycles",
//    'LBL_SETTING_TIMEOUT' => "Error Timeout",
//    'LBL_SETTING' => "Settings",
    'LBL_PMSE_SHOW_PROCESS' => 'Show Process',
);

