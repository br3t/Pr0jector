<?php
include("config.php");
include("library.php");
//-----------------------
//   Запрет на кэширование
header("Expires: Mon, 7 May 1984 06:00:00 GMT");
header("Last-Modified".gmdate('D, d M Y H:i:s')." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");	//HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");//	HTTP/1.0
header("Content-type: text/html; charset=utf-8");
//-----------------------------------------
$MY_RULZ = array();
if(isset($_SESSION['login'])&&isset($_SESSION['hpass']))
 $MY_RULZ = are_logged();
if(!isset($MY_RULZ['uid'])&&$_SERVER['PHP_SELF']!=$SEC['proektor_path']."/index.php"&&$_SERVER['PHP_SELF']!=$SEC['proektor_path']."/registration.php"&&$_SERVER['PHP_SELF']!=$SEC['proektor_path']."/install.php"&&$_SERVER['PHP_SELF']!=$SEC['proektor_path']."/error404.php")
 header("Location: index.php");
//-----------------------------------------
// header string
$HEAD_STR = <<<HST
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$PAGES_TITLE}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="{$SEC['proektor_path']}/images/style.css" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" />
<script type='text/javascript' src='{$SEC['proektor_path']}/images/jquery-1.6.2.min.js'></script>
<script type='text/javascript' src='{$SEC['proektor_path']}/images/jquery.cookie.js'></script>
<script type='text/javascript' src='{$SEC['proektor_path']}/images/script.js'></script>
</head>
<body>
<div id='n1' class='notice_hide'><div class='notice_in1'><div class='notice_in2'></div></div></div>
<table width="100%" rules="none" border="0">
<tr class="logo"><td>&nbsp;</td></tr>
<tr><td>
HST;
//-------------------------------------------
//  online_users string
$uonline = show_uzers_online();
$ONLINE_USERS_STR = <<<OUS
</td></tr>
<tr><td><b>{$LF_other['uonline']}:</b> {$uonline}
OUS;
//-------------------------------------------
//  bottom string
$CYEAR = date("Y");
$BOTTOM_STR = <<<BST
</td></tr>
<tr><td>_________________________________<br />&copy; {$COMPANY_NAME}, {$SINCE_YEAR}-{$CYEAR}<br />
&copy; {$other['powered_by']} <b>Bret</b> (<a href="http://snk-games.ru">SNK games</a>) v0.{$SCR_VER}, 2009-{$CYEAR}
</td></tr>
</table>
</body>
</html>
BST;
//------------------------------------
//  show special message
 $emess = "";
 if(isset($_GET['mid']))
  if(is_numeric($_GET['mid']))
   if(isset($LF_mess[(int) $_GET['mid']]))
    $emess = $LF_mess[(int) $_GET['mid']];
$ERROR_MESSAGE = "";
if($emess!="")
 $ERROR_MESSAGE = '<noscript><p><div class="notice">'.$emess.'</div></p></noscript><script type="text/javascript">errstr="'.$emess.'";</script>';
//-------------------------
//  event calendar
$EVENTCALENDAR_STR = '';
// latest posts
$LATEST_POSTS = '';
if(isset($MY_RULZ['plushour']))
{
 $nowtime = time()+floor($MY_RULZ['plushour'])*3600;
 $EVENTCALENDAR_STR = '<div class="event_calendar">'.event_calendar(date('m', $nowtime), date('Y', $nowtime)).'</div>';
 $LATEST_POSTS = latest_posts();
}
//--------------------------------------------
//  login form or userblock  <p>{$emess}</p>
$REG_BUT = "";
if(!isset($MY_RULZ['uid']))
{
if($SEC['registration_code']=="")
 $REG_BUT = "<input type='button' value='".$formz['registr']."' onclick='self.location.replace(\"registration.php\");' />";
$LOGIN_FORM = <<<LFO
{$ERROR_MESSAGE}
<p><div class='eform'><form method='post' action='actions.php'><b>{$formz['sysin']}</b><br />
{$formz['login']}: <input type='text' name='login' /><br />
{$formz['pass']}: <input type='password' name='pass' /><br />
<input type='submit' name='send' value='{$LF_formz['submit']}' />
{$REG_BUT}
</form></div></p>
LFO;
}
else
{
 $nick_pr = infiltrtext($MY_RULZ['nick']);
 $adm_link = "";
 $pms_dolink = "";
 $pms = count_pms($MY_RULZ['uid']);
 if($pms)
  $pms_dolink = "?act=new";
 if($MY_RULZ['state']>2)
  $adm_link = "<tr><td>[<a href='admin.php'>{$LF_links['admin']}</a>]</td></tr>";
 $LOGIN_FORM = <<<LUB
 {$ERROR_MESSAGE}
<p><div class='eform'><table rules='none' border='0' align='center'>
<tr><td>{$LF_other['aloha']}, <b>{$nick_pr}</b></td></tr>
<tr><td><img src='images/avatar/a{$MY_RULZ['avatar']}' class='avatar' /></td></tr>
<tr><td>[<a href='tasklist.php'>{$LF_links['mtasks']}</a>]</td></tr>
<tr><td>[<a href='messages.php{$pms_dolink}'>{$LF_links['messages']}{$pms}</a>]</td></tr>
<tr><td>[<a href='files.php'>{$LF_links['myfiles']}</a>]</td></tr>
<tr><td>[<a href='userlist.php?id={$MY_RULZ['uid']}'>{$LF_links['mprofile']}</a>]</td></tr>
{$adm_link}
<tr><td>[<a href='actions.php?logout=1'>{$LF_links['logout']}</a>]</td></tr>
</table>
</form></div>
{$EVENTCALENDAR_STR}</p>
{$LATEST_POSTS}
LUB;
//-------------------------
//  menubar
$NOW = time()+(floor($MY_RULZ['plushour'])*3600);
$now_fdate = showftime($NOW);
$MENUBAR = <<<MB
<table width="100%" rules='none' border='0'>
<tr><td>[<a href='news.php' />{$LF_links['news']}</a>]
[<a href='projects.php' />{$LF_links['projects']}</a>]
[<a href='bugs.php' />{$LF_links['bugs']}</a>]
[<a href='events.php?last=1' />{$LF_links['events']}</a>]
[<a href='userlist.php' />{$LF_links['users']}</a>]
[<a href='groups.php' />{$LF_links['groups']}</a>]
<!-- [<a href='about.php' />{$LF_links['about']}</a>] //--></td>
<td style='text-align: right;'>{$LF_other['now']} {$now_fdate}</td></tr></table>
MB;
}
//-------------------------
//  begin work_place
function tplBeginWorkPlace($header)
{
 $resultString = <<<RSLT
 <h3>{$header}</h3>
 <table class='card'>
 <tr>
 <td>
RSLT;
 return $resultString;
}
?>