<?php
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	if ( $mapp_opid )
	{
		if ( isset( $mapp_array[$mapp_opid] ) ) { $arn = $mapp_array[$mapp_opid]["a"] ; $platform = $mapp_array[$mapp_opid]["p"] ; }
		if ( isset( $arn ) && $arn )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;
			Util_MAPP_Publish( $mapp_opid, "new_request", $platform, $arn, $requestinfo["question"] ) ;
		}
	}
	else
	{
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
		if ( $deptinfo["smtp"] )
		{
			$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;
		}

		$question = ( strlen( $requestinfo["question"] ) > 100 ) ? substr( $requestinfo["question"], 0, 100 ) . "..." : $requestinfo["question"] ;
		$question = preg_replace( "/<br>/", " ", $question ) ;
		Util_Email_SendEmail( $opinfo_next["name"], $opinfo_next["email"], $requestinfo["vname"], base64_decode( $opinfo_next["smsnum"] ), "Chat Request", $question, "sms" ) ;
	}
?>