{*
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
*}
<div>
    <link rel="stylesheet" type="text/css"
          href="{sugar_getjspath file='modules/ModuleBuilder/tpls/ListEditor.css'}"></link>
    <link rel="stylesheet" type="text/css"
          href="{sugar_getjspath file='modules/ModuleBuilder/tpls/MBModule/dropdown.css'}"></link>
    <form name='dropdown_form' onsubmit="return false">
        <input type='hidden' name='module' value='ModuleBuilder'>
        <input type='hidden' name='action' value='saveroledropdown'>
        <input type='hidden' name='to_pdf' value='true'>
        <input type='hidden' name='view_module' value='{$module_name}'>
        <input type='hidden' name='view_package' value='{$package_name}'>
        <input type='hidden' name='dropdown_role' value='{$dropdown_role}'>
        <input type='hidden' id='list_value' name='list_value' value=''>
        {* This indicates that this dropdown is being created from a new field *}
        {if ($fromNewField)}
            <input type='hidden' name='is_new' value='1'>
        {/if}
        {if ($refreshTree)}
            <input type='hidden' name='refreshTree' value='1'>
        {/if}
        <style>
            {literal}
            .listContainer {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .listContainer li {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .listContainer li .fieldValue {
                line-height: 30px;
            }

            li.deleted {
                background-color: #AAA;
            }

            .deleted td.first {
                text-decoration: line-through;
            }

            {/literal}
        </style>
        <table>
            <tr>
                <td colspan='2'>
                    <input id="saveBtn" type='button' class='button' onclick='SimpleList.handleSave()'
                           value='{sugar_translate label='LBL_SAVE_BUTTON_LABEL'}'>
                    <input type='button' class='button' onclick='SimpleList.undo()'
                           value='{sugar_translate label='LBL_BTN_UNDO'}'>
                    <input type='button' class='button' onclick='SimpleList.redo()'
                           value='{sugar_translate label='LBL_BTN_REDO'}'>
                    <input type='button' class='button' name='cancel' value='{sugar_translate label='LBL_BTN_CANCEL'}'
                           onclick='ModuleBuilder.tabPanel.get("activeTab").close()'>
                </td>
                <td style="text-align: right">
                    <label>{sugar_translate label='LBL_ROLE'}
                        {html_options name='dropdown_role' selected=$dropdown_role options=$roles}
                    </label>
                </td>
            </tr>
            <tr>
                <td colspan='3'>
                    <hr/>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span class='mbLBLL'>{sugar_translate label='LBL_DROPDOWN_TITLE_NAME'}:&nbsp;</span>
                    {if $name }
                        <input type='hidden' id='dropdown_name' name='dropdown_name'
                               value='{$dropdown_name}'>{$dropdown_name}
                    {else}
                        <input type='text' id='dropdown_name' name='dropdown_name' value={$prepopulated_name}>
                    {/if}
                </td>
            </tr>
            <tr>
                <td colspan="3" class='mbLBLL'>
                    {sugar_translate label='LBL_DROPDOWN_LANGUAGE'}:&nbsp;
                    {html_options name='dropdown_lang' options=$available_languages selected=$selected_lang}
                </td>
            </tr>
            <tr>
                <td colspan="3" style='padding-top:10px;' class='mbLBLL'>{sugar_translate label='LBL_DROPDOWN_ITEMS'}:
                </td>
            </tr>
            <tr>
                <td colspan="2"><b>{sugar_translate label='LBL_DROPDOWN_ITEM_NAME'}</b><span
                            class='fieldValue'>[{sugar_translate label='LBL_DROPDOWN_ITEM_LABEL'}]<span>
                </td>
                <td style="text-align: right">
                    <input type='button' class='button' value='{sugar_translate label='LBL_BTN_SORT_ASCENDING'}'
                           onclick='SimpleList.sortAscending()'>
                    <input type='button' class='button' value='{sugar_translate label='LBL_BTN_SORT_DESCENDING'}'
                           onclick='SimpleList.sortDescending()'>
                </td>
            </tr>
            <tr>
                <td colspan='3'>
                    <ul id="ul1" class="listContainer">
                        {foreach from=$role_options key='id' item='checked'}
                            {if isset($options[$id])}
                            <li class="draggable{if !$checked} deleted{/if}" id="{$id}">
                                <table width='100%'>
                                    <tr>
                                        {if $options[$id] == ''}
                                            <td class="first">
                                                {sugar_translate label='LBL_BLANK'}
                                                   <span class='fieldValue' id='span_'>
                                                       [{sugar_translate label='LBL_BLANK'}]
                                                   </span>
                                            </td>
                                            <td align="right">
                                                <input id='value_' value='-blank-' type='hidden'>
                                                <input type="hidden" value="0" name="dropdown_keys[-blank-]">
                                                <input type="checkbox" value="1" {if $checked}checked{/if}
                                                       name="dropdown_keys[-blank-]">
                                            </td>
                                        {else}
                                            <td class="first">
                                               <b>{$id}</b>
                                               <span class='fieldValue' id='span_{$id}'>
                                                  [{$options[$id]}]
                                               </span>
                                            </td>
                                            <td align='right'>
                                                <input id='value_{$id}' value='{$options[$id]|escape}' type='hidden'>
                                                <input type="hidden" value="0" name="dropdown_keys[{$id}]">
                                                <input type="checkbox" value="1" {if $checked}checked{/if}
                                                       name="dropdown_keys[{$id}]">
                                            </td>
                                        {/if}
                                    </tr>
                                </table>
                            </li>
                            {/if}
                        {/foreach}
                    </ul>
                </td>
            </tr>
        </table>
    </form>
    {literal}
    <script>
        addForm('dropdown_form');
        addToValidate('dropdown_form', 'dropdown_name', 'DBName', false, SUGAR.language.get("ModuleBuilder", "LBL_JS_VALIDATE_NAME"));
        addToValidate('dropdown_form', 'drop_value', 'varchar', false, SUGAR.language.get("ModuleBuilder", "LBL_JS_VALIDATE_LABEL"));
        eval({/literal}{$ul_list}{literal});
        SimpleList.name = {/literal}'{$dropdown_name}'{literal};
        SimpleList.requiredOptions = {/literal}{$required_items}{literal};
        SimpleList.ul_list = list;
        SimpleList.init({/literal}'{$editImage}'{literal}, {/literal}'{$deleteImage}'{literal});
        ModuleBuilder.helpSetup('dropdowns', 'editdropdown');

        var addListenerFields = ['drop_name', 'drop_value'];
        YAHOO.util.Event.addListener(addListenerFields, "keydown", function (e) {
            if (e.keyCode == 13) {
                YAHOO.util.Event.stopEvent(e);
            }
        });

        $('input[type="checkbox"]', "#ul1").on('change', function() {
            $(this).parents('li').toggleClass('deleted');
        });

        $('select').on('change', function () {
            if ($(this).val() === 'default') {
                this.form.action.value = 'dropdown';
            } else {
                this.form.action.value = 'roledropdown';
            }
            ModuleBuilder.handleSave("dropdown_form");
        });

    </script>
    <script>// Bug in FF4 where it doesn't run the last script. Remove when the bug is fixed.</script>
    {/literal}
</div>

