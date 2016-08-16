<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		$opid_cookie = Util_Format_Sanatize( $_COOKIE["phplive_opID"], "n" ) ;
		if ( $action == "sms_send" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		
			$phonenum = Util_Format_Sanatize( Util_Format_GetVar( "phonenum" ), "ln" ) ;
			$carrier = Util_Format_Sanatize( Util_Format_GetVar( "carrier" ), "ln" ) ;

			$opinfo = Ops_get_OpInfoByID( $dbh, $opid_cookie ) ;
			if ( !$opinfo["sms"] )
				$json_data = "json_data = { \"status\": 0, \"error\": \"SMS is not enabled for this account.\" }; " ;
			else if ( $opinfo["sms"] < time()-60 )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

				$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;

				for ( $c = 0; $c < count( $departments ); ++$c )
				{
					$deptinfo = $departments[$c] ;
					if ( $deptinfo["smtp"] )
					{
						$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;
						break 1 ;
					}
				}

				$vcode = rand( 1000, 9999 ) ;
				$smsnum = "$phonenum@$carrier" ;
				$error = Util_Email_SendEmail( $opinfo["name"], $opinfo["email"], "Mobile", $smsnum, "Verification", "Verification Code: $vcode", "sms" ) ;

				if ( $error )
					$json_data = "json_data = { \"status\": 0, \"error\": \"$error\" }; " ;
				else
				{
					Ops_update_OpValue( $dbh, $opid_cookie, "sms", $vcode ) ;
					Ops_update_OpValue( $dbh, $opid_cookie, "smsnum", base64_encode( $smsnum ) ) ;

					$json_data = "json_data = { \"status\": 1 }; " ;
				}
			}
			else
			{
				$time_left = $opinfo["sms"] - ( time()-60 ) ;
				$json_data = "json_data = { \"status\": 0, \"error\": \"Please try again in $time_left seconds.\" }; " ;
			}
		}
		else if ( $action == "sms_verify" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		
			$code = Util_Format_Sanatize( Util_Format_GetVar( "code" ), "n" ) ;

			$opinfo = Ops_get_OpInfoByID( $dbh, $opid_cookie ) ;
			if ( !$opinfo["sms"] )
				$json_data = "json_data = { \"status\": 0, \"error\": \"SMS is not enabled for this account.\" }; " ;
			else if ( ( $code == 1 ) || ( $code == 2 ) )
			{
				Ops_update_OpValue( $dbh, $opid_cookie, "sms", $code ) ;
				$json_data = "json_data = { \"status\": 1 };" ;
			}
			else if ( ( $opinfo["sms"] == 1 ) || ( $opinfo["sms"] == 2 ) )
				$json_data = "json_data = { \"status\": 1, \"error\": \"Mobile number has already been verified.\" }; " ;
			else if ( $opinfo["sms"] == $code )
			{
				Ops_update_OpValue( $dbh, $opid_cookie, "sms", 1 ) ;
				$json_data = "json_data = { \"status\": 1 }; " ;
			}
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"Verification code is invalid.\" }; " ;
		}
		else if ( $action == "dn_toggle" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		
			$dn = Util_Format_Sanatize( Util_Format_GetVar( "dn" ), "n" ) ;

			Ops_update_OpVarValue( $dbh, $opid_cookie, "dn_request", $dn ) ;
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else if ( $action == "dn_toggle_response" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		
			$dn = Util_Format_Sanatize( Util_Format_GetVar( "dn" ), "n" ) ;

			Ops_update_OpVarValue( $dbh, $opid_cookie, "dn_response", $dn ) ;
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else if ( $action == "console_sound" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		
			$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "n" ) ;

			Ops_update_OpVarValue( $dbh, $opid_cookie, "sound", $value ) ;
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else if ( $action == "console_blink" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		
			$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "n" ) ;

			Ops_update_OpVarValue( $dbh, $opid_cookie, "blink", $value ) ;
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else if ( $action == "console_blink_r" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		
			$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "n" ) ;

			Ops_update_OpVarValue( $dbh, $opid_cookie, "blink_r", $value ) ;
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else if ( $action == "dn_always" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		
			$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "n" ) ;

			Ops_update_OpVarValue( $dbh, $opid_cookie, "dn_always", $value ) ;
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0 };" ;
	}

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>
