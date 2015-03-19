<?php
include("template.php");
print($HEAD_STR);
?>
<script type='text/javascript' src='images/jquery_1_3_2_min.js'></script>  
<table>
<tr><td width='100px' style="valign: top;vertical-align: top;">
<?php
print($LOGIN_FORM);
?></td>
<td style="valign: top;vertical-align: top;">
<?php
 if(isset($_FILES['f']))
 {
  print("<pre>");print_r($_FILES['f']);print("</pre>");
  $ish = file_get_contents($_FILES['f']['tmp_name']);
  print("<textarea rows='10px' cols='80'>".base64_encode($ish)."</textarea>");
 }
 $re = "август";
 print("<p>".$re."<br />".mb_substr($re, 0, 3)."</p>");
?>
<form enctype='multipart/form-data' method='post'>
<input type='file' name='f' />
<input type='submit' value='submit' /></form>
</td>
</tr>
</table>
<?php
print($BOTTOM_STR);
?>