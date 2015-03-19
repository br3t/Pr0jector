<?php
include("template.php");
if(isset($MY_RULZ['uid']))
 header("Location: news.php");
print($HEAD_STR);
?>
<table>
<tr><td width='100px' style="valign: top;vertical-align: top;">
<?php
print($LOGIN_FORM);
?></td>
<td style="valign: top;vertical-align: top;">
<?php
 include("languages/about_".$MY_LOCALE.".html");
?>
</td>
</tr>
</table>
<?php
print($BOTTOM_STR);
?>