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
$MESS_BAR = "<p>[<a href='messages.php?act=new'>{$LF_links['newpms']}</a>] [<a href='messages.php'>{$LF_links['inpms']}</a>] [<a href='messages.php?act=outcome'>{$LF_links['outpms']}</a>]</p>";
if(isset($_GET['act']))
 switch($_GET['act'])
 {
  case 'outcome':
   print("<h3>{$LF_links['outpms']}</h3>".$MESS_BAR);
   print(show_messages('out'));
  break;
  case 'new':
   print("<h3>{$LF_links['newpms']}</h3>".$MESS_BAR);
   print(show_messages('new'));
  break;
  case 'send':
   print("<h3>{$LF_links['newpm']}</h3>".$MESS_BAR);
   print(show_new_message_form($_GET));
  break;
  default:
   print("<h3>{$LF_links['inpms']}</h3>".$MESS_BAR);
   print(show_messages('in'));
  break;
 }
else  if(isset($_GET['id']))
{
 print("<h3>{$LF_messz['body']}</h3>".$MESS_BAR);
 print(show_mess1($_GET['id']));
}
else
{
 print("<h3>{$LF_links['inpms']}</h3>".$MESS_BAR);
 print(show_messages('in'));
}
?>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR);
print($BOTTOM_STR);
?>