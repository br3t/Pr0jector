<?php
class ProjectorSubTask extends ProjectorConnect
{
	// отображение строк таблицы с подзадачами
	function ProjectorShowSubTasksList($tid, $ctrlstr='tryauthor')
	{
		global $DBASE, $LF_other, $MY_RULZ;
		$rez = "";
		$isauthor = false;
		if(!is_resource($this->dbconn))
			$this->ProjectorConnect();
		if($ctrlstr=='tryauthor')
		{
			$uzr = $this->ProjectorQuery("SELECT `tmaker` FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($tid)."'");
		    $isauthor = (bool)($uzr[0]['tmaker']==$MY_RULZ['uid']);
		}
		else if($ctrlstr!='')
		{
			$isauthor = true;
		}
		$rarr = $this->ProjectorLastQuery("SELECT * FROM `".$DBASE['prefix']."SBTSKZ` WHERE `stask`='".floor($tid)."'");
		if(count($rarr)>0)
		{
			for($i=0; $i<count($rarr); $i++)
			{
				$clss = $i%2==0?'list_even':'list_odd';
				$rez.= "<tr class='".$clss."'><td style='width:20px'>".($i+1).".</td><td class='b'>".short_words(infiltrtext($rarr[$i]['sname']))."</td><td>".time_left(60*$rarr[$i]['stime'])."</td><td style='width:30px'><img src='images/icons/".(($rarr[$i]['sready']==1)?"yes":"no").".gif' /></td><td>";
				if($isauthor)
					$rez .= "&nbsp;<a href='javascript: show_edit_subtask_form(".floor($tid).", ".floor($rarr[$i]['sid']).", words)' title='".$LF_other['editsubtask']."'><img src='images/icons/edit.gif' class='smallicon' /></a> <a href='javascript: if(confirm(\"".$LF_other['rlydelsubtask']."\")) delete_subtask(".floor($rarr[$i]['sid']).", ".floor($tid).");' title='".$LF_other['delsubtask']."'><img src='images/icons/delete.gif' class='smallicon' /></a>";
				$rez.= "</td></tr>";
			}
			
		}
		else
			$rez = "<tr><td><span class='rse'>".$LF_other['nosubtasks']."</span></td></tr>";
		return $rez;
	}
	// отображение подзадач с формой добавления
	function ProjectorShowSubTasksTab($tid)
	{
		global $DBASE, $LF_other, $MY_RULZ;
		$rez = "";
		$this->ProjectorConnect();
		$uzr = $this->ProjectorQuery("SELECT `tmaker` FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($tid)."'");
		if($uzr[0]['tmaker']==$MY_RULZ['uid'])
			$rez .= "<script>var words = new Array(\"{$LF_other['subtaskeditform'][0]}\", \"{$LF_other['subtaskeditform'][1]}\", \"{$LF_other['subtaskeditform'][2]}\", \"{$LF_other['subtaskeditform'][3]}\", \"{$LF_other['save']}\", \"{$LF_other['cansel']}\", \"{$LF_other['subtaskeditform'][4]}\", \"{$LF_other['subtaskeditform'][5]}\");</script><p><a href='javascript:show_edit_subtask_form(".floor($tid).", -1, words);' title='".$LF_other['addmtask']."'><img class='smallicon' src='images/icons/add.gif' /></a></p><div id='stform'></div>";
		$rez .= "<table width='100%' id='subtasklist' cellspacing='0'>";
		$rez .= $this->ProjectorShowSubTasksList($tid);
		$rez .= "</table>";
		return $rez;
	}
	// редактирование подзадачи
	function EditSubTask($arr)
	{
		global $DBASE, $LF_other, $MY_RULZ;
		$this->ProjectorConnect();
		if($this->IfUserInTask($MY_RULZ['uid'], $arr['editsubtask'], $arr['stask']))
		{
			$arr = array_map('mysql_real_escape_string', $arr);
			if(trim($arr['sname'])=="")
				$arr['sname'] = $LF_other['mynewsubtask'];
			if($arr['editsubtask']==-1)
				$query = "INSERT INTO `".$DBASE['prefix']."SBTSKZ` VALUES (0, '{$arr['sname']}', '".intval($arr['stask'])."', '".intval($arr['stime']*60)."', '".($arr['sready']=='true'?'1':'0')."')";
			else
				$query = "UPDATE `".$DBASE['prefix']."SBTSKZ` SET `sname`='{$arr['sname']}', `stime`='".intval($arr['stime']*60)."', `sready`='".($arr['sready']=='false'?'0':'1')."' WHERE `sid`='".floor($arr['editsubtask'])."'";
			$uzr = $this->ProjectorQuery($query);
		}
		$rez = $this->ProjectorShowSubTasksList(floor($arr['stask']), 'allready connected');
		$this->ProjectorDisconnect();
		return $rez;
	}
	// форма редактирования подзадачи
	function ShowFormEditSubTask($sid)
	{
		global $DBASE, $LF_other, $MY_RULZ;
		$rez ='';
		$this->ProjectorConnect();
		if($this->IfUserInTask($MY_RULZ['uid'], $sid, 0))
		{
			$query = "SELECT * FROM `".$DBASE['prefix']."SBTSKZ` WHERE `sid`='".floor($sid)."'";
			$rarr = $this->ProjectorQuery($query);
			$rarr[0] = array_map('infiltrtext', $rarr[0]);
		}
		return $rarr;
	}
	// имеет ли право пользователь редактировть подзадачу
	function IfUserInTask($uid, $sid, $tid)
	{
		global $DBASE;
		if($sid>0)
			$query = "SELECT `".$DBASE['prefix']."TSKZ`.`tmaker` FROM `".$DBASE['prefix']."TSKZ`, `".$DBASE['prefix']."SBTSKZ` WHERE `sid`='".floor($sid)."' AND `".$DBASE['prefix']."TSKZ`.`tid`=`".$DBASE['prefix']."SBTSKZ`.`stask`";
		else
			$query = "SELECT `tmaker` FROM `".$DBASE['prefix']."TSKZ` WHERE `tid`='".floor($tid)."'";
		$uzr = $this->ProjectorQuery($query);
		if($uzr[0]['tmaker']==$uid)
			return true;
		else
			return false;
			
	}
	// удаление подзадачи
	function DeleteSubTask($arr)
	{
		global $DBASE, $LF_other, $MY_RULZ;
		$this->ProjectorConnect();
		if($this->IfUserInTask($MY_RULZ['uid'], $arr['deletesubtask'], $arr['stask']))
		{
			$query = "DELETE FROM `".$DBASE['prefix']."SBTSKZ` WHERE `sid`='".floor($arr['deletesubtask'])."'";
			$uzr = $this->ProjectorQuery($query);
		}
		$rez = $this->ProjectorShowSubTasksList(floor($arr['stask']), 'allready connected');
		$this->ProjectorDisconnect();
		return $rez;
	}
}
?>