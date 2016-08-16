<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "deptops" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

		$departments = Depts_get_AllDepts( $dbh ) ;
		$json_data = "json_data = { \"status\": 1, \"departments\": [  " ;
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			$dept_ops = Depts_get_DeptOps( $dbh, $department["deptID"] ) ;

			$json_data .= "{ \"deptid\": $department[deptID], \"name\": \"$department[name]\", \"operators\": [  " ;
			for ( $c2 = 0; $c2 < count( $dept_ops ); ++$c2 )
			{
				$operator = $dept_ops[$c2] ;
				$requests = Chat_get_OpTotalRequests( $dbh, $operator["opID"] ) ;

				$json_data .= "{ \"opid\": $operator[opID], \"status\": $operator[status], \"name\": \"$operator[name]\", \"email\": \"$operator[email]\", \"requests\": $requests }," ;
			}
			$json_data = substr_replace( $json_data, "", -1 ) ;
			$json_data .= "	] }," ;
		}
		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else
		$json_data = "json_data = { \"status\": 0 };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>