<?php
	if ( defined( 'API_Vars_put' ) ) { return ; }
	define( 'API_Vars_put', true ) ;

	FUNCTION Vars_put_Social( &$dbh,
					$deptid,
					$social,
					$status,
					$tooltip,
					$url )
	{
		if ( $social == "" )
			return false ;

		LIST( $deptid, $social, $status, $tooltip, $url ) = database_mysql_quote( $dbh, $deptid, $social, $status, $tooltip, $url ) ;

		if ( $tooltip && $url )
			$query = "REPLACE INTO p_socials VALUES( $deptid, $status, '$social', '$tooltip', '$url' )" ;
		else
			$query = "DELETE FROM p_socials WHERE deptID = $deptid AND social = '$social'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;

		return false ;
	}
?>