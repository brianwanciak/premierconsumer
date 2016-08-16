<?php
	if ( defined( 'API_Lang_put' ) ) { return ; }
	define( 'API_Lang_put', true ) ;

	FUNCTION Lang_put_Lang( &$dbh,
					$lang,
					$value )
	{
		if ( ( $lang == "" ) || ( $value == "" ) )
			return false ;

		LIST( $lang, $value ) = database_mysql_quote( $dbh, $lang, $value ) ;

		$query = "REPLACE INTO p_lang_packs VALUES ( '$lang', '$value' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>