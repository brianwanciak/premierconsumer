<?php
	if ( defined( 'API_Setup_get' ) ) { return ; }
	define( 'API_Setup_get', true ) ;

	FUNCTION Setup_get_InfoByLogin( &$dbh,
					$login )
	{
		if ( $login == "" )
			return false ;

		LIST( $login ) = database_mysql_quote( $dbh, $login ) ;

		$query = "SELECT * FROM p_admins WHERE ( ( login = '$login' ) OR ( email = '$login' ) )" ;
		database_mysql_query( $dbh, $query ) ;
		$nresults = database_mysql_nresults( $dbh ) ;

		if ( ( $dbh[ 'ok' ] ) && $nresults )
		{
			if ( $nresults == 1 )
			{
				$data = database_mysql_fetchrow( $dbh ) ;
				return $data ;
			}
			else
				return Array( "adminID"=>0, "error"=>"1" ) ;
		}
		return false ;
	}

	FUNCTION Setup_get_InfoByID( &$dbh,
					$adminid )
	{
		if ( $adminid == "" )
			return false ;

		LIST( $adminid ) = database_mysql_quote( $dbh, $adminid ) ;

		$query = "SELECT * FROM p_admins WHERE adminID = $adminid LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Setup_get_AllAccounts( &$dbh )
	{
		$query = "SELECT * FROM p_admins ORDER BY created DESC" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}
?>