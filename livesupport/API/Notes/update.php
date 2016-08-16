<?php
	if ( defined( 'API_Lang_update' ) ) { return ; }
	define( 'API_Lang_update', true ) ;

	FUNCTION Lang_update_LangValue( &$dbh,
					  $lang,
					  $value )
	{
		if ( ( $lang == "" ) || ( $value == "" ) )
			return false ;
		
		LIST( $lang, $value ) = database_mysql_quote( $dbh, $lang, $value ) ;

		$query = "UPDATE p_lang_packs SET lang_vars = '$value' WHERE lang = '$lang'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>