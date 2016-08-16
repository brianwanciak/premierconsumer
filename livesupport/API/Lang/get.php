<?php
	if ( defined( 'API_Lang_get' ) ) { return ; }
	define( 'API_Lang_get', true ) ;

	FUNCTION Lang_get_Lang( &$dbh,
						$lang )
	{
		if ( $lang == "" )
			return false ;

		LIST( $lang ) = database_mysql_quote( $dbh, $lang ) ;

		$query = "SELECT * FROM p_lang_packs WHERE lang = '$lang'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		} return false ;
	}
?>