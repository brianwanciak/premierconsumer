<?php
	if ( defined( 'API_Notes_put' ) ) { return ; }
	define( 'API_Notes_put', true ) ;

	FUNCTION Notes_put_Note( &$dbh,
					$opid,
					$deptid,
					$ces,
					$message )
	{
		if ( ( $ces == "" ) || ( $message == "" ) )
			return false ;

		$now = time() ;
		LIST( $ces, $message ) = database_mysql_quote( $dbh, $ces, $message ) ;

		$query = "INSERT INTO p_notes VALUES ( 0, $now, $opid, $deptid, '$ces', '$message' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = database_mysql_insertid( $dbh ) ;
			return $id ;
		}
		return false ;
	}
?>