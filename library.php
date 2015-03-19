<?php
session_name("ProJector");
$SCR_VER = "110814";
if(!isset($_SESSION))
	session_start();
include("lib/strings.php");
include("lib/connect_class.php");
include("lib/subtask_class.php");
//------------------------------------------
//* global connection here
$DB = null; 
$REAL_ESCAPE = false;
if(function_exists('mysql_real_escape_string'))
		$REAL_ESCAPE = true;
//$REAL_ESCAPE = false;
//------------------------------------------
//  чисто отладочная функция :)
function wttemp($qwert)
{
	$re = fopen('waag.php', 'a+');
	$rezstr = $qwert;
	if(is_array($qwert))
		$rezstr = '<pre>'.print_r($qwert, true).'</pre>';
	fwrite($re, "<p><a name='J'></a><b>//-------------<br />\nFILE ".$_SERVER['PHP_SELF']." at ".date('d-m-Y, H:i (O)')." say:</b><br />\n ".$rezstr."</p>\n");
	fclose($re);
}
//----------------------------------------
//
function time_left($tt)
{
	global $LF_time;
	$rez = "";
	$mi = floor($tt/60);
	$ho = floor($mi/60);
	$da = floor($ho/24);
	$mo = floor($da/30);
	$ye = floor($da/365);
	if($ye>0)
		$rez = human_plural_form($ye, $LF_time['year'], '')." ".human_plural_form(($mo%12), $LF_time['month'], '')." ".human_plural_form(($da%30), $LF_time['day'], '');
	else if($mo>0)
		$rez = human_plural_form(($mo%12), $LF_time['month'], '')." ".human_plural_form(($da%30), $LF_time['day'], ''). " ".human_plural_form(($ho%24), $LF_time['hour'], '');
	else if($da>0)
		$rez = human_plural_form(($da%30), $LF_time['day'], ''). " ".human_plural_form(($ho%24), $LF_time['hour'], '')." ".human_plural_form(($mi%60), $LF_time['minute'], '');
	else
		$rez = human_plural_form(($ho%24), $LF_time['hour'], '')." ".human_plural_form(($mi%60), $LF_time['minute'], '');
	return $rez;
}
//----------------------------------------
//  метод кодирования пароля
function passcoding($base)
{
	global $SEC;
	return md5($base.$SEC['seed']);
}
//----------------------------------------
//   обработка формы регистрации
function try_register_form($arr)
{
	global $SEC, $formz, $DBASE, $LF_formz, $COMPANY_NAME, $LF_messz,$REAL_ESCAPE;
	$arr['error_messages'] = "";
	$count_regusers = 1;
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if(mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."UZRZ` WHERE 1=1";
		$all_users = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		$count_regusers = mysql_num_rows($all_users);
		while($row=mysql_fetch_array($all_users))
		{
			if($row['login']==$arr["login"])
			{
				$arr['error_messages'] .= $formz['usalready']."<br />";
				break;
			}
		}
	}
	else
		$arr['error_messages'] .= "Can't connect db".mysql_errno() . ": " . mysql_error();
	$pass = trim($arr["pass"]);
	$spass = trim($arr["spass"]);
	$arr = array_map("trim", $arr);
	if($REAL_ESCAPE)
		$arr = array_map("mysql_real_escape_string", $arr);
	else
		$arr = array_map("mysql_escape_string", $arr);
	if($arr["nick"]=="")
		$arr["nick"] =  $arr["login"];
	$arr['show_form'] = true;
	if((strlen($arr["login"])<1)||($arr["pass"]=="")||($arr["spass"]=="")||($arr["email"]==""))
		$arr['error_messages'] .= $formz['notallf']."<br />";
	if(strlen($arr["login"])<3)
		$arr['error_messages'] .= $formz['logshort']."<br />";
	if(strlen($pass)<6)
		$arr['error_messages'] .= $formz['passshort']."<br />"; 
	if($pass!=$spass)
		$arr['error_messages'] .= $formz['passdiff']."<br />";
	if($arr["icq"]!=""&&(!ereg("[0-9]{5,9}", $arr["icq"])))
		$arr['error_messages'] .= $formz['icqdig']."<br />";
	$arr['plushour'] = floor(trim($arr['plushour']));
	//  конец проверок 
	$nln_id = 0;
	if($arr['error_messages']=="") 
	{
		$arr['error_messages'] = "<p class='rwe'>".$formz['regready'];
		$uzr_rules = 1;
		$uzr_come = time();
		$uzr_hash = md5($uzr_come.$SEC['noise']);
		$uzr_pass = passcoding($pass);
		$ubdate = floor(compacttime($arr, 'ub'));
		if($SEC['check_email'])
		{
			$arr['error_messages'].= "<br />".$formz['emact'];
			$uzr_rules -= 2;
			$letter_body = $formz['activation_letter_body']."\nhttp://".$_SERVER['SERVER_NAME'].$SEC['proektor_path']."/actions.php?uzrcode=".$uzr_hash."\n____________________________\n".$formz['activation_letter_delim']."\n".$formz['login'].": ".$arr['login']."\n".$formz['pass'].": ".$pass."\n\n".$LF_messz['notice']['bestregards']." ".$COMPANY_NAME;
			$headers = 'From: br3t <bret.snk@gmail.com>';
			mail($arr["email"], $formz['activation_letter_subject'], $letter_body, $headers);
		}
		if($SEC['check_admin'])
		{
			$arr['error_messages'].= "<br />".$formz['admact'];
			$uzr_rules -= 1;
		}
		$arr['error_messages'] .= "</p>";
		$arr['show_form'] = false;
		// если первый - то админ
		$ustate = 3; 
		if($count_regusers>0)
			$ustate = $uzr_rules;  
		// зарегать его!
		if(mysql_select_db($DBASE['name']))
		{
			$que = "INSERT INTO `".$DBASE['prefix']."UZRZ` VALUES ('0', '{$arr['login']}', '{$uzr_pass}', '{$arr['nick']}', '{$arr['name']}', '{$arr['sname']}', '{$arr['surname']}', '{$arr['icq']}', '{$arr['email']}', '{$arr['jabber']}', '{$arr['hphone']}', '{$arr['mphone']}', '{$arr['country']}', '{$arr['city']}', '{$arr['adress']}', '{$ustate}', {$uzr_come}, '{$uzr_hash}', 1, '0.png', '{$arr['plushour']}', '{$ubdate}')";
			$rez = mysql_query($que, $db) or die("Error on registration:".$que.mysql_errno() . ": " . mysql_error());
			$que2 = "SELECT max(`uid`) FROM `".$DBASE['prefix']."UZRZ`";
			$rez2 = mysql_query($que2, $db) or die("On onln:".$que2.mysql_errno() . ": " . mysql_error());
			$rezrow = mysql_fetch_array($rez2, MYSQL_ASSOC);
			if($rezrow)
				$nln_id = $rezrow['max(`uid`)'];
		}
	}
	else
		$arr['error_messages'] = "<span class='rse'>".$arr['error_messages']."</span>";
	mysql_close($db);
	if(floor($nln_id)>0)
		iamonline($nln_id, 1);
	return $arr;
}
//-------------------
// активация по email
function try_activate($cd)
{
	global $DBASE;
	$activated = false;
	if(ereg("[0-9a-f]{32}", $_GET['uzrcode']))
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if(mysql_select_db($DBASE['name']))
		{
			$que = "UPDATE `".$DBASE['prefix']."UZRZ` SET state=state+2 WHERE (hash='".$cd."' AND state<0)";
			$activated = @mysql_query($que, $db);
		}
		mysql_close($db);
	}
	return $activated;
}
//----------------------
/** Отображает стандартно отформатированное время и дату
* @param $s int timestamp отображаемой даты
* @param $withhours int отображать часы и минуты
* @return htnl_string */
function showftime($s, $withhours=1)
{
	$rez = "";
	if($withhours==1)
		$rez .= date("G", $s)."<sup class='minutes'>".date("i", $s)."</sup>&nbsp;&nbsp;";
	$rez .= "<b>".date("j-m-Y", $s)."</b>";
	return $rez;
}
//----------------------
// проверка данных формы залогина - есть ли пользователь7
function try_login($try_l, $try_p)
{
	global $SEC, $DBASE, $REAL_ESCAPE;
	$rez_arr = Array('login'=>"", 'pass'=>"", 'error'=>-1);
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if(mysql_select_db($DBASE['name']))
	{
		if($REAL_ESCAPE)
		{
			$try_l = mysql_real_escape_string($try_l);
			$try_p = passcoding(mysql_real_escape_string($try_p));
		}
		else
		{
			$try_l = mysql_escape_string($try_l);
			$try_p = passcoding(mysql_escape_string($try_p));
		}
		$que = "SELECT * FROM `".$DBASE['prefix']."UZRZ` WHERE (login='{$try_l}' AND pass='{$try_p}')";
		$rezalt = mysql_query($que, $db);
		$rezrow = mysql_fetch_array($rezalt, MYSQL_ASSOC);
		if($rezrow)
		{
			if((int) $rezrow['state']>0)
			{
				$rez_arr['login'] = $try_l;
				$rez_arr['pass'] = $try_p;
			}
			else
				$rez_arr['error'] = 4;
		}
		else
			$rez_arr['error'] = 3;
	}
	else
		$rez_arr['error'] = 2;
	mysql_close($db);
	return $rez_arr;
}
//------------------------
// are logged7
function are_logged()
{
	global $SEC, $DBASE, $LF_mess, $REAL_ESCAPE;
	$fuzer = array('error'=>"");
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if(mysql_select_db($DBASE['name']))
	{
		if($REAL_ESCAPE)
		{
			$tryl = mysql_real_escape_string($_SESSION['login']);
			$tryp = mysql_real_escape_string($_SESSION['hpass']); 
		}
		else
		{
			$tryl = mysql_escape_string($_SESSION['login']);
			$tryp = mysql_escape_string($_SESSION['hpass']); 
		}
		$que = "SELECT * FROM `".$DBASE['prefix']."UZRZ` WHERE login='{$tryl}'";
		$rez = mysql_query($que, $db) or die("On session trying:".$que.mysql_errno() . ": " . mysql_error());
		$rezarr = mysql_fetch_array($rez);
		if(count($rezarr)>0)
		{
			if($tryp==passcoding($rezarr['pass']))
				$fuzer += $rezarr;
			else
				$fuzer['error'] = "<p class='rse'>".$LF_mess[5]."!</p>";   
		}
		else
			$fuzer['error'] = $LF_mess[3];
	}
	else
		$fuzer['error'] = $LF_mess[2];
	@mysql_close($db);
	if(isset($fuzer['uid']))
		iamonline($fuzer['uid']);
	return $fuzer; 
}
//-------------------------
//  пробуем подключиться к бд при инсталляции скрипта
function tryconn($arr, $qwa, $dbpre)
{
	global $LF_install, $LF_other, $LF_mess;
	$dbconn = @mysql_connect($arr['dbhost'], $arr['dblogin'], $arr['dbpass']);
	if(!$dbconn)
		return $LF_install['motto']."<p class='rse'>".$LF_install['nodbconnect']." ".$LF_mess[5]." - имени хоста, имени пользователя и пароля.<br /><a href='?step=1'>".$LF_other['back']."</a></p>";
	else
	{
		if(trim($arr['dbname'])=='')
			return $LF_install['motto']."<p class='rse'>".$LF_install['nodbconnect']." ".$LF_mess[5]." - имени базы данных.<br /><a href='?step=1'>".$LF_other['back']."</a></p>";
		$dbanswer = mysql_select_db($arr['dbname'], $dbconn);
		if(!$dbanswer)
		{
			$dbanswer = mysql_query("CREATE DATABASE `".$arr['dbname']."`", $dbconn);
			$dbanswer = mysql_select_db($arr['dbname'], $dbconn);
		}
		$logg = "";
		if($dbanswer)
			for($i=0; $i<count($qwa); $i++)
			{
				$dbanswer = mysql_query($qwa[$i], $dbconn) or wttemp("dberror[".$i."] = ".mysql_errno() . ": " . mysql_error());
				$logg .= mysql_errno() . ": " . mysql_error()."<br />";
			} 
		@mysql_close($dbconn); 
		if(!$dbanswer)
		{
			return $LF_install['motto']."<p class='rse'>".$LF_install['nodbconnect']."<br />$logg<br /> ".$LF_mess[5].".<br /><a href='?step=1'>".$LF_other['back']."</a></p>";
		}
		else
		{
			$fp = fopen("connection.php", "w");
			fwrite($fp, "<?php\n//----------------------\n// DATABASE PARAMETRS\n// host\n\$DBASE['adress'] = '".$arr['dbhost']."';\n// login \n\$DBASE['login'] = '".$arr['dblogin']."'; \n// password \n\$DBASE['pass'] = '".$arr['dbpass']."';\n // dbname\n \$DBASE['name'] = '".$arr['dbname']."';\n // prefix\n \$DBASE['prefix'] = '".$dbpre."';\n ?>");
			fclose($fp);
			return $LF_install['complete'];
		}
	}
}
//-----------------------------------------------------
//   отображение информации о пользователе или списка пользователей
function show_user_info($_id)
{
	global $DBASE, $LF_links, $LF_mess, $LF_other, $MY_RULZ, $odd_effect;
	$rez = $LF_mess[2];
	$ran = time();
	$db = new ProjectorConnect();
	$rezarr3 = $db->ProjectorQuery("SELECT * FROM `".$DBASE['prefix']."NLN` WHERE oid='".floor($_id)."'");
	if(isset($rezarr3[0]['odate']))
		$lastactdate = floor($rezarr3[0]['odate']);
	else
		$lastactdate = 0;
	$rezarr = $db->ProjectorQuery("SELECT * FROM `".$DBASE['prefix']."UZRZ` WHERE uid='".floor($_id)."'");
	if(isset($rezarr[0]))
	{
		$db->ProjectorDisconnect();
		$rezarr = array_map('infiltrtext', $rezarr[0]);
		//  отображаем инфо о пользователе
		if($_id==$MY_RULZ['uid'])
			$rez = "<h3>".$LF_links['mprofile']."</h3>";
		else
			$rez = "<h3>".$LF_links['profile']."</h3>";
		$rez .= "<table class='card'><tr style='vertical-align: top;'><td width='70px'><img src='images/avatar/a{$rezarr['avatar']}'  class='avatar' /></td><td><dl><dt><b>{$rezarr['name']} <a href='#' ".show_status($rezarr['state']).">{$rezarr['nick']}</a> {$rezarr['sname']} {$rezarr['surname']}</b></dt>";
		if($MY_RULZ['state']>2)
			$rez.="<dt>".$LF_other['registered'].": ".showftime($rezarr['come'])."</dt><dt>".$LF_other['lastactivity'].": ".showftime($lastactdate+(floor($MY_RULZ['plushour'])*3600))."</dt>";
		if($_id!=$MY_RULZ['uid']) 
		{
			$NOW = time()+(floor($rezarr['plushour'])*3600);
			$rez.="<dt><img src='images/icons/clock.gif' alt='home' class='smallicon' /> ".showftime($NOW)."</dt>";
		}
		if($rezarr['ubdate']!=0)
			$rez.="<dt><img src='images/icons/hbirth.gif' alt='Birthday' class='smallicon' /> ".showftime(floor($rezarr['ubdate']), 0)."</dt>";   
		$rez.="<dt><img src='images/icons/email.gif' alt='Email' class='smallicon' /> ".$rezarr['email']."</dt>"; 
		if($rezarr['icq'])
			$rez.="<dt><img border='0' src='http://wwp.icq.com/scripts/online.dll?img=5&icq={$rezarr['icq']}&ran={$ran}' alt='ICQ' width='18px' height='18px' /> {$rezarr['icq']}</dt>";
		if($rezarr['jabber'])
			$rez.="<dt><img src='images/icons/jabber.gif' alt='Jabber' class='smallicon' /> {$rezarr['jabber']}</dt>"; 
		if($rezarr['hphone'])
			$rez.="<dt><img src='images/icons/hphone.gif' alt='Hphone' class='smallicon' /> + ".$rezarr['hphone']."</dt>";
		if($rezarr['mphone'])
			$rez.="<dt><img src='images/icons/mphone.gif' alt='mphone' class='smallicon' /> + ".$rezarr['mphone']."</dt>";
		if($rezarr['country'])
			$rez.="<dt><img src='images/icons/home.gif' alt='home' class='smallicon' /> ".$rezarr['country'].(isset($rezarr['city'])?(", ".$rezarr['city']):"").(isset($rezarr['adress'])?(" (".$rezarr['adress'].")"):"")."</dt>";
		$rez .= "<dt>".$LF_other['usergroups'].": ".gen_user_groups($_id)."</dt><dt>"; 
		if($_id!=$MY_RULZ['uid'])
			$rez .= "<br />[<a href='messages.php?act=send&to=".$_id."' />".$LF_other['sendamess']."</a>]<br />[<a href='tasklist.php?user=".$_id."' />".$LF_other['userstask']."</a>]";
		if($_id!=$MY_RULZ['uid'])
			$rez .= "<br />[<a href='files.php?user=".$_id."'>{$LF_links['files']} {$LF_other['usera']}</a>]";   
		if($_id==$MY_RULZ['uid']||$MY_RULZ['state']>2)
			$rez .= "<br />[<a href='edit.php?user=".$_id."' />".$LF_other['editprofile']."</a>]";
		$rez .= "</dt></dl></td></tr></table>";
	}
	else
	{
		// отображаем список пользователей
		$rez = "<h3>".$LF_links['users']."</h3>\n".$odd_effect."\n<table class='form' rules='none' border='0' width='100%' cellspacing='0'>";
		$rezarr = $db->ProjectorLastQuery("SELECT * FROM `".$DBASE['prefix']."UZRZ` ORDER BY `nick` ASC");
		for($i=0; $i<count($rezarr); $i++)
		{
			$rez .= "<tr><td> </td><td>".infiltrtext($rezarr[$i]['name'])." <a ".show_status($rezarr[$i]['state'])." href='userlist.php?id=".$rezarr[$i]['uid']."'>".infiltrtext($rezarr[$i]['nick'])."</a> ".infiltrtext($rezarr[$i]['surname'])."</td></tr>";
		}
		$rez .= "</table>";
	}
	return $rez; 
}
//----------------------
//   обновить активность
function iamonline($iid, $firsttime=0)
{
	global $DBASE;
	$rezq = false;
	$now = time();
	$db = new ProjectorConnect();
	if($firsttime==1)
		$rezarr = $db->ProjectorInsUpdQuery("INSERT INTO `".$DBASE['prefix']."NLN` VALUES (".floor($iid).", ".time().")");
	else
		$rezarr = $db->ProjectorInsUpdQuery("UPDATE `".$DBASE['prefix']."NLN` SET odate=".time()." WHERE oid=".floor($iid));
	return $rezq;
}
//-----------------------
//   блок пользователей онлайн
function show_uzers_online()
{
	global $DBASE, $SONLINE, $LF_other;
	$rezq = false;
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT `uid`, `nick`, `state`, `odate` FROM `".$DBASE['prefix']."UZRZ`, `".$DBASE['prefix']."NLN` WHERE `".$DBASE['prefix']."NLN`.`oid`=`".$DBASE['prefix']."UZRZ`.`uid` ORDER BY `odate` DESC";
		$rez =  mysql_query($que, $db) or die("On OnlineShow trying:".$que.mysql_errno() . ": " . mysql_error());
		if($rez)
		{
			$rezq = "";
			$now = time();
			while($rez1 = mysql_fetch_assoc($rez))
			{
				$dtame = $now-floor($rez1['odate']);
				if($dtame<=$SONLINE)
				{
					if($rezq != "")
						$rezq .= ", ";
					$tt = (floor($dtame/60)).":".($dtame%60);
					$rezq .= "<a href='userlist.php?id=".$rez1['uid']."'".show_status($rez1['state']).">".infiltrtext($rez1['nick'])."</a> <em title='".$LF_other['uptime'].$tt."'>(".$tt.")</em>";
				}
			}
		}
	}
	@mysql_close($db); 
	return $rezq;
}
//----------------------------------
//   вывод статуса
function show_status($bI)
{
	global $LF_STATS;
	if(floor($bI)<1)
		return "class='user0' alt='".$LF_STATS[$bI+2]."' title='".$LF_STATS[$bI+2]."'";
	else
		return "class='user".($bI)."' alt='".$LF_STATS[$bI+2]."' title='".$LF_STATS[$bI+2]."'";
}
//----------------------------------
//  возвращает пользователя по id; $rez['uid']=='' если юзера не существует
function user_exist($id)
{
	global $DBASE;
	$rez = array('uid'=>'', 'nick'=>'nobody', 'state'=>'0');
	if($id==0)
		$rez = array('uid'=>'', 'nick'=>'ROBOT', 'state'=>'3');
	else
	{ 
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."UZRZ` WHERE `".$DBASE['prefix']."UZRZ`.`uid`=".floor($id);
			$qrez =  mysql_query($que, $db);
			if($qrez1 = mysql_fetch_assoc($qrez))
				if($qrez1['uid']>0)
					$rez = $qrez1;
		}
	} 
	return $rez;
}
//-----------------------------------
//   отображает форму редактирования профиля
function show_edit_profile_form($u9, $edfielsd)
{
	// u9=0 при регистрации; u9=1 при редактировании пользователя 
	global $DBASE, $formz, $ASK_SNAME, $SEC, $LF_formz, $LF_links, $MY_RULZ, $LF_STATS, $AV_MAX_WEIGHT, $LF_other;
	$edfielsd = array_map('infiltrtext', $edfielsd);
	$reg_code = $SEC['registration_code']?("?code=".$SEC['registration_code']):"";
	$SNAME_FIELD = "";
	if($ASK_SNAME)
		$SNAME_FIELD = '<tr><td class="rig">'.$formz["sname"].'</td><td><input type="text" size="15" name="sname"  value="'.$edfielsd['sname'].'" /></td></tr>';
	$curtime = date('H:i:s');
	$bdate = 0;
	if($u9==1)
		$bdate = floor($edfielsd['ubdate']);
	else if(isset($edfielsd['ubday']))
		$bdate = compacttime($edfielsd, 'ub');
	$bdate_form = gen_time_edit_form($bdate, 'ub', 0); 
	if($u9==0)
		$rez_form = "<form method='post' action='".$_SERVER['PHP_SELF'].$reg_code."'>";
	else
		$rez_form = "<h3>".$LF_links['editprofile']." <a href='userlist.php?id=".$edfielsd['uid']."' ".show_status($edfielsd['state']).">".$edfielsd['nick']."</a></h3>";
	if($u9==0)
		$rez_form.="<form method='post' action='".$_SERVER['PHP_SELF'].$reg_code."'>";
	else
		$rez_form.="<form method='post' action='actions.php'  enctype='multipart/form-data'>";
	$rez_form.= "<table width='600px' class='form' cellspacing='5px' cellpadding='5px'>";
	if($u9==0) 
		$rez_form.= "<tr><td colspan='2' class='cent'>{$formz['reqfields']}</td></tr>\n<tr><td  class='rig'>{$formz['login']}<sup class='reqf'>*</sup></td><td><input type='text' size='15' name='login' value='{$edfielsd['login']}' /></td></tr>\n<tr><td class='rig'>{$formz['pass']}<sup class='reqf'>*</sup></td><td><input type='password' size='15' name='pass' /></td></tr>\n<tr><td class='rig'>{$formz['spass']}<sup class='reqf'>*</sup></td><td><input type='password' size='15' name='spass' /></td></tr>";
	else
		$rez_form.= "<input type='hidden' name='id' value='".$edfielsd['uid']."' /><tr><td class='rig'>{$formz['login']}</td><td>{$edfielsd['login']}</td></tr>";
	$rez_form.=<<<RFO
<tr><td width='50%' class='rig'>{$formz['nick']}</td><td><input type="text" size="15" name="nick" value="{$edfielsd['nick']}" /></td></tr>
<tr><td class='rig'>{$formz['name']}</td><td><input type="text" size="15" name="name" value="{$edfielsd['name']}" /></td></tr>
{$SNAME_FIELD}
<tr><td class='rig'>{$formz['surname']}</td><td><input type="text" size="15" name="surname" value="{$edfielsd['surname']}" /></td></tr>
<tr><td class='rig'>{$LF_formz['birthdate']}</td><td>{$bdate_form}</td></tr>
<tr><td class='rig'>{$formz['icq']}</td><td><input type="text" size="9" maxlength="9" name="icq" class="digit" value="{$edfielsd['icq']}"  /></td></tr>
RFO;
if($u9==0)
{
	$rez_form.= "<tr><td class='rig'>{$formz['email']}<sup class='reqf'>*</sup></td><td><input type='text' size='15' name='email' value='{$edfielsd['email']}' /></td></tr>";
	$smart_plus_hour = "\n<script type='text/javascript'>\nvar serv_h = ".floor(date('H')).";\nvar user_h = new Date().getHours();\n//alert(user_h-serv_h);\ndocument.write('<input type=\"text\" id=\"plushour\" name=\"plushour\" class=\"digit\" cols=20 value=\"'+(user_h-serv_h)+'\" />');\n</script>";
	$chavatar = "";
}
else
{
	$smart_plus_hour = "<input type='text' id='plushour' name='plushour' class='digit' cols='20' value='".$edfielsd['plushour']."' />";
	$agr = floor($AV_MAX_WEIGHT/1024).".".($AV_MAX_WEIGHT%1024);
	$chavatar = "<tr><td class='rig'>{$LF_formz['chavatar']} ({$LF_other['mstbenomuch']} {$agr} Kb)</td><td><input type='file' name='avatar' /></td></tr>";
}
	$rez_form.=<<<RFO2
	<tr><td class='rig'>{$LF_formz['jabber']}</td><td><input type="text" size="15" name="jabber" value="{$edfielsd['jabber']}" /></td></tr>
<tr><td class='rig'>{$formz['hphone']}</td><td>+<input type="text" size="17" maxlength="17" name="hphone" value="{$edfielsd['hphone']}"   /></td></tr>
<tr><td class='rig'>{$formz['mphone']}</td><td>+<input type="text" size="17" maxlength="17" name="mphone" value="{$edfielsd['mphone']}"   /></td></tr>
<tr><td class='rig'>{$formz['country']}</td><td><input type="text" size="15" name="country" value="{$edfielsd['country']}" /></td></tr>
<tr><td class='rig'>{$formz['city']}</td><td><input type="text" size="15" name="city" value="{$edfielsd['city']}" /></td></tr>
<tr><td class='rig'>{$formz['adress']}</td><td><textarea name="adress" cols="20" rows="5">{$edfielsd['adress']}</textarea></td></tr>
$chavatar
<tr><td class='rig'>{$LF_formz['plushour']}{$curtime})</td><td>{$smart_plus_hour} {$LF_formz['h']}<input name="ubhour" value="23" type="hidden" /><input name="ubmin" value="59" type="hidden" /></td></tr>
RFO2;
if(isset($MY_RULZ['state'])&&$_SERVER['PHP_SELF']!='/registration.php')
	$rez_form.=<<<ADF
<tr><td class='rig'>{$LF_formz['status']}</td>
<td><b>{$LF_STATS[$edfielsd['state']+2]}</b></td></tr>
ADF;
if($u9!=0)
{
	$stat_list = "<select name='state'>";
		for($i=-2; $i<4; $i++)
			$stat_list.= "<option value='".$i."'".(($edfielsd['state']==$i)?" selected='selected'":"").">".$LF_STATS[$i+2]."</option>\n";
	$stat_list.="</select>";
	$rez_form.= "<tr><td colspan='2' align='center'><a name='e1'></a><a class='bI' href='#e1'><b>[";
	if($MY_RULZ['state']>2)
		$rez_form .= $LF_formz['ediem_adm']."]</b></a><div class='hint'><table class='form'>";
	else
		$rez_form .= $LF_formz['ediem']."]</b></a><div class='hint'><table class='form'>";
	if($MY_RULZ['uid']==$edfielsd['uid'])
		$rez_form .= "<tr><td>{$formz['pass']}<sup class='reqf'>*</sup></td><td><input type='password' size='15' name='oldpass' /></td></tr>";
	if($MY_RULZ['state']>2)
		$rez_form .= "<tr><td class='rig'>{$LF_formz['status']}</td><td>{$stat_list}</td></tr>";
	$rez_form .="<tr><td class='rig'>{$formz['email']}</td><td><input type='text' size='15' name='email' value='{$edfielsd['email']}' /></td></tr><tr><td class='rig'>{$LF_formz['npass']}</td><td><input type='password' size='15' name='npass' value='' /></td></tr><tr><td class='rig'>{$LF_formz['npass2']}</td><td><input type='password' size='15' name='npass2' value='' /></td></tr>";
		if($MY_RULZ['uid']==$edfielsd['uid'])
		$rez_form .= "<tr><td colspan='2' class='cent'>{$LF_formz['oldpassreq']}</td></tr>";
	$rez_form .= "</table></div></td></tr>"; 
}
if($u9==0)
	$rez_form.="<tr><td colspan='2' class='cent'><input type='submit' name='registration' value='".$LF_formz['letsreg']."' /></td></tr></table></form>";
else
	$rez_form.="<tr><td colspan='2' class='cent'><input type='button' value='".$LF_other['quitnosave']."' onclick='window.history.back(1);' /> <input type='submit' name='saveprofile' value='".$LF_formz['savechanges']."' /></td></tr></table></form>";
return $rez_form;
}
//-------------------------------------
//   сохранение изменений профиля
function save_profile_changes($arr)
{
	global $DBASE, $MY_RULZ, $AV_MAX_WEIGHT,$REAL_ESCAPE;
	if($MY_RULZ['state']<3&&$MY_RULZ['uid']!=$arr['id'])
	{
		header("Location: userlist.php?mid=7");
		exit(0);
	} 
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		if($REAL_ESCAPE)
			$arr = array_map("mysql_real_escape_string", $arr);
		else
			$arr = array_map("mysql_escape_string", $arr);
		$arr = array_map("trim", $arr);
		$arr['icq'] = floor($arr['icq']);
		$arr['hphone'] = floor($arr['hphone']);
		$arr['mphone'] = floor($arr['mphone']);
		$arr['plushour'] = floor($arr['plushour']);
		if($arr['plushour']<-12||$arr['plushour']>12)
			$arr['plushour'] = 0;
		$set_str = "";
		$arr['ubdate'] = compacttime($arr, 'ub');
		unset($arr['ubday'],$arr['ubmon'],$arr['ubyear'],$arr['ubhour'], $arr['ubmin']);
		foreach($arr as $k => $v)
		{
			if(isset($v)&&$k!='id'&&$k!='saveprofile'&&$k!='npass'&&$k!='npass2'&&$k!='email'&&$k!='oldpass'&&$k!='state')
			{
				if($set_str!="")
					$set_str.=", ";
				$set_str.="`".$k."`='".$v."'"; 
			}
		}
		if($MY_RULZ['uid']==$arr['id'])
		{
			if(!empty($arr['oldpass']))
			{
				$que = "SELECT `pass` FROM ".$DBASE['prefix']."UZRZ WHERE `uid`=".floor($arr['id']);
				$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
				if($qrez1 = mysql_fetch_assoc($qrez))
					if(passcoding($arr['oldpass'])==$qrez1['pass'])
					{
						if(strlen($arr['email'])>0)
							$set_str.=", `email`='".$arr['email']."'";
						if(strlen($arr['npass'])>0)
						{
							if($arr['npass']!=$arr['npass2'])
							{
								header("Location: userlist.php?mid=11");
								exit(0);
							}
							else
							{
								$set_str.=", `pass`='".passcoding($arr['npass'])."'";
							}
						}
					}
					else
					{
						header("Location: userlist.php?mid=10");
						exit(0);
					}
			}
		}
		else if($MY_RULZ['state']>2)
		{
			if(strlen($arr['email'])>0)
				$set_str.=", `email`='".$arr['email']."'";
			if(strlen($arr['npass'])>0)
				$set_str.=", `pass`='".passcoding($arr['npass'])."'";
			$set_str.=", `state`='".floor($arr['state'])."'"; 
		}
	}
		if(isset($_FILES['avatar']))
		{
			$av_type_arr = array("image/gif", "image/png", "image/jpeg");
			if(in_array($_FILES['avatar']['type'], $av_type_arr)&&($_FILES['avatar']['size']<$AV_MAX_WEIGHT))
			{
				$u9 = user_exist($arr['id']);
				$old_av = $u9['avatar'];
				$ras = explode("/", $_FILES['avatar']['type']);
				$pref = floor($arr['id'])."_".time().".".$ras[1];
				@unlink("images/avatar/a".$old_av);
				if(rename($_FILES['avatar']['tmp_name'], "images/avatar/a".$pref));
					$set_str.=", `avatar`='".$pref."' ";
			}
		} 
		$que = "UPDATE ".$DBASE['prefix']."UZRZ SET ".$set_str." WHERE `uid`=".floor($arr['id']);
		$rez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		mysql_close($db);
		if(!$rez)
		{ 
			header("Location: userlist.php?mid=2");
			exit(0);
		}
	return;  
}
//-------------------------------------
//  получение статуса по коду
function getstatebycode($code)
{
	global $LF_STATS;
	return $LF_STATS[floor($code)+2];
}
//------------------------------------
//   чистка данных перед выводом на экран
function infiltrtext($str)
{
	$str = stripslashes($str);
	$str = htmlspecialchars($str, ENT_QUOTES); 
	return $str;
}
//--------------------------------------
//    получить количество ЛС
function count_pms($tid)
{
	global $DBASE;
	$a = "";
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT `mid` FROM ".$DBASE['prefix']."MSSGZ WHERE `mtarget`=".floor($tid)." and `mget`=0";
		$rez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		$a = mysql_affected_rows();
		mysql_close($db);
		if($a>0)
			$a = " (".$a.")";
		else
			$a = "";  
	}
	return $a;
}
//------------------------------------------
//   форма нового ЛС
function show_new_message_form($ar)
{
	global $DBASE, $MY_RULZ, $LF_messz, $LF_mess;
	$uzfromstring = "<a href='userlist.php?id=".$MY_RULZ['uid']."' ".show_status($MY_RULZ['state']).">".infiltrtext($MY_RULZ['nick'])."</a>";
	$uztostring = "";
	$nouzer = 0;
	$t['avatar'] = $MY_RULZ['avatar'];
	if(!isset($ar['to']))
	{
	}
	else
	{
		$t = user_exist($ar['to']);
		if($t)
		{
			$t = array_map('infiltrtext', $t);
			$uztostring = gen_user_link($t)."<input type='hidden' name='mto' value='".floor($t['uid'])."' />";
		} 
		else
			$nouzer = 1;
	}
	$bb_buts = gen_bb_buts('body');
	$mass = <<<U
	<form action='actions.php' method='post'><table class='card'><tr style='vertical-align: top;'><td width='70px'><img src='images/avatar/a{$t['avatar']}' class='avatar' /></td>
	<td>
	<dl>
	<dt><b>{$LF_messz['from']}:</b> {$uzfromstring}</dt>
	<dt><b>{$LF_messz['for']}:</b> {$uztostring}</dt>
	<dt><b>{$LF_messz['subj']}:</b> <input type='text' name='subj' /></dt>
	<dt><b>{$LF_messz['body']}:</b></dt>
	<dt>$bb_buts</dt>
	<dt><textarea id='body' name='body'  cols='80' rows='10'></textarea></dt>
	<dt><input type='reset' value='{$LF_messz['reset']}' /> <input type='submit' value='{$LF_messz['send']}' name='sendm' /></dt>
	</dl></td></tr></table></form>
U;
if(!$nouzer)
	return $mass;
else
	return "<p>".$LF_mess[6]."</p>";
}
//---------------------------
//    отправка нового сообщения
function send_message($arr, $uid, $tryLetterFlag=1)
{
	global $DBASE, $MY_RULZ, $LF_messz, $ADMINSEMAIL,$REAL_ESCAPE;
	$author = user_exist($uid);
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		if($REAL_ESCAPE)
			$arr = array_map('mysql_real_escape_string', $arr);
		else
			$arr = array_map('mysql_escape_string', $arr);  
		if(trim($arr['subj'])=="")
			$arr['subj'] = $LF_messz['nosubj'];
		$que2 = "SELECT * FROM `".$DBASE['prefix']."UZRZ` WHERE `uid`='".floor($arr['mto'])."'";
		$qrez2 = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
		$qrezu2 = mysql_fetch_array($qrez2);
		if(isset($qrezu2['letters']))
		{
			$que = "INSERT INTO `".$DBASE['prefix']."MSSGZ` values ('0', '".$arr['subj']."', '".$arr['body']."', '".time()."', '0', '0', '".floor($uid)."', '".floor($arr['mto'])."')";
			$rez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			if(($qrezu2['letters']==1&&$tryLetterFlag==1)||($tryLetterFlag==0))
				mail($qrezu2['email'], $LF_messz['mail_newpm'][0], $LF_messz['mail_newpm'][1]." ".gen_user_link($author)." ".$LF_messz['mail_newpm'][2].".\n".$LF_messz['notice']['bestregards'], "From: ".$ADMINSEMAIL); 
		}
		mysql_close($db);
	} 
}
//------------------------------
//    отображение сообщений
function show_messages($par)
{
	global $DBASE, $MY_RULZ, $LF_messz, $LF_other;
	$rez = "<table width='100%' class='form'><tr><th width='30px'>&nbsp;</th><th>".(($par=='out')?$LF_messz['for']:$LF_messz['from'])."</th><th>".$LF_messz['subj']."</th><th>".$LF_messz['sended']."</th><th>&nbsp;</th></tr>";
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
			switch($par)
		{
				case 'in':
					$que = "SELECT * FROM `".$DBASE['prefix']."MSSGZ` WHERE `mtarget`='".$MY_RULZ['uid']."' and `mget`<3 ORDER BY `mdate` DESC";
				break;
				case 'out':
					$que = "SELECT * FROM `".$DBASE['prefix']."MSSGZ` WHERE `mauthor`='".$MY_RULZ['uid']."' and `mget`<>2 ORDER BY `mdate` DESC";
				break;
				case 'new':
				default:
					$que = "SELECT * FROM `".$DBASE['prefix']."MSSGZ` WHERE `mtarget`='".$MY_RULZ['uid']."' and `mget`='0' ORDER BY `mdate` DESC";
				break;
		}
		$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		mysql_close($db);
		$prrez = "";
		while($qrez1 = mysql_fetch_assoc($qrez))
		{
			if($par=='out')
			{
				$aut_arr = user_exist($qrez1['mtarget']);
				$aut = array_map('infiltrtext', $aut_arr);
			} 
			else
			{
				$aut_arr = user_exist($qrez1['mauthor']);
				$aut = array_map('infiltrtext', $aut_arr);
			}
			$todo = "";
			if($qrez1['mauthor']==$MY_RULZ['uid']&&$qrez1['mget']>0||$qrez1['mtarget']==$MY_RULZ['uid'])
				$todo = "<a href='actions.php?delmess=".$qrez1['mid']."'><img src='images/icons/delete.gif' alt='".$LF_other['delete']."' title='".$LF_other['delete']."' /></a>";
			$alt = "alt='".(($qrez1['mget']==0)?($LF_messz['unred']):($LF_messz['red']))."' title='".(($qrez1['mget']==0)?($LF_messz['unred']):($LF_messz['red']))."'";    
			$prrez .= "<tr class='cent'><td class='cent'><img src='images/icons/".(($qrez1['mget']==0)?("un"):(""))."read.gif' ".$alt." /></td><td class='cent'>".gen_user_link($aut)."</td><td><a href='?id=".$qrez1['mid']."'>".$qrez1['mtheme']."</a></td><td>".showftime($qrez1['mdate']+(floor($MY_RULZ['plushour'])*3600))."</td><td>".$todo."</td></tr>"; 
		}
		if($prrez=="")
			$rez .= "<tr><td colspan='5'><p class='rse'>".$LF_messz['nomssgz']."</p></td></tr>";
		else
			$rez .= $prrez;  
	} 
	$rez .= "</table>";
	return $rez;
}
//--------------------------------
//     отображение сообщения
function show_mess1($id)
{
	global $DBASE, $MY_RULZ, $LF_messz, $LF_other;
	$srez = "<span class='rse'>".$LF_messz['nomssgz']."</span>";
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."MSSGZ` WHERE `mid`='".floor($id)."'";
		$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_fetch_assoc($qrez);
		if($qrez1)
		{
			if($qrez1['mauthor']==$MY_RULZ['uid']&&$qrez1['mget']!=2||$qrez1['mtarget']==$MY_RULZ['uid']&&$qrez1['mget']!=3)
			{
				$qrez1= array_map('infiltrtext', $qrez1);
				$fr = array_map('infiltrtext', user_exist($qrez1['mauthor']));
				$tto = array_map('infiltrtext', user_exist($qrez1['mtarget']));
				$bot = "";
				if($fr['uid']!=$MY_RULZ['uid']&&$fr['uid']>0)
					$bot .= "[<a href='messages.php?act=send&to=".floor($fr['uid'])."' />".$LF_messz['answer']."</a>]";
				if($qrez1['mauthor']==$MY_RULZ['uid']&&$qrez1['mget']>0||$qrez1['mtarget']==$MY_RULZ['uid'])
					$bot .= " [<a href='actions.php?delmess=".floor($qrez1['mid'])."'>".$LF_other['delete']."</a>]";
				$srez = "<table class='card'><tr style='vertical-align: top;'><td width='70px'><img src='images/avatar/a".$MY_RULZ['avatar']."'  class='avatar' /></td>\n<td>\n<dl>\n<dt><b>".$LF_messz['sended'].":</b> ".showftime($qrez1['mdate']+(floor($MY_RULZ['plushour'])*3600))."</dt>\n <dt><b>".$LF_messz['from'].":</b> ".gen_user_link($fr)."</dt>\n<dt><b>".$LF_messz['for'].":</b> ".gen_user_link($tto)."</dt>
	<dt><b>".$LF_messz['subj'].":</b> ".$qrez1['mtheme']."</dt>\n<dt><b>".$LF_messz['body'].":</b></dt>\n<dt class='mbody'>".parse_post($qrez1['mcontent'])."</dt><dt>".$bot."</dt></dl></td></tr></table>";
				// помечаем как прочитанное
				if($qrez1['mtarget']==$MY_RULZ['uid']&&$qrez1['mget']==0)
				{
					$que = "UPDATE `".$DBASE['prefix']."MSSGZ` SET `mget`=1 WHERE `mid`='".floor($id)."'";
					$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
				}
			}
		}
		mysql_close($db);
	}
	return $srez;
}
//-------------------------
//   создание ссылки на пользователя из массива данных
function gen_user_link($arr)
{
	if(floor($arr['uid'])>0)
		return "<a href='userlist.php?id=".floor($arr['uid'])."' ".show_status($arr['state']).">".$arr['nick']."</a>";
	else
		return "<span ".show_status($arr['state']).">".$arr['nick']."</span>";
}
//-------------------------
//   удаление сообщения
function del_mess($id)
{
	global $DBASE, $MY_RULZ, $LF_messz;
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."MSSGZ` WHERE `mid`='".floor($id)."'";
		$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_fetch_assoc($qrez);
		if($qrez1['mauthor']==$MY_RULZ['uid']&&$qrez1['mget']==3||$qrez1['mtarget']==$MY_RULZ['uid']&&$qrez1['mget']==2)
		{
			$que2 = "DELETE FROM `".$DBASE['prefix']."MSSGZ` WHERE `mid`='".floor($id)."'";
			$qrez = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
		}
		else if($qrez1['mauthor']==$MY_RULZ['uid']&&$qrez1['mget']>0)
		{
			$que2 = "UPDATE `".$DBASE['prefix']."MSSGZ` SET `mget`=2 WHERE `mid`='".floor($id)."'";
			$qrez2 = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
		}
		else if($qrez1['mtarget']==$MY_RULZ['uid'])
		{
			$que2 = "UPDATE `".$DBASE['prefix']."MSSGZ` SET `mget`=3 WHERE `mid`='".floor($id)."'";
			$qrez2 = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
		}
	}
	mysql_close($db);
}
//------------------------------
//   отобразить проект
function show_project($id)
{
	global $DBASE, $LF_other, $LF_PRO_STT, $MY_RULZ, $LF_formz;
	$rez = "<p>[<a href='edit.php?project=-1' />".$LF_other['addpro']."</a>]";
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."PRJCTZ` WHERE `pid`='".floor($id)."'";
		$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_fetch_array($qrez);
		if($qrez1)
		{
			mysql_close($db);
			$qrez1 = array_map('infiltrtext', $qrez1);
			$ul = gen_user_link(array_map('infiltrtext', user_exist($qrez1['plider'])));
			$bdate = showftime($qrez1['pbdate']+(floor($MY_RULZ['plushour'])*3600));
			$edate = showftime($qrez1['pedate']+(floor($MY_RULZ['plushour'])*3600));
			$pdesc = parse_post($qrez1['pdesc']);
			$qrez1['pid'] = floor($qrez1['pid']);
			$show_tasks = show_task(-1, $qrez1['pid'], -1);
			$showBugs = showBugList($qrez1['pid']);
			$road_map = roadMap($qrez1['pid']);
			$show_files = show_file(-1, $qrez1['pid'], -1, -1);
			$show_posts = show_posts($qrez1['pid'], 0, (isset($_GET['page']))?($_GET['page']):(-1));
			$state = $LF_PRO_STT[floor($qrez1['pstate'])];
			if($qrez1['plider']==$MY_RULZ['uid']||$MY_RULZ['state']>2)
				$rez .= " [<a href='edit.php?project=".$qrez1['pid']."'>".$LF_other['editpro']."</a>]";
			if($MY_RULZ['state']>2)
				$rez .= " [<a href='javascript: if(confirm(\"".$LF_other['rlydelpro']."?\")) window.location.replace(\"actions.php?delproject=".$qrez1['pid']."\");'>".$LF_other['delproject']."</a>]";
			if($qrez1['plider']==$MY_RULZ['uid']||$MY_RULZ['state']>2)
				$rez .= " [<a href='edit.php?project=".floor($qrez1['pid'])."&task=-1'>".$LF_formz['addtask']."</a>]";
			$rez .= "</p>
			<table class='card' width='100%'>
			<tr><td><h4>{$qrez1['pname']}</h4>
			<div class='openCard'><span data-cardOpen='{$LF_other['openAll']}' data-cardClose='{$LF_other['closeAll']}'></span></div></td></tr>
			<tr><td>
				<table width='100%' class='form' cellpadding='3' cellspacing='0' rules='none' border='0'>
				<tr style='vertical-align: top;' class='list_even'>
					<td width='100px' rowspan='4'><img class='plogo' src='images/logos/p{$qrez1['plogo']}' alt='{$qrez1['pname']}' /></td><td width='25%'>{$LF_other['lider']}</td><td>{$ul}</td>
				</tr>
				<tr>
					<td>{$LF_other['state']}</td><td>{$state}</td>
				</tr>
				<tr class='list_even'>
					<td>{$LF_other['project_start']}</td><td>{$bdate}</td>
				</tr>
				<tr>
					<td>{$LF_other['project_end']}</td><td>{$edate}</td>
				</tr>
				<tr class='list_even'>
					<td colspan='3'><span class='notlink'>{ {$LF_other['project_desc']} }</span><div class='hint'>{$pdesc}</div></td>
				</tr>
				<tr>
					<td colspan='3'><span class='notlink'>{ {$LF_other['project_tasks']} }</span><div class='hint'>{$show_tasks}</div></td>
				</tr>
				<tr class='list_even'>
					<td colspan='3'><span class='notlink'>{ {$LF_other['project_roadmap']} }</span><div class='hint'>{$road_map}</div></td>
				</tr>
				<tr>
					<td colspan='3'>{$showBugs}</td>
				</tr>
				<tr class='list_even'>
					<td colspan='3'><span class='notlink'>{ {$LF_other['project_files']} }</span><div class='hint'>{$show_files}</div></td></tr></table></td></tr><tr><td>{$show_posts}</td>
				</tr>
			</table>";
		}
		else
		{
			$que2 = "SELECT * FROM `".$DBASE['prefix']."PRJCTZ`";
			$qrez2 = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
			$i = 1;
			$rez .= "</p><table width='100%' class='form' cellpadding='3' cellspacing='0' rules='none' border='0'><tr><th width='30px'>№</th><th>".$LF_other['project']."</th><th>".$LF_other['lider']."</th><th>".$LF_other['state']."</th></tr>";
			while($qrez12 = mysql_fetch_array($qrez2))
			{
				$u = array_map('infiltrtext', user_exist($qrez12['plider']));
				$rez .="<tr class='cent'><td>".$i."</td><td><a href='projects.php?id=".floor($qrez12['pid'])."'>".infiltrtext($qrez12['pname'])."</a></td><td>".gen_user_link($u)."</td><td>".$LF_PRO_STT[floor($qrez12['pstate'])]."</td></tr>";
				$i++;
			}
			$rez .= "</table>";
			mysql_close($db);
		}
	}
	return $rez;
}
//----------------------
//    show posts
function show_posts($id, $t=0, $page=-1, $last_ev=-1)
{
	global $MY_RULZ, $LF_other, $LF_messz, $DBASE, $POST_EDIT_TIME, $POSTS_ON_PAGE;
	$rez = "";
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."PSTZ` WHERE `ptask`='".floor($id)."' and `postintask`='".floor($t)."' ORDER BY `pdate`";
		$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_num_rows($qrez);
		if($qrez1>0)
		{
			$page = floor($page);
			$pages = ceil($qrez1/$POSTS_ON_PAGE);
			if($page<0||$page>$pages)
				$page = $pages;
			$plist = "";
			for($i=1; $i<=$pages;$i++)
			{
				if($i==$page)
					$plist .= "[".$i."] ";
				else
					$plist .= "[<a href='?id=".$id."&page=".$i."#posts' />".$i."</a>] ";    
			}
			$rez = "<script type='text/javascript'>$.cookie('posts_".floor($t)."_".floor($id)."', ".time().", { expires: 100 }); </script><table class='posts' cellpadding='3' cellspacing='0' rules='cols'><tr class='posts_even'><th colspan='2'><a name='posts'>{$LF_other['discussion']}</a>  {$plist}</th></tr>"; 
			$que = "SELECT * FROM `".$DBASE['prefix']."PSTZ` WHERE `ptask`='".floor($id)."' and `postintask`='".floor($t)."' GROUP BY `pdate` LIMIT ".(($page-1)*$POSTS_ON_PAGE).",".$POSTS_ON_PAGE;
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error()); //posts_odd
			$i=0;
			while($qrez1 = mysql_fetch_assoc($qrez))
			{
				$rules = "<br />";
				if($MY_RULZ['uid']!=$qrez1['pauthor'])
					$rules .= "<a href='messages.php?act=send&to=".$qrez1['pauthor']."'><img src='images/icons/unread.gif' title='".$LF_other['sendamess']."' /></a>";
				if($MY_RULZ['state']>1||($MY_RULZ['uid']==$qrez1['pauthor']&&(time()-$qrez1['pdate']<$POST_EDIT_TIME)))
					$rules .= " <a href='edit.php?post={$qrez1['pid']}'><img src='images/icons/edit.gif' alt='{$LF_other['editpost']}' title='{$LF_other['editpost']}' /></a> <a href='javascript: if(confirm(\"{$LF_other['rlydelpost']}\")) window.location.replace(\"actions.php?delpost={$qrez1['pid']}&t={$qrez1['ptask']}\");'><img src='images/icons/delete.gif' alt='{$LF_other['delete']}' title='{$LF_other['delete']}' /></a>";
				$qrez1 = array_map('infiltrtext', $qrez1);
				$ul = gen_user_link(array_map('infiltrtext', user_exist($qrez1['pauthor'])));
				$rez .= "<tr".(($i%2)?(" class='posts_even'"):(""))."><td width='160px' class='cent'><img src='images/icons/to.gif' class='smile' onclick='AddText(\"[user=".$qrez1['pauthor']."]\", \"post\");' />".$ul ."<br />@ ".showftime($qrez1['pdate']+(floor($MY_RULZ['plushour'])*3600)).$rules." <a href='#".$qrez1['pid']."'><img src='images/icons/link.gif' class='smile' title='".$LF_other['postlink']."' /></a></td><td><a name='".$qrez1['pid']."'></a>".parse_post($qrez1['pcontent'])."</td></tr>";
				$i++;
			}
			$arr['ptask'] = $id;
			$arr['postintask'] = $t;
			$rez .= "<tr><td colspan='2' class='cent'><span class='notlink'>{ {$LF_other['to_discuss']} }</span><div class='hint'>".show_edit_post_form(1, $arr)."</div></td></tr>";
		}
		else
		{
			$arr['ptask'] = $id;
			$arr['postintask'] = $t;
			$rez .= "<table class='posts' cellpadding='3' cellspacing='0' rules='cols'><tr><th colspan='2'><a name='posts'>{$LF_other['discussion']}</a></th></tr><tr><td colspan='2' class='cent'>{$LF_other['noposts']}</td></tr><tr><td colspan='2' class='cent'><span class='notlink'>{ {$LF_other['to_discuss']} }</span><div class='hint'>".show_edit_post_form(1, $arr)."</div></td></tr>";
		}
		mysql_close($db); 
	}
	$rez .= "</table>";
	return $rez;
}
//--------------------
//  save new post
function add_new_post($arr)
{
	global $MY_RULZ, $DBASE,$REAL_ESCAPE;
	if($MY_RULZ['state']>0)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			if($REAL_ESCAPE)
				$arr = array_map("mysql_real_escape_string", $arr);
			else
				$arr = array_map("mysql_escape_string", $arr);
			$arr['type'] = floor($arr['type']);
			$arr['tid'] = floor($arr['tid']);
			$que = "INSERT INTO `".$DBASE['prefix']."PSTZ` values ('0', '".$arr['post']." ', '".time()."', '".$arr['type']."', '".$arr['tid']."', '".$MY_RULZ['uid']."')";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		}
		mysql_close($db);
	}
}
//--------------------
//  edit post
function edit_post($arr)
{
	global $MY_RULZ, $DBASE, $POST_EDIT_TIME,$REAL_ESCAPE;
	if($MY_RULZ['state']>0)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			if($REAL_ESCAPE)
				$arr = array_map("mysql_real_escape_string", $arr);
			else
				$arr = array_map("mysql_escape_string", $arr);
			if($MY_RULZ['state']<2)
			{
				$que = "SELECT * FROM `".$DBASE['prefix']."PSTZ` WHERE `pid`='".floor($arr['pid'])."'";
				$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
				$qrez1 = mysql_fetch_assoc($qrez);
				if($qrez1)
				{
					if($MY_RULZ['uid']!=$qrez1['pauthor']||(time()-$qrez1['pdate']>$POST_EDIT_TIME))
						return "id=".floor($qrez1['ptask'])."&mid=8";
				} 
				else
					return "id=0&mid=8";
			}
			$que = "UPDATE `".$DBASE['prefix']."PSTZ` SET `pcontent`='".$arr['post']." ' WHERE `pid`='".floor($arr['pid'])."'";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			if($qrez)
				return "id=".floor($arr['tid'])."&mid=9";
			else
				return "id=".floor($arr['tid'])."&mid=8";
		} 
	}
}
//---------------------
// удалить пост
function del_post($arr)
{
	global $MY_RULZ, $DBASE, $POST_EDIT_TIME;
	$retu = "";
	if($MY_RULZ['state']>0)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			if($MY_RULZ['state']<2)
			{
				$que = "SELECT * FROM `".$DBASE['prefix']."PSTZ` WHERE `pid`='".floor($arr['delpost'])."'";
				$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
				$qrez1 = mysql_fetch_assoc($qrez);
				if($MY_RULZ['uid']!=$qrez1['pauthor']||(time()-$qrez1['pdate']>$POST_EDIT_TIME))
					return "&id=".$arr['t']."&mid=8";
			}
			$que = "DELETE FROM `".$DBASE['prefix']."PSTZ` WHERE `pid`='".floor($arr['delpost'])."'";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			mysql_close($db);
			return "&id=".$arr['t']."&mid=9";
		}
	}
}
//---------------------
// удаление постов в категории
function delete_posts($type, $tid)
{
	global $MY_RULZ, $DBASE;
	if($MY_RULZ['state']>1)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			if($MY_RULZ['state']>1)
			{
				$que = "DELETE FROM `".$DBASE['prefix']."PSTZ` WHERE `postintask`='".floor($type)."' AND`ptask`='".floor($tid)."'";
				$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			}
			mysql_close($db);
		}
	}
	return true;
}
//-------------------
//   форма редактирования поста
function show_edit_post_form($new=1, $arr)
{
	global $MY_RULZ, $LF_messz, $LF_other;
	$rez = "";
	$bb_buts = gen_bb_buts('post');
	if($new)
	{
		$ul = gen_user_link(array_map('infiltrtext', $MY_RULZ));
		$ptime = showftime(time()+(floor($MY_RULZ['plushour'])*3600));
		$pid = "";
		$but = "<input type='submit' value='".$LF_messz['send']."' name='addnewpost' />";
		$arr['pcontent'] = "";
		$newpostclass1 = "";
		$newpostclass2 = "";
	} 
	else
	{
		$ul = gen_user_link(array_map('infiltrtext', user_exist($arr['pauthor'])));
		$ptime = showftime($arr['pdate']+(floor($MY_RULZ['plushour'])*3600));
		$pid = "<input type='hidden' name='pid' value='".floor($arr['pid'])."' />";
		$but = "<input type='submit' value='".$LF_other['editpost']."' name='editpost' />";
		$newpostclass1 = "<table class='card'><tr><td>";
		$newpostclass2 = "</td></tr></table>";
	} 
	$rez .= $newpostclass1."<p><b>".$ul."</b> @ ".$ptime."<br/><form action='actions.php?id=".floor($arr['ptask'])."' method='post'><input type='hidden' name='tid' value='".floor($arr['ptask'])."' /><input type='hidden' name='type' value='".floor($arr['postintask'])."' />".$pid.$bb_buts."<textarea name='post' id='post' rows='10' cols='80' placeholder='o_O'>".infiltrtext($arr['pcontent'])."</textarea><br /><input type='button' value='".$LF_other['quitnosave']."' onclick='window.history.back(1);' /> <input type='reset' value='".$LF_messz['reset']."' /> ".$but."</form></p>".$newpostclass2;
	return $rez;
}
//--------------------
// show news
function show_news($id, $type, $page=-1, $last=0)
{
	global $MY_RULZ, $DBASE, $NEWS_ON_PAGE, $POSTS_ON_PAGE, $LF_other, $SYMBOLS_SHORT_NEWS, $LF_time;
	$rez = "";
	$sh_all = 1;
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		if($id>-1)
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."NWZ` WHERE `nid`='".floor($id)."' and `ntype`='".floor($type)."'";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			$qrez1 = mysql_fetch_assoc($qrez);
			if($qrez1)
				$sh_all = 0;
		}
		if($sh_all==0)
		{
			mysql_close($db);
			$qrez1 = array_map('infiltrtext', $qrez1);
			$ul = gen_user_link(array_map('infiltrtext', user_exist($qrez1['nauthor'])));
			$tstamp = floor($qrez1['ndate'])+(floor($MY_RULZ['plushour'])*3600);
			$cal = "<div class='calendar'>".date('d', $tstamp)."<br />".$LF_time['mon_short'][floor(date('m', $tstamp))%13-1]."</div>";
			$ndesc = parse_post($qrez1['ndesc']);
			$show_posts = show_posts(floor($qrez1['nid']), floor($type), (isset($_GET['page']))?($_GET['page']):(-1));
			if($type==3)
			{
				$sec_str = $LF_other['added'].": ".showftime($tstamp)."&nbsp;&nbsp;&nbsp;&nbsp;".$LF_other['author'].": ".$ul;
				$item = 'news';
			}
			else
			{
				$item = 'event';
				if(time()>$tstamp)
					$time_last = $LF_other['event_end']." (".date('d-m-y', $tstamp).")";
				else
					$time_last = $LF_other['event_for'].": ".time_left($tstamp-time())." (".$LF_other['evented']." ".date('d-m-y', $tstamp).")";
				$sec_str = $time_last."&nbsp;&nbsp;&nbsp;&nbsp;".$LF_other['author'].": ".$ul;
			}
			if($MY_RULZ['state']>1)
				$sec_str .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='edit.php?".$item."={$qrez1['nid']}'><img src='images/icons/edit.gif' alt='{$LF_other['edititems'.$type]}' title='{$LF_other['edititems'.$type]}' /></a> <a href='javascript: if(confirm(\"{$LF_other['rlydelitems'.$type]}\")) window.location.replace(\"actions.php?delnews={$qrez1['nid']}\");'><img src='images/icons/delete.gif' alt='{$LF_other['delete']}' title='{$LF_other['delete']}' /></a>";
			$rez .= "<table class='card'><tr><td class='rig smf bot'>".$sec_str."</td></tr><tr><td>".$cal."<h4> ".$qrez1['nname']."</h4></td></tr><tr><td class='mbody'>".$ndesc."</td></tr><tr><td>".$show_posts."</td></tr></table>"; 
		}
		else
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."NWZ` WHERE `ntype`='".floor($type)."'";
			if(floor($last)>0)
				$que = "SELECT * FROM `".$DBASE['prefix']."NWZ` WHERE `ntype`='".floor($type)."' and `ndate`>".time()." ORDER BY `ndate` ASC LIMIT 0,".$NEWS_ON_PAGE;
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			$qrez1 = mysql_num_rows($qrez);
			$page = floor($page);
			$pages = ceil($qrez1/$NEWS_ON_PAGE);
			if($page<1||$page>$pages)
				$page = 1;
			$plist = "";
			for($i=1; $i<=$pages;$i++)
			{
				if($i==$page)
					$plist .= "[".$i."] ";
				else
					$plist .= "[<a href='?page=".$i."' />".$i."</a>] ";    
			}
			if($qrez1<1)
				$rez .= $LF_other['no_items'.$type];
			else
			{
				$que = "SELECT * FROM `".$DBASE['prefix']."NWZ` WHERE `ntype`='".floor($type)."' ORDER BY `ndate` DESC LIMIT ".(($page-1)*$NEWS_ON_PAGE).",".$NEWS_ON_PAGE;
				if(floor($last)>0)
					$que = "SELECT * FROM `".$DBASE['prefix']."NWZ` WHERE `ntype`='".floor($type)."' and `ndate`>".time()." ORDER BY `ndate` ASC LIMIT 0,".$NEWS_ON_PAGE;
				$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
				$rez .= "<table rules='none' border='0' width='100%'><tr><td>".$LF_other['allitems'.$type].": ".$plist."</td></tr>";
				while($qrez1 = mysql_fetch_assoc($qrez))
				{
					$qrez1 = array_map('infiltrtext', $qrez1);
					$ul = gen_user_link(array_map('infiltrtext', user_exist($qrez1['nauthor'])));
					$tstamp = floor($qrez1['ndate'])+(floor($MY_RULZ['plushour'])*3600);
					$cal = "<div class='calendar'>".date('d', $tstamp)."<br />".$LF_time['mon_short'][floor(date('m', $tstamp))%13-1]."</div>";
					$que2 = "SELECT * FROM `".$DBASE['prefix']."PSTZ` WHERE `postintask`='".floor($type)."' and `ptask`= '".floor($qrez1['nid'])."'";
					$qrez2 = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
					//$comms = $LF_other['comments']." (".floor(mysql_num_rows($qrez2)).")";
					$comms = "(".human_plural_form(floor(mysql_num_rows($qrez2)), $LF_other['comment'], $LF_other['no']).")";
					$rez .= "<tr><td>";
					if($type==3)
					{
						$sec_str = $LF_other['added'].": ".showftime($tstamp)."&nbsp;&nbsp;&nbsp;&nbsp;".$LF_other['author'].": ".$ul;
						$item = 'news';
						$lin = "news.php?id=";
					}
					else
					{
						$tstamp = floor($qrez1['ndate'])+(floor($MY_RULZ['plushour'])*3600);
						if(time()>$tstamp)
							$time_last = $LF_other['event_end']." (".date('d-m-y', $tstamp).")";
						else
							$time_last = $LF_other['event_for'].": ".time_left($tstamp-time())." (".$LF_other['evented']." ".date('d-m-y', $tstamp).")";
						$sec_str = $time_last."&nbsp;&nbsp;&nbsp;&nbsp;".$LF_other['author'].": ".$ul;
						$item = 'event';
						$lin = "events.php?id=";
					}
					if($MY_RULZ['state']>1)
						$sec_str .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='edit.php?".$item."={$qrez1['nid']}'><img src='images/icons/edit.gif' alt='{$LF_other['edititems'.$type]}' title='{$LF_other['edititems'.$type]}' /></a> <a href='javascript: if(confirm(\"{$LF_other['rlydelitems'.$type]}\")) window.location.replace(\"actions.php?del{$item}={$qrez1['nid']}\");'><img src='images/icons/delete.gif' alt='{$LF_other['delete']}' title='{$LF_other['delete']}' /></a>";
					$rez .= "<table class='card'><tr><td class='rig smf bot'>".$sec_str."</td></tr><tr style='vertical-align: top;'><td>".$cal."<h4>".$qrez1['nname']."</h4></td></tr><tr><td class='mbody'>".parse_post(substr($qrez1['ndesc'], 0, $SYMBOLS_SHORT_NEWS))." <a href='".$lin.floor($qrez1['nid'])."'>. . .</a></td></tr><tr><td><a href='".$lin.floor($qrez1['nid'])."#posts' />".$comms."</a></td></tr></table>";
					$rez .= "</td></tr>";
				}
				mysql_close($db);
				$rez .= "</table>";
			}
		}
	}
	return $rez;
}
//----------------------
//   add||edit news form
function show_edit_news_form($id, $type)
{
	global $MY_RULZ, $DBASE, $LF_other, $LF_messz, $LF_time;
	$id = floor($id);
	$type = (($type==3)?3:4);
	$ul = gen_user_link(array_map('infiltrtext', $MY_RULZ));
	$nname = "";
	$ndecs = "";
	$rez = "<form method='post' action='actions.php'><table class='card'><tr><td>";
	if($type==3)
	{
		$sec_str = "<input type='hidden' name='ndate' value='".time()."' />".$LF_other['added'].": ".showftime(time()+(floor($MY_RULZ['plushour'])*3600))."&nbsp;&nbsp;&nbsp;&nbsp;".$LF_other['author'].": ".$ul;
		$but = "<input type='submit' name='nnews' value='".$LF_other['addnews']."' />";
	}
	else
	{
		$new_time = time()+(floor($MY_RULZ['plushour'])*3600);
		$sec_str = "<b>".$LF_other['eventdate']."</b>: ".gen_time_edit_form(time(), 'e');
		$but = "<input type='submit' name='nevent' value='".$LF_other['addevent']."' />";
	}
	if($id!=-1)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."NWZ` WHERE `nid`='".floor($id)."'";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			if(floor(mysql_num_rows($qrez))>0)
			{
				$qrez1 = mysql_fetch_assoc($qrez);
				$qrez1 = array_map('infiltrtext', $qrez1);
				$nname = $qrez1['nname'];
				$ndecs = $qrez1['ndesc'];
				if($type==3)
				{
					$sec_str = "<input type='hidden' name='ndate' value='".floor($qrez1['ndate'])."' />".$LF_other['added'].": ".showftime($qrez1['ndate']+(floor($MY_RULZ['plushour'])*3600))."&nbsp;&nbsp;&nbsp;&nbsp;".$LF_other['author'].": ".$ul;
					$but = "<input type='submit' name='nnews' value='".$LF_other['edititems3']."' />";
				} 
				else
				{
					$new_time = floor($qrez1['ndate'])+(floor($MY_RULZ['plushour'])*3600);
					$sec_str = "<b>".$LF_other['eventdate']."</b>: ".gen_time_edit_form($new_time, 'e');
					$but = "<input type='submit' name='nevent' value='".$LF_other['edititems4']."' />";
				}
			}
			mysql_close($db);
		}
	}
	$rez.= "<b>".$LF_other['itemtitle'.$type]."</b>: <input type='text' name='nname' value='".$nname."' maxlength='80' size='50' /></td></tr><tr><td>".$sec_str."</td></tr><tr><td><input type='hidden' name='id' value='".$id."' />".gen_bb_buts('ndesc')."<br /><textarea id='ndesc' name='ndesc' placeholder='o_O' cols='80' rows='15'>".$ndecs."</textarea></td></tr><tr><td><input type='button' value='".$LF_other['quitnosave']."' onclick='window.history.back(1);' /> <input type='reset' value='".$LF_messz['reset']."' /> ".$but."</td></tr></table></form>";
	return $rez;
}
//---------------------
//  save news/events
function edit_news($arr, $type)
{
	global $MY_RULZ, $DBASE, $LF_other, $LF_messz, $REAL_ESCAPE;
	$type = (($type==3)?3:4);
	if($type==3)
	{
		$rez = "news.php";
	} 
	else
	{
		$rez = "events.php";
		$arr['ndate'] = mktime(floor($arr['ehour']%24), floor($arr['emin']%60), 0, floor($arr['emon'])%12+1, floor($arr['eday']%32), floor($arr['eyear']))-(floor($MY_RULZ['plushour'])*3600);
	}
	$qrez2 = false;
	$qrez3 = false;
	if($MY_RULZ['state']>2)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			if($REAL_ESCAPE)
				$arr = array_map("mysql_real_escape_string", $arr);
			else
				$arr = array_map("mysql_escape_string", $arr);
			if($arr['id']!=-1)
			{
				$que = "SELECT * FROM `".$DBASE['prefix']."NWZ` WHERE `nid`='".floor($arr['id'])."'";
				$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
				if(floor(mysql_num_rows($qrez))>0)
				{
					$que2 = "UPDATE `".$DBASE['prefix']."NWZ` SET `ndate`='".floor($arr['ndate'])."', `nname`='".$arr['nname']."', `ndesc`='".$arr['ndesc']."' WHERE `nid`='".floor($arr['id'])."'";
					$qrez2 = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
					$rez .= "?id=".$arr['id']."&mid=9";
				}
			}
			if(!$qrez2)
			{
				$que3 = "INSERT INTO `".$DBASE['prefix']."NWZ` values ('0', '".$type." ', '".floor($arr['ndate'])."', '".$arr['nname']."', '".$arr['ndesc']." ', '".$MY_RULZ['uid']."')";
				$qrez3 = mysql_query($que3, $db) or die(mysql_errno() . ": " . mysql_error());
				$rez .= "?last=1&mid=9";
			}
			mysql_close($db);
		}
	}
	return $rez;
}
//------------------------------
//  news deleting
function del_item($id, $type)
{
	global $MY_RULZ, $DBASE, $LF_other, $LF_messz;
	$type = (($type==3)?3:4);
	if($type==3)
		$rez = "news.php";
	else
		$rez = "events.php?last=1";
	if($MY_RULZ['state']>2)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			$que = "DELETE FROM `".$DBASE['prefix']."NWZ` WHERE `nid`='".floor($id)."'";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			mysql_close($db);
		}
	}
	return $rez; 
}
//----------------------------------
//  генерация списка месяцев
function gen_month_list($p, $pref)
{
	global $LF_other, $LF_time;
	$p = floor($p)-1;
	$rez = "<select name='".$pref."mon'>";
	for($i=0; $i<12; $i++)
	{
		$rez .= "<option value='".$i."'".(($p==$i)?" selected='selected'":("")).">".$LF_time['mon_long'][$i]."</option>";
	}
	$rez .= "</select>";
	return $rez;
}
//----------------------------------
// генерация кнопок ББ-кодов
function gen_bb_buts($target)
{
	global $TAGS, $SMILES;
	$rez = <<<SC
<script type='text/javascript'>
	$(document).ready(function () {
		$("#$target").click(function(){
			storeCaret(this);
			});
		$("#$target").change(function(){
			storeCaret(this);
			});
		$("#$target").select(function(){
			TAOnSelect(event,'message');
			storeCaret(this);
			});  
});
	</script>
SC;
	for($i=0; $i<count($TAGS);$i++)
	{
		$rez .= "<input type='button' value='".$TAGS[$i]."' onclick='AddTag(\"[".$TAGS[$i]."]\",\"[/".$TAGS[$i]."]\", \"".$target."\");' />\n";
	}
	$rez .= "<input type='button' value='a' onclick='AddTag(\"[a=http:\/\/]\",\"[/a]\", \"".$target."\");' />\n";
	$rez .= "<input type='button' value='br' onclick='AddText(\"[br]\", \"".$target."\");' />\n";
	$rez .= "<input type='button' value='img' onclick='AddTag(\"[img]\",\"[/img]\", \"".$target."\");' />\n";
	$rez .= "<input type='button' value='hint' onclick='AddTag(\"[hint=ClickMe]\",\"[/hint]\", \"".$target."\");' /><br />\n";
	for($i=0; $i<count($SMILES);$i++)
		$rez .= " <img class='smile' src='images/smiles/s".$i.".gif' onclick='AddText(\"".$SMILES[$i]."\", \"".$target."\");' />\n";
	$rez .= "<br />"; 
	return $rez;
}
//-------------------------------------
//        отображение формы добавления/редактирования проекта
function show_edit_project_form($id)
{
	global $DBASE, $MY_RULZ, $LF_other, $LF_formz, $PLOGO_MAX_WEIGHT, $LF_messz;
	$rez = "";
	$epid = 0;
	$epname = $epdesc = "";
	$epbdate = time()+(floor($MY_RULZ['plushour'])*3600);
	$epedate = $epbdate+60;
	$eplider = $MY_RULZ['uid'];
	$epstate = 1;
	$eplogo = "0.png";
	$ul = gen_user_link(array_map('infiltrtext', user_exist($eplider)));
	$agr = floor($PLOGO_MAX_WEIGHT/1024).".".($PLOGO_MAX_WEIGHT%1024);
	$but = "<input type='submit' value='".$LF_other['addpro']."' name='newpro' />";
	if($id!=-1)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			$que2 = "SELECT * FROM `".$DBASE['prefix']."PRJCTZ` WHERE `pid`='".floor($id)."'";
			$qrez2 = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
			$arr2 = mysql_fetch_assoc($qrez2);
			if(isset($arr2['plider']))
			{
				if($arr2['plider']==$MY_RULZ['uid']||$MY_RULZ['state']>2)
				{
					$arr2 = array_map('infiltrtext', $arr2);
					$epid = $arr2['pid'];
					$epname = $arr2['pname'];
					$epdesc = $arr2['pdesc'];
					$epbdate = $arr2['pbdate'] + floor($MY_RULZ['plushour'])*3600;
					$epedate = $arr2['pedate'] + floor($MY_RULZ['plushour'])*3600;
					$epstate = $arr2['pstate'];
					$eplogo = $arr2['plogo'];
					//$ul = gen_user_link(array_map('infiltrtext', user_exist($arr2['plider'])));
					$ul = gen_user_list_in_group(0, $arr2['plider']);
					$but = "<input type='submit' value='".$LF_other['editpro']."' name='editpro' />";
				}
			} 
		}
	}
	$rez .= "<form method='post' action='actions.php' enctype='multipart/form-data'>\n<table class='card'>\n<tr style='vertical-align: top;'><td>\n<table width='100%' class='form' cellpadding='3' cellspacing='0' rules='none' border='0'>\n<tr><td width='300px'>".$LF_other['projectname']."</td><td><input type='text' name='pname' value='".$epname."' /><input type='hidden' name='pid' value='".$epid."' /></td></tr>\n<tr><td>".$LF_other['lider']."</td><td>".$ul."<in</td></tr>\n<tr><td>".$LF_other['state']."</td><td>".gen_pro_state_list($epstate)."</td></tr>\n<tr><td>".$LF_other['projectlogo']."</td><td><img src='images/logos/p".$eplogo."'  class='plogo'  alt='".$LF_other['projectlogo']."' /></td></tr><tr><td>".$LF_formz['chplogo']." (".$LF_other['mstbenomuch']." ".$agr." Kb)</td><td><input type='file' name='plogo' /></td></tr><tr><td>".$LF_other['project_start']."</td><td>".gen_time_edit_form($epbdate, 'pb')."</td></tr><tr><td>".$LF_other['project_end']."</td><td>".gen_time_edit_form($epedate, 'pe')."</td></tr><tr><td>".$LF_other['project_desc']."</td><td>".gen_bb_buts('pdesc')."<br /><textarea id='pdesc' name='pdesc' class='desc'>".$epdesc."</textarea></td></tr><tr><td colspan='2' class='cent'><input type='button' value='".$LF_other['quitnosave']."' onclick='window.history.back(1);' /> <input type='reset' value='".$LF_messz['reset']."' /> ".$but."</td></tr></table>\n</td></tr>\n</table>\n</form>";
	return $rez;
}
//-------------------------------------
//   сохранение нового/изменений проекта
function add_new_project($arr)
{
	global $MY_RULZ, $DBASE, $LF_other, $LF_PRO_STT, $PLOGO_MAX_WEIGHT, $REAL_ESCAPE;
	$rez = "";
	if($MY_RULZ['state']>0)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			if($REAL_ESCAPE)
				$arr = array_map("mysql_real_escape_string", $arr);
			else
				$arr = array_map("mysql_escape_string", $arr);
			if(trim($arr['pname'])=="")
				$arr['pname'] = $LF_other['projectnew'];
			if(trim($arr['pdesc'])=="")
				$arr['pdesc'] = $LF_other['project_desc'];
			$arr['pstate'] = floor($arr['pstate']);
			if($arr['pstate']<0||$arr['pstate']>=count($LF_PRO_STT))
				$arr['pstate'] = 1;
			$arr['pbdate'] = mktime(floor($arr['pbhour']%24), floor($arr['pbmin']%60), 0, floor($arr['pbmon']%12+1), floor($arr['pbday']%31), floor($arr['pbyear'])) - floor($MY_RULZ['plushour'])*3600;
			$arr['pedate'] = mktime(floor($arr['pehour']%24), floor($arr['pemin']%60), 0, floor($arr['pemon']%12+1), floor($arr['peday']%31), floor($arr['peyear'])) - floor($MY_RULZ['plushour'])*3600;
			if($arr['pedate']<=$arr['pbdate'])
				$arr['pedate'] = $arr['pbdate'] + 60;
			$arr['plogo'] = '';
				if(isset($_FILES['plogo']))
				{
					$av_type_arr = array("image/gif", "image/png", "image/jpeg");
					if(in_array($_FILES['plogo']['type'], $av_type_arr)&&($_FILES['plogo']['size']<$PLOGO_MAX_WEIGHT))
					{
						$old_lo = $arr['plogo'];
						$ras = explode("/", $_FILES['plogo']['type']);
						$pref = floor($arr['pid'])."_".time().".".$ras[1];
						if($old_lo!="0.png")
							@unlink("images/logos/p".$old_lo);
						if(rename($_FILES['plogo']['tmp_name'], "images/logos/p".$pref));
									$arr['plogo'] = $pref;
					}
				}
			if($arr['plogo']=='')
				$arr['plogo'] = "0.png"; 
			if($arr['pid']==0)
			{
				$que = "INSERT INTO `".$DBASE['prefix']."PRJCTZ` values ('0', '".$arr['pname']." ', '".$arr['pdesc']."', '".$arr['pbdate']."', '".$arr['pedate']." ', '".$MY_RULZ['uid']."', '".$arr['pstate']."', '".$arr['plogo']."')";
				$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
				mysql_close($db);
				$rez = "projects.php";
			}
			else
			{
				$que2 = "SELECT * FROM `".$DBASE['prefix']."PRJCTZ` WHERE `pid`='".floor($arr['pid'])."'";
				$qrez2 = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
				$arr2 = mysql_fetch_assoc($qrez2);
				if(isset($arr2['plider']))
				{
					$arr2 = array_map('infiltrtext', $arr2);
					if($arr2['plider']==$MY_RULZ['uid']||$MY_RULZ['state']>2)
					{
						if($arr['plogo']=="0.png")
							$chplogo = '';
						else
							$chplogo = ", `plogo`='".$arr['plogo']."'";
						$u = user_exist($arr['tmaker']);
						if($u['uid']>0)
							$pli = ", `plider`='".floor($arr['tmaker'])."'";
						else
							$pli = '';
						$que3 = "UPDATE `".$DBASE['prefix']."PRJCTZ` SET `pname`='".$arr['pname']."', `pdesc`='".$arr['pdesc']."', `pbdate`='".$arr['pbdate']."', `pedate`='".$arr['pedate']."'".$pli.", `pstate`='".$arr['pstate']."'".$chplogo." WHERE `pid`='".floor($arr['pid'])."'";
						$qrez3 = mysql_query($que3, $db) or die(mysql_errno() . ": " . mysql_error());
						$rez = "projects.php?id=".floor($arr['pid']);
					}
				}
				mysql_close($db);
			}
		} 
	}
	else
		$rez = "index.php";
	return $rez;
}
//-------------------------------------
//    удаление проекта
function del_project($id)
{
	global $MY_RULZ, $DBASE;
	if($MY_RULZ['state']>0)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."PRJCTZ` WHERE `pid`='".floor($id)."'";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			$qrez1 = mysql_fetch_assoc($qrez);
			if(mysql_affected_rows()>0)
			{
				if($qrez1['plider']==$MY_RULZ['uid']||$MY_RULZ['state']>2)
				{
					$que2 = "DELETE FROM `".$DBASE['prefix']."PRJCTZ` WHERE `pid`='".floor($id)."'";
					$qrez2 = mysql_query($que2, $db) or die(mysql_errno() . ": " . mysql_error());
				}
			}
			mysql_close($db);
		}
	} 
	return "projects.php";
}
//-------------------------------------
//  генерации формы редатирования даты
function gen_time_edit_form($somedate, $pref, $withHours=1)
{
	global $MY_RULZ, $LF_other, $LF_time;
	$rez = "";
	$rez .= "<input type='text' name='".$pref."day' class='day' size='2' maxlength='2' title='".$LF_time['day'][2]."' value='".floor(date('d', $somedate))."' />".gen_month_list(date('m', $somedate), $pref)."<input type='text' name='".$pref."year' class='digit' size='4' maxlength='4' title='".$LF_time['year'][2]."' value='".floor(date('Y', $somedate))."' />";
	if($withHours==1)
		$rez .= " @ <input type='text' name='".$pref."hour' class='hour' size='2' maxlength='2' title='".$LF_time['hour'][2]."' value='".floor(date('H', $somedate))."' /><sup><input class='minutes' type='text' name='".$pref."min' class='digit' size='2' maxlength='2' title='".$LF_time['minute'][2]."' value='".floor(date('i', $somedate))."' /></sup>";
	return $rez;
}
//-----------------------------------
//  список выбора пользователя
function gen_user_list_in_group($gid, $seluser)
{
	global $DBASE, $MY_RULZ;
	$rez = gen_user_link(array_map('infiltrtext', user_exist($seluser)))."<input type='hidden' name='tmaker' value='".floor($seluser)."' />";
	$db_1534 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_1534&&mysql_select_db($DBASE['name']))
	{
		$rarr = array();
		$que = "SELECT `uid`, `state`, `nick` FROM `".$DBASE['prefix']."UZRZ`, `".$DBASE['prefix']."UZRZinGRPZ` WHERE `".$DBASE['prefix']."UZRZ`.`state`>'0' AND `".$DBASE['prefix']."UZRZ`.`uid`=`".$DBASE['prefix']."UZRZinGRPZ`.`guid` AND `".$DBASE['prefix']."UZRZinGRPZ`.`ggid`=".floor($gid)."  ORDER BY `nick`";
		if($gid<1)
			$que = "SELECT `uid`, `state`, `nick` FROM `".$DBASE['prefix']."UZRZ` WHERE `state`>'0' ORDER BY `nick`";
		$qrez = mysql_query($que, $db_1534) or die(mysql_errno() . ": " . mysql_error());
		//wttemp("Line: ".__LINE__."; Error ".mysql_errno() . ": " . mysql_error());
		while($qrez1 = mysql_fetch_assoc($qrez))
			$rarr[] = $qrez1;
		mysql_close($db_1534);
		if(count($rarr)>0)
		{
			$rez = "<select name='tmaker' id='tmaker' class='user'>";
			for($i=0;$i<count($rarr);$i++)
			{
				$rez .= "<option ".(($rarr[$i]['uid']==$seluser)?("selected='selected'"):(''))." value='".floor($rarr[$i]['uid'])."' ".show_status($rarr[$i]['state'])." class='user'>".infiltrtext($rarr[$i]['nick'])."</option>";
			}
			$rez .= "</select>";
		}
	}
	return $rez;
}
//-----------------------------------
//   форма добавления/редактирования задачи/подзадачи
function show_edit_task_form($arr)
{
	global $MY_RULZ, $LF_other, $LF_mess, $LF_TASK_STT, $LF_formz, $LF_messz, $DBASE;
	$rez = $LF_mess[5];
	$uinpro = is_user_in_project($MY_RULZ['uid'], $arr['project']);
	if($uinpro||is_user_in_task($MY_RULZ['uid'], $arr['task']))
	{
		$tmaker = $MY_RULZ['uid'];
		$tname = $tdesc = "";
		$tpro = get_pro_link(floor($arr['project']));
		$tbdate = time() + floor($MY_RULZ['plushour'])*3600;
		$tedate = $tbdate + 60;
		$tstate = 1;
		$tid = 0;
		$tready = 0;
		$tgroup = 0;
		$tmtask = 0;
		$sec_str = $LF_other['projectname'];
		$but = "<input type='submit' name='new_task' value='".$LF_formz['addtask']."' />";
		if($arr['task']!=-1)
		{
			$db_1 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
			if($db_1&&mysql_select_db($DBASE['name']))
			{
				$que = "SELECT * FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($arr['task'])."'";
				$qrez = mysql_query($que, $db_1) or die(mysql_errno() . ": " . mysql_error());
				$qrez1 = mysql_fetch_assoc($qrez);
				mysql_close($db_1);
				if(isset($qrez1['tid']))
				{
					if($qrez1['tmaker']==$MY_RULZ['uid']&&$qrez1['tmtask']==0&&!$uinpro)
						return $rez;
					$tmaker = $qrez1['tmaker'];
					$tname = infiltrtext($qrez1['tname']);
					$tdesc = infiltrtext($qrez1['tdesk']);
					$tpro = get_pro_link(floor($qrez1['tpro']));
					$tbdate = floor($qrez1['tbdate']) + floor($MY_RULZ['plushour'])*3600;
					$tedate = floor($qrez1['tedate']) + floor($MY_RULZ['plushour'])*3600;
					$tstate = floor($qrez1['tstate']);
					$tid = floor($qrez1['tid']);
					$tready = floor($qrez1['tready']);
					$tgroup = floor($qrez1['tgroup']);
					$tmtask = floor($qrez1['tmtask']);
					if($tmtask>0)
					{ 
						$sec_str = $LF_other['mothertask'];
						$tpro .= ' -> '.get_task_link($qrez1['tmtask']);
					} 
					$but = "<input type='submit' name='new_task' value='".$LF_other['edittask']."' />";
				}
			}
		}
		$user_list = gen_user_list_in_group($tgroup, $tmaker);
		$rez = "<form action='actions.php' method='post'><table class='card'><tr style='vertical-align: top;'><td>\n<table width='100%' class='form' cellpadding='3' cellspacing='0' rules='none' border='0'><tr><td>".$sec_str."</td><td>".$tpro."<input type='hidden' name='tpro' value='".floor($arr['project'])."' /><input type='hidden' name='tmtask' value='".$tmtask."' /></td></tr><tr><td>".$LF_other['taskname']."</td><td><input type='text' name='tname' value='".$tname."' /></td></tr><tr><td>".$LF_other['taskgroup']."</td><td>".gen_groups_list($tgroup)."</td></tr><tr><td>".$LF_other['maker']."</td><td><span id='uslist'>".$user_list."</span></td></tr><tr><td>".$LF_other['state']."</td><td>".gen_list_from_array($LF_TASK_STT, floor($tstate), 'tstate')."</td></tr><tr><td>".$LF_other['taskready']."</td><td><input type='text' size='3' maxlength='3' name='tready' value='".$tready."' />%</td></tr><tr><td>".$LF_other['task_start']."</td><td>".gen_time_edit_form($tbdate, 'tb')."</td></tr><tr><td>".$LF_other['task_end']."</td><td>".gen_time_edit_form($tedate, 'te')."</td></tr><tr><td>".$LF_other['taskdesc']."</td><td>".gen_bb_buts('tdesc')."<br /><textarea name='tdesc' id='tdesc' cols='70' rows='15'>".$tdesc."</textarea></td></tr><tr><td colspan='2' class='cent'><input type='button' value='".$LF_other['quitnosave']."' onclick='window.history.back(1);' /> <input type='reset' value='".$LF_messz['reset']."' /> ".$but."<input type='hidden' name='tid' value='".floor($tid)."' /></td></tr></table></td></tr></table></form>";
	} 
	return $rez;
}
//-----------------------------------------------------
//    форма добавления подзадачи
function show_add_subtask_form($tid)
{
	global $MY_RULZ, $LF_other, $LF_mess, $LF_TASK_STT, $LF_formz, $LF_messz, $DBASE;
	$rez = $LF_mess[5];
	$db_1653 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_1653&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."TSKZ`,`".$DBASE['prefix']."PRJCTZ`  WHERE `tid`='".floor($tid)."' AND `tpro`=`pid`";
		$qrez = mysql_query($que, $db_1653) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_fetch_assoc($qrez);
		mysql_close($db_1653);
		if($MY_RULZ['uid']==$qrez1['tmaker']||$MY_RULZ['uid']==$qrez1['plider']||$MY_RULZ['state']>2)
		{
			$tmaker = $qrez1['tmaker'];
			$tname = $tdesc = "";
			$tpro = get_pro_link(floor($qrez1['tpro']));
			$tmtask = get_task_link(floor($qrez1['tid']));
			$tbdate = time() + floor($MY_RULZ['plushour'])*3600;
			$tedate = $tbdate + 60;
			$tstate = 1;
			$tid = 0;
			$tready = 0;
			$tgroup = floor($qrez1['tgroup']);
			$user = gen_user_link(array_map('infiltrtext',user_exist($tmaker)));
			$rez = "<form action='actions.php' method='post'><table class='card'><tr style='vertical-align: top;'><td>\n<table width='100%' class='form' cellpadding='3' cellspacing='0' rules='none' border='0'><tr><td>".$LF_other['mothertask']."</td><td>".$tpro." -> ".$tmtask."<input type='hidden' name='tmtask' value='".floor($qrez1['tid'])."' /><input type='hidden' name='tpro' value='".floor($qrez1['tpro'])."' /></td></tr><tr><td>".$LF_other['taskname']."</td><td><input type='text' name='tname' value='".$tname."' /></td></tr><tr><td>".$LF_other['taskgroup']."</td><td>".gen_group_link($tgroup)."<input type='hidden' name='tgroup' value='".$tgroup."' /></td></tr><tr><td>".$LF_other['maker']."</td><td><span id='uslist'>".$user."</span><input type='hidden' name='tmaker' value='".$tmaker."' /></td></tr><tr><td>".$LF_other['state']."</td><td>".gen_list_from_array($LF_TASK_STT, floor($tstate), 'tstate')."</td></tr><tr><td>".$LF_other['taskready']."</td><td><input type='text' size='3' maxlength='3' name='tready' value='".$tready."' />%</td></tr><tr><td>".$LF_other['task_start']."</td><td>".gen_time_edit_form($tbdate, 'tb')."</td></tr><tr><td>".$LF_other['task_end']."</td><td>".gen_time_edit_form($tedate, 'te')."</td></tr><tr><td>".$LF_other['taskdesc']."</td><td>".gen_bb_buts('tdesc')."<br /><textarea name='tdesc' id='tdesc' cols='70' rows='15'>".$tdesc."</textarea></td></tr><tr><td colspan='2' class='cent'><input type='button' value='".$LF_other['quitnosave']."' onclick='window.history.back(1);' /> <input type='reset' value='".$LF_messz['reset']."' /> <input type='submit' name='new_task' value='".$LF_other['addmtask']."' /><input type='hidden' name='tid' value='".floor($tid)."' /></td></tr></table></td></tr></table></form>";
		}
	}
	return $rez;
}
//-----------------------------------
//   проверка прав доступа к проекту
function is_user_in_project($userid, $proid)
{
	global $MY_RULZ, $DBASE;
	$user = user_exist($userid);
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."PRJCTZ` WHERE `pid`='".floor($proid)."'";
		$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_fetch_assoc($qrez);
		mysql_close($db);
	} 
	$rez = false;
	if((($user['state']>2)||($user['uid']==$qrez1['plider']))&&isset($qrez1['plider']))
		$rez = true;
	return $rez;
}
//-----------------------------------
//   проверка прав доступа к задаче
function is_user_in_task($userid, $taskid)
{
	global $MY_RULZ, $DBASE;
	$user = user_exist($userid);
	$db_4 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_4&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($taskid)."'";
		$qrez = mysql_query($que, $db_4) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_fetch_assoc($qrez);
	}
	mysql_close($db_4);
	$rez = false;
	if(isset($qrez1['tmaker']))
	{
		$uinpro = is_user_in_project($userid, $qrez1['tpro']);
		if(($user['state']>2)||$uinpro||($userid['uid']==$qrez1['tmaker']))
			$rez = true;
	}
	return $rez;
}
//--------------------------------------
//    преобразование пользовательсуой даты в timestamp
function compacttime($arr, $pref)
{
	$arrnames = array('hour', 'min', 'mon', 'day', 'year');
	for($i=0; $i<count($arrnames); $i++)
		$arr[$pref.$arrnames[$i]] = floor($arr[$pref.$arrnames[$i]]);
	$rez = mktime(($arr[$pref.'hour']%24), ($arr[$pref.'min']%60), 0, ($arr[$pref.'mon']%12+1), ($arr[$pref.'day']%32), floor($arr[$pref.'year']));
	return $rez;
}
//--------------------------------------
//    получить ссылку на проект
function get_pro_link($id)
{
	global $DBASE, $LF_other;
	$rez = "<a href='projects.php?id=".floor($id)."' class='user0'>".$LF_other['project']."</a>";
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."PRJCTZ` WHERE `pid`='".floor($id)."'";
		$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_fetch_assoc($qrez);
		mysql_close($db);
		if(isset($qrez1['pname']))
			$rez = "<a href='projects.php?id=".floor($id)."'>".infiltrtext($qrez1['pname'])."</a>";
	} 
	return $rez;
}
//--------------------------------------
//    получить ссылку на задачу
function get_task_link($id)
{
	global $DBASE, $LF_other;
	$rez = false;
	$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($id)."'";
		$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_fetch_assoc($qrez);
		mysql_close($db);
		if(isset($qrez1['tname']))
			$rez = "<a href='tasklist.php?id=".floor($id)."'>".infiltrtext($qrez1['tname'])."</a>";
	} 
	return $rez;
}
//-------------------------------------
//   генерация списка статусов проекта // delete me
function gen_list_from_array($arr, $selected, $field_name)
{
	$rez = "<select name='".$field_name."'>\n";
	for($i=0; $i<count($arr); $i++)
		$rez .= "<option value='".$i."'".(($i==$selected)?(" selected='selected'"):("")).">".$arr[$i]."</option>\n";
	$rez .= "</select>\n";
	return $rez;
}
//-------------------------------------
//   генерация списка статусов проекта
function gen_pro_state_list($e)
{
	global $LF_PRO_STT;
	$rez = "<select name='pstate'>\n";
	for($i=0; $i<count($LF_PRO_STT); $i++)
		$rez .= "<option value='".$i."'".(($i==$e)?(" selected='selected'"):("")).">".$LF_PRO_STT[$i]."</option>\n";
	$rez .= "</select>\n";
	return $rez;
}
//-------------------------------------
/**   генерация списка чего-либо
*
*
*/
function generateAnyList($fieldName, $array, $selectedIndex)
{
	$rez = "<select name='".$fieldName."'>\n";
	for($i=0; $i<count($array); $i++)
		$rez .= "<option value='".$i."'".(($i==$selectedIndex)?(" selected='selected'"):("")).">".$array[$i]."</option>\n";
	$rez .= "</select>\n";
	return $rez;
}
//---------------------------------------
//    отображение задачи/задач
function show_task($id=-1, $proid=-1, $userid=-1)
{
	global  $MY_RULZ, $LF_other, $LF_time, $DBASE, $LF_TASK_STT, $LF_mess;
	$rez = $zag = "";
	$err_str = '';
	$rezarr = array();
	if($id>-1)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($id)."'";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			$rezarr = mysql_fetch_assoc($qrez);
			mysql_close($db);
			if(isset($rezarr['tid']))
			{
				$uintask = is_user_in_project($MY_RULZ['uid'], $rezarr['tpro']);
				$err_str = "<p>";
				if($uintask)
					$err_str .= "[<a href='edit.php?task=".floor($rezarr['tid'])."&project=".floor($rezarr['tpro'])."'>".$LF_other['edittask']."</a>] [<a href='javascript: if(confirm(\"".$LF_other['rlydeltask']."\")) window.location.replace(\"actions.php?deltask=".floor($rezarr['tid'])."\");'>".$LF_other['deltask']."</a>]";
				else if($MY_RULZ['uid']==$rezarr['tmaker']&&$rezarr['tmtask']>0)
					$err_str .= "[<a href='edit.php?task=".floor($rezarr['tid'])."&project=".floor($rezarr['tpro'])."'>".$LF_other['edittask']."</a>] [<a href='javascript: if(confirm(\"".$LF_other['rlydeltask']."\")) window.location.replace(\"actions.php?deltask=".floor($rezarr['tid'])."\");'>".$LF_other['deltask']."</a>]";
				if(($MY_RULZ['uid']==$rezarr['tmaker']||$uintask)&&$rezarr['tmtask']==0)
					$err_str .= " [<a href='edit.php?mtask=".floor($rezarr['tid'])."'>".$LF_other['addmtask']."</a>]";
				$err_str .= '</p>';
				$progressedit = floor($rezarr['tready'])."%";
				if($MY_RULZ['uid']==$rezarr['tmaker'])
					$progressedit = "[<a id='ctpl' href='javascript:changetaskprogress(\"".$LF_other['promptchangetaskprogress']."\", ".$rezarr['tid'].");'>".floor($rezarr['tready'])."%</a>]";    
				$timeprogress = (time()>$rezarr['tedate'])?(100):((time()<$rezarr['tbdate'])?(0):(floor(((time()-$rezarr['tbdate'])*100)/($rezarr['tedate']-$rezarr['tbdate']))));
				$first_str = "<tr><td width='200px'>".$LF_other['project']."</td><td>".get_pro_link($rezarr['tpro'])."</td></tr>";
				$show_stasks = '';
				$show_subtasksCalendar = roadMap($rezarr['tid'], 0);
				if($rezarr['tmtask']>0)
				{
					$first_str = "<tr><td width='200px'>".$LF_other['mothertask']."</td><td>".get_pro_link($rezarr['tpro'])." -> ".get_task_link($rezarr['tmtask'])."</td></tr>";
					$show_stasks = '';
				}
				$kriy_subtasks = new ProjectorSubTask();
				$show_files = show_file(-1, -1, -1, $rezarr['tid']);
				$err_str .= "<table width='100%' cellpadding='3' cellspacing='0' class='card'>
				<tr><td>
				<h4>".infiltrtext($rezarr['tname'])."</h4>
				<table width='100%' cellpadding='3' cellspacing='0' class='form'>".$first_str.
				"<tr><td>".$LF_other['maker']."</td><td>".gen_user_link(array_map('infiltrtext', user_exist($rezarr['tmaker'])))."</td></tr>
				<tr><td>".$LF_other['taskgroup']."</td><td>".gen_group_link($rezarr['tgroup'])."</td></tr>
				<tr><td>".$LF_other['state']."</td><td>".$LF_TASK_STT[$rezarr['tstate']]."</td></tr>
				<tr><td>".$LF_other['task_start']."</td><td>".showftime(floor($rezarr['tbdate']+ floor($MY_RULZ['plushour'])*3600))."</td></tr>
				<tr><td>".$LF_other['task_end']."</td><td>".showftime(floor($rezarr['tedate']+ floor($MY_RULZ['plushour'])*3600))."</td></tr>
				<tr class='vcent'><td colspan='2'><script type='text/javascript'>timeprogress = ".$timeprogress.";workprogress = ".floor($rezarr['tready']).";</script><table><tr><td>".$LF_other['timeline']."</td><td rowspan='2'><canvas id='timebar' width='400px' height='40px'>Canvas disabled :(</canvas></td><td>".$timeprogress."%</td></tr><tr><td>".$LF_other['taskready']."</td><td>".$progressedit."</td></tr></table></td></tr><tr><td>".$LF_other['taskdesc']."</td><td>".parse_post(infiltrtext($rezarr['tdesk']))."</td></tr>
				<tr><td colspan='2'><span class='notlink'>{ {$LF_other['subtasks_calendar']} }</span><div class='hint'>{$show_subtasksCalendar}</div></td></tr>".
				$show_stasks.
				"<tr><td colspan='2'><span class='notlink'>{ {$LF_other['sub_tasks']} }</span><div class='hint'>".$kriy_subtasks->ProjectorShowSubTasksTab($rezarr['tid'])."</div></td></tr>
				<tr><td colspan='2'><span class='notlink'>{ {$LF_other['task_files']} }</span><div class='hint'>{$show_files}</div> [<a href='edit.php?addfile2task=".$rezarr['tid']."'>".$LF_other['addfile']."</a>]</td></tr></table>".show_posts(floor($rezarr['tid']), 1, -1, -1)."</td></tr></table>";
			}
			else
			{
				$err_str = $LF_mess[5];
			}
		}
		else
			$err_str = "<span class='rwe'>".(1)."</span>";  
	}
	else if($proid>-1)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."TSKZ` WHERE `tpro`='".floor($proid)."' AND `tmtask`='0'";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			while($t = mysql_fetch_assoc($qrez))
				$rezarr[] = $t;
			mysql_close($db);
			$zag = '<tr class="cent b"><td>№</td><td>'.$LF_other['taskname'].'</td><td>'.$LF_other['maker'].'</td><td>'.$LF_other['state'].'</td><td>'.$LF_other['task_end'].'</td></tr>';
		}
		else
			$err_str = $LF_mess[2];
	}
	else if($userid>-1)
	{
		$db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."TSKZ` WHERE `tmaker`='".floor($userid)."' AND `tmtask`='0'";
			$qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
			while($t = mysql_fetch_assoc($qrez))
				$rezarr[] = $t;
			mysql_close($db);
			if(isset($rezarr[0]['tid']))
				$zag = '<tr class="cent b"><td>№</td><td>'.$LF_other['taskname'].'</td><td>'.$LF_other['project'].'</td><td>'.$LF_other['state'].'</td><td>'.$LF_other['task_end'].'</td></tr>';
			else
				$err_str = $LF_mess[13];
		}
		else
			$err_str = $LF_mess[2];
	}
	//**
	if($err_str!='')
		$rez .= $err_str;
	else
	{
		$rez = "<table width='100%' cellpadding='3' cellspacing='0' rules='none' border='0' class='form'>".$zag;
		for($i=0; $i<count($rezarr); $i++)
		{
			if($proid>-1)
				$sec_cell = gen_user_link(array_map('infiltrtext', user_exist($rezarr[$i]['tmaker'])));
			else
				$sec_cell = get_pro_link($rezarr[$i]['tpro']);
			$dopstyle = ""; 
			if($rezarr[$i]['tstate']==1&&time()>$rezarr[$i]['tedate'])
				$dopstyle = " rse";
			else if($rezarr[$i]['tstate']==0||$rezarr[$i]['tstate']==2)
				$dopstyle = " rwe";     
			$rez .= '<tr class="cent'.$dopstyle.'"><td>'.($i+1).'</td><td><a href="tasklist.php?id='.floor($rezarr[$i]['tid']).'"><span class="'.$dopstyle.'">'.infiltrtext($rezarr[$i]['tname']).'</span></a></td><td>'.$sec_cell.'</td><td>'.$LF_TASK_STT[$rezarr[$i]['tstate']].(($rezarr[$i]['tstate']==1)?(' ('.floor($rezarr[$i]['tready']).'%)'):('')).'</td><td>'.showftime(floor($rezarr[$i]['tedate']+ floor($MY_RULZ['plushour'])*3600)).'</td></tr>';
		}
		$rez .= '</table>';
	}
	return $rez;
}
//-----------------------------
// показать подзадачи
function show_subtasks($tid)
{
	global  $MY_RULZ, $LF_other, $LF_time, $DBASE, $LF_TASK_STT, $LF_mess;
	$rez = "<span class='rse'>".$LF_other['nosubtasks']."</span>";
	$con = new ProjectorConnect();
	$rezarr = $con->ProjectorQuery("SELECT * FROM `".$DBASE['prefix']."TSKZ` WHERE `tmtask`=".floor($tid));
	$con->ProjectorDisconnect();
	if(count($rezarr)>0)
	{
		$rez = '<table width="100%" class="form"><tr class="cent b"><td>№</td><td>'.$LF_other['subtaskname'].'</td><td>'.$LF_other['state'].'</td><td>'.$LF_other['task_end'].'</td></tr>';
		for($i=0; $i<count($rezarr); $i++)
		{
			$dopstyle = ""; 
			if($rezarr[$i]['tstate']==1&&time()>$rezarr[$i]['tedate'])
				$dopstyle = " rse";
			else if($rezarr[$i]['tstate']==0||$rezarr[$i]['tstate']==2)
				$dopstyle = " rwe"; 
			$rez .= '<tr class="cent'.$dopstyle.'"><td>'.($i+1).'</td><td><a href="tasklist.php?id='.floor($rezarr[$i]['tid']).'"><span class="'.$dopstyle.'">'.infiltrtext($rezarr[$i]['tname']).'</span></a></td><td>'.$LF_TASK_STT[$rezarr[$i]['tstate']].(($rezarr[$i]['tstate']==1)?(' ('.floor($rezarr[$i]['tready']).'%)'):('')).'</td><td>'.showftime(floor($rezarr[$i]['tedate']+ floor($MY_RULZ['plushour'])*3600)).'</td></tr>';
		}
		$rez .= '</table>';
	}
	return $rez;
}
//---------------------------------------
//   редактировать задачу
function edit_task($arr)
{
	global $MY_RULZ, $LF_other, $LF_time, $DBASE, $LF_TASK_STT, $REAL_ESCAPE;
	$tid = 0;
	if((is_user_in_project($MY_RULZ['uid'], $arr['tpro']))||($arr['tmaker']==$MY_RULZ['uid']&&$arr['tmtask']>0))
	{
		if(floor($arr['tid'])>0)
				if(is_user_in_task($MY_RULZ['uid'], floor($arr['tid'])))
					$tid = floor($arr['tid']);
		$tmakerexist = user_exist($arr['tmaker']);
		if($tmakerexist['uid']=='')
			$arr['tmaker'] = 1;
		$db_2 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db_2&&mysql_select_db($DBASE['name']))
		{
			if($REAL_ESCAPE)
				$arr = array_map("mysql_real_escape_string", $arr);
			else
				$arr = array_map("mysql_escape_string", $arr);
			$arr['tname'] = trim($arr['tname']);
			if($arr['tname']=='')
				$arr['tname'] = $LF_other['newtask'];
			$arr['tdesc'] = trim($arr['tdesc']);
			if($arr['tdesc']=='')
				$arr['tdesc'] = $LF_other['newtaskdesc'];
			if($arr['tstate']<0||$arr['tstate']>=count($LF_TASK_STT))
				$arr['tstate'] = 1;
			$arr['tbdate'] = compacttime($arr, 'tb') - floor($MY_RULZ['plushour'])*3600;
			$arr['tedate'] = compacttime($arr, 'te') - floor($MY_RULZ['plushour'])*3600;
			$arr['tready'] = (floor($arr['tready'])>100)?(100):((floor($arr['tready'])<0)?(0):(floor($arr['tready'])));
			$arr['tgroup']= floor($arr['tgroup']);
			if($arr['tedate']<=$arr['tbdate'])
				$arr['tedate'] = $arr['tbdate'] + 60;
			if($tid>0)  
				$que = "UPDATE `".$DBASE['prefix']."TSKZ` SET `tname`='".$arr['tname']."', `tdesk`='".$arr['tdesc']."', `tmaker`='".$arr['tmaker']."', `tbdate`='".$arr['tbdate']."', `tedate`='".$arr['tedate']."', `tstate`='".$arr['tstate']."', `tnotice`='0', `tready`='".$arr['tready']."', `tgroup`='".$arr['tgroup']."', `tmtask`='".$arr['tmtask']."' WHERE `tid`='".$tid."'";
			else
				$que = "INSERT INTO `".$DBASE['prefix']."TSKZ` values ('0', '".$arr['tname']." ', '".$arr['tdesc']." ', '".$arr['tpro']."', '".$arr['tmaker']."', '".$arr['tbdate']."', '".$arr['tedate']."', '".$arr['tstate']."', '0', '".$arr['tready']."','".$arr['tgroup']."','".$arr['tmtask']."')";
			$qrez = mysql_query($que, $db_2) or die(mysql_errno() . ": " . mysql_error());
			if($qrez&&$tid==0)
			{
				$que2 = "SELECT max(`tid`) FROM `".$DBASE['prefix']."TSKZ`";
				$qrez2 = mysql_query($que2, $db_2) or die(mysql_errno() . ": " . mysql_error());
				$qrez23 = mysql_fetch_assoc($qrez2);
				$tid = floor($qrez23['max(`tid`)']);
			}
			mysql_close($db_2);
		}
	}
	return 'tasklist.php?id='.$tid;
}
//---------------------------------------
//  удаление задачи
function del_task($id)
{
	global $DBASE, $MY_RULZ;
	$rez = "tasklist.php";
	if(is_user_in_task($MY_RULZ['uid'], $id))
	{
		$db_3 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db_3&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($id)."'";
			$qrez = mysql_query($que, $db_3) or die(mysql_errno() . ": " . mysql_error());
			$qrezu = mysql_fetch_assoc($qrez);
			if($qrezu['tmaker']!=$MY_RULZ['uid']||$MY_RULZ['state']>2||($qrezu['tmaker']==$MY_RULZ['uid']&&$qrezu['tmtask']>0))
			{
				$que2 = "DELETE FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($id)."'";
				$qrez2 = mysql_query($que2, $db_3) or die(mysql_errno() . ": " . mysql_error());
				$que3 = "DELETE FROM `".$DBASE['prefix']."PSTZ` WHERE `postintask`=1 AND `ptask`='".floor($id)."'";
				$qrez3 = mysql_query($que3, $db_3) or die(mysql_errno() . ": " . mysql_error());
				$rez = 'projects.php?id='.$qrezu['tpro'].'&mid=9';
				if($qrezu['tmtask']>0)
					$rez = 'tasklist.php?id='.floor($qrezu['tmtask']).'&mid=9';
			}
			mysql_close($db_3); 
		}
	}
	return $rez;
}
//--------------------------------------
//  календарь событий
function event_calendar($month, $year)
{
	global $DBASE, $LF_time, $WEEK_BEGIN_DAY, $MY_RULZ;
	$tstamp = mktime(0, 0, 0, $month, 1, $year);
	$tstamp2 = mktime(0, 0, 0, $month+1, 1, $year);
	$firstDay = date('w', $tstamp);
	$days31 = date('t', $tstamp);
	$fillBegin = ($firstDay-$WEEK_BEGIN_DAY+7)%7;
	$fillEnd = (7 - ($fillBegin+$days31)%7)%7;
	$newRow = (7-$fillBegin)%7;
	$weekEnd1 = (7 - $firstDay)%7;
	$weekEnd2 = (8 - $firstDay)%7;
	$rarr = array();
	$db_1873 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_1873&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."NWZ` WHERE `ntype`='4' and `ndate`>".$tstamp." and `ndate`<".$tstamp2;
		$qrez = mysql_query($que, $db_1873) or die(mysql_errno() . ": " . mysql_error());
		while($qrezu = mysql_fetch_assoc($qrez))
			$rarr[floor(date('d', $qrezu['ndate']))] = array($qrezu['nid'], infiltrtext($qrezu['nname']));
		mysql_close($db_1873);
	}  
	$rez = '<table width="100%" class="caltab"><tr><td colspan="7" class="cent b">'.$LF_time['mon_long'][floor($month)-1].' '.floor($year).'</td></tr><tr class="smf cent b">';
	for($i=0; $i<7; $i++)
		$rez .= '<td'.(((($i+$WEEK_BEGIN_DAY+1)%7)==$weekEnd1||(($i+$WEEK_BEGIN_DAY+1)%7)==$weekEnd2)?" class='weekend'":"").'>'.$LF_time["day_short"][($i+$WEEK_BEGIN_DAY)%7].'</td>';
	$rez .= '</tr><tr class="smf cent">';
	// заполнитель в начале
	for($k = 1; $k<=$fillBegin; $k++)
		$rez .="<td".(((($k+$WEEK_BEGIN_DAY)%7)==$weekEnd1||(($k+$WEEK_BEGIN_DAY)%7)==$weekEnd2)?" class='weekend'":"").">&nbsp;</td>";
	// пошли дни месяца
	for($i=1; $i<=$days31; $i++)
	{
		$dayClass = 'class="';
		if($i==floor(date('d', time()+floor($MY_RULZ['plushour'])*3600)))
			$dayClass .= 'event_calendar_today';
		if($i%7==$weekEnd1||$i%7==$weekEnd2)
			$dayClass .= ' weekend';
		$dayClass .= '"';
		$rez.='<td '.$dayClass.'>';
		if(isset($rarr[$i]))
			$rez.= '<a href="events.php?id='.$rarr[$i][0].'" title="&#47;&#47; '.$rarr[$i][1].'"><span class="event_calendar_event">'.(($i<10)?'&nbsp;':'').($i).'</span></a></td>';
		else
			$rez .= (($i<10)?'&nbsp;':'').($i).'</td>';
		if($i%7==$newRow)
		{ 
			$rez .= '</tr>';
			if($i!=$days31)
				$rez .= '<tr class="smf cent">';
		}  
	}
	// заполнитель в конце
	for($k = 0; $k<$fillEnd; $k++)
		$rez .="<td".((($k+$i)%7==$weekEnd1||($k+$i)%7==$weekEnd2)?" class='weekend'":"").">&nbsp;</td>";
	$rez .= '</tr></table>';
	return $rez;
}
//--------------------------------------
//  вывод размера файла
function gen_filesize($s)
{
	global $LF_other;
	$rez = "";
	if($s<1024)
		$rez = $s." ".$LF_other['b'];
	else
		if($s<(1024*1024))
			$rez = floor($s/1024).",".($s%1024)." ".$LF_other['kb'];
		else
			$rez = floor($s/(1024*1024)).",".(($s/1024)%1024)." ".$LF_other['Mb'];
	return $rez;
}
//--------------------------------------
//   отобразить файлы/файл
function show_file($id=-1, $proid=-1, $userid=-1, $taskid=-1)
{
	global $LF_other, $DBASE, $MY_RULZ, $LF_messz, $LF_links;
	$rez = '<span class="rse">'.$LF_other['nofiles'].'</span>';
	$arr = array();
	$db_1 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_1&&mysql_select_db($DBASE['name']))
	{
		if($id>0)
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."FLZ`, `".$DBASE['prefix']."TSKZ` WHERE `".$DBASE['prefix']."FLZ`.`fid`='".floor($id)."' AND (`".$DBASE['prefix']."FLZ`.`ftask`=`".$DBASE['prefix']."TSKZ`.`tid` OR `".$DBASE['prefix']."FLZ`.`ftask`=0) LIMIT 1";
			$qrez = mysql_query($que, $db_1) or die(mysql_errno() . ": " . mysql_error());
			$qrez1 = mysql_fetch_assoc($qrez);
			mysql_close($db_1);
			if(isset($qrez1['fid']))
			{
				$buts = "";
				if($MY_RULZ['state']>2||$MY_RULZ['uid']==$qrez1['fauthor'])
					$buts = "<a href='edit.php?editfile=".$qrez1['fid']."' title='".$LF_other['editfile']."'><img src='images/icons/edit.gif' class='smallicon' /></a> <a href='javascript: if(confirm(\"{$LF_other['rlydelfile']}\")) window.location.replace(\"actions.php?delfile=".$qrez1['fid']."\");' title='".$LF_other['delete']."'><img src='images/icons/delete.gif' class='smallicon' /></a>";
				$rez = "<table class='card'><tr><td><table class='form'>";
				$rassh = explode('.', $qrez1['fpath']);
				if($qrez1['ftask']>0)
					{
						$for_str = $LF_messz['for']." ".get_pro_link($qrez1['tpro'])."<img src='images/icons/arrow.gif' /><a href='tasklist.php?id=".floor($qrez1['tid'])."'>".infiltrtext($qrez1['tname'])."</a>";
					}
					else
					{
						$for_str = "<span class='b'>[".$LF_links['myfiles']."]</span>";
					} 
				$rez .= "<tr><td width='200px'>".$LF_other['filename']."</td><td><span class='rwe'>".infiltrtext($qrez1['fname'])."</span> ".$buts."</td></tr><tr><td>".$LF_other['filesize']."</td><td>".gen_filesize($qrez1['fsize'])."</td></tr><tr><td>".$LF_other['author']."</td><td>".gen_user_link(array_map('infiltrtext', user_exist(floor($qrez1['fauthor']))))."</td></tr><tr><td>".$LF_other['added']."</td><td>".showftime($qrez1['fdate']+ floor($MY_RULZ['plushour'])*3600)."</td></tr><tr><td >".$LF_other['filedesc']."</td><td>".$for_str."<br /><br />".infiltrtext($qrez1['fdesc'])."</td></tr><tr><td colspan='2' class='cent'>[<a href='download.php?id=".intval($qrez1['fid'])."'><img src='images/icons/save.gif' /> ".$LF_other['download']."</a>]</td></tr></table></td></tr>";
				$rez .= "</table>";
			}   
		}
		else if($proid>0)
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."FLZ`,`".$DBASE['prefix']."TSKZ` WHERE `".$DBASE['prefix']."FLZ`.`ftask`=`".$DBASE['prefix']."TSKZ`.`tid` AND `".$DBASE['prefix']."TSKZ`.`tpro`='".floor($proid)."'";
			$qrez = mysql_query($que, $db_1) or die(mysql_errno() . ": " . mysql_error());
			mysql_close($db_1);
			while($qrez1 = mysql_fetch_assoc($qrez))
				$arr[] = $qrez1;
			if(count($arr)>0)
			{
				$rez = "<table class='form'>";
				for($i=0; $i<count($arr); $i++)
				{
					$buts = "";
					if($MY_RULZ['state']>2||$MY_RULZ['uid']==$arr[$i]['fauthor'])
						$buts = "<a href='edit.php?editfile=".$arr[$i]['fid']."' title='".$LF_other['editfile']."'><img src='images/icons/edit.gif' class='smallicon' /></a> <a href='javascript: if(confirm(\"{$LF_other['rlydelfile']}\")) window.location.replace(\"actions.php?delfile=".$arr[$i]['fid']."\");' title='".$LF_other['delete']."'><img src='images/icons/delete.gif' class='smallicon' /></a>";
					$rassh = explode('.', $arr[$i]['fpath']);
					$rez .= "<tr><td width='10px'>".($i+1).".</td><td><a href='download.php?id=".intval($arr[$i]['fid'])."'><img src='images/icons/save.gif' /></a> <a href='files.php?id=".$arr[$i]['fid']."'>".infiltrtext($arr[$i]['fname']).".".($rassh[count($rassh)-1])."</a> ".$LF_other['by']." ".gen_user_link(array_map('infiltrtext', user_exist(floor($arr[$i]['fauthor']))))."</td><td>".$buts."</td></tr>";
				} 
				$rez .= "</table>";
			}   
		}
		else if($userid>-1)
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."FLZ`,`".$DBASE['prefix']."TSKZ` WHERE (`".$DBASE['prefix']."FLZ`.`ftask`=`".$DBASE['prefix']."TSKZ`.`tid`) AND `".$DBASE['prefix']."FLZ`.`fauthor`='".floor($userid)."'";
			$qrez = mysql_query($que, $db_1) or die(mysql_errno() . ": " . mysql_error());
			while($qrez1 = mysql_fetch_assoc($qrez))
				$arr[] = $qrez1;
			$que2 = "SELECT * FROM `".$DBASE['prefix']."FLZ` WHERE (`".$DBASE['prefix']."FLZ`.`ftask`=0) AND `".$DBASE['prefix']."FLZ`.`fauthor`='".floor($userid)."'";
			$qrez2 = mysql_query($que2, $db_1) or die(mysql_errno() . ": " . mysql_error());
			while($qrezu2 = mysql_fetch_assoc($qrez2))
				$arr[] = $qrezu2;
			mysql_close($db_1);
			if(count($arr)>0)
			{
				$rez = "<table class='form'>";
				for($i=0; $i<count($arr); $i++)
				{
					$buts = "";
					if($MY_RULZ['state']>2||$MY_RULZ['uid']==$arr[$i]['fauthor'])
						$buts = "<td style='width:50px'>&nbsp;<a href='edit.php?editfile=".$arr[$i]['fid']."' title='".$LF_other['editfile']."'><img src='images/icons/edit.gif' class='smallicon' /></a> <a href='javascript: if(confirm(\"{$LF_other['rlydelfile']}\")) window.location.replace(\"actions.php?delfile=".$arr[$i]['fid']."\");' title='".$LF_other['delete']."'><img src='images/icons/delete.gif' class='smallicon' /></a></td>";
					$rassh = explode('.', $arr[$i]['fpath']);
					if($arr[$i]['ftask']>0)
					{
						$for_str = $LF_messz['for']." ".get_pro_link($arr[$i]['tpro'])."<img src='images/icons/arrow.gif' /><a href='tasklist.php?id=".floor($arr[$i]['tid'])."'>".infiltrtext($arr[$i]['tname'])."</a>";
					}
					else
					{
						$for_str = "<span class='b'>[".$LF_links['myfiles']."]</span>";
					} 
					$rez .= "<tr><td width='10px'>".($i+1).".</td><td>".$for_str."</td><td><a href='download.php?id=".intval($arr[$i]['fid'])."'><img src='images/icons/save.gif' /></a><td>&nbsp;<a href='files.php?id=".$arr[$i]['fid']."'>".infiltrtext($arr[$i]['fname']).".".($rassh[count($rassh)-1])."</a></td>".$buts."</tr>";
				} 
				$rez .= "</table>";
			}   
		}
		else if($taskid>-1)
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."FLZ` WHERE `ftask`='".floor($taskid)."'";
			$qrez = mysql_query($que, $db_1) or die(mysql_errno() . ": " . mysql_error());
			mysql_close($db_1);
			while($qrez1 = mysql_fetch_assoc($qrez))
				$arr[] = $qrez1;
			if(count($arr)>0)
			{
				$rez = "<table class='form'>";
				for($i=0; $i<count($arr); $i++)
				{
					$buts = "";
					if($MY_RULZ['state']>2||$MY_RULZ['uid']==$arr[$i]['fauthor'])
						$buts = "<a href='edit.php?editfile=".$arr[$i]['fid']."' title='".$LF_other['editfile']."'><img src='images/icons/edit.gif' class='smallicon' /></a> <a href='javascript: if(confirm(\"{$LF_other['rlydelfile']}\")) window.location.replace(\"actions.php?delfile=".$arr[$i]['fid']."\");' title='".$LF_other['delete']."'><img src='images/icons/delete.gif' class='smallicon' /></a>";
					$rassh = explode('.', $arr[$i]['fpath']);
					$rez .= "<tr><td width='10px'>".($i+1).".</td><td><a href='download.php?id=".intval($arr[$i]['fid'])."'><img src='images/icons/save.gif' /></a> <a href='files.php?id=".$arr[$i]['fid']."'>".infiltrtext($arr[$i]['fname']).".".($rassh[count($rassh)-1])."</a> ".$LF_other['by']." ".gen_user_link(array_map('infiltrtext', user_exist(floor($arr[$i]['fauthor']))))."</td><td>".$buts."</td></tr>";
				} 
				$rez .= "</table>";
			}   
		}
	}
	return $rez;
}
//-------------------------------
//  форма добавить/отредактировать файл
function show_edit_file_form($id, $task)
{
	global $MY_RULZ, $DBASE, $LF_other, $LF_messz, $LF_links, $ALLOW_EXTENSIONS, $FILE_MAX_WEIGHT;
	$rez = "<form method='post' action='actions.php' enctype='multipart/form-data'><table class='card'><tr><td><table class='form'>";
	$fname = "";
	$fdesc = "";
	$but = "<input name='editfile' type='submit' value='".$LF_other['addfile']."' />";
	$aexts = "";
	for($i=0; $i<count($ALLOW_EXTENSIONS); $i++)
		if($aexts == "")
			$aexts .= "'".$ALLOW_EXTENSIONS[$i]."'";
		else
			$aexts .= ", '".$ALLOW_EXTENSIONS[$i]."'";
	$ffile = "<tr><td>".$LF_other['file']." (".$LF_other['allowext'].": ".$aexts.";<br />".$LF_other['mstbenomuch']." ".gen_filesize($FILE_MAX_WEIGHT).")</td><td><input type='hidden' name ='MAX_FILE_SIZE' value='".$FILE_MAX_WEIGHT."' /><input name='ffile' type='file' /><input type='hidden' name='fid' value='-1' /></td></tr>";
	if($id>-1)
	{
		//$ftask = get_task_link($task);
		$db_1974 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db_1974&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."FLZ` WHERE `fid`='".floor($id)."'";
			$qrez = mysql_query($que, $db_1974) or die(mysql_errno() . ": " . mysql_error());
			$qrez1 = mysql_fetch_assoc($qrez);
			mysql_close($db_1974);
			if($qrez1['fid'])
			{
				if($qrez1['ftask']!=0)
					$ftask = get_task_link($qrez1['ftask']);
				else
					$ftask = "<span class='b'>[".$LF_links['myfiles']."]</span>";
				$task = floor($qrez1['ftask']);
				$but = "<input name='editfile' type='submit' value='".$LF_other['editfile']."' />";
				$fname = infiltrtext($qrez1['fname']);
				$fdesc = infiltrtext($qrez1['fdesc']);
				$ffile = "<tr><td>".$LF_other['file']."</td><td>[<a href='download.php?id=".intval($qrez1['fid'])."'><img class='smallicon' src='images/icons/save.gif' /> ".$LF_other['download']."</a>] ".gen_filesize($qrez1['fsize'])."<input type='hidden' name='fid' value='".$qrez1['fid']."' /></td></tr>";
			}
		}
	}
	else
	{
		if($task!=0)
			$ftask = get_task_link($task);
		else
			$ftask = "[".$LF_links['myfiles']."]";
	}
	$rez .= "<tr><td width='300px'>".$LF_messz['for']."</td> <td>".$ftask."<input type='hidden' name='ftask' value='".$task."' /></td></tr>
	<tr><td>".$LF_other['filename']."</td> <td><input type='text' name='fname' value='".$fname."' /></td></tr>
	<tr><td>".$LF_other['filedesc']."</td><td><textarea id='fdesc' name='fdesc' class='desc'>".$fdesc."</textarea></td></tr>".$ffile.
	"<tr><td colspan='2' class='cent'><input type='button' value='".$LF_other['quitnosave']."' onclick='window.history.back(1);' /> <input type='reset' value='".$LF_messz['reset']."' /> ".$but."</td></tr>
	</table></td></tr></table></form>";
	return $rez;
}
//----------------------------
// добавление/редактирование файла
function edit_file($arr)
{
	global $MY_RULZ, $DBASE, $LF_other, $ALLOW_EXTENSIONS, $FILE_MAX_WEIGHT, $REAL_ESCAPE;
	$rez = "files.php";
	if(get_task_link($arr['ftask'])||$arr['ftask']==0)
	{
		$db_1955 = new ProjectorConnect();
		if($REAL_ESCAPE)
			$arr = array_map("mysql_real_escape_string", $arr);
		else
			$arr = array_map("mysql_escape_string", $arr);
		$arr['fname'] = trim($arr['fname']);
		if($arr['fname']=='')
			$arr['fname'] = $LF_other['file'];
		$arr['fdesc'] = trim($arr['fdesc']);
		if($arr['fdesc']=='')
			$arr['fdesc'] = $LF_other['newfiledesc'];
		$fdate = time();
		$ftask = floor($arr['ftask']);
		$fauthor = $MY_RULZ['uid'];
		$arr['fpath'] = "";
		if($arr['fid']>0)
		{
			$qrez = $db_1955->ProjectorQuery("SELECT * FROM `".$DBASE['prefix']."FLZ` WHERE `fid`='".floor($arr['fid'])."' LIMIT 1");
			if(isset($qrez[0]['fid']))
			{
				if($MY_RULZ['state']>2||$MY_RULZ['uid']==$qrez[0]['fauthor'])
				{
					$qrez = $db_1955->ProjectorQuery("UPDATE `".$DBASE['prefix']."FLZ` SET `fname`='".$arr['fname']."', `fdesc`='".$arr['fdesc']."' WHERE `fid`='".$qrez[0]['fid']."'");
					$rez = "files.php?id=".floor($arr['fid'])."&mid=9";
				}
				else
					$rez = "files.php?id=".floor($qrez[0]['fid'])."&mid=7";
			}
			else
				$rez = "files.php?mid=12";
		}
		else
		{
			if(isset($_FILES['ffile']))
			{
				$ext = strtolower(pathinfo($_FILES['ffile']['name'], PATHINFO_EXTENSION));
				if(in_array($ext, $ALLOW_EXTENSIONS))
				{
					if($_FILES['ffile']['size']<$FILE_MAX_WEIGHT)
					{
						$ras = explode(".", $_FILES['ffile']['name']);
						$pref = floor($fauthor)."_".time().".".$ext;
						$fsize = $_FILES['ffile']['size'];
						if(move_uploaded_file($_FILES['ffile']['tmp_name'], "files/f".$pref))
						{
							$arr['fpath'] = $pref;
							$qrez = $db_1955->ProjectorQuery("INSERT INTO `".$DBASE['prefix']."FLZ` values ('0', '".$arr['fpath']."', '".$arr['fname']."', '".$arr['fdesc']."', '".$fdate."', '".$ftask."', '".$fauthor."', '".$fsize."')");
							if($qrez[0])
							{
								if($ftask>0)
									$rez = "tasklist.php?id=".floor($arr['ftask'])."&mid=17";
								else
									$rez = "files.php?mid=17";
							} 
							else
								$rez = "tasklist.php?id=".floor($arr['ftask'])."&mid=2";
						} 
						else
							$rez = "tasklist.php?id=".floor($arr['ftask'])."&mid=12";
					}
					else
						$rez = "tasklist.php?id=".floor($arr['ftask'])."&mid=15"; 
				}
				else
					$rez = "tasklist.php?id=".floor($arr['ftask'])."&mid=14";
			}
			else
				$rez = "tasklist.php?id=".floor($arr['ftask'])."&mid=5";    
		}
		$db_1955->ProjectorDisconnect();
	}
	else
		$rez = "files.php?mid=5";   
	return $rez;
}
//----------------------------
// удаление файла
function del_file($id)
{
	global $MY_RULZ, $DBASE;
	$rez = "files.php";
	$db_2030 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_2030&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."FLZ` WHERE `fid`='".floor($id)."'";
		$qrez = mysql_query($que, $db_2030) or die(mysql_errno() . ": " . mysql_error());
		$qrez1 = mysql_fetch_assoc($qrez);
		if($qrez1['fid'])
		{
			if($MY_RULZ['state']>2||$MY_RULZ['uid']==$qrez1['fauthor'])
			{
				if(unlink('files/f'.$qrez1['fpath']))
				{
					$que2 = "DELETE FROM `".$DBASE['prefix']."FLZ` WHERE `fid`='".floor($id)."'";
					$qrez2 = mysql_query($que2, $db_2030) or die(mysql_errno() . ": " . mysql_error());
					$rez = "files.php?user=".floor($qrez1['fauthor'])."&mid=16";
				} 
				else
					$rez = "files.php?id=".floor($qrez1['fid'])."&mid=8";
			}
		}
		mysql_close($db_2030);
	}
	return $rez;
}
//-------------------------------
//   автоматическая отправка личных сообшений
function autoprivatemessage($type, $touid, $arr)
{
	global $MY_RULZ, $LF_messz, $COMPANY_NAME, $SEC, $LF_other;
	$rez = false;
	$rarr = array();
	$rarr['mto'] = $touid;
	switch($type)
	{
		case 'taskfail':
			$rarr['subj'] = $LF_messz['notice']['taskfail'][0]." '".infiltrtext($arr['tname'])."'";
			$rarr['body'] = "[i]".lang_to_genitive_case($LF_other['lider'])." [user=".floor($arr['plider'])."][br]".lang_to_genitive_case($LF_other['maker'])." [user=".floor($arr['tmaker'])."][/i][br][br][tt]".$LF_messz['notice']['taskfail'][1]." [user=".floor($arr['tmaker'])."] " .$LF_messz['notice']['taskfail'][2]." [a=http://".$_SERVER['SERVER_NAME'].$SEC['proektor_path']."/tasklist.php?id=".floor($arr['tid'])."]".infiltrtext($arr['tname'])."[/a].[br]".$LF_messz['notice']['bestregards']." ".$COMPANY_NAME."[/tt]";
			send_message($rarr, 0);
			$rez = true;
		break;
		case 'tasknotice':
			$rarr['subj'] = $LF_messz['notice']['tasknotice'][0]." '".infiltrtext($arr['tname'])."'";
			$rarr['body'] = "[i]".lang_to_genitive_case($LF_other['lider'])." [user=".floor($arr['plider'])."][br]".lang_to_genitive_case($LF_other['maker'])." [user=".floor($arr['tmaker'])."][/i][br][br][tt]".$LF_messz['notice']['tasknotice'][1]." [user=".floor($arr['tmaker'])."] " .$LF_messz['notice']['tasknotice'][2]." ".time_left($arr['tedate']-time())." ".$LF_messz['notice']['tasknotice'][3]." [a=http://".$_SERVER['SERVER_NAME'].$SEC['proektor_path']."/tasklist.php?id=".floor($arr['tid'])."]".infiltrtext($arr['tname'])."[/a].[br]".$LF_messz['notice']['bestregards']." ".$COMPANY_NAME."[/tt]";
			send_message($rarr, 0);
			$rez = true;
		break;
		case 'tasknew':
			$rarr['subj'] = $LF_messz['notice']['tasknew'][0]." '".infiltrtext($arr['tname'])."'";
			$rarr['body'] = "[i]".$LF_other['added'].": [user=".floor($MY_RULZ['uid'])."][br]".lang_to_genitive_case($LF_other['lider'])." [user=".floor($arr['plider'])."][br]".lang_to_genitive_case($LF_other['maker'])." [user=".floor($arr['tmaker'])."][/i][br][br][tt]".$LF_messz['notice']['tasknew'][1]." [user=".floor($arr['tmaker'])."] " .$LF_messz['notice']['tasknew'][2]." [a=http://".$_SERVER['SERVER_NAME'].$SEC['proektor_path']."/tasklist.php?id=".floor($arr['tid'])."]".infiltrtext($arr['tname'])."[/a].[br]".$LF_messz['notice']['bestregards']." ".$COMPANY_NAME."[/tt]";
			send_message($rarr, 0);
			$rez = true;
		break;
		default:
		break;
	}
	return $rez;
}
//--------------------------------
//
function show_group_info($id)
{
	global $DBASE, $LF_other, $MY_RULZ, $DEFAULT_GROUP_ICON;
	$rarr = array();
	$rez = "</p><p class='rse'>".$LF_other['nogroups']."</p>";
	$db_2203 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_2203&&mysql_select_db($DBASE['name']))
	{
		if($id>0)
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."GRPZ` WHERE `gid`='".floor($id)."'";
			$qrez = mysql_query($que, $db_2203) or die(mysql_errno() . ": " . mysql_error());
			if($qrez1 = mysql_fetch_assoc($qrez))
			{
				$id = floor($id);
				mysql_close($db_2203);
				if($qrez1['gicon']=="")
					$gicon = $DEFAULT_GROUP_ICON;
				else
					$gicon = infiltrtext($qrez1['gicon']); 
				$isadmin = $editcontentstr = "";
				$groupcontent = gen_group_users($id);
				if($MY_RULZ['state']>2)
				{
					$isadmin = " [<a href='edit.php?group=".$id."'>".$LF_other['editgroup']."</a>] [<a href='javascript: if(confirm(\"".$LF_other['rlydelgroup']."\")) window.location.replace(\"actions.php?delgroup=".$id."\");'>".$LF_other['delgroup']."</a>]";
					$editcontentstr = "<tr><td>".$LF_other['editgroupcons']."</td><td><p id='usoutgr'>".$groupcontent[2]."</p></td></tr>";
				} 
				$rez = $isadmin."</p><table class='card'><tr><td><h4><img class='smallicon' src='data:image/gif;base64,".$gicon."' /> ".infiltrtext($qrez1['gname'])."</h4><table class='form'><tr><td>".$LF_other['groupdesc']."</td><td>".infiltrtext($qrez1['gdesc'])."</td></tr><tr><td>".$LF_other['groupcons']."</td><td><p id='usingr'>".$groupcontent[0]."</p></td></tr>".$editcontentstr."</table>".show_posts($id, 2)."</td></tr></table>";
			}
			else
			{
				mysql_close($db_2203);
				$rez = "</p><p class='rse'>".$LF_other['nogroup']."</p>";
			} 
		}
		else
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."GRPZ`";
			$qrez = mysql_query($que, $db_2203) or die(mysql_errno() . ": " . mysql_error());
			mysql_close($db_2203);
			while($qrez1 = mysql_fetch_assoc($qrez))
				$rarr[] = $qrez1;
			if(count($rarr)>0)
			{
				$rez = "</p><table class='form'>";
				for($i=0; $i<count($rarr); $i++)
				{
						if($rarr[$i]['gicon']=="")
						$gicon = $DEFAULT_GROUP_ICON;
					else
						$gicon = infiltrtext($rarr[$i]['gicon']);    
					$rez.="<tr><td width='300px'> <a href='groups.php?id=".infiltrtext($rarr[$i]['gid'])."'><img class='smallicon' src='data:image/gif;base64,".$gicon."' /> ".infiltrtext($rarr[$i]['gname'])."</a></td><td class='cent'>".infiltrtext($rarr[$i]['gdesc'])."</td></tr>";
				} 
				$rez .= "</table>"; 
			}
		}
	} 
	return $rez;
}
//-------------------
//  форма редактирования/добавления группы
function show_edit_group_form($id)
{
	global $DBASE, $MY_RULZ, $LF_other, $LF_messz, $DEFAULT_GROUP_ICON;
	$rez = "";
	if($id==-1)
	{
		$but = "<input name='newgroup' type='submit' value='".$LF_other['addgroup']."' />";
		$gname = "";
		$gdesc = "";
		$gicon = $DEFAULT_GROUP_ICON;
	}
	else
	{
		$db_2260 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db_2260&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."GRPZ` WHERE `gid`='".floor($id)."'";
			$qrez = mysql_query($que, $db_2260) or die(mysql_errno() . ": " . mysql_error());
			mysql_close($db_2260);
			if($qrez1 = mysql_fetch_assoc($qrez))
			{
				$but = "<input name='editgroup' type='submit' value='".$LF_other['editgroup']."' />";
				$gname = infiltrtext($qrez1['gname']);
				$gdesc = infiltrtext($qrez1['gdesc']);
				if($qrez1['gicon']=="")
					$gicon = $DEFAULT_GROUP_ICON;
				else
					$gicon = infiltrtext($qrez1['gicon']);    
			}
			else
				return "<span class='rse'>".$LF_other['nogroup']."</span>";
		}
		else
			return "<span class='rse'>".$LF_mess[2]."</span>";
	}
	$rez = "<table class='card'><tr><td><form method='post' action='actions.php' enctype='multipart/form-data'><input type='hidden' name='gid' value='".floor($id)."' /><table class='form'><tr><td width='200px'>".$LF_other['groupname']."</td><td><input type='text' name='gname' value='".$gname."' /></td></tr><tr><td>".$LF_other['groupicon']."</td><td><img class='smallicon' src='data:image/gif;base64,".$gicon."' /> <input type='file' name='gicon' /></td></tr><tr><td>".$LF_other['groupdesc']."</td><td><textarea class='desc' name='gdesc'>".$gdesc."</textarea></td></tr><tr><td colspan='2' class='cent'><input type='button' value='".$LF_other['quitnosave']."' onclick='window.history.back(1);' /> <input type='reset' value='".$LF_messz['reset']."' /> ".$but."</td></tr></table></form></td></tr></table>";
	return $rez;
}
//------------------------
//   редактирование группы
function edit_group($arr)
{
	global $DBASE, $MY_RULZ, $LF_other, $REAL_ESCAPE;
	$rez = "groups.php?id=".floor($arr['gid']);
	$gicon = '';
	if($MY_RULZ['state']>2)
	{
		$db_2288 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db_2288&&mysql_select_db($DBASE['name']))
		{
			if($REAL_ESCAPE)
				$arr = array_map("mysql_real_escape_string", $arr);
			else
				$arr = array_map("mysql_escape_string", $arr);
			if(isset($_FILES['gicon']))
			{
				if($_FILES['gicon']['size']<1024*1.5)
				{
					$ish = file_get_contents($_FILES['gicon']['tmp_name']);
					$gicon = base64_encode($ish);
				}
			}
			if(trim($arr['gname'])=='')
				$arr['gname'] = $LF_other['groupname'];
			if(trim($arr['gdesc'])=='')
				$arr['gdesc'] = $LF_other['groupdesc']; 
			if($arr['gid']==-1)
			{
				$que = "INSERT INTO `".$DBASE['prefix']."GRPZ` VALUES ('0', '".$arr['gname']."', '".$arr['gdesc']."', '".$gicon."')";
				$qrez = mysql_query($que, $db_2288) or die(mysql_errno() . ": " . mysql_error());
				$rez .= $qrez?"&mid=9":"&mid=8";
			}
			else
			{
				$que = "SELECT * FROM `".$DBASE['prefix']."GRPZ` WHERE `gid`='".floor($arr['gid'])."'";
				$qrez = mysql_query($que, $db_2288) or die(mysql_errno() . ": " . mysql_error());
				if($qrez1 = mysql_fetch_assoc($qrez))
				{
					$gicqu = "";
					if($gicon!="")
						$gicqu = ", `gicon`='".$gicon."'";
					$que2 = "UPDATE `".$DBASE['prefix']."GRPZ` SET `gname`='".$arr['gname']."', `gdesc`='".$arr['gdesc']."'".$gicqu." WHERE `gid`='".floor($arr['gid'])."'";
					$qrez2= mysql_query($que2, $db_2288) or die(mysql_errno() . ": " . mysql_error());
					$rez .= $qrez?"&mid=9":"&mid=8";
				}
				else
					$rez .= "&mid=5";
			}
			mysql_close($db_2288);
		}
		else
			$rez .= "&mid=2";
	}
	else
		$rez .= "&mid=7";
	return $rez;
}
//------------------------
//   удаление группы
function del_group($id)
{
	global $DBASE, $MY_RULZ;
	$rez = "groups.php";
	if($MY_RULZ['state']>2)
	{
		$db_2376 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db_2376&&mysql_select_db($DBASE['name']))
		{
			$que5 = "SELECT * FROM `".$DBASE['prefix']."UZRZinGRPZ` WHERE `ggid`='".floor($id)."'";
			$qrez5 = mysql_query($que5, $db_2376) or die(mysql_errno() . ": " . mysql_error());
			if(!mysql_fetch_array($qrez5))
			{
				$que = "DELETE FROM `".$DBASE['prefix']."GRPZ` WHERE `gid`='".floor($id)."'";
				$qrez = mysql_query($que, $db_2376) or die(mysql_errno() . ": " . mysql_error());
				mysql_close($db_2376);
				$dp = delete_posts(2, $id);
				if($dp&&$qrez)
					$rez .= "?mid=9";
				else
					$rez .= "?mid=8";
			}
			else
			{
				mysql_close($db_2376);
				$rez .= "?mid=8";
			}   
		}
		else
			$rez .= "?mid=2";
	}
	return $rez;
}
//------------------------------
//    отображение групп пользователя
function gen_user_groups($id)
{
	global $DBASE, $LF_other, $DEFAULT_GROUP_ICON;
	$rez = $LF_other['nogroups'];
	$prez = "";
	$db_2417 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_2417&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."UZRZinGRPZ`, `".$DBASE['prefix']."GRPZ` WHERE `".$DBASE['prefix']."UZRZinGRPZ`.`guid`='".floor($id)."' AND `".$DBASE['prefix']."GRPZ`.`gid`=`".$DBASE['prefix']."UZRZinGRPZ`.`ggid`";
		$qrez = mysql_query($que, $db_2417) or die(mysql_errno() . ": " . mysql_error());
		mysql_close($db_2417);
		while($qrezu = mysql_fetch_array($qrez))
		{
			$gicon = $DEFAULT_GROUP_ICON;
			if($qrezu['gicon']!='')
				$gicon = $qrezu['gicon'];
			$prez .= "<a href='groups.php?id=".infiltrtext($qrezu['gid'])."'><img class='smallicon' src='data:image/gif;base64,".$gicon."' title='".infiltrtext($qrezu['gname'])."' /></a> ";
		}
	}
	if($prez != "")
		$rez = $prez;
	return $rez;
}
//------------------------------
//    отображение пользователей группы
function gen_group_users($id)
{
	global $DBASE, $MY_RULZ, $LF_other;
	// rezarr[0] - линки пользователей группы, rezarr[1] - список пользователей группы, rezarr[2] - сnисок пользователей вне группы 
	$rezarr = array('','','');
	$db_2443 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_2443&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT `uid`, `nick`, `state` FROM `".$DBASE['prefix']."UZRZ` ORDER BY `nick`";
		$qrez = mysql_query($que, $db_2443) or die(mysql_errno() . ": " . mysql_error());
		$users = $uings = array();
		while($qrezu = mysql_fetch_array($qrez))
			$users[] = $qrezu;
		$que2 = "SELECT `guid` FROM `".$DBASE['prefix']."UZRZinGRPZ` WHERE `ggid`='".floor($id)."'";
		$qrez2 = mysql_query($que2, $db_2443) or die(mysql_errno() . ": " . mysql_error());
		while($qrezu2 = mysql_fetch_array($qrez2))
			$uings[] = $qrezu2['guid'];
		mysql_close($db_2443);
		$uc = count($users);
		for($i=0; $i<$uc; $i++)
		{
			if(in_array($users[$i]['uid'], $uings))
			{
				$rezarr[0] .= "<a href='userlist.php?id=".floor($users[$i]['uid'])."' ".show_status($users[$i]['state']).">".infiltrtext($users[$i]['nick'])."</a>";
				if($MY_RULZ['state']>2)
					$rezarr[0] .= ' [<a href="javascript: user2group('.floor($users[$i]['uid']).', '.floor($id).');">'.$LF_other['deluser2group'].'</a>]';
				$rezarr[0] .= "<br />";
				if($rezarr[1]=='')
					$rezarr[1] = "<select name='tmaker' class='user'>";
				$rezarr[1] .= "<option value='".floor($users[$i]['uid'])."' ".show_status($users[$i]['state']).">".infiltrtext($users[$i]['nick'])."</option>";
			}
			else
			{
				if($rezarr[2]=='')
					$rezarr[2] = "<select name='addusertogroup' id='addusertogroup' onchange='synclink();'><option value='0'>".$LF_other['selectuser']."</option>";
				$rezarr[2] .= "<option value='".floor($users[$i]['uid'])."' ".show_status($users[$i]['state']).">".infiltrtext($users[$i]['nick'])."</option>";
			}
		}
	}
	if($rezarr[1]!='')
		$rezarr[1] .= "</select>";
	if($rezarr[2]!='')
		$rezarr[2] .= "</select> [<a href='' id='addlink' tag='".$id."'>".$LF_other['adduser2group']."</a>]";
	return $rezarr;
}
//----------------------------
//   добавление/удаление пользователя  в группе
function adduser2group($arr)
{
	global $DBASE, $MY_RULZ;
	$rezarr = array('ia'=>'','il'=>'','ol'=>'');
	if($MY_RULZ['state']>2&&floor($arr['user2add'])>0)
	{
		$db_2486 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if($db_2486&&mysql_select_db($DBASE['name']))
		{
			$que = "SELECT * FROM `".$DBASE['prefix']."GRPZ` WHERE `gid`='".floor($arr['groupid'])."'";
			$qrez = mysql_query($que, $db_2486) or die(mysql_errno() . ": " . mysql_error());
			if(mysql_fetch_array($qrez))
			{
				$que2 = "SELECT * FROM `".$DBASE['prefix']."UZRZinGRPZ` WHERE `guid`='".floor($arr['user2add'])."' AND `ggid`='".floor($arr['groupid'])."'";
				$qrez2 = mysql_query($que2, $db_2486) or die(mysql_errno() . ": " . mysql_error());
				$qrezu2 = mysql_fetch_array($qrez2);
				if(isset($qrezu2[0]['guid']))
					$que3 = "DELETE FROM `".$DBASE['prefix']."UZRZinGRPZ` WHERE `guid`='".floor($arr['user2add'])."' AND `ggid`='".floor($arr['groupid'])."'";
				else
					$que3 = "INSERT INTO`".$DBASE['prefix']."UZRZinGRPZ` VALUES ('".floor($arr['user2add'])."', '".floor($arr['groupid'])."')";
				$qrez3 = mysql_query($que3, $db_2486) or die(mysql_errno() . ": " . mysql_error());
				mysql_close($db_2486);
			}
		}
	}
	$prezarr = gen_group_users($arr['groupid']);
	$rezarr['ia'] = $prezarr[0];
	$rezarr['il'] = $prezarr[1];
	$rezarr['ol'] = $prezarr[2];
	return $rezarr;
}
//------------------------
//    создание списка групп
function gen_groups_list($selid)
{
	global $DBASE, $MY_RULZ, $LF_other;
	$rez = "<select id='tgroup' name='tgroup'><option value='0' ".(($selid==0)?("selected='selected'"):("")).">".$LF_other['withoutgroup']."</option>";
	$db_2536 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_2536&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."GRPZ`";
		$qrez = mysql_query($que, $db_2536) or die(mysql_errno() . ": " . mysql_error());
		mysql_close($db_2536);
		while($qrezu=mysql_fetch_array($qrez))
			$rez .= "<option value='".floor($qrezu['gid'])."'".(($selid==floor($qrezu['gid']))?(" selected='selected'"):("")).">".infiltrtext($qrezu['gname'])."</option>";
	}
	$rez .= "</select>";
	return $rez; 
}
//------------------------
//    создание ссылки на группу
function gen_group_link($gid)
{
	global $DBASE, $MY_RULZ, $LF_other;
	$rez = '<span class="rse">'.$LF_other['nogroups'].'</span>';
	$db_2595 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_2595&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."GRPZ` WHERE `gid`='".floor($gid)."'";
		$qrez = mysql_query($que, $db_2595) or die(mysql_errno() . ": " . mysql_error());
		mysql_close($db_2595);
		if($qrezu=mysql_fetch_array($qrez))
			$rez = "<a href='groups.php?id=".floor($qrezu['gid'])."'>".infiltrtext($qrezu['gname'])."</a>";
	}
	return $rez; 
}
//----------------------------
//  изменения прогресса задачи исполнителем
function change_task_progress($proc, $tid)
{
	global $DBASE, $MY_RULZ;
	$rez = false;
	$db_2621 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_2621&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($tid)."'";
		$qrez = mysql_query($que, $db_2621) or die(mysql_errno() . ": " . mysql_error());
		if($qrezu=mysql_fetch_array($qrez))
		{
			if($MY_RULZ['uid']==$qrezu['tmaker'])
			{
				$proc = floor($proc);
				$proc = ($proc<0)?(0):(($proc>100)?(100):($proc));
				$que2 = "UPDATE `".$DBASE['prefix']."TSKZ` SET `tready`='".$proc."' WHERE `tid`='".floor($tid)."'";
				$qrez2 = mysql_query($que2, $db_2621) or die(mysql_errno() . ": " . mysql_error());
				$rez = $proc;
			}
		}
		mysql_close($db_2621);
	}
	return $rez;
}
//-----------------------
/** 
* Вывод последних опубликованных постов
* @param нет
* @return string html-код со слоем, где в таблице размещены последние посты
*/
function latest_posts()
{
	global $DBASE, $MY_RULZ, $LF_other;
	$rarr = array();
	$rez = "<div class='eform smf'><span class='b'>".$LF_other['latestposts']."</span><table class='form'>";
	$db_2674 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_2674&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."PSTZ` as `t2` join (SELECT max(`pdate`) as pd FROM `".$DBASE['prefix']."PSTZ` GROUP BY `ptask`,`postintask` ORDER BY `pdate` DESC) as `t1` WHERE `t1`.`pd`=`t2`.`pdate` ORDER BY `t2`.`pdate` DESC LIMIT 5";
		$qrez = mysql_query($que, $db_2674) or die(mysql_errno() . ": " . mysql_error());
		while($qrezu=mysql_fetch_array($qrez))
			$rarr[] = $qrezu;
		mysql_close($db_2674);
		for($i=0; $i<count($rarr);$i++)
		{
			$auth = array_map('infiltrtext', user_exist($rarr[$i]['pauthor']));
			$post_words = explode(" ", unparse_post($rarr[$i]['pcontent']));
			$pcount = count($post_words);
			$post_words = implode(" ", array_map('short_words', array_slice($post_words, 0, 4)));
			$post_cut = implode(" ", array_slice(explode(" ",$post_words), 0, 4)).(($pcount>5)?('...'):(''));
			$rez .= "<tr><td><span class='smf'><a href='posts.php?id=".$rarr[$i]['pid']."'><img src='images/icons/post.gif' /> ".showftime($rarr[$i]['pdate']+ floor($MY_RULZ['plushour'])*3600)."</a> ".gen_user_link($auth).":</span><br />".$post_cut."</td></tr>"; //mb_substr($rarr[$i]['pcontent'], 0, 20, "utf-8")
		}
	}
	$rez .= "</table></div>";
	return $rez;
}
//-----------------------
/** 
* Редирект на страницу с постом по "короткой ссылке"
* @param $pid идентификатор поста
* @return string url страницы, на которую происходит редирект
*/
function redir_post($pid)
{
	global $DBASE, $MY_RULZ, $POSTS_ON_PAGE;
	$rez = "";
	$larr = array("projects", "tasklist", "groups", "news", "events", "files", "bugs");
	$db_2703 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
	if($db_2703&&mysql_select_db($DBASE['name']))
	{
		$que = "SELECT * FROM `".$DBASE['prefix']."PSTZ` WHERE `pid`='".floor($pid)."'";
		$qrez = mysql_query($que, $db_2703) or die(mysql_errno() . ": " . mysql_error());
		$qrezu=mysql_fetch_array($qrez);
		if($qrezu['pid'])
		{
			$que2 = "SELECT COUNT(*) as `bI` FROM `".$DBASE['prefix']."PSTZ` WHERE `ptask`='".floor($qrezu['ptask'])."' AND `postintask`='".floor($qrezu['postintask'])."' AND `pid`<'".floor($qrezu['pid'])."'";
			$qrez2 = mysql_query($que2, $db_2703) or die(mysql_errno() . ": " . mysql_error());
			$qrezu2 = mysql_fetch_array($qrez2);
			if($qrezu2['bI']>=0)
			{
				$page = ceil(($qrezu2['bI']+1)/$POSTS_ON_PAGE);
				$rez = $larr[floor($qrezu['postintask'])].".php?id=".floor($qrezu['ptask'])."&page=".$page."#".floor($pid);
			}
		}
		mysql_close($db_2703);
	}
	return $rez;
}
//-----------------------
/** 
* Выдача файла на скачивание (с возможностью докачки)
* @param $filepath – string путь к файлу, который мы хотим отдать
* @param $filename – string под каким именем файл отдаётся
* @param $mimetype – string тип отдаваемых данных (можно не менять)
* @return string url страницы, на которую происходит редирект
*/
function func_download_file($filepath, $filename, $mimetype = 'application/octet-stream')
{
				$fsize = filesize($filepath); // берем размер файла
				$ftime = date('D, d M Y H:i:s T', filemtime($filepath)); // определяем дату его модификации
				$fd = @fopen($filepath, 'rb'); // открываем файл на чтение в бинарном режиме
				if(isset($_SERVER['HTTP_RANGE']))
				{ // поддерживается ли докачка?
								$range = $_SERVER['HTTP_RANGE']; // определяем, с какого байта скачивать файл
								$range = str_replace('bytes=', '', $range);
								list($range, $end) = explode('-', $range);
								if (!empty($range))
												fseek($fd, $range);
				}
				else
								$range = 0; // докачка не поддерживается
				if($range)
								header($_SERVER['SERVER_PROTOCOL'].' 206 Partial Content'); // говорим браузеру, что это часть какого-то контента
				else
								header($_SERVER['SERVER_PROTOCOL'].' 200 OK'); // стандартный ответ браузеру
				// прочие заголовки, необходимые для правильной работы
				$filename = str_replace(" ", "_", $filename);
				header('Content-Disposition: attachment; filename='.$filename);
				header('Last-Modified: '.$ftime);
				header('Accept-Ranges: bytes');
				header('Content-Length: '.($fsize - $range));
				if($range)
								header("Content-Range: bytes $range-".($fsize - 1).'/'.$fsize);
				header('Content-Type: '.$mimetype);
				fpassthru($fd); // отдаем часть файла в браузер (программу докачки)
				fclose($fd);
				exit;
}
//-----------------------
/** 
* roadMap()
* Генерация календаря задач
* @param $pid int идентификатор проекта для отображения
* @param $isProject int определяет роадмап для проекта (1) или для задачи (0) 
* @return string html с календарём задач
*/
function roadMap($pid, $isProject=1)
{
	global $DBASE, $MY_RULZ, $LF_time, $LF_other, $SEC, $LF_TASK_STT;
	$tableCols = 10;
	$cellWidth = 50; //px
	$pid = intval($pid);
	$resultString = "";
	$db = new ProjectorConnect();
	if($isProject==1)
		$tasks = $db->ProjectorQuery('SELECT * FROM '.$DBASE['prefix'].'TSKZ, '.$DBASE['prefix'].'UZRZ WHERE tpro='.$pid.' AND tmaker=uid AND tmtask=0 ORDER BY tbdate ASC');
	else
		$tasks = $db->ProjectorQuery('SELECT * FROM '.$DBASE['prefix'].'TSKZ, '.$DBASE['prefix'].'UZRZ WHERE tmtask='.$pid.' AND tmaker=uid ORDER BY tbdate ASC');
	if(count($tasks)>0)
	{
		if($isProject==1)
			$dates = $db->ProjectorQuery('SELECT min(`tbdate`),  max(`tedate`) FROM '.$DBASE['prefix'].'TSKZ WHERE tpro='.$pid.' AND tmtask=0');
		else
			$dates = $db->ProjectorQuery('SELECT min(`tbdate`),  max(`tedate`) FROM '.$DBASE['prefix'].'TSKZ WHERE tmtask='.$pid);
		$tFirstDate = $dates[0]['min(`tbdate`)'];
		$tLastDate = $dates[0]['max(`tedate`)'];
		$dDate = $tLastDate - $tFirstDate;
		$dTime = array(0, 24, 24*10, 24*30, 24*365);
		$timeRow2 = 0;
		for($i=1; $i<count($dTime); $i++)
		{
			$lowLimit = $dTime[$i-1]*3600*$tableCols;
			$hightLimit = $dTime[$i]*3600*$tableCols;
			if($dDate>$lowLimit&&$dDate<=$hightLimit)
			{
				$timeRow2 = $i;
				break;
			}
		}
		$resultString = "<script type='text/javascript' src='{$SEC['proektor_path']}/images/jquery.tooltip.js'></script>
<script type='text/javascript'>
$(function() {
$('#tooltiper a').tooltip({
				track: false,
				delay: 0,
				showURL: false,
				fade: 200
});
});
</script>
<table width='100%' rules='all' border='1' class='list_odd'>";
		$DeltaTime = $dTime[$i]*3600;
		$period = $DeltaTime*$tableCols;
		$tdRow1 = array();
		$tdRow2 = array();
		$monthId = array();
		$monthCols = array();
		$eps = 0;
		$tFirstDateDay = date('d', $tFirstDate);
		$tFirstDateArray = getdate($tFirstDate);
		//--- switch
		switch($timeRow2)
		{
			// поденная разбивка
			case 1:
				$tFirstDateDayOpty = mktime(0, 0, 0, $tFirstDateArray['mon'], $tFirstDateArray['mday'], $tFirstDateArray['year']);
				$tFirstDate = $tFirstDateDayOpty;
				$debStr = print_r($tFirstDateArray, true);
				$firstDay = intval(date('d', $tFirstDateDayOpty));
				$daysInMonth = date('t', $tFirstDateDayOpty);
				for($l=0; $l<$tableCols; $l++)
				{
					$tdRow2[$l] = ($firstDay+$l-1)%$daysInMonth+1;
					$tdRow1[$l] = intval(date('m', $tFirstDateDayOpty))+floor(($firstDay+$l-1)/$daysInMonth);
				}
				$timeRow2String = '<tr><th>'.$LF_time['day'][0].'</th>';
				for($n=0; $n<count($tdRow1); $n++)  // группируем одинаковые месяцы
				{
					if($n==0)
					{
						$monthId[0] = $tdRow1[$n];
						$monthCols[0] = 1;
					}
					else
					{
						$lastId = count($monthId)-1;
						if($tdRow1[$n]==$monthId[$lastId])
							$monthCols[$lastId] += 1;
						else
						{
							$monthId[$lastId+1] = $tdRow1[$n];
							$monthCols[$lastId+1] = 1;
						}
					}
				}
				$timeRow1String = '<tr><th>'.$LF_time['month'][0].'</th>';
				for($m=0; $m<count($monthId); $m++)
				{
					$timeRow1String .= '<th colspan="'.intval($monthCols[$m]).'">'.$LF_time['mon_short'][(intval($monthId[$m])-1)].'</th>';
				}
				$timeRow1String .= '</tr>';
			break;
			// декадная разбивка
			case 2:
				if($tFirstDateDay>29)
					$tFirstDateOpty = mktime(0, 0, 0, $tFirstDateArray['mon'], 21, $tFirstDateArray['year']);
				else
					$tFirstDateOpty = mktime(0, 0, 0, $tFirstDateArray['mon'], (floor($tFirstDateArray['mday']/10)+1), $tFirstDateArray['year']);
				$tFirstDate = $tFirstDateOpty;
				$tFirstDateDayOpty = date('d', $tFirstDateOpty);
				$firstDecade = floor($tFirstDateDayOpty/10);
				for($l=0; $l<$tableCols; $l++)
				{
					$decades = $firstDecade + $l;
					$tdRow2[$l] = 1+($decades%3);
					$tdRow1[$l] = intval(date('m', $tFirstDateOpty))+floor(($decades)/3);
				}
				$timeRow2String = '<tr><th>'.$LF_time['decade'][0].'</th>';
				for($n=0; $n<count($tdRow1); $n++)  // группируем одинаковые месяцы
				{
					if($n==0)
					{
						$monthId[0] = $tdRow1[$n];
						$monthCols[0] = 1;
					}
					else
					{
						$lastId = count($monthId)-1;
						if($tdRow1[$n]==$monthId[$lastId])
							$monthCols[$lastId] += 1;
						else
						{
							$monthId[$lastId+1] = $tdRow1[$n];
							$monthCols[$lastId+1] = 1;
						}
					}
				}
				$timeRow1String = '<tr><th>'.$LF_time['month'][0].'</th>';
				for($m=0; $m<count($monthId); $m++)
				{
					$timeRow1String .= '<th colspan="'.intval($monthCols[$m]).'">'.$LF_time['mon_short'][(intval($monthId[$m])-1)].'</th>';
				}
				$timeRow1String .= '</tr>';
			break;
			// месячная разбивка
			case 3:
				$tFirstDateMonthOpty = mktime(0, 0, 0, $tFirstDateArray['mon'], 1, $tFirstDateArray['year']);
				$tFirstDate = $tFirstDateMonthOpty;
				$firstMonth = intval(date('m', $tFirstDateMonthOpty));
				for($l=0; $l<$tableCols; $l++)
				{
					$tdRow2[$l] = ($firstMonth+$l-1)%12+1;
					$tdRow1[$l] = intval(date('Y', $tFirstDateMonthOpty))+floor(($firstMonth+$l-1)/12);
				}
				$timeRow2String = '<tr><th>'.$LF_time['month'][0].'</th>';
				$timeRow1String = '<tr><th>'.$LF_time['year'][0].'</th>';
				for($n=0; $n<count($tdRow1); $n++)  // группируем одинаковые месяцы
				{
					if($n==0)
					{
						$monthId[0] = $tdRow1[$n];
						$monthCols[0] = 1;
					}
					else
					{
						$lastId = count($monthId)-1;
						if($tdRow1[$n]==$monthId[$lastId])
							$monthCols[$lastId] += 1;
						else
						{
							$monthId[$lastId+1] = $tdRow1[$n];
							$monthCols[$lastId+1] = 1;
						}
					}
				}
				for($m=0; $m<count($monthId); $m++)
				{
					$timeRow1String .= '<th colspan="'.intval($monthCols[$m]).'">'.intval(($monthId[$m])).'</th>';
				}
				$timeRow1String .= '</tr>';
			break;
			// годовая разбивка
			default:
				$tFirstDateYearOpty = mktime(0, 0, 0, 0, 1, $tFirstDateArray['year']);
				$tFirstDate = $tFirstDateYearOpty;
				$firstYear = intval($tFirstDateArray['year']);
				for($l=0; $l<$tableCols; $l++)
				{
					$tdRow2[$l] = $firstYear+$l;
					$tdRow1[$l] = '&nbsp;';
				}
				$timeRow2String = '<tr><th>'.$LF_time['year'][0].'</th>';
				$timeRow1String = '';
			break;
		}
		//--- END switch
		// генерируем заголовок таблицы
		for($j=0; $j<$tableCols; $j++)
		{
			$timeRow2String .= '<th width="'.($cellWidth-1).'px">'.$tdRow2[$j].'</th>';
		}
		$resultString .= $timeRow1String.$timeRow2String.'</tr>';
		// инфо для изображений
		$totalWidth =  $tableCols * $cellWidth;
		// выводим задачи
		for($i=0; $i<count($tasks); $i++)
		{
			$imgDTimeBegin = floatval($tasks[$i]['tbdate'])-$tFirstDate;
			if($imgDTimeBegin<0)
				$imgDTimeBegin = 0;
			$imgDPixelBegin = intval(($totalWidth*$imgDTimeBegin)/$period);
			$imgDTimeWork = floatval($tasks[$i]['tedate']) - floatval($tasks[$i]['tbdate']);
			$imgDPixelWork = intval(($totalWidth*$imgDTimeWork)/$period);
			if($imgDPixelWork<1)
				$imgDPixelWork = 1;
			$timeprogress = $tasks[$i]['tready'];
			$rcolor = ($timeprogress<50)?(intval(255*$timeprogress/50)):(255);
			$gcolor = ($timeprogress>50)?(intval(255*(100-$timeprogress)/50)):(255);
			$dopcolor = ""; 
			if($tasks[$i]['tstate']==1&&time()>$tasks[$i]['tedate'])
				$dopcolor = "color:  #880000";
			else if($tasks[$i]['tstate']==0||$tasks[$i]['tstate']==2)
				$dopcolor = "color:  #000088";
			$resultString .= '<tr>
			<td class="smf" id="tooltiper">'.($i+1).'. <a style="'.$dopcolor.'" href="tasklist.php?id='.$tasks[$i]['tid'].'" title="'.$LF_other['maker'].': '.infiltrtext($tasks[$i]['nick']).'<br />'.$LF_other['taskready'].': '.$LF_TASK_STT[$tasks[$i]['tstate']].(($tasks[$i]['tstate']==1)?(' ('.floor($tasks[$i]['tready']).'%)'):('')).'<br />'.$LF_other['task_start'].': '.showftime($tasks[$i]['tbdate']).'<br />'.$LF_other['task_end'].': '.showftime($tasks[$i]['tedate']).'">'.infiltrtext($tasks[$i]['tname']).'</a></td>
			<td colspan="'.($tableCols).'">
				<img src="images/empty.png" style="margin: 0px 0px 0px '.$imgDPixelBegin.'px; height: 20px; width: '.$imgDPixelWork.'px; background-color: rgb('.$rcolor.','.$gcolor.',0)" />
			</td>
			</tr>';
		}
		$resultString .= '</table>';
	}
	return $resultString;
}
//----------------------------------
/**
*   showBugList()
*   Show bug-list for selected project 
*
*/
function showBugList($pid)
{
	global $DB, $DBASE, $LF_other;
	include('tpl/addBugForm.php');
	if(!isset($DB->dbconn))
		$DB = new ProjectorConnect();
	$bugs = $DB->ProjectorQuery('SELECT * FROM '.$DBASE['prefix'].'BGZ WHERE bpid='.intval($pid));
	$c = count($bugs);
	$resultString = <<<BFORM
	<span class='notlink'>{ {$LF_other['bugs']}: {$c} }</span>
	<div class='hint'>
	<a name='addNewBug' title='{$LF_other['bugDetected']}' class='notlink'><img class="smallicon" src="images/icons/add.gif" /></a>
	<div class='hint'>{$ADDBUGFORM}</div>
BFORM;
	if(count($bugs)<1)
	{
		$resultString .= "<span class='rwe'>".$LF_other['noBugs']."</span>";
	}
	else
	{
		$resultString .= "<table width='100%' rules='all'>";
		for($i=0; $i<count($bugs);$i++)
		{
			$bugs[$i] = array_map('infiltrtext', $bugs[$i]);
			$resultString .= "<tr><td>".($i+1)."</td><td>[".$LF_other['bugState'][$bugs[$i]['bstate']]."]</td><td><a href='bugs.php?id=".$bugs[$i]['bid']."'>#".$bugs[$i]['bid']." ".$bugs[$i]['bname']."</a></td><td>";
			if($bugs[$i]['btid']>0)
				$resultString .= "<a href='tasklist.php?id=".$bugs[$i]['btid']."'>".$LF_other['linkedTask']."</a>";
			else
				$resultString .= '&nbsp;';
			$resultString .= "</td></tr>";
		}
		$resultString .= "</table>";
	}
	$resultString .= "</div>";
	return $resultString;
}
//----------------------------------
/**
*   add_new_bug()
*
*/
function add_new_bug($form)
{
	global $DB, $DBASE, $MY_RULZ, $SEC, $LF_other;
	$resultURL = '';
	if($MY_RULZ['state']>0)
	{ 
		if(!isset($DB->dbconn))
			$DB = new ProjectorConnect();
		$form['bid'] = intval($form['bid']);
		if($form['bid']>-1)
		{
			$resultURL = 'bugs.php';
			//* editting existing bug
			$bug = $DB->ProjectorQuery('SELECT * FROM '.$DBASE['prefix'].'BGZ  WHERE bid='.$form['bid']);
			if(count($bug)>0)
			{
				$theForm = array_map('mysql_real_escape_string', $form); 
				if($theForm['bugTask']<0)
				{
					/*wttemp("[a=http://{$_SERVER['SERVER_NAME']}/{$SEC['proektor_path']}bugs.php?id=".$form['bid']."]");*/
					$newTask = $DB->ProjectorQuery("INSERT INTO {$DBASE['prefix']}TSKZ VALUES ('0', '{$LF_other['fixingBug']} #{$bug[0]['bid']} ', '{$LF_other['fixingBug']} [a=http://{$_SERVER['SERVER_NAME']}{$SEC['proektor_path']}/bugs.php?id=".$theForm['bid']."]#{$bug[0]['bid']}[/a]', '{$bug[0]['bpid']}', '".$MY_RULZ['uid']."', '".time()."', '".(time()+10000)."', '1', '0', '0','0','0')");
					$theForm['bugTask'] = mysql_insert_id();
				}
				wttemp($theForm['bugState']);
				$editBug = $DB->ProjectorQuery("UPDATE {$DBASE['prefix']}BGZ SET `bname`='{$theForm['bugHead']}', `bdescription`='{$theForm['bugDesc']}', `btid`='{$theForm['bugTask']}', `bstate`='".($theForm['bugState']%count($LF_other['bugState']))."'");
				//* need to add notice
				if($editBug)
				{
					$resultURL .= "?id={$bug[0]['bid']}&mid=9";
				}
				else
				{
					$resultURL .= "?id={$bug[0]['bid']}&mid=8";
				}
			}
			else
				$resultURL .= "?mid=5";
		}
		else
		{
			$form['pid'] = intval($form['pid']);
			//* adding new bug
			$project = $DB->ProjectorQuery('SELECT * FROM '.$DBASE['prefix'].'PRJCTZ  WHERE pstate=1 AND pid='.$form['pid']);
			if(count($project)>0)
			{
				$form = array_map('mysql_real_escape_string', $form);
				$bug = $DB->ProjectorInsUpdQuery('INSERT INTO '.$DBASE['prefix'].'BGZ VALUES (0, 0 , "'.$form["bugHead"].'", "'.$form["bugDesc"].'", '.$form['pid'].', 0, '.$MY_RULZ['uid'].', '.time().')');
				if($bug[0])
					$mid = 9;
				else
					$mid = 8;
			}
			else
				$mid = 8;
			$resultURL = 'projects.php?mid='.$mid.'&id='.$form['pid'];
		}
		
	}
	return $resultURL;
}
//----------------------------------
/**
*   show_bug_info()
*
*/
function show_bug_info($id)
{
	global $DB, $DBASE, $MY_RULZ, $LF_other;
    $id = intval($id);
	if(!isset($DB->dbconn))
		$DB = new ProjectorConnect();
	$resultString = "";
	if($id>-1)
	{
		$bug = $DB->ProjectorQuery('SELECT * FROM '.$DBASE['prefix'].'BGZ, '.$DBASE['prefix'].'PRJCTZ, '.$DBASE['prefix'].'UZRZ  WHERE bpid=pid AND uid=plider AND bid='.$id);
        if(count($bug)>0)
        {
            $bug[0] = array_map('infiltrtext', $bug[0]);
			$editLinks = '';
			if($MY_RULZ['state']>1||$MY_RULZ['uid']==$bug[0]['buid'])
			{
				$editLinks = "[<a href='edit.php?bug={$id}'>{$LF_other['editBug']}</a>] ".
				"[<a href='javascript: if(confirm(\"{$LF_other['rlyDelBug']}\")) window.location.replace(\"actions.php?delBug={$id}\");'>{$LF_other['deleteBug']}</a>] ";
			}
            $resultString = "<p>{$editLinks}[<a href='bugs.php'>{$LF_other['fullBugList']}</a>]</p><table class='card'><tr><td>".
            "<table class='form'><tr><td>{$LF_other['bugHeader']}</td><td>#".$bug[0]['bid']." ".$bug[0]['bname']."</td></tr><tr><td>{$LF_other['state']}</td><td>".$LF_other['bugState'][$bug[0]['bstate']]."</td></tr><tr><td>{$LF_other['bugDesc']}</td><td>".$bug[0]['bdescription']."</td></tr><tr><td>{$LF_other['added']}</td><td>".showftime($bug[0]['btime']+(floor($MY_RULZ['plushour'])*3600))."</td></tr><tr><td>{$LF_other['linkedTask']}</td><td>".
			($bug[0]['btid']>0?"<a href='tasklist.php?id=".intval($bug[0]['btid'])."'>{$LF_other['Link']}</a>":$LF_other['no'])."</td></tr><tr><td>     
            {$LF_other['projectname']}</td><td><a href='projects.php?id=".$bug[0]['pid']."'>".$bug[0]['pname']."</a></td></tr></table>".show_posts($id, 6, (isset($_GET['page']))?($_GET['page']):(-1))."</td></tr></table>";
        }
	}
	if($resultString == "")
	{
		$bugs = $DB->ProjectorQuery('SELECT * FROM '.$DBASE['prefix'].'BGZ, '.$DBASE['prefix'].'PRJCTZ  WHERE bpid=pid ORDER BY pid ASC');
		if(count($bugs)>0)
		{
			$resultString = "<table class='card'><tr><td><table class='form'>";
			for($i=0; $i<count($bugs); $i++)
			{
				$bugs[$i] = array_map('infiltrtext', $bugs[$i]);
				$resultString .= "<tr><td>".($i+1).".</td><td>@<a href='projects.php?id=".$bugs[$i]['pid']."'>".$bugs[$i]['pname']."</a></td><td>[".$LF_other['bugState'][$bugs[$i]['bstate']]."]</td><td><a href='bugs.php?id=".$bugs[$i]['bid']."'>#".$bugs[$i]['bid']." ".$bugs[$i]['bname']."</a></td><td>";
				if($bugs[$i]['btid']>0)
					$resultString .= "<a href='tasklist.php?id=".$bugs[$i]['btid']."'>".$LF_other['linkedTask']."</a>";
				else
					$resultString .= '&nbsp;';
				$resultString .= "</td></tr>";
			}
			$resultString .= "</table></td></tr></table>";
		}
			
		else
		{
			$resultString = "<table class='card'><tr><td>{$LF_other['noBugs']}</td></tr></table>";
		}
	}
    return $resultString;
}
//----------------------------------
/**
*   deleteBug()
*
*/
function deleteBug($id)
{
	global $DB, $DBASE, $MY_RULZ, $LF_other;
	$resultString = 'bugs.php';
    $id = intval($id);
	if(!isset($DB->dbconn))
		$DB = new ProjectorConnect();
	$bug = $DB->ProjectorQuery('SELECT * FROM '.$DBASE['prefix'].'BGZ, '.$DBASE['prefix'].'PRJCTZ  WHERE bpid=pid AND bid='.$id);
	if(count($bug)>0)
	{
		if($MY_RULZ['state']>2||$MY_RULZ['uid']==$bug[0]['plider'])
		{
			if($DB->ProjectorQuery('DELETE FROM '.$DBASE['prefix'].'BGZ WHERE bid='.$id))
			{
				$DB->ProjectorQuery('DELETE FROM '.$DBASE['prefix'].'PSTZ WHERE postintask=6 AND ptask='.$id);
				$resultString .= "?mid=9";
			}
			else
				$resultString .= "?mid=8";
		}
		else
			$resultString .= "?mid=7";
	}
	else
		$resultString .= "?mid=5";
	return $resultString;
}
//----------------------------------
/**
*   showEditBugForm()
*
*/
function showEditBugForm($id)
{
	global $DB, $DBASE, $MY_RULZ, $LF_other, $LF_mess;
	$resultString = $LF_mess[7];
	$id = intval($id);
	if(!isset($DB->dbconn))
		$DB = new ProjectorConnect();
	$bug = $DB->ProjectorQuery('SELECT * FROM '.$DBASE['prefix'].'BGZ, '.$DBASE['prefix'].'PRJCTZ WHERE bpid=pid  AND bid='.$id);
	if(count($bug)>0)
	{
		if($MY_RULZ['state']>1||$MY_RULZ['uid']==$bug[0]['plider'])
		{
			$theBug = array_map('infiltrtext', $bug[0]);
			$theBug['btid'] = intval($theBug['btid']);
			$bugStateList = generateAnyList('bugState', $LF_other['bugState'], $theBug['bstate']);
			$budLinkedTask = "";
			if($theBug['btid']>0)
				$budLinkedTask = "<li><input type='radio' name='bugTask' value='{$theBug['btid']}'  checked='checked' />{$LF_other['useExisting']}: <a href='tasklist.php?id={$theBug['btid']}'>{$LF_other['linkedTask']}</a></li>";
			$bugAddedTime = showftime($bug[0]['btime']+(floor($MY_RULZ['plushour'])*3600));
			$resultString = "<table class='card'><tr><td>";
			include('tpl/addBugFormLong.php');
			$resultString .= $ADDBUGFORMLONG."</td></tr></table>";
		}
	}
	else
	{
		$resultString = $LF_mess[8];
	}
	return $resultString;
}
?>