<?php
	if ( defined( 'API_Util_Upload_File' ) ) { return ; }	
	define( 'API_Util_Upload_File', true ) ;

	FUNCTION Util_Upload_File( $icon, $deptid )
	{
		global $CONF ;
		$now = time() ;
		$extension = $error = $filename = "" ;

		if ( !defined( 'API_Util_Vals' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

		if ( ( $icon == "profile" ) && isset( $_SERVER['CONTENT_LENGTH'] ) && ( $_SERVER['CONTENT_LENGTH'] > 50000 ) )
			$error = "The uploaded file exceeds the allowed size of 50 kb." ;
		else if ( isset( $_SERVER['CONTENT_LENGTH'] ) && ( $_SERVER['CONTENT_LENGTH'] > 200000 ) )
			$error = "The uploaded file exceeds the allowed size of 200 kb." ;
		else if ( !$_FILES[$icon]['tmp_name'] )
			$error = "Nothing to upload." ;
		else if ( isset( $_FILES[$icon]['size'] ) )
		{
			$filename = basename( $_FILES[$icon]['name'] ) ;
			$fileinfo = getimagesize( $_FILES[$icon]['tmp_name'] ) ;
			$filetype = $_FILES[$icon]['type'] ;
			$errorno = $_FILES[$icon]['error'] ;
			$filesize = $_FILES[$icon]['size'] ;
			$filename_parts = explode( ".", $filename ) ;

			if ( $errorno == UPLOAD_ERR_NO_FILE )
				$error = "Nothing to upload." ;
			else if ( !is_uploaded_file( $_FILES[$icon]['tmp_name'] ) )
				$error = "Invalid file." ;
			else if ( !$fileinfo )
				$error = "Please provide a valid image file.  Accepted formats are GIF, PNG, JPG or JPEG formats." ;
			else if ( count( $filename_parts ) == 1 )
				$error = "File name format is invalid.  Could not detect the image type extension." ;
			else if ( count( $filename_parts ) > 2 )
				$error = "File name format is invalid.  File name should contain only one dot within the name. (example: image.jpg)" ;
			else if ( !preg_match( "/(gif)|(jpeg)|(jpg)|(png)/i", $filename_parts[1] ) )
				$error = "Please provide a valid image file.  Accepted formats are GIF, PNG, JPG or JPEG." ;
			else if ( ( $icon == "logo" ) && ( $fileinfo[0] > 520 ) )
				$error = "Image width of ".$fileinfo[0]." pixels is greater then allowed maximum logo width of 520 pixels."  ;
			else if ( ( $icon == "logo" ) && ( $fileinfo[1] > 150 ) )
				$error = "Image height of ".$fileinfo[1]." pixels is greater then allowed maximum logo height of 150 pixels."  ;
			else if ( $errorno == UPLOAD_ERR_OK )
			{
				if ( preg_match( "/gif/i", $filetype ) )
					$extension = "GIF" ;
				else if ( preg_match( "/(jpeg)|(jpg)/i", $filetype ) )
					$extension = "JPEG" ;
				else if ( preg_match( "/png/i", $filetype ) )
					$extension = "PNG" ;

				if ( $extension )
				{
					if ( preg_match( "/(online)|(offline)|(initiate)|(logo)|(profile)/", $icon ) )
					{
						$filename = $icon."_$deptid" ;

						if ( is_file( "$CONF[CONF_ROOT]/$filename.PNG" ) )
							unlink( "$CONF[CONF_ROOT]/$filename.PNG" ) ;
						else if ( is_file( "$CONF[CONF_ROOT]/$filename.JPEG" ) )
							unlink( "$CONF[CONF_ROOT]/$filename.JPEG" ) ;
						else if ( is_file( "$CONF[CONF_ROOT]/$filename.GIF" ) )
							unlink( "$CONF[CONF_ROOT]/$filename.GIF" ) ;

						$filename = $icon."_$deptid.$extension" ;
					}
					else
						$filename = "$icon.$extension" ;

					if( move_uploaded_file( $_FILES[$icon]['tmp_name'], "$CONF[CONF_ROOT]/$filename" ) )
					{
						if ( preg_match( "/(logo)|(initiate)/", $icon ) && !$deptid )
							$error = ( Util_Vals_WriteToConfFile( $icon, $filename ) ) ? "" : "Could not write to config file." ;
					}
					else
						$error = "Could not process uploading of files." ;
				}
				else
					$error = "Please provide a valid image file.  GIF, PNG, JPG or JPEG formats only." ;
			}
			else if ( $errorno == UPLOAD_ERR_NO_TMP_DIR )
				$error = "Upload temp dir not set or not writeable.  Check the value of \"upload_tmp_dir\" in the php.ini file." ;
			else if ( $errorno == UPLOAD_ERR_FORM_SIZE )
				$error = "The uploaded file exceeds the allowed file size of 200kb." ;
			else if ( $errorno == UPLOAD_ERR_INI_SIZE )
				$error = "The uploaded file exceeds the upload_max_filesize directive." ;
			else if ( $errorno )
				$error = "Error in uploading. [errorno: $errorno]" ;
			else
				$error = "Error in uploading." ;
		}
		else
			$error = "Please provide a valid image file.  GIF, PNG or JPEG formats only." ;
		return Array( $error, $filename ) ;
	}
?>