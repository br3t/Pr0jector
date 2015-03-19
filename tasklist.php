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
if(isset($_GET['user']))
{
 if($_GET['user']==$MY_RULZ['uid'])
  $tit = $LF_links['mtasks'];
 else
  $tit = $LF_links['tasks']." ".$LF_messz['for']." ".gen_user_link(array_map('infiltrtext', user_exist($_GET['user'])));
 print("<h3>".$tit."</h3>");
 print(show_task(-1, -1, $_GET['user']));
}
else if(isset($_GET['id']))
{
 print("<h3>".$LF_links['tasks']."</h3>");
 print(show_task($_GET['id'], -1, -1));
} 
else
{
 print("<h3>".$LF_links['mtasks']."</h3>");
 print(show_task(-1, -1, $MY_RULZ['uid']));
}
?>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR);
print($BOTTOM_STR);
?>