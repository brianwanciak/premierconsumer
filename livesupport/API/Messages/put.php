<?php
	if ( defined( 'API_Messages_put' ) ) { return ; }
	define( 'API_Messages_put', true ) ;

	FUNCTION Messages_put_Message( &$dbh,
					$vis_token,
					$deptid,
					$chat,
					$footprints,
					$ip,
					$ces,
					$vname,
					$vemail,
					$subject,
					$onpage,
					$refer,
					$custom,
					$message )
	{
		if ( ( $deptid == "" ) || ( $vname == "" ) || ( $vemail == "" )
			|| ( $subject == "" ) || ( $message == "" ) )
			return false ;

		$now = time() ;

		LIST( $vis_token, $deptid, $chat, $footprints, $ip, $ces, $vname, $vemail, $subject, $onpage, $refer, $custom, $message ) = database_mysql_quote( $dbh, $vis_token, $deptid, $chat, $footprints, $ip, $ces, $vname, $vemail, $subject, $onpage, $refer, $custom, $message ) ;

		$query = "INSERT INTO p_messages VALUES ( NULL, $now, $chat, $deptid, $footprints, '$ip', '$vname', '$vemail', '$ces', '$vis_token', '$subject', '$onpage', '$refer', '$custom', '$message' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = database_mysql_insertid( $dbh ) ;
			return $id ;
		}

		return false ;
	}
?>