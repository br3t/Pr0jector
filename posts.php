<?php
include("config.php");
include("library.php");
if(isset($_GET['id']))
{
 $redirurl = redir_post($_GET['id']);
 if($redirurl!='')
  header("Location: ".$redirurl);
 else
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
?>