<?php
	if ( defined( 'API_Util_Upload_' ) ) { return ; }	
	define( 'API_Util_Upload_', true ) ;

	FUNCTION Util_Upload_GetChatIcon( $prefix, $deptid )
	{
		global $CONF ;

		$now = time() ; $custom = 0 ; $image_path = "$CONF[DOCUMENT_ROOT]/pics/icons/$prefix".".gif" ; $image_type = "image/gif" ;
		if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.GIF" ) ) { $custom = 1 ;
			$image_path = "$CONF[CONF_ROOT]/$prefix"."_$deptid.GIF" ; $image_type = "image/gif" ; }
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.JPEG" ) ) { $custom = 1 ;
			$image_path = "$CONF[CONF_ROOT]/$prefix"."_$deptid.JPEG" ; $image_type = "image/jpeg" ; }
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.PNG" ) ) { $custom = 1 ;
			$image_path = "$CONF[CONF_ROOT]/$prefix"."_$deptid.PNG" ; $image_type = "image/png" ; }
		return Util_Upload_Output( $custom, $deptid, $image_path, $image_type ) ;
	}

	FUNCTION Util_Upload_GetLogo( $prefix, $deptid )
	{
		global $CONF ;
		global $theme ;

		if ( isset( $theme ) && $theme ) { $local_theme = $theme ; }
		else { $local_theme = $CONF["THEME"] ; }

		$now = time() ; $custom = 0 ; $image_path = "$CONF[DOCUMENT_ROOT]/pics/$prefix.png" ; $image_type = "image/png" ;
		if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.GIF" ) ) { $custom = 1 ;
			$image_path = "$CONF[CONF_ROOT]/$prefix"."_$deptid.GIF" ; $image_type = "image/gif" ; }
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.JPEG" ) ) { $custom = 1 ;
			$image_path = "$CONF[CONF_ROOT]/$prefix"."_$deptid.JPEG" ; $image_type = "image/jpeg" ; }
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.PNG" ) ) { $custom = 1 ;
			$image_path = "$CONF[CONF_ROOT]/$prefix"."_$deptid.PNG" ; $image_type = "image/png" ; }
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_0.GIF" ) ) { $custom = 1 ;
			$image_path = "$CONF[CONF_ROOT]/$prefix"."_0.GIF" ; $image_type = "image/gif" ; }
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_0.JPEG" ) ) { $custom = 1 ;
			$image_path = "$CONF[CONF_ROOT]/$prefix"."_0.JPEG" ; $image_type = "image/jpeg" ; }
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_0.PNG" ) ) { $custom = 1 ;
			$image_path = "$CONF[CONF_ROOT]/$prefix"."_0.PNG" ; $image_type = "image/png" ; }
		else if ( is_file( "$CONF[DOCUMENT_ROOT]/themes/$local_theme/$prefix.png" ) ) { $custom = 1 ;
			$image_path = "$CONF[DOCUMENT_ROOT]/themes/$local_theme/$prefix.png" ; $image_type = "image/png" ; }
		return Util_Upload_Output( $custom, $deptid, $image_path, $image_type ) ;
	}

	FUNCTION Util_Upload_GetInitiate( $deptid )
	{
		global $CONF ;

		$now = time() ; $image_path = "$CONF[DOCUMENT_ROOT]/themes/initiate/initiate.gif" ; $image_type = "image/gif" ;
		if ( isset( $CONF["icon_initiate"] ) && $CONF["icon_initiate"] && is_file( "$CONF[CONF_ROOT]/$CONF[icon_initiate]" ) ) {
			$image_path = "$CONF[CONF_ROOT]/$CONF[icon_initiate]" ;
			LIST( $null, $image_info ) = getimagesize( "$CONF[CONF_ROOT]/$CONF[icon_initiate]" ) ;
			$image_type = image_type_to_mime_type( $image_info[1] ) ; }
		return Util_Upload_Output( 1, $deptid, $image_path, $image_type ) ;
	}

	FUNCTION Util_Upload_Output( $custom, $deptid, $image_path, $image_type )
	{
		$image_binary = file_get_contents( $image_path ) ;
		$image_base64 = base64_encode( $image_binary ) ;
		$custom_flag = ( $custom ) ? ",_$deptid." : "" ;
		return ( "data:".$image_type.";base64,".$image_base64 ) ;
	}
?>