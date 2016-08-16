<?php
	if ( defined( 'API_Ops_get_ext' ) ) { return ; }
	define( 'API_Ops_get_ext', true ) ;

	FUNCTION Ops_get_ext_OpInfoByLogin( &$dbh,
					$login )
	{
		if ( $login == "" ) { return false ; }

		LIST( $login ) = database_mysql_quote( $dbh, $login ) ;

		$query = "SELECT * FROM p_operators WHERE login = '$login' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}
?>