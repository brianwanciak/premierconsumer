<?php
	if ( defined( 'API_Chat_Util' ) ) { return ; }
	define( 'API_Chat_Util', true ) ;

	FUNCTION UtilChat_AppendToChatfile( $chatfile,
							$string )
	{
		if ( ( $chatfile == "" ) || ( $string == "" ) )
			return false ;
		global $CONF ; global $VARS_MAX_CHAT_FILESIZE ;
		$string .= "<>" ; // add new line marker

		$filesize = is_file( "$CONF[CHAT_IO_DIR]/$chatfile" ) ? filesize( "$CONF[CHAT_IO_DIR]/$chatfile" ) : 0 ;
		if ( $filesize < $VARS_MAX_CHAT_FILESIZE )
		{
			$fp = fopen( "$CONF[CHAT_IO_DIR]/$chatfile", "a" ) ;
			fwrite( $fp, $string, strlen( $string ) ) ;
			fclose( $fp ) ;
		} return true ;
	}

	FUNCTION UtilChat_ExportChat( $chatfile )
	{
		if ( $chatfile == "" )
			return false ;
		global $CONF ;

		$output = Array() ;
		if ( is_file( "$CONF[CHAT_IO_DIR]/$chatfile" ) )
		{
			$trans_raw = file_get_contents( "$CONF[CHAT_IO_DIR]/$chatfile" ) ;
			$output[] = $trans_raw ;
			$output[] = preg_replace( "/<(.*?)>/", "", preg_replace( "/<>/", "\r\n", $trans_raw ) ) ;
		} return $output ;
	}

	FUNCTION UtilChat_WriteIsWriting( $theces, $theflag, $theisop, $theisop_, $theisop__ )
	{
		if ( $theces == "" )
			return false ;
		global $CONF ;

		$iid = $theisop ;
		$typing_file = "$theces$iid.txt" ;
		if ( is_file( "$CONF[CHAT_IO_DIR]/$theces.txt" ) )
		{
			if ( $theflag )
			{
				if ( !is_file( "$CONF[TYPE_IO_DIR]/$typing_file" ) )
					touch( "$CONF[TYPE_IO_DIR]/$typing_file" ) ;
			}
			else
			{
				if ( is_file( "$CONF[TYPE_IO_DIR]/$typing_file" ) )
					unlink( "$CONF[TYPE_IO_DIR]/$typing_file" ) ;
			} return true ;
		}
		else
			return false ;
	}
?>