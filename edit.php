<?php
include("template.php");
$rez = "";
//--------------------------
// редактируем пост
if(isset($_GET['post']))
{
  $db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
  if($db&&mysql_select_db($DBASE['name']))
  {
   $que = "SELECT * FROM `".$DBASE['prefix']."PSTZ` WHERE `pid`='".floor($_GET['post'])."'";
   $qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
   $qrez1 = mysql_fetch_assoc($qrez);
   mysql_close($db);
   if(!$qrez1['pid'])
   {
    mysql_close($db);
    header("Location: tasklist.php?user=".$MY_RULZ['uid']."&mid=8");
    exit(0);
   }
   else if($MY_RULZ['state']<2)
   {
    if($MY_RULZ['uid']!=$qrez1['pauthor']||(time()-$qrez1['pdate']>$POST_EDIT_TIME))
    {
     header("Location: tasklist.php?user=".$MY_RULZ['uid']."&mid=7");
     exit(0);
    } 
   }
   $rez = "<h3>".$LF_other['editpost']."</h3>".show_edit_post_form(0, $qrez1);
  }
}
//-----------------------
// редактируем профиль пользователя
else if(isset($_GET['user']))
{
 $user = user_exist($_GET['user']);
 if(isset($user['uid'])&&$user['uid']>0)
 {
  if($MY_RULZ['state']>2||$MY_RULZ['uid']==$user['uid'])
   $rez = show_edit_profile_form(1, $user);
  else
  {
   header("Location: userlist.php?mid=6");
   exit(0);
  }
 }
 else
 {
  header("Location: userlist.php?mid=5");
  exit(0);
 }
}
//-----------------------
// добавляем новость
else if(isset($_GET['news']))
{
 if($MY_RULZ['state']<2)
 {
  header("Location: tasklist.php?id=".$MY_RULZ['uid']."&mid=8");
  exit(0);
 }
}
//-----------------------
// добавляем событие
else if(isset($_GET['event']))
{
 if($MY_RULZ['state']<2)
 {
  header("Location: tasklist.php?id=".$MY_RULZ['uid']."&mid=8");
  exit(0);
 }
}
//-----------------------
// добавляем задачу
else if(isset($_GET['task'])&&isset($_GET['project']))
{
 // выкинуть не имеющего к проекту отношения юзера
 if($MY_RULZ['state']<1)
 {
  header("Location: tasklist.php?id=".$MY_RULZ['uid']."&mid=8");
  exit(0);
 }
}
//-----------------------
// добавляем файл
else if(isset($_GET['addfile2task']))
{
 if(!get_task_link($_GET['addfile2task'])&&0!=floor($_GET['addfile2task']))
 {
  header("Location: news.php?mid=5");
  exit(0);
 }
}
//-----------------------
// добавляем подзадачу
else if(isset($_GET['mtask']))
{
 if(!is_user_in_task($MY_RULZ['uid'], $_GET['mtask']))
 {
  header("Location: tasklist.php?id=".$MY_RULZ['uid']."&mid=5");
  exit(0);
 }
 else
 {
  $rez = "<h3>".$LF_other['addmtask']."</h3>";
  $rez .= show_add_subtask_form($_GET['mtask']);
 }
}
//-----------------------
// редактируем файл
else if(isset($_GET['editfile']))
{
 if($MY_RULZ['state']<1)
 {
  header("Location: index.php?mid=5");
  exit(0);
 }
}
//-----------------------
// редактируем группу
else if(isset($_GET['group']))
{
 if($MY_RULZ['state']<3)
 {
  header("Location: groups.php?id=".floor($_GET['group'])."&mid=8");
  exit(0);
 }
 else
 {
  if(floor($_GET['group'])==-1)
   $rez = "<h3>".$LF_other['addgroup']."</h3>";
  else
   $rez = "<h3>".$LF_other['editgroup']."</h3>";  
  $rez .= show_edit_group_form($_GET['group']);
 } 
}
//--------------------------
//  редактируем проект
else if(isset($_GET['project']))
{
 if($MY_RULZ['state']>0&&$_GET['project']!=-1)
 {
  $db = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
  if($db&&mysql_select_db($DBASE['name']))
  {
   $que = "SELECT * FROM `".$DBASE['prefix']."PRJCTZ` WHERE `pid`='".floor($_GET['project'])."'";
   $qrez = mysql_query($que, $db) or die(mysql_errno() . ": " . mysql_error());
   $qrez1 = mysql_fetch_assoc($qrez);
   if($qrez1['pid'])
   {
    if($MY_RULZ['state']<3&&$qrez1['plider']!=$MY_RULZ['uid'])
    {
     mysql_close($db);
     header("Location: tasklist.php?id=".$MY_RULZ['uid']."&mid=7");
     exit(0);
    }
   }
   else
   {
    mysql_close($db);
    header("Location: tasklist.php?id=".$MY_RULZ['uid']."&mid=5");
    exit(0);
   }
   mysql_close($db);
  }
 }
}
//-----------------------
// editting bug
else if(isset($_GET['bug']))
{
 if($MY_RULZ['state']<1)
 {
  header("Location: index.php?mid=5");
  exit(0);
 }
}
//------------------------
else
{
 header("Location: tasklist.php?id=".$MY_RULZ['uid']."&mid=8");
 exit(0);
} 
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
if(isset($_GET['post']))
{
 print($rez);
}
//-----------------------
// редактируем профиль пользователя || добавляем подзадачу || редактируем группу
else if(isset($_GET['user'])||isset($_GET['mtask'])||isset($_GET['group']))
{
 print($rez);
}
//-----------------------
// добавляем новость
else if(isset($_GET['news']))
{
 if($_GET['news']==-1)
  print("<h3>".$LF_other['addnews']."</h3>");
 else
  print("<h3>".$LF_other['edititems3']."</h3>");
 print(show_edit_news_form($_GET['news'], 3));
}
//-----------------------
// добавляем событие
else if(isset($_GET['event']))
{
 if($_GET['event']==-1)
  print("<h3>".$LF_other['addevent']."</h3>");
 else
  print("<h3>".$LF_other['edititems4']."</h3>");
 print(show_edit_news_form($_GET['event'], 4));
}
//-----------------------
// добавляем задачу
else if(isset($_GET['task'])&&isset($_GET['project']))
{
 if($_GET['task']==-1)
  print("<h3>".$LF_formz['addtask']."</h3>");
 else
  print("<h3>".$LF_formz['edittask']."</h3>");
 print(show_edit_task_form($_GET));
}
//-----------------------
// добавляем файл к задаче
else if(isset($_GET['addfile2task']))
{
 print("<h3>".$LF_other['addfile']."</h3>");
 print(show_edit_file_form(-1, $_GET['addfile2task']));
}
//-----------------------
// редактируем файл
else if(isset($_GET['editfile']))
{
 print("<h3>".$LF_other['editfile']."</h3>");
 print(show_edit_file_form($_GET['editfile'], -1));
}
//--------------------------
//  редактируем проект
else if(isset($_GET['project']))
{
 if($_GET['project']==-1)
  print("<h3>".$LF_other['addpro']."</h3>");
 else
  print("<h3>".$LF_other['editpro']."</h3>");
 print(show_edit_project_form($_GET['project']));
}
//--------------------------
//  редактируем баг
else if(isset($_GET['bug']))
{
 if($_GET['bug']==-1)
  print("<h3>".$LF_other['bugDetected']."</h3>");
 else
  print("<h3>".$LF_other['editBug']."</h3>");
 print(showEditBugForm($_GET['bug']));
}
?>
</td>
</tr>
</table>
<?php
print($ONLINE_USERS_STR);
print($BOTTOM_STR);
?>