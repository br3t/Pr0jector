<?php
include("template.php");
print($HEAD_STR);
?>
<table width="100%">
<tr><td width='100px' style="valign: top;vertical-align: top;">
<?php
print($LOGIN_FORM);
?></td>
<td style="valign: top;vertical-align: top;">
<?php
print($MENUBAR);
if(isset($_GET['id']))
{
 print("<h3>".$LF_links['files']."</h3>".show_file($_GET['id']));
}
else if(isset($_GET['user']))
{
 print("<h3>".$LF_links['files']." ".$LF_other['usera']." ".gen_user_link(array_map('infiltrtext', user_exist(floor($_GET['user']))))."</h3>".(($_GET['user']===$MY_RULZ['uid'])?("[<a href='edit.php?addfile2task=0'>".$LF_other['addfile']."</a>]"):"").show_file(-1,-1, $_GET['user']));
}
else
{
 print("<h3>".$LF_links['myfiles']."</h3><p>[<a href='edit.php?addfile2task=0'>".$LF_other['addfile']."</a>]</p>".show_file(-1,-1, $MY_RULZ['uid']));
}
?>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR.
$BOTTOM_STR);
?>