<?php

class ProjectorConnect
{
	var $dbconn;
	var $qrezarr = array();
	function ProjectorConnect()
	{
		global $DBASE;
		$this->dbconn = mysql_connect($DBASE['adress'], $DBASE['login'], $DBASE['pass']);
		if (!$this->dbconn||!mysql_select_db($DBASE['name']))
		{
			die("Connection problem");
		}
	}
	function ProjectorQuery($query_str)
	{
		$this->qrezarr = array();
		//wttemp("connect_class.php => Query: ".$query_str);
		$qrez = mysql_query($query_str, $this->dbconn) or die(mysql_errno() . ": " . mysql_error());
		if(is_bool($qrez))
			$this->qrezarr[0] = $qrez;
		else
			while($fetch_row = mysql_fetch_assoc($qrez))
				$this->qrezarr[] = $fetch_row;
		return $this->qrezarr;
	}
	function ProjectorLastQuery($query_str)
	{
		$this->ProjectorQuery($query_str);
		return $this->qrezarr;
	}
	function ProjectorInsUpdQuery($query_str)
	{
		$this->qrezarr = array();
		$this->qrezarr[0] = mysql_query($query_str, $this->dbconn) or die(mysql_errno() . ": " . mysql_error());
		mysql_close($this->dbconn);
		return $this->qrezarr;
	}
	function ProjectorDisconnect()
	{
		mysql_close($this->dbconn);
	}
}

?>