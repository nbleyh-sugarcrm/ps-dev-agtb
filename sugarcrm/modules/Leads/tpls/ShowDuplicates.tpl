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
{$TITLE}
<p>
<form action='index.php' method='post' name='Save'>
{sugar_csrf_form_token}
<input type="hidden" name="module" value="Leads">
<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
<input type="hidden" name="return_action" value="{$RETURN_ACTION}">
<input type="hidden" name="return_id" value="{$RETURN_ID}">
<input type="hidden" name="inbound_email_id" value="{$INBOUND_EMAIL_ID|escape:'html':'UTF-8'}">
<input type="hidden" name="start" value="{$START}">
<input type="hidden" name="dup_checked" value="true">
<input type="hidden" name="action" value="">
{$INPUT_FIELDS}
<table cellpadding="0" cellspacing="0" width="100%" border="0" >
<tr>
<td>
<table cellpadding="0" cellspacing="0" width="100%" border="0" >
<tr>
<td  valign='top' align='left'>{$FORMBODY}{$FORMFOOTER}{$POSTFORM}</td>
</tr>
</table>
</td>
</tr>
</table>
<p>
