<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "a" ), "ln" ) ;
	$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

	if ( $action == "t" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;

		$ces = Util_Format_Sanatize( Util_Format_GetVar( "c" ), "lns" ) ;
		$isop = Util_Format_Sanatize( Util_Format_GetVar( "isop" ), "n" ) ;
		$isop_ = Util_Format_Sanatize( Util_Format_GetVar( "isop_" ), "n" ) ;
		$isop__ = Util_Format_Sanatize( Util_Format_GetVar( "isop__" ), "n" ) ;
		$flag = Util_Format_Sanatize( Util_Format_GetVar( "f" ), "n" ) ;

		if ( $flag ) { UtilChat_WriteIsWriting( $ces, $flag, $isop, $isop_, $isop__ ) ; }
		else { UtilChat_WriteIsWriting( $ces, $flag, $isop, $isop_, $isop__ ) ; }

		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else
		$json_data = "json_data = { \"status\": 0 };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	print "$json_data" ;
	exit ;
?>