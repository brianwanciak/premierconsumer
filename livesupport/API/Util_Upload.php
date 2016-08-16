<?php
	if ( defined( 'API_Util_Upload' ) ) { return ; }	
	define( 'API_Util_Upload', true ) ;

	FUNCTION Util_Upload_GetChatIcon( $prefix, $deptid )
	{
		global $CONF ;

		$now = time() ;
		if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.GIF" ) )
			return "$CONF[UPLOAD_HTTP]/$prefix"."_$deptid.GIF?".$now ;
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.JPEG" ) )
			return "$CONF[UPLOAD_HTTP]/$prefix"."_$deptid.JPEG?".$now ;
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.PNG" ) )
			return "$CONF[UPLOAD_HTTP]/$prefix"."_$deptid.PNG?".$now ;
		else if ( isset( $CONF[$prefix] ) && is_file( "$CONF[CONF_ROOT]/$CONF[$prefix]" ) && $CONF["$prefix"] )
			return "$CONF[UPLOAD_HTTP]/$CONF[$prefix]?".$now ;
		else
			return "$CONF[BASE_URL]/pics/icons/$prefix".".gif" ;
	}

	FUNCTION Util_Upload_GetLogo( $prefix, $deptid )
	{
		global $CONF ;
		global $theme ;

		if ( isset( $theme ) && $theme ) { $local_theme = $theme ; }
		else { $local_theme = $CONF["THEME"] ; }

		$now = time() ;
		if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.GIF" ) )
			return "$CONF[UPLOAD_HTTP]/$prefix"."_$deptid.GIF?".$now ;
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.JPEG" ) )
			return "$CONF[UPLOAD_HTTP]/$prefix"."_$deptid.JPEG?".$now ;
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_$deptid.PNG" ) )
			return "$CONF[UPLOAD_HTTP]/$prefix"."_$deptid.PNG?".$now ;
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_0.GIF" ) )
			return "$CONF[UPLOAD_HTTP]/$prefix"."_0.GIF?".$now ;
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_0.JPEG" ) )
			return "$CONF[UPLOAD_HTTP]/$prefix"."_0.JPEG?".$now ;
		else if ( is_file( "$CONF[CONF_ROOT]/$prefix"."_0.PNG" ) )
			return "$CONF[UPLOAD_HTTP]/$prefix"."_0.PNG?".$now ;
		else if ( is_file( "$CONF[DOCUMENT_ROOT]/themes/$local_theme/$prefix.png" ) )
			return "$CONF[BASE_URL]/themes/$local_theme/$prefix.png?".$now ;
		else
			return "$CONF[BASE_URL]/pics/$prefix.png" ;
	}

	FUNCTION Util_Upload_GetInitiate( $deptid )
	{
		global $CONF ;

		$now = time() ;
		if ( isset( $CONF["icon_initiate"] ) && $CONF["icon_initiate"] && is_file( "$CONF[CONF_ROOT]/$CONF[icon_initiate]" ) )
			return "$CONF[UPLOAD_HTTP]/$CONF[icon_initiate]?".$now ;
		else if ( is_file( "$CONF[DOCUMENT_ROOT]/themes/initiate/initiate.gif" ) )
			return "$CONF[BASE_URL]/themes/initiate/initiate.gif?".$now ;
		else
			return "$CONF[BASE_URL]/pics/icons/initiate.gif" ;
	}
?>