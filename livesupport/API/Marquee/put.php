<?php
	if ( defined( 'API_Marquee_put' ) ) { return ; }
	define( 'API_Marquee_put', true ) ;

	FUNCTION Marquee_put_Marquee( &$dbh,
					$marqid,
					$deptid,
					$snapshot,
					$message )
	{
		if ( ( $marqid == "" ) || ( $deptid == "" ) || ( $snapshot == "" )
			|| ( $message == "" ) )
			return false ;

		if ( !$marqid ) { $marqid = "NULL" ; }
		LIST( $marqid, $deptid, $snapshot, $message ) = database_mysql_quote( $dbh, $marqid, $deptid, $snapshot, $message ) ;

		$marqinfo = Marquee_get_MarqueeInfo( $dbh, $marqid ) ;
		if ( isset( $marqinfo["display"] ) )
			$display = $marqinfo["display"] ;
		else
		{
			$query = "SELECT count(*) AS total FROM p_marquees WHERE deptID = $deptid" ;
			database_mysql_query( $dbh, $query ) ;
			$data = database_mysql_fetchrow( $dbh ) ;
			$display = ( isset( $data["total"] ) ) ? $data["total"] + 1 : 1 ;
		}

		$query = "REPLACE INTO p_marquees VALUES ( $marqid, $display, $deptid, '$snapshot', '$message' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>