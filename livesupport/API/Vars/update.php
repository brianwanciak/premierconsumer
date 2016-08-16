<?php
	if ( defined( 'API_Vars_update' ) ) { return ; }
	define( 'API_Vars_update', true ) ;

	FUNCTION Vars_update_Var( &$dbh,
					$tbl_name,
					$value )
	{
		if ( $tbl_name == "" )
			return false ;
		
		LIST( $tbl_name, $value ) = database_mysql_quote( $dbh, $tbl_name, $value ) ;

		$query = "SELECT * FROM p_vars LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;
		$total = database_mysql_nresults( $dbh ) ;

		if ( !$total )
		{
			$query = "INSERT INTO p_vars VALUES(0, 0, 1, 0, 0, 'UTF-8', 0)" ;
			database_mysql_query( $dbh, $query ) ;
		}

		$query = "UPDATE p_vars SET $tbl_name = '$value' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>