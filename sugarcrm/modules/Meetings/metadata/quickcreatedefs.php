<?php
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
$viewdefs ['Meetings'] =
array (
  'QuickCreate' =>
  array (
    'templateMeta' =>
    array (
      'maxColumns' => '2',
      'form' =>
      array (
        'hidden' =>
        array (
           '<input type="hidden" name="isSaveAndNew" value="false">',
        ),
        'buttons' =>
        array (

          array (
            'customCode' => '<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button" onclick="SUGAR.meetings.fill_invitees();this.form.action.value=\'Save\'; this.form.return_action.value=\'DetailView\'; {if isset($smarty.request.isDuplicate) && $smarty.request.isDuplicate eq "true"}this.form.return_id.value=\'\'; {/if}return check_form(\'EditView\');" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}">',
          ),
           'CANCEL',

          array (
            'customCode' => '<input title="{$MOD.LBL_SEND_BUTTON_TITLE}" class="button" onclick="this.form.send_invites.value=\'1\';SUGAR.meetings.fill_invitees();this.form.action.value=\'Save\';this.form.return_action.value=\'EditView\';this.form.return_module.value=\'{$smarty.request.return_module}\';return check_form(\'EditView\');" type="submit" name="button" value="{$MOD.LBL_SEND_BUTTON_LABEL}">',
          ),

          array (
            'customCode' => '{if $fields.status.value != "Held"}<input title="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_TITLE}" accessKey="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_KEY}" class="button" onclick="SUGAR.meetings.fill_invitees(); this.form.status.value=\'Held\'; this.form.action.value=\'Save\'; this.form.return_module.value=\'Meetings\'; this.form.isDuplicate.value=true; this.form.isSaveAndNew.value=true; this.form.return_action.value=\'EditView\'; this.form.return_id.value=\'{$fields.id.value}\'; return check_form(\'EditView\');" type="submit" name="button" value="{$APP.LBL_CLOSE_AND_CREATE_BUTTON_LABEL}">{/if}',
          ),
        ),
        'headerTpl' => 'modules/Meetings/tpls/header.tpl',
        'footerTpl' => 'modules/Meetings/tpls/footer.tpl',
      ),
      'widths' =>
      array (

        array (
          'label' => '10',
          'field' => '30',
        ),

        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'javascript' => '<script type="text/javascript">{$JSON_CONFIG_JAVASCRIPT}</script>
{sugar_getscript file="cache/include/javascript/sugar_grp_jsolait.js"}
<script>toggle_portal_flag();function toggle_portal_flag()  {literal} { {/literal} {$TOGGLE_JS} {literal} } {/literal} </script>',
      'useTabs' => false,
    ),
    'panels' =>
    array (
      'default' =>
      array (

        array (

          array (
            'name' => 'name',
            'displayParams' =>
            array (
              'required' => true,
            ),
          ),

          array (
            'name' => 'status',
            'fields' =>
            array (

              array (
                'name' => 'status',
              ),
            ),
          ),
        ),
        //BEGIN SUGARCRM flav!=com ONLY
        array(
        	'type',
        	'password'
        ),
        //END SUGARCRM flav!=com ONLY
        array (

          array (
            'name' => 'date_start',
            'type' => 'datetimecombo',
            'displayParams' =>
            array (
              'required' => true,
              'updateCallback' => 'SugarWidgetScheduler.update_time();',
            ),
          ),

          array (
            'name' => 'parent_name',
            'label' => 'LBL_LIST_RELATED_TO',
          ),
        ),
        
        array (
          array (
            'name' => 'date_end',
            'type' => 'datetimecombo',
            'displayParams' =>
            array (
              'required' => true,
              'updateCallback' => 'SugarWidgetScheduler.update_time();',
            ),
          ),
          array (
            'name' => 'location',
            'comment' => 'Meeting location',
            'label' => 'LBL_LOCATION',
          ),
        ),
        
        array(        
          array (
            'name' => 'duration',
            'customCode' => '
                @@FIELD@@
                <span id="duration_text"></span>
                <input id="duration_hours" name="duration_hours" type="hidden" value="{$fields.duration_hours.value}">
                <input id="duration_minutes" name="duration_minutes" type="hidden" value="{$fields.duration_minutes.value}">
                {sugar_getscript file="modules/Meetings/duration_dependency.js"}
                <script type="text/javascript">
                    var date_time_format = "{$CALENDAR_FORMAT}";
                    {literal}
                    SUGAR.util.doWhen(function(){return typeof DurationDependency != "undefined" && typeof document.getElementById("duration") != "undefined"}, function(){
                        var duration_dependency = new DurationDependency("date_start","date_end","duration",date_time_format);
                    });
                    {/literal}
                </script>            
            ',
          ),          
          array (
            'name' => 'reminder_time',
            'customCode' => '{include file="modules/Meetings/tpls/reminders.tpl"}',
            'label' => 'LBL_REMINDER',
          ),
       ),

         array (
         	 array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
          ),
           //BEGIN SUGARCRM flav=pro ONLY
            array (
              'name' => 'team_name',
            ),
            //END SUGARCRM flav=pro ONLY

        ),

        array (

          array (
            'name' => 'description',
            'comment' => 'Full text of the note',
            'label' => 'LBL_DESCRIPTION',
          ),
        ),
      ),
    ),
  ),
);
?>
