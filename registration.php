<?php
include("template.php");
// is registration with code7
if($SEC['registration_code']!="")
{
 if(isset($_GET['code']))
 {
  if($_GET['code']!=$SEC['registration_code'])
  {
   header("Location: error404.php");
   exit(0);
  }
 }
 else
 {
  header("Location: error404.php");
  exit(0);
 }
}
//-------------------------------------
//  if data send
$edfielsd = Array('error_messages'=>"", 'login'=>"", 'nick'=>"", 'name'=>"", 'sname'=>"", 'surname'=>"", 'icq'=>"", 'email'=>"", 'jabber'=>"", 'mphone'=>"", 'hphone'=>"", 'country'=>"", 'city'=>"", 'adress'=>"", 'plushour'=>0, 'letters'=>"", 'show_form'=>true);
if(isset($_POST['registration']))
 $edfielsd = try_register_form($_POST);
//-------------------------------------
print($HEAD_STR);
print("<h3>".$formz['registr']."</h3>");
print($edfielsd['error_messages']);
$REG_FORM = "";
if($edfielsd['show_form'])
 $REG_FORM = show_edit_profile_form(0, $edfielsd);
print($REG_FORM);
print("<p>[".$LF_other['ret_index']."]</p>");
print($BOTTOM_STR);
?>