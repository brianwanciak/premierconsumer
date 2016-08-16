<?php
/////////////////////////////////////////////////////////////////////////////
//      Target Country by IP Address  - advanced redirection
//		- for MySql, Plain text databases-
//	Copyright (C) 2004 Jgsoft Associates - http://www.analysespider.com/
/////////////////////////////////////////////////////////////////////////////

function anp_get_country($ipn)
{
	global $anp_mysql_host,$anp_mysql_user,$anp_mysql_pass,$anp_mysql_dbname,$anp_mysql_table;
	mysql_connect($anp_mysql_host, $anp_mysql_user, $anp_mysql_pass);
	@mysql_select_db("$anp_mysql_dbname") or die ("Unable to select database");
	$rez=mysql_query("SELECT iso_code, (ip_to - ip_from) AS d  FROM $anp_mysql_table WHERE ip_to >=$ipn AND ip_from<=$ipn ORDER BY d ASC LIMIT 0,1");
	list($c,$d)=@mysql_fetch_row($rez);
	mysql_close();
	if(_DEBUG_MODE) echo "Geting country from mysql : '$c' <br>";
	return($c);
}
?>