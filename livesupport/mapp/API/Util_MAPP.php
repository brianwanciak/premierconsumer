<?php
	if ( defined( 'Util_MAPP' ) ) { return ; }
	define( 'Util_MAPP', true ) ;

	FUNCTION Util_MAPP_Publish( $opid, $push_type, $platform, $arn, $message )
	{
		if ( $arn == "no_arn" ) { return true ; }
		global $CONF ; global $VALS ; global $KEY ; global $VARS_SET_VERIFYPEER ;

		if ( ( isset( $CONF["MAPP_KEY"] ) && $CONF["MAPP_KEY"] ) && ( ( $platform >= 1 ) && ( $platform <= 4 ) ) && $opid && $arn && $message )
		{
			$op_sounds = isset( $VALS["op_sounds"] ) ? unserialize( $VALS["op_sounds"] ) : Array() ;
			if ( isset( $op_sounds[$opid] ) ) { $op_sounds_vals = $op_sounds[$opid] ; }
			else { $op_sounds_vals = Array( "default", "default" ) ; }
			if ( $push_type == "new_request" ) { $sound = "new_request_".$op_sounds_vals[0] ; }
			else if ( $push_type == "new_text" ) { $sound = "new_text_".$op_sounds_vals[1] ; }
			else { $sound = "" ; }

			$message = ( strlen( $message ) > 75 ) ? substr( $message, 0, 75 ) . "..." : $message ;
			$message = preg_replace( "/'/", "", strip_tags( $message ) ) ;
			$request = curl_init( "https://mapp1.phplivesupport.com/Util/mapp_process.php" ) ;
			curl_setopt( $request, CURLOPT_RETURNTRANSFER, true ) ;
			curl_setopt( $request, CURLOPT_CUSTOMREQUEST, "POST" ) ;
			curl_setopt( $request, CURLOPT_POSTFIELDS, array( "a"=>"$arn", "m"=>"$message", "p"=>"$platform", "s"=>"$sound", "k"=>"$CONF[MAPP_KEY]", "ck"=>"$KEY" ) ) ;
			if ( !isset( $VARS_SET_VERIFYPEER ) || ( $VARS_SET_VERIFYPEER == 1 ) )
			{
				curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, true ) ;
				curl_setopt( $request, CURLOPT_CAINFO, "$CONF[DOCUMENT_ROOT]/mapp/API/cacert.pem" ) ;
			}
			else { curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, false ) ; }
			$response = curl_exec( $request ) ;
			$curl_errno = curl_errno( $request ) ;
			$status = curl_getinfo( $request, CURLINFO_HTTP_CODE ) ; 
			curl_close( $request ) ;
			if ( $response == 1 ) { return true ; }
		} return false ;
	}

	FUNCTION Util_MAPP_WriteMappFile( $valname, $val )
	{
		global $CONF ;
		global $MAPP_VALS ; $val = preg_replace( "/'/", "", $val ) ;

		if ( !isset( $MAPP_VALS[$valname] ) ) { $MAPP_VALS[$valname] = "" ; }

		$conf_vars = "\$MAPP_VALS = Array() ; " ;
		foreach( $MAPP_VALS as $key => $value )
		{
			if ( $key == $valname )
				$MAPP_VALS[$key] = $val ;
			$conf_vars .= " \$MAPP_VALS['$key'] = '".$MAPP_VALS[$key]."' ; " ;
		} $conf_vars = preg_replace( "/`/", "", $conf_vars ) ;

		$conf_string = "< php $conf_vars ?>" ;
		$conf_string = preg_replace( "/< php/", "<?php", preg_replace( "/  +/", " ", $conf_string ) ) ;

		if ( $fp = fopen( "$CONF[CONF_ROOT]/mapp.php", "w" ) )
		{
			fwrite( $fp, $conf_string, strlen( $conf_string ) ) ; fclose( $fp ) ;
			return true ;
		} else { return false ; }
	}
?>