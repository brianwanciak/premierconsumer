<?php
	// cache options (in case needed)
	/////////////////////////////////////////////////
	if ( isset( $NO_CACHE ) && $NO_CACHE )
	{
		HEADER( "Expires: Fri, 31 Dec 1999 01:00:00 GMT" ) ;
		HEADER( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" ) ; 
		HEADER( "Cache-Control: no-store, no-cache, must-revalidate" ) ; 
		HEADER( "Cache-Control: post-check=0, pre-check=0", false ) ;
		HEADER( "Pragma: no-cache" ) ;
	}
?>