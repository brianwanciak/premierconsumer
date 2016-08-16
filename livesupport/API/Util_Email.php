<?php
	if ( defined( 'API_Util_Email' ) ) { return ; }	
	define( 'API_Util_Email', true ) ;

	$ERROR_EMAIL = "" ;
	FUNCTION eMailErrorHandler($errno, $errstr, $errfile, $errline) { global $ERROR_EMAIL ; $ERROR_EMAIL = $errstr ; }
	FUNCTION Util_Email_SendEmail( $from_name, $from_email, $to_name, $to_email, $subject, $message, $extra, $bcc = Array() )
	{
		global $CONF ;
		global $smtp_array ;
		global $ERROR_EMAIL ;
		global $DMARC_DOMAINS ;

		if ( $extra == "trans" )
		{
			$message = preg_replace( "/<>/", "\r\n\r\n", $message ) ;
			$message = preg_replace( "/<disconnected><d(\d)>(.*?)<\/div>/", "\r\n$2\r\n-------------------------------------\r\n", $message ) ;
			$message = preg_replace( "/<div class='ca'><i>(.*?)<\/i><\/div>/", "-------------------------------------\r\n$1\r\n-------------------------------------", $message ) ;
			$message = preg_replace( "/===\r\n\r\n===/", "-------------------------------------", $message ) ;
			$message = preg_replace( "/<div class='co'><b>(.*?)<timestamp_(\d+)_co>:<\/b> /", "\r\n$1:\r\n", $message ) ;
			$message = preg_replace( "/<v>/", "", $message ) ; // old chat transcript format clean
			$message = preg_replace( "/<div class='cv'><b>(.*?)<timestamp_(\d+)_cv>:<\/b> /", "\r\n$1:\r\n", $message ) ;
			$message = preg_replace( "/<br>/", "\r\n", $message ) ;
			$message = preg_replace( "/<(.*?)>/", "", $message ) ;
			$message = stripslashes( preg_replace( "/-dollar-/", "\$", $message ) ) ;
		}

		if ( $extra == "sms" )
		{
			$headers = "From: $from_name <$from_email>" . "\r\n" ;
			$subject_new = $subject ;
			if ( preg_match( "/(russian)/", $CONF["lang"] ) && !preg_match( "/Verification Code:/", $message ) )
				$message = "New chat request." ;
		}
		else
		{
			LIST( $null, $domain ) = explode( "@", $from_email ) ;
			if ( isset( $DMARC_DOMAINS ) && is_array( $DMARC_DOMAINS ) && isset( $DMARC_DOMAINS[$domain] ) )
			{
				$headers = "From: ".'=?UTF-8?B?'.base64_encode( $to_name ).'?='." <$to_email>" . "\r\n" ;
				$from_name = $to_name ;
				$from_email = $to_email ;
			}
			else { $headers = "From: ".'=?UTF-8?B?'.base64_encode( $from_name ).'?='." <$from_email>" . "\r\n" ; }
			$headers .= "MIME-Version: 1.0" . "\n" ;
			$headers .= "Content-type: text/plain; charset=UTF-8" . "\r\n" ;
			for ( $c = 0; $c < count( $bcc ); ++$c ) { $headers .= "Bcc: $bcc[$c]\r\n" ; }
			$subject_new = '=?UTF-8?B?'.base64_encode( $subject ).'?=' ;
		}

		// SMTP
		//ini_set( SMTP, "localhost" ) ;

		if ( $to_email )
		{
			if ( isset( $smtp_array ) )
			{
				$CONF["SMTP_HOST"] = $smtp_array["host"] ;
				$CONF["SMTP_LOGIN"] = $smtp_array["login"] ;
				$CONF["SMTP_PASS"] = $smtp_array["pass"] ;
				$CONF["SMTP_PORT"] = $smtp_array["port"] ;
				$CONF["SMTP_CRYPT"] = ( isset( $smtp_array["crypt"] ) ) ? $smtp_array["crypt"] : "" ;
				$CONF["SMTP_API"] = isset( $smtp_array["api"] ) ? $smtp_array["api"] : "" ;
				$CONF["SMTP_DOMAIN"] = isset( $smtp_array["domain"] ) ? $smtp_array["domain"] : "" ;
			}

			set_error_handler('eMailErrorHandler') ;
			if ( !isset( $CONF["SMTP_PASS"] ) && is_file( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Extra.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Extra.php" ) ; }
			if ( isset( $CONF["SMTP_PASS"] ) )
			{
				if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Email_SMTP.php" ) )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Email_SMTP.php" ) ;

					$subject_new = $subject ;
					$error = Util_Email_SMTP_SwiftMailer( $to_email, $to_name, $from_email, $from_name, $subject_new, $message, $bcc ) ;
					if ( defined( 'API_Util_Error' ) ) { set_error_handler( "ErrorHandler" ) ; }

					if ( $error == "NONE" ) { return false ; }
					else { return $error ; }
				}
				else
					return "SMTP addon not found or addon upgrade is needed. [e1]" ;
			}
			else
			{
				if ( mail( $to_email, $subject_new, $message, $headers ) ) { if ( defined( 'API_Util_Error' ) ) { set_error_handler( "ErrorHandler" ) ; } return false ; }
				else
				{
					if ( defined( 'API_Util_Error' ) ) { set_error_handler( "ErrorHandler" ) ; }

					if ( preg_match( "/failed to connect/i", $ERROR_EMAIL ) )
						return "Could not connect to local mail server or mail server is not installed." ;
					else
						return "Email error: $ERROR_EMAIL" ;
				}
			}
		}
		else
			return "Recipient is invalid." ;
	}
?>