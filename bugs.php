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
 print('<h3>'.$LF_links['bugs'].'</h3>');
 $bugId = -1;
 if(isset($_GET['id']))
  $bugId = intval($_GET['id']);
 print(show_bug_info($bugId));
?>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR);
print($BOTTOM_STR);
?>