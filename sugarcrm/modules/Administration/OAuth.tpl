<form name="RegisterForSnip" method="POST" action="index.php" >
<input type='hidden' name='action' value='OAuth'/>
<input type='hidden' name='module' value='Administration'/>
<input type='hidden' name='sid' value='{$sid}'/>

{if !empty($VERIFY)}
{$MOD.LBL_OAUTH_VALIDATION}: {$VERIFY}<br/>
{/if}

<table>
<tr>
<td>{$MOD.LBL_OAUTH_REQUEST}: </td><td><input name="token" value="{$token}"/></td>
</tr>
<tr>
<td>{$MOD.LBL_OAUTH_ROLE}: </td><td>{html_options name="role" id="role" options=$roles}</td>
</tr>
</table>

<input type="submit" name="authorize" value="{$MOD.LBL_OAUTH_AUTHORIZE}"/><br/>
</form>