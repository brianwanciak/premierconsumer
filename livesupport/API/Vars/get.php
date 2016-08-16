<?php
	if ( defined( 'API_Vars_get' ) ) { return ; }
	define( 'API_Vars_get', true ) ;

	FUNCTION Vars_get_Socials( &$dbh,
				$deptid )
	{
		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$query = "SELECT * FROM p_socials WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$socials = Array() ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
			{
				$social = $data["social"] ;
				$socials[$social] = $data ;
			}
			return $socials ;
		}
		return false ;
	}

?>