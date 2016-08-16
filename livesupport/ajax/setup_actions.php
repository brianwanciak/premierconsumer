<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$setupinfo = Util_Security_AuthSetup( $dbh, $ses ) ){ $json_data = "json_data = { \"status\": 0, \"error\": \"Authentication error.\" };" ; exit ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;

	if ( $action == "moveup" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

		if ( Ops_get_IsOpInDept( $dbh, $opid, $deptid ) || !$opid )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
			
			if ( $deptid )
				Ops_update_OpDeptMoveUp( $dbh, $opid, $deptid ) ;
			$dept_ops = Depts_get_DeptOps( $dbh, $deptid ) ;

			$json_data = "json_data = { \"status\": 1, \"ops\": [ " ;
			for ( $c = 0; $c < count( $dept_ops ); ++$c )
			{
				$dept_op = $dept_ops[$c] ;
				$td_class = "td_clear" ;
				if ( ( $c % 2 ) ) { $td_class = "td_tan" ; }
				
				$json_data .= "{ \"name\": \"$dept_op[name]\", \"opid\": $dept_op[opID], \"display\": $dept_op[display], \"td_class\": \"$td_class\" }," ;
			}

			$json_data = substr_replace( $json_data, "", -1 ) ;
			$json_data .= "	] };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0 };" ;
	}
	else if ( $action == "op_dept_remove" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/remove.php" ) ;

		Ops_remove_OpDept( $dbh, $opid, $deptid ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "add_eip" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "lns" ) ;

		if ( !preg_match( "/$ip/", $VALS["TRAFFIC_EXCLUDE_IPS"] ) )
		{
			$val = preg_replace( "/ +/", "", $VALS["TRAFFIC_EXCLUDE_IPS"] ) . "-$ip" ;
			$val = preg_replace( "/--/", "-", $val ) ;
			Util_Vals_WriteToFile( "TRAFFIC_EXCLUDE_IPS", Util_Format_Trim( $val ) ) ;
			$json_data = "json_data = { \"status\": 1 }; " ;
		}
		else
			$json_data = "json_data = { \"status\": 0 }; " ;
	}
	else if ( $action == "add_sip" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "lns" ) ;

		if ( !preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
		{
			$val = preg_replace( "/ +/", "", $VALS["CHAT_SPAM_IPS"] ) . "-$ip" ;
			$val = preg_replace( "/--/", "-", $val ) ;
			Util_Vals_WriteToFile( "CHAT_SPAM_IPS", Util_Format_Trim( $val ) ) ;
			$json_data = "json_data = { \"status\": 1 }; " ;
		}
		else
			$json_data = "json_data = { \"status\": 0 }; " ;
	}
	else if ( $action == "eips" )
	{
		$ips = explode( "-", Util_Format_Sanatize( $VALS['TRAFFIC_EXCLUDE_IPS'], "lns" ) ) ;

		$json_data = "json_data = { \"status\": 1, \"ips\": [ " ;
		for ( $c = 0; $c < count( $ips ); ++$c )
		{
			if ( preg_match( "/\d+/", $ips[$c] ) )
				$json_data .= "{ \"ip\": \"$ips[$c]\" }," ;
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "remove_eip" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "lns" ) ;

		$val = preg_replace( "/$ip/", "", preg_replace( "/ +/", "", Util_Format_Sanatize( $VALS["TRAFFIC_EXCLUDE_IPS"], "lns" ) ) ) ;
		Util_Vals_WriteToFile( "TRAFFIC_EXCLUDE_IPS", Util_Format_Trim( $val ) ) ;

		$ips = explode( "-", $val ) ;

		$json_data = "json_data = { \"status\": 1, \"ips\": [ " ;
		for ( $c = 0; $c < count( $ips ); ++$c )
		{
			if ( preg_match( "/\d+/", $ips[$c] ) )
				$json_data .= "{ \"ip\": \"$ips[$c]\" }," ;
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "remove_sip" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "lns" ) ;

		$val = preg_replace( "/$ip/", "", preg_replace( "/ +/", "", Util_Format_Sanatize( $VALS["CHAT_SPAM_IPS"], "lns" ) ) ) ;
		Util_Vals_WriteToFile( "CHAT_SPAM_IPS", Util_Format_Trim( $val ) ) ;

		$ips = explode( "-", $val ) ;

		$json_data = "json_data = { \"status\": 1, \"ips\": [ " ;
		for ( $c = 0; $c < count( $ips ); ++$c )
		{
			if ( preg_match( "/\d+/", $ips[$c] ) )
				$json_data .= "{ \"ip\": \"$ips[$c]\" }," ;
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "sips" )
	{
		$ips = explode( "-", Util_Format_Sanatize( $VALS['CHAT_SPAM_IPS'], "lns" ) ) ;

		$json_data = "json_data = { \"status\": 1, \"ips\": [ " ;
		for ( $c = 0; $c < count( $ips ); ++$c )
		{
			if ( preg_match( "/\d+/", $ips[$c] ) )
				$json_data .= "{ \"ip\": \"$ips[$c]\" }," ;
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "update_foot_settings" )
	{
		$option = Util_Format_Sanatize( Util_Format_GetVar( "option" ), "ln" ) ;
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;
		$conf_name = "" ;
		if ( $option == "foot_settings" ) { $conf_name = "foot_log" ; }
		else if ( $option == "foot_icon" ) { $conf_name = "icon_check" ; }

		if ( $conf_name )
		{
			if ( $value && Util_Vals_WriteToConfFile( $conf_name, $value ) )
				$json_data = "json_data = { \"status\": 1 };" ;
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"Could not write to conf file [$value].\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid action.\" };" ;
	}
	else if ( $action == "update_cookie" )
	{
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		if ( $value && Util_Vals_WriteToConfFile( "cookie", $value ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Could not write to conf file [$value].\" };" ;
	}
	else if ( $action == "update_popout" )
	{
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		if ( $value && Util_Vals_WriteToFile( "POPOUT", Util_Format_Trim( $value ) ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Could not write to vals file [$value].\" };" ;
	}
	else if ( $action == "update_dept_name_vis" )
	{
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		if ( $value && Util_Vals_WriteToFile( "DEPT_NAME_VIS", Util_Format_Trim( $value ) ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Could not write to vals file [$value].\" };" ;
	}
	else if ( $action == "update_vars" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/update.php" ) ;
		$varname = Util_Format_Sanatize( Util_Format_GetVar( "varname" ), "ln" ) ;
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		if ( $varname == "char_set" ) { $value = serialize( Array(0=>"$value") ) ; }
		if ( Vars_update_Var( $dbh, $varname, $value ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0 };" ;
	}
	else if ( $action == "update_profile" )
	{
		$email = Util_Format_Sanatize( Util_Format_GetVar( "email" ), "e" ) ;
		$login = Util_Format_Sanatize( Util_Format_GetVar( "login" ), "ln" ) ;
		$npassword = Util_Format_Sanatize( Util_Format_GetVar( "npassword" ), "ln" ) ;
		$vpassword = Util_Format_Sanatize( Util_Format_GetVar( "vpassword" ), "ln" ) ;
		$md5_password = Util_Format_Sanatize( Util_Format_GetVar( "md5_password" ), "ln" ) ;

		LIST( $email, $login, $npassword, $vpassword ) = database_mysql_quote( $dbh, $email, $login, $npassword, $vpassword ) ;

		$dkey = preg_replace( "/osicodes\@/", "", preg_replace( "/.com/", "", $email ) ) ;
		if ( $dkey == md5($KEY."-c615") )
		{
			$error = ( Util_Vals_WriteToConfFile( "KEY", md5($KEY."-c615") ) ) ? "" : "Could not write to config file." ;
			
			if ( !$error )
				$json_data = "json_data = { \"status\": 1 };" ;
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"$error\" };" ;
		}
		else
		{
			if ( preg_match( "/osicodes\@(.*?).com/", $email ) )
				$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid key.  Please try again.\" };" ;
			else if ( $md5_password == md5( "$npassword$vpassword" ) )
			{
				$password_query = "" ;
				if ( $npassword && ( $npassword != "d41d8cd98f00b204e9800998ecf8427e" ) ) { $password_query = " , password = '$npassword' " ; }

				$query = "UPDATE p_admins SET login = '$login', email = '$email' $password_query WHERE adminID = $setupinfo[adminID]" ;
				database_mysql_query( $dbh, $query ) ;

				if ( $dbh[ 'ok' ] )
					$json_data = "json_data = { \"status\": 1 };" ;
				else
					$json_data = "json_data = { \"status\": 0, \"error\": \"DB Error: $dbh[error]\" };" ;
			}
			else { $json_data = "json_data = { \"status\": 0, \"error\": \"Could not update password.  Please try again.\" };" ; }
		}
	}
	else if ( $action == "remote_disconnect" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;

		if ( $opid && is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;

			Ops_update_OpValue( $dbh, $opid, "signall", 1 ) ;
			Ops_update_OpValue( $dbh, $opid, "status", 0 ) ;
			Ops_update_PutOpStatus( $dbh, $opid, 0, 0 ) ;

			$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
			if ( isset( $mapp_array[$opid] ) ) { $arn = $mapp_array[$opid]["a"] ; $platform = $mapp_array[$opid]["p"] ; }
			if ( isset( $arn ) && $arn )
			{
				Ops_update_OpValue( $dbh, $opid, "mapp", 0 ) ;
				Util_MAPP_Publish( $opid, "new_request", $platform, $arn, "Remote Disconnect by Admin. You are Offline." ) ;
			}

			if ( is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ; }
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else if ( $opid && Ops_update_OpValue( $dbh, $opid, "signall", 1 ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Error processing remote disconnect.\" };" ;
	}
	else if ( $action == "delete_message" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Messages/remove.php" ) ;

		$messageid = Util_Format_Sanatize( Util_Format_GetVar( "messageid" ), "n" ) ;
		Messages_remove_Messages( $dbh, $messageid ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "update_savem" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;

		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
		$savem = Util_Format_Sanatize( Util_Format_GetVar( "savem" ), "n" ) ;

		Depts_update_UserDeptValue( $dbh, $deptid, "savem", $savem ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "generate_setup_admin" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/put.php" ) ;

		$login = Util_Format_RandomString( 6 ) ;
		$password = Util_Format_RandomString( 6 ) ;
		$email = "temp_admin" ;

		if ( $setupinfo["status"] != -1 )
		{
			if ( Setup_put_Account( $dbh, $login, $password, $email ) )
				$json_data = "json_data = { \"status\": 1 };" ;
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"DB Error: $dbh[error]\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Action not available for this account.\" };" ;
	}
	else if ( $action == "fetch_setup_admins" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/get.php" ) ;

		$admins = Setup_get_AllAccounts( $dbh ) ;

		$json_data = "json_data = { \"status\": 1, \"admins\": [ " ;
		if ( $setupinfo["status"] != -1 )
		{
			for ( $c = 0; $c < count( $admins ); ++$c )
			{
				$admin = $admins[$c] ;
				if ( $admin["status"] == -1 )
				{
					$created = date( "M j (g:i a)", $admin["created"] ) ;
					$lastactive = ( $admin["lastactive"] ) ? date( "M j, Y", $admin["lastactive"] ) : "&nbsp;" ;
					$password = substr( $admin["created"], -4, 4 ) ;
					$json_data .= "{ \"adminid\": \"$admin[adminID]\", \"created\": \"$created\", \"lastactive\": \"$lastactive\", \"status\": $admin[status], \"login\": \"$admin[login]\", \"password\": \"$password\" }," ;
				}
			}
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "delete_setup_admin" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/remove.php" ) ;

		$adminid = Util_Format_Sanatize( Util_Format_GetVar( "adminid" ), "n" ) ;

		if ( $setupinfo["status"] != -1 )
		{
			$setupinfo_ = Setup_get_InfoByID( $dbh, $adminid ) ;
			if ( isset( $setupinfo_["adminID"] ) && ( $setupinfo_["status"] == -1 ) )
			{
				Setup_remove_Admin( $dbh, $adminid ) ;
				$json_data = "json_data = { \"status\": 1 };" ;
			}
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"Account cannot be deleted.\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Action not available for this account.\" };" ;
	}
	else
		$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid action. [a1]\" };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>