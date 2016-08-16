<?php
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$akey = Util_Format_Sanatize( Util_Format_GetVar( "akey" ), "ln" ) ;

	if ( $akey && isset( $CONF["API_KEY"] ) && ( $akey == $CONF["API_KEY"] ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove_itr.php" ) ;

		Footprints_remove_itr_Expired_U( $dbh ) ;
		$total_traffics = Footprints_get_itr_TotalFootprints_U( $dbh ) ;
		database_mysql_close( $dbh ) ;

		print "$total_traffics" ;
	}
	else { print "Invalid API Key." ; }
?>