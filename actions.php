<?php
include("config.php");
include("library.php");
$mid = -1;
$getid = "";
$redir_url = "";
//----------------------------
//  activation by email_link
if(isset($_GET['uzrcode']))
{
 //wttemp("code send");
 if(try_activate($_GET['uzrcode']))
  $mid = 1;
 else
  $mid = 0; 
}
//-------------------------------------
// try to log in
else if(isset($_POST['send']))
{
 if(isset($_POST['login'])&&isset($_POST['pass']))
 {
  $rez_arr = try_login($_POST['login'], $_POST['pass']);
  if($rez_arr['error']==-1)
  {
   //session_register('login');
   $_SESSION['login'] = $rez_arr['login'];
   //session_register('hpass');
   $_SESSION['hpass'] = passcoding($rez_arr['pass']);
  }
  else
  {
   $mid = $rez_arr['error'];
  }
 }
}
//-------------------------
else if(isset($_GET['logout']))
{
 //wttemp("logout");
 //session_unregister('login');
 //session_unregister('hpass');
 $_SESSION = array();
 header("Location: index.php");
 exit(0);
}
//--------------------
// send new mess
else if(isset($_POST['sendm']))
{
 if(isset($_POST['mto']))
  if(user_exist($_POST['mto']))
  {
   $MY_RULZ = are_logged();
   send_message($_POST, $MY_RULZ['uid']);
  }  
 header("Location: messages.php?act=outcome");
 exit(0);
}
//--------------------
// del post
else if(isset($_GET['delpost']))
{
 $MY_RULZ = are_logged();
 $getid = del_post($_GET);
}
//--------------------
// edit post
else if(isset($_POST['editpost']))
{
 $MY_RULZ = are_logged();
 $getid = edit_post($_POST);
 if($_POST['type']==0)
  $fl = "projects";
 else
  $fl = "userlist"; 
 header("Location: ".$fl.".php?".$getid);
 exit(0);
}
//---------------------------
else if(isset($_POST['saveprofile']))
{
 $MY_RULZ = are_logged();
 if($_POST['id']==$MY_RULZ['uid']||($MY_RULZ['state']>2&&is_array(user_exist($_POST['id']))))
 {
  save_profile_changes($_POST);
  $redir_url = "userlist.php?id=".floor($_POST['id']);
 } 
 else
  $redir_url = "userlist.php?mid=8";
}
//------------------
else if(isset($_GET['delmess']))
{
 $MY_RULZ = are_logged();
 del_mess(floor($_GET['delmess']));
}
//-------------------------
else if(isset($_POST['nnews']))
{
 $MY_RULZ = are_logged();
 $redir_url = edit_news($_POST, 3);
}
//------------------
else if(isset($_GET['delnews']))
{
 $MY_RULZ = are_logged();
 $redir_url = del_item(floor($_GET['delnews']), 3);
}
//-------------------------
else if(isset($_POST['nevent']))
{
 $MY_RULZ = are_logged();
 $redir_url = edit_news($_POST, 4);
}
//------------------
else if(isset($_GET['delevent']))
{
 $MY_RULZ = are_logged();
 $redir_url = del_item(floor($_GET['delevent']), 4);
}
//-------------------------
else if(isset($_POST['addnewpost']))
{
 $MY_RULZ = are_logged();
 add_new_post($_POST);
}
//-------------------------
else if(isset($_POST['newpro'])||isset($_POST['editpro']))
{
 $MY_RULZ = are_logged();
 $redir_url = add_new_project($_POST);
}
//-------------------------
else if(isset($_GET['delproject']))
{
 $MY_RULZ = are_logged();
 $redir_url = del_project($_GET['delproject']);
}
//-------------------------
else if(isset($_POST['new_task']))
{
 $MY_RULZ = are_logged();
 $redir_url = edit_task($_POST);
}
//-------------------------
else if(isset($_GET['deltask']))
{
 $MY_RULZ = are_logged();
 $redir_url = del_task($_GET['deltask']);
}
//-------------------------
else if(isset($_POST['editfile']))
{
 $MY_RULZ = are_logged();
 $redir_url = edit_file($_POST);
}
//-------------------------
else if(isset($_GET['delfile']))
{
 $MY_RULZ = are_logged();
 $redir_url = del_file($_GET['delfile']);
}
//-------------------------
else if(isset($_POST['newgroup'])||isset($_POST['editgroup']))
{
 $MY_RULZ = are_logged();
 $redir_url = edit_group($_POST);
}
//-------------------------
else if(isset($_GET['delgroup']))
{
 $MY_RULZ = are_logged();
 $redir_url = del_group($_GET['delgroup']);
}
//-------------------------
//  AJAX добавление/удаления пользоватиля в группу
else if(isset($_POST['user2add'])&&isset($_POST['groupid']))
{
 $MY_RULZ = are_logged();
 $rarr = adduser2group($_POST);
 echo json_encode($rarr);
 exit(0);
}
//-------------------------
//  AJAX  вывод списка пользоватилей группы при редактировании задачи
else if(isset($_POST['group4users'])&&$_POST['seluser'])
{
 $MY_RULZ = are_logged(); 
 $rarr = array();
 $rarr['rez'] = gen_user_list_in_group($_POST['group4users'], $_POST['seluser']);
 echo json_encode($rarr);
 exit(0);
}
//-------------------------
//  AJAX  изменения процента выполнения задачи исполнителем
else if(isset($_POST['changetaskprogress'])&&$_POST['tid'])
{
 $MY_RULZ = are_logged(); 
 $rarr = array();
 $rarr['rez'] = change_task_progress($_POST['changetaskprogress'], $_POST['tid']);
 echo json_encode($rarr);
 exit(0);
}
//-------------------------
//  AJAX  редактирования подзадачи исполнителем
else if(isset($_POST['editsubtask']))
{
 $MY_RULZ = are_logged(); 
 $db = new ProjectorSubTask();
 $rarr = array('rez' => $db->EditSubTask($_POST));
 echo json_encode($rarr);
 exit(0);
}
//-------------------------
//  AJAX  отображения формы изменения подзадачи
else if(isset($_POST['showformsubtask']))
{
 $MY_RULZ = are_logged(); 
 $db = new ProjectorSubTask();
 $rarr = $db->ShowFormEditSubTask($_POST['showformsubtask']);
 echo json_encode($rarr);
 exit(0);
}
//-------------------------
//  AJAX  удаление подзадачи
else if(isset($_POST['deletesubtask']))
{
 $MY_RULZ = are_logged(); 
 $db = new ProjectorSubTask();
 $rarr = array('rez' => $db->DeleteSubTask($_POST));
 echo json_encode($rarr);
 exit(0);
}
//---------------------------
//  
else if(isset($_POST['addNewBug']))
{
 $MY_RULZ = are_logged();
 $redir_url = add_new_bug($_POST);
}
//---------------------------
//  delete Bug
else if(isset($_GET['delBug']))
{
 $MY_RULZ = are_logged();
 $redir_url = deleteBug($_GET['delBug']);
}
//---------------------------
if(isset($_GET['id']))
 $getid .= "&id=".$_GET['id'];
//---------------------------
if($redir_url!="")
{
 header("Location: ".$redir_url);
}
else if(isset($_SERVER['HTTP_REFERER']))
{
 $pa = explode('?', $_SERVER['HTTP_REFERER']);
 header("Location: ".$pa[0].(($mid>-1)?("?mid=".$mid):("?")).$getid);
} 
else
 header("Location: index.php?mid=".$mid);
exit(0); 
?>