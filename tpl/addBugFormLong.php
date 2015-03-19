<?php
$ADDBUGFORMLONG = <<<ABFL
<form method='post' action='actions.php'>
<input type='hidden' name='bid' value='{$theBug['bid']}' />
<table class='form'>
	<tr>
		<td>{$LF_other['project']}</td>
		<td><a href='projects.php?id={$theBug['pid']}'>{$theBug['pname']}</a></td>
	</tr>
	<tr>
		<td>{$LF_other['bugHeader']}</td>
		<td><input type='text' name='bugHead' value='{$theBug['bname']}' /></td>
	</tr>
	<tr>
		<td>{$LF_other['linkedTask']}</td>
		<td>
			<ul class='noStyleList'>
				<li><input type='radio' name='bugTask' value='0' checked='checked' />{$LF_other['no']}</li>
				<li><input type='radio' name='bugTask' value='-1' />{$LF_other['newtask']}</li>
				{$budLinkedTask}
		</td>
	</tr>
	<tr>
		<td>{$LF_other['state']}</td>
		<td>{$bugStateList}</td>
	</tr>
	<tr>
		<td>{$LF_other['added']}</td>
		<td>{$bugAddedTime}</td>
	</tr>
	<tr>
		<td>{$LF_other['bugDesc']}</td>
		<td><textarea cols='70' rows='10' name='bugDesc'>{$theBug['bdescription']}</textarea></td>
	</tr>
	<tr>
		<td class='rig'><input type='submit' name='addNewBug' value='{$LF_other['save']}' /></td>
		<td><input type="button" value="{$LF_other['quitnosave']}" onclick="window.history.back(1);"></td>
	</tr>
</table>
</form>
ABFL;
?>