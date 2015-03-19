<?php
include("config.php");
include("library.php");
$MY_RULZ = array();
if(isset($_SESSION['login'])&&isset($_SESSION['hpass']))
 $MY_RULZ = are_logged();
if(!isset($MY_RULZ['state'])||$MY_RULZ['state']<1)
{
	header("Location: error404.php");
	exit(0);
}
else
{
	$con = new ProjectorConnect();
	$rarr = $con->ProjectorLastQuery("SELECT * FROM `".$DBASE['prefix']."FLZ` WHERE `fid`='".floor($_GET['id'])."'");
	if(isset($rarr[0]['fpath']))
	{
		$ext = explode(".", $rarr[0]['fpath']);
		$filename = $rarr[0]['fname'].'.'.array_pop($ext);
		func_download_file("files/f".$rarr[0]['fpath'], $filename);
	}
	else
	{
		header("Location: error404.php");
		exit(0);
	}
}
?>