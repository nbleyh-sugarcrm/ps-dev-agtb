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


{include file="modules/DynamicFields/templates/Fields/Forms/coreTop.tpl"}

<tr>
    <td class="mbLBL">{sugar_translate module="DynamicFields" label="COLUMN_TITLE_HTML_CONTENT"}:</td>
    <td>
    {if $hideLevel < 5}
        <textarea name='htmlarea' id='htmlarea' cols=50 rows=10>{$HTML_EDITOR}</textarea>
        <input type='hidden' name='ext4' id='ext4' value='{$cf.ext4}'/>
    {else}
        <textarea name='htmlarea' id='htmlarea' cols=50 rows=10 disabled>{$HTML_EDITOR}</textarea>
        <input type='hidden' name='htmlarea' value='{$HTML_EDITOR}'/>
    {/if}
        <br>
    </td>
</tr>
{include file="modules/DynamicFields/templates/Fields/Forms/coreBottom.tpl"}

<script type="text/javascript" language="Javascript">
SUGAR.ajaxLoad = true;
var html_editor = '{$HTML_EDITOR|escape:javascript}';
{literal}
var setupMCE = function() {
	ModuleBuilder.tabPanel.get("activeTab").closeEvent.subscribe(function() {
		tinyMCE.execCommand('mceRemoveControl', false, 'htmlarea');
	});
	tinyMCE.execCommand('mceAddControl', false, 'htmlarea');
	editor = tinyMCE.get('htmlarea');
	editor.setContent(html_editor);
	document.forms.popup_form.required.value = false;
	YAHOO.util.Dom.getAncestorByTagName(document.forms.popup_form.required, 'tr').style.display='none';
}
document.popup_form.presave = function(){
    var tiny = tinyMCE.getInstanceById('htmlarea');
    if ( (null != tiny) || ("undefined" != typeof(tiny)) ) {
         document.getElementById('ext4').value = tiny.getContent();
    } else {
         document.getElementById('ext4').value = document.getElementById('htmlarea').value;
    }
    document.getElementById('ext4').style.display = '';
};
{/literal}
{if $hideLevel < 5}
	setTimeout("setupMCE();", 500);
{/if}
</script>