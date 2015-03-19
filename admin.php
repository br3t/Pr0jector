<?php
include("template.php");
if(!isset($MY_RULZ['uid']))
 header("Location: news.php");
else if($MY_RULZ['state']<3)
 header("Location: news.php");
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
print("<h3>".$LF_links['admin']."</h3>");
?>
<!-- <a href='test.php'>test.php</a><br /> //-->
<p>
<?php
$tst = file_get_contents('last_cron.txt');
print("Last cron: ".date('H:i:s, d-m-Y', floor($tst))." (".time_left(time()-floor($tst))." ago)");
?> <a href='cron_me.php' target='_blank'>cron_me.php</a><br /><a href="waag.php" target="_blank">waag.php</a><br />
<a href='phpinfo.php' target='_blank'>phpinfo.php</a><br />
<?php
if($SEC['registration_code']=='')
 print("Open registration mode");
else
 print("Closed registration mode: registering with link <u>http://".$_SERVER['HTTP_HOST'].$SEC['proektor_path']."/registration.php?code=".$SEC['registration_code']."</u>");
?>
</p>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR);
print($BOTTOM_STR);
?>