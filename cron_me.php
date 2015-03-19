<?php
include("config.php");
include("library.php");
$pm_list = array();
$db_4 = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
if($db_4&&mysql_select_db($DBASE['name']))
{
 $que = "SELECT * FROM `".$DBASE['prefix']."TSKZ`, `".$DBASE['prefix']."PRJCTZ` WHERE `".$DBASE['prefix']."TSKZ`.`tnotice`<'2' AND `".$DBASE['prefix']."TSKZ`.`tpro`=`".$DBASE['prefix']."PRJCTZ`.`pid` AND `".$DBASE['prefix']."TSKZ`.`tstate`='1'";
 $qrez = mysql_query($que, $db_4) or die(mysql_errno() . ": " . mysql_error());
 while($qrezu=mysql_fetch_assoc($qrez))
 {
  if(time()>=$qrezu['tedate'])
  {
   $pm_list[] = array('taskfail', $qrezu['tmaker'], $qrezu);
   if($qrezu['plider']!=$qrezu['tmaker'])
    $pm_list[] = array('taskfail', $qrezu['plider'], $qrezu);
   $que2 = "UPDATE `".$DBASE['prefix']."TSKZ` SET `tnotice`='2' WHERE `tid`='".floor($qrezu['tid'])."'";
   $qrez2 = mysql_query($que2, $db_4) or die(mysql_errno() . ": " . mysql_error());
  }
  else if((time()+$TASK_NOTICE_TIME)>=$qrezu['tedate']&&$qrezu['tnotice']<1)
  {
   $pm_list[] = array('tasknotice', $qrezu['tmaker'], $qrezu);
   //$pm_list[] = array('tasknew', $qrezu['tmaker'], $qrezu);
   if($qrezu['plider']!=$qrezu['tmaker'])
    $pm_list[] = array('tasknotice', $qrezu['plider'], $qrezu);
   $que2 = "UPDATE `".$DBASE['prefix']."TSKZ` SET `tnotice`='1' WHERE `tid`='".floor($qrezu['tid'])."'";
   $qrez2 = mysql_query($que2, $db_4) or die(mysql_errno() . ": " . mysql_error()); 
  }
 }
 $que3 = "DELETE FROM `".$DBASE['prefix']."MSSGZ` WHERE `mget`='3' AND `mauthor`='0'";
 $qrez3 = mysql_query($que3, $db_4) or die(mysql_errno() . ": " . mysql_error());
 mysql_close($db_4);
}
//-------------------------
wttemp(serialize($pm_list));
for($i=0; $i<count($pm_list); $i++)
 autoprivatemessage($pm_list[$i][0], $pm_list[$i][1], $pm_list[$i][2]);
 //------------------------
file_put_contents("last_cron.txt", time());
?>
