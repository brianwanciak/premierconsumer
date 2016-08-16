<?php
	if ( defined( 'API_Depts_put' ) ) { return ; }
	define( 'API_Depts_put', true ) ;

	FUNCTION Depts_put_Department( &$dbh,
					$deptid,
					$name,
					$email,
					$visible,
					$queue,
					$rtype,
					$rtime,
					$rloop,
					$savem,
					$smtp,
					$tshare,
					$texpire,
					$lang )
	{
		if ( ( $name == "" ) || ( $email == "" )  || ( $rtime == "" ) || ( $lang == "" ) )
			return false ;

		global $CONF ;
		global $LANG ;

		LIST( $name ) = database_mysql_quote( $dbh, $name ) ;

		$query = "SELECT * FROM p_departments WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$department = database_mysql_fetchrow( $dbh ) ;

		if ( isset( $department["deptID"] ) )
		{
			$emailt = $department["emailt"] ; $emailt_bcc = $department["emailt_bcc"] ;
			$temail = $department["temail"] ;
			$temaild = $department["temaild"] ;
			$custom = $department["custom"] ;
			$smtp = $department["smtp"] ;

			$msg_greet = $department["msg_greet"] ;
			$msg_offline = $department["msg_offline"] ;
			$msg_busy = $department["msg_busy"] ;
			$msg_transcript = $department["msg_email"] ;
		}
		else
		{
			$deptid = "NULL" ;
			$emailt = "" ; $emailt_bcc = 0 ;
			$temail = 1 ;
			$temaild = 0 ;
			$custom = "" ;
			$smtp = ( !$smtp ) ? "" : $smtp ;

			$msg_greet = $LANG["CHAT_NOTIFY_LOOKING_FOR_OP"] ;
			$msg_offline = $msg_busy = $LANG["CHAT_NOTIFY_OP_NOT_FOUND"] ;
			$msg_transcript = "Hi %%visitor%%,\r\n\r\nThank you for taking the time to chat with us.  Here is the complete chat transcript for your reference:\r\n\r\n%%transcript%%\r\n\r\nThank you,\r\n%%operator%%\r\n%%op_email%%\r\n" ;
		}

		LIST( $deptid, $email, $emailt, $emailt_bcc, $visible, $queue, $rtype, $rtime, $rloop, $savem, $custom, $smtp, $tshare, $texpire, $temail, $lang, $msg_greet, $msg_offline, $msg_busy, $transcript ) = database_mysql_quote( $dbh, $deptid, $email, $emailt, $emailt_bcc, $visible, $queue, $rtype, $rtime, $rloop, $savem, $custom, $smtp, $tshare, $texpire, $temail, $lang, $msg_greet, $msg_busy, $msg_offline, $msg_transcript ) ;

		$query = "INSERT INTO p_departments VALUES ( $deptid, $visible, $queue, $tshare, $texpire, 1, 1, $temail, $temaild, $rtype, $rtime, $rloop, $savem, '$custom', '$smtp', '$lang', '$name', '$email', '$emailt', $emailt_bcc, '$msg_greet', '$msg_offline', '$msg_busy', '$transcript' ) ON DUPLICATE KEY UPDATE name = '$name', email = '$email', visible = $visible, queue = $queue, rtype = $rtype, rtime = $rtime, rloop = $rloop, savem = $savem, tshare = $tshare, texpire = $texpire, lang = '$lang'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = ( $deptid && ( $deptid != "NULL" ) ) ? $deptid : database_mysql_insertid( $dbh ) ;
			$query = "UPDATE p_dept_ops SET visible = '$visible' WHERE deptID = '$deptid'" ;
			database_mysql_query( $dbh, $query ) ;
			return $id ;
		}

		return false ;
	}
?>