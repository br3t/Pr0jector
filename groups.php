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
print('<h3>'.$LF_links['groups'].'</h3>');
if($MY_RULZ['state']>2)
 print('<p>[<a href="edit.php?group=-1">'.$LF_other['addgroup'].'</a>]');
$id = "";
if(isset($_GET['id']))
 $id = floor($_GET['id']);
print(show_group_info($id));
?>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR);
print($BOTTOM_STR);
?>