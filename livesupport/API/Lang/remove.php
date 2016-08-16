<?php
	if ( defined( 'API_Lang_remove' ) ) { return ; }
	define( 'API_Lang_remove', true ) ;

	FUNCTION Lang_remove_Lang( &$dbh,
						$lang )
	{
		if ( $lang == "" )
			return false ;

		LIST( $lang ) = database_mysql_quote( $dbh, $lang ) ;

		$query = "DELETE FROM p_lang_packs WHERE lang = '$lang'" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>