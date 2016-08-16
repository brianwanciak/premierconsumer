<?php
	if ( defined( 'API_Util_Functions_itr' ) ) { return ; }	
	define( 'API_Util_Functions_itr', true ) ;

	FUNCTION Util_Functions_itr_GetHostname( $ip )
	{
		if ( $ip ) { return gethostbyaddr( $ip ) ; }
		else { return $ip ; }
	}

	FUNCTION Util_Functions_itr_Encrypt( $salt, $string )
	{
		return base64_encode( mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($salt), $string, MCRYPT_MODE_CBC, md5(md5($salt))) ) ;
	}

	FUNCTION Util_Functions_itr_Decrypt( $salt, $encrypted )
	{
		return rtrim( mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($salt), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($salt))), "\0" ) ;
	}

?>