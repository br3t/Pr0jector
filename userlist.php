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
$tit = $LF_links['profile'];
$id = "";
if(isset($_GET['id']))
 $id = floor($_GET['id']);
print(show_user_info($id));
?>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR);
print($BOTTOM_STR);
?>