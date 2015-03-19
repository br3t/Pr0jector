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
print("<h3>".$LF_links['news']."</h3>");
if($MY_RULZ['state']>1)
 print("<p>[<a href='edit.php?news=-1'>".$LF_other['addnews']."</a>]</p>");
$id = -1;
if(isset($_GET['id']))
 $id = floor($_GET['id']);
print(show_news($id, 3, @$_GET['page']));
?>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR);
print($BOTTOM_STR);
?>