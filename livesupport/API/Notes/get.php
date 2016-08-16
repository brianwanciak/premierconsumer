<?php
	if ( defined( 'API_Notes_get' ) ) { return ; }
	define( 'API_Notes_get', true ) ;

	FUNCTION Notes_get_NoteInfo( &$dbh,
						$noteid )
	{
		if ( $noteid == "" )
			return false ;

		LIST( $noteid ) = database_mysql_quote( $dbh, $noteid ) ;

		$query = "SELECT * FROM p_notes WHERE noteID = '$noteid'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		} return false ;
	}
?>