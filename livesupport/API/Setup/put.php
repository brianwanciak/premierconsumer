<?php
	if ( defined( 'API_Setup_put' ) ) { return ; }
	define( 'API_Setup_put', true ) ;

	FUNCTION Setup_put_Account( &$dbh,
					$login,
					$password,
					$email )
	{
		if ( ( $login == "" ) || ( $password == "" ) || ( $email == "" ) )
			return false ;
		
		$now = time() ;
		// bypass password parameter for now so that the password is presentable instead of md5
		$password = substr( $now, -4, 4 ) ;
		LIST( $login, $password, $email ) = database_mysql_quote( $dbh, $login, md5( $password ), $email ) ;

		$query = "INSERT INTO p_admins VALUES ( NULL, $now, 0, -1, '', '$login', '$password', '$email' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;

		return false ;
	}

?>