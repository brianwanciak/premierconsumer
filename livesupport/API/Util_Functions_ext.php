<?php
	if ( defined( 'API_Util_Functions_ext' ) ) { return ; }	
	define( 'API_Util_Functions_ext', true ) ;

	FUNCTION Util_Functions_ext_GenerateCes( &$dbh )
	{
		global $CONF ;
		$query = "SELECT * FROM p_admins ORDER BY created ASC LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ; $data = database_mysql_fetchrow( $dbh ) ;
		$ces_prefix = time()-$data["created"] ; $ces = $ces_prefix.Util_Format_RandomString(2) ;
		return $ces ;
	}
?>