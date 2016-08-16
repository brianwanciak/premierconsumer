<?php
	if ( defined( 'API_Util_Security' ) ) { return ; }	
	define( 'API_Util_Security', true ) ;

	FUNCTION Util_Security_AuthSetup( &$dbh,
					$ses,
					$adminid = 0 )
	{
		if ( $ses == "" )
			return false ;

		$adminid = isset( $_COOKIE["phplive_adminID"] ) ? Util_Format_Sanatize( $_COOKIE["phplive_adminID"], "n" ) : 0 ;
		LIST( $adminid, $ses ) = database_mysql_quote( $dbh, $adminid, $ses ) ;

		$query = "SELECT * FROM p_admins WHERE adminID = '$adminid' AND ses = '$ses' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( isset( $data["adminID"] ) )
				return $data ;
		}
		return false ;
	}

	FUNCTION Util_Security_AuthOp( &$dbh,
					$ses,
					$opid = 0,
					$wp = 0 )
	{
		if ( $ses == "" )
			return false ;

		if ( !$opid && !$wp )
			$opid = isset( $_COOKIE["phplive_opID"] ) ? Util_Format_Sanatize( $_COOKIE["phplive_opID"], "n" ) : 0 ;
		LIST( $opid, $ses ) = database_mysql_quote( $dbh, $opid, $ses ) ;

		$query = "SELECT * FROM p_operators WHERE opID = '$opid' AND ses = '$ses' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( isset( $data["opID"] ) )
				return $data ;
		}
		return false ;
	}
?>