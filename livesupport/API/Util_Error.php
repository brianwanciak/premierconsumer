<?php
	if ( defined( 'API_Util_Error' ) ) { return ; }	
	define( 'API_Util_Error', true ) ;
	error_reporting(0) ;

	FUNCTION ErrorHandler( $errno, $errmsg, $filename, $linenum, $vars ) 
	{
		global $CONF ; global $PHPLIVE_HOST ; global $KEY ; global $dbh ;
		if ( isset( $dbh ) && isset( $dbh['con'] ) ) { database_mysql_close( $dbh ) ; }
		$ckey = ( isset( $KEY ) ) ? $KEY : "error-no-license" ;
		$query = ( isset( $_SERVER["QUERY_STRING"] ) ) ? $_SERVER["QUERY_STRING"] : "" ;
		if ( phpversion() >= "5.1.0" ){ date_default_timezone_set( "America/New_York" ) ; }

		// 600-699 is custom error reserved for PHP Live!
		$errortype = array (
			1		=>  "Error",
			2		=>  "Warning",
			4		=>  "Parsing Error",
			8		=>  "Notice",
			16		=>  "Core Error",
			32		=>  "Core Warning",
			64		=>  "Compile Error",
			128		=>  "Compile Warning",
			256		=>  "User Error",
			512		=>  "User Warning",
			1024	=>  "User Notice",
			600		=>	"PHP Live! DB Connection Failed",
			601		=>	"PHP Live! Configuration Missing",
			602		=>	"PHP Live! Operator Session Expired",
			603		=>	"PHP Live! Chat Request Not Created",
			604		=>	"PHP Live! DB Data Error",
			605		=>	"PHP Live! Error",
			606		=>	"PHP Live! Patch Loop Error",
			607		=>	"PHP Live! version not compatible with WinApp",
			608		=>	"PHP Live! Setup Session Expired",
			609		=>	"PHP Live! directory or file permission denied.",
			610		=>	"PHP Live! -- error placeholder --",
			611		=>	"PHP Live! Update your language pack.",
			612		=>	"PHP Live! DOCUMENT_ROOT is invalid.",
			613		=>	"PHP Live! MySQL PDO Extension Not Enabled",
			614		=>	"PHP Live! MySQLi Extension Not Enabled"
		) ;
		if ( $errno == 602 ) { HEADER( "location: $CONF[BASE_URL]/logout.php?$query&errno=$errno&action=logout" ) ; exit ; }
		else if ( $errno == 608 ) { HEADER( "location: $CONF[BASE_URL]/logout.php?$query&menu=sa&action=logout" ) ; exit ; }
		if ( $errno )
		{
			if ( preg_match( "/gethostbyaddr/", $errmsg ) ) { return true ; }
			else {
				$errmsg = strip_tags( $errmsg ) ;
				if ( preg_match( "/Connection timed out/i", $errmsg ) && preg_match( "/phplivesupport/i", $PHPLIVE_HOST ) ) { $errmsg = "<span style='font-size: 16px; font-weight: bold;'>System is currently being updated.  Thank you for your patience.</span>" ; }
				$errmsg_query = urlencode( $errmsg ) ;

				$admin_email = ( isset( $_SERVER['SERVER_ADMIN'] ) ) ? $_SERVER['SERVER_ADMIN'] : "tech@osicodesinc.com" ;
				$script = ( isset( $_SERVER['SCRIPT_NAME'] ) ) ? $_SERVER['SCRIPT_NAME'] : $filename ;
				$path_array = explode( "/", $script ) ;
				$path_total = count( $path_array ) ;

				if ( $path_total )
				{
					if ( $path_array[$path_total-2] == "ops" )
						$script_path = "ops/".$path_array[$path_total-1] ;
					else if ( $path_array[$path_total-2] == "setup" )
						$script_path = "setup/".$path_array[$path_total-1] ;
					else
						$script_path = $path_array[$path_total-1] ;
				}
				$script_encoded = urlencode( $script_path ) ;

				$output = file_get_contents( "$CONF[DOCUMENT_ROOT]/files/error_notice.php" ) ;
				$output = preg_replace( "/%file%/", $script_path, $output ) ;
				$output = preg_replace( "/%line%/", $linenum, $output ) ;
				$output = preg_replace( "/%error%/", $errmsg, $output ) ;
				$output = preg_replace( "/%base_url%/", $CONF["BASE_URL"], $output ) ;
				$output = preg_replace( "/%solution%/", "http://www.phplivesupport.com/help_desk.php?errornum=$errno&error=$errmsg_query&script=$script_encoded&line=$linenum&key=$ckey", $output ) ;
				print $output ;

				if ( isset( $admin_email ) && $admin_email && 0 )
				{
					if ( !defined( 'API_Util_Email' ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ; }
					Util_Email_SendEmail( $admin_email, $admin_email, $admin_email, $admin_email, "Error: $errortype[$errno]", "$errmsg\r\n\r\nline #$linenum $script", "" ) ;
				}
				exit ;
			}
		}
	} set_error_handler( "ErrorHandler" ) ;
?>