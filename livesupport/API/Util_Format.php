<?php
	if ( defined( 'API_Util_Format' ) ) { return ; }	
	define( 'API_Util_Format', true ) ;

	FUNCTION Util_Format_Sanatize( $string, $flag )
	{
		if ( !is_array( $string ) ) { $string = trim( $string, "\x00" ) ; }
		switch ( $flag )
		{
			case ( "a" ):
				return ( is_array( $string ) ) ? $string : Array() ; break ;
			case ( "n" ):
				$varout = preg_replace( "/[^0-9.-]/i", "", $string ) ;
				return $varout ;
				break ;
			case ( "ln" ):
				$temp = preg_replace( "/[`\$*%=<>\(\)\[\]\|\{\}\/\\\]/i", "", $string ) ;
				$varout = ( $temp == "0" ) ? "" : $temp ;
				return $varout ; break ;
			case ( "lns" ):
				return preg_replace( "/[^a-z0-9.:\-]/i", "", $string ) ; break ;
			case ( "e" ):
				return preg_replace( "/[^a-z0-9_.\-@]/i", "", Util_Format_Trim( $string ) ) ; break ;
			case ( "v" ):
				return preg_replace( "/(%20)|(%00)|(%3Cv%3E)|(<v>)/", "", Util_Format_Trim( $string ) ) ; break ;
			case ( "base_url" ):
				return preg_replace( "/[\$\!`\"<>'\?;]/i", "", Util_Format_Trim( preg_replace( "/hphp/i", "http", $string ) ) ) ; break ;
			case ( "url" ):
				return preg_replace( "/[\$\!`\"<>'\(\); ]/i", "", Util_Format_Trim( preg_replace( "/hphp/i", "http", $string ) ) ) ; break ;
			case ( "title" ):
				return preg_replace( "/[`\$=\!<>;]/i", "", Util_Format_Trim( preg_replace( "/hphp/i", "http", $string ) ) ) ; break ;
			case ( "htmltags" ):
				return Util_Format_ConvertTags( $string ) ; break ;
			case ( "timezone" ):
				return preg_replace( "/['`?\$*%=<>\(\)\[\]\|\{\}\\\]/i", "", $string ) ; break ;
			case ( "notags" ):
				return strip_tags( $string ) ; break ;
			default:
				return $string ;
		}
	}

	FUNCTION Util_Format_URL( $string )
	{
		return preg_replace( "/http/i", "hphp", $string ) ;
	}

	FUNCTION Util_Format_Trim( $string )
	{
		return preg_replace( "/(\r\n)|(\r)|(\n)/", "", $string ) ;
	}

	FUNCTION Util_Format_ConvertTags( $string )
	{
		$string = preg_replace( "/>/", "&gt;", $string ) ;
		return preg_replace( "/</", "&lt;", $string ) ;
	}

	FUNCTION Util_Format_ConvertQuotes( $string )
	{
		$string = preg_replace( "/'/", "&lsquo;", $string ) ;
		return preg_replace( "/\"/", "&quot;", $string ) ;
	}

	FUNCTION Util_Format_StripQuotes( $string )
	{
		return preg_replace( "/[\"']/", "", $string ) ;
	}

	FUNCTION Util_Format_Duration( $duration )
	{
		$string = $minutes = $hours = "" ;

		$minutes = round( $duration/60 ) ;
		if ( $minutes >= 60 )
		{
			$hours = floor( $minutes/60 ) ;
			$minutes = $minutes % 60 ;
			$string = "$hours h $minutes m" ;
		}
		else
			$string = "$minutes min" ;
		return $string ;
	}

	FUNCTION Util_Format_GetVar( $varname, $method = "" )
	{
		$varout = 0 ;
		if ( isset( $_POST[$varname] ) )
			$varout = $_POST[$varname] ;
		else if ( isset( $_GET[$varname] ) )
			$varout = $_GET[$varname] ;
		if ( function_exists( "get_magic_quotes_gpc" ) && get_magic_quotes_gpc() && !is_array( $varout ) )
			$varout = stripslashes( $varout ) ;
		return $varout ;
	}

	FUNCTION Util_Format_GetOS( $agent )
	{
		global $CONF ;
		if ( !defined( 'API_Util_Mobile' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Mobile.php" ) ;
		
		$mobile = Util_Mobile_Detect() ;
		if ( $mobile ) { $os = 5 ; }
		else if ( preg_match( "/Windows/i", $agent ) ) { $os = 1 ; }
		else if ( preg_match( "/Mac/i", $agent ) ) { $os = 2 ; }
		else { $os = 4 ; }

		if ( preg_match( "/MSIE/i", $agent ) ) { $browser = 1 ; }
		else if ( preg_match( "/Firefox/i", $agent ) ) { $browser = 2 ; }
		else if ( preg_match( "/Chrome/i", $agent ) ) { $browser = 3 ; }
		else if ( preg_match( "/Safari/i", $agent ) ) { $browser = 4 ; }
		else { $browser = 6 ; } return Array( $os, $browser ) ;
	}

	FUNCTION Util_Format_RandomString( $length = 5, $chars = '23456789abcdefghjkmnpqrstuvwxyz')
	{
		$charLength = strlen($chars)-1;

		$randomString = "";
		for($i = 0 ; $i < $length ; $i++)
			$randomString .= $chars[mt_rand(0,$charLength)];
		return $randomString;
	}

	FUNCTION Util_Format_DEBUG( $string ) { global $CONF ; file_put_contents( "$CONF[CONF_ROOT]/debug.txt", $string, FILE_APPEND ) ; }

	FUNCTION Util_Format_Get_Vars( &$dbh )
	{
		$query = "SELECT * FROM p_vars LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ; return $data ;
		} return false ;
	}

	FUNCTION Util_Format_Update_TimeStamp( &$dbh, $ts_table, $now )
	{
		if ( !preg_match( "/^(clean)|(clear)$/", $ts_table ) ) { return false ; }
		LIST( $now ) = database_mysql_quote( $dbh, $now ) ;

		$query = "UPDATE p_vars SET ts_$ts_table = $now" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Util_Format_ExplodeString( $delim, $string )
	{
		$output = explode( $delim, $string ) ;
		for ( $c = 0; $c < count( $output ); ++$c )
		{
			if ( !$output[$c] ) { unset( $output[$c] ) ; }
		} return $output ;
	}
?>
