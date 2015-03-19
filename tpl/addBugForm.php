<?php
$ADDBUGFORM = <<<ABF
<fieldset>
<legend class='b'>{$LF_other['bugDetected']}</legend>
<form method='post' action='actions.php'>
<input type='hidden' name='bid' value='-1' />
<input type='hidden' name='pid' value='{$pid}' />
{$LF_other['bugHeader']}: <input type='text' name='bugHead' /><br />
{$LF_other['bugDesc']}: <textarea name='bugDesc'></textarea><br />
<input type='submit' name='addNewBug' value='{$LF_other['save']}' />
<input type='button' onclick='$(this).parent().parent().parent().toggle("fast");' value='{$LF_other['cansel']}' />
</form>
</fieldset>
ABF;
?>