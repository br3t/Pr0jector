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
if(isset($_GET['last']))
 print("<h3>".$LF_links['last_events']."</h3>");
else
 print("<h3>".$LF_links['events']."</h3>");
print("<p>[<a href='events.php?last=1'>".$LF_links['last_events']."</a>] [<a href='events.php'>".$LF_links['all_events']."</a>]"); 
if($MY_RULZ['state']>1)
 print(" [<a href='edit.php?event=-1'>".$LF_other['addevent']."</a>]");
print("</p>"); 
$id = -1;
if(isset($_GET['id']))
 $id = floor($_GET['id']);
print(show_news($id, 4, @$_GET['page'], @$_GET['last']));
?>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR);
print($BOTTOM_STR);
?>