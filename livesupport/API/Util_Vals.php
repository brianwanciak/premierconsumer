<?php
	if ( defined( 'API_Util_Vals' ) ) { return ; }	
	define( 'API_Util_Vals', true ) ;

	FUNCTION Util_Vals_WriteToConfFile( $valname, $val )
	{
		global $CONF ; $val = preg_replace( "/['`]/", "", $val ) ;

		if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { $conf_vars = "" ; }
		else { $conf_vars = "\$CONF = Array() ;\n" ; }
		foreach( $CONF as $key => $value )
		{
			if ( ( $key != "CHAT_IO_DIR" ) && ( $key != "TYPE_IO_DIR" ) )
			{
				if ( $key == "SQLPASS" ) { $CONF[$key] = stripslashes( $value ) ; }
				if ( $key == $valname ) { $CONF[$key] = $val ; }
				if ( $key == "DOCUMENT_ROOT" )
				{
					$conf_vars .= "\$CONF['$key'] = addslashes( '".$CONF[$key]."' ) ;\n" ;
				} else { $conf_vars .= "\$CONF['$key'] = '".$CONF[$key]."' ;\n" ; }
			}
		}

		// auto add new conf value if not exist
		if ( !isset( $CONF[$valname] ) ) { $conf_vars .= "\$CONF['$valname'] = '$val' ;\n" ; }
		$conf_vars = preg_replace( "/`/", "", $conf_vars ) ;

		$conf_string = "< php\n	$conf_vars" ;
		$conf_string .= "	if ( phpversion() >= '5.1.0' ){ date_default_timezone_set( \$CONF['TIMEZONE'] ) ; }\n" ;
		$conf_string .= "	include_once( \"\$CONF[DOCUMENT_ROOT]/API/Util_Vars.php\" ) ;\n?>" ;
		$conf_string = preg_replace( "/< php/", "<?php", $conf_string ) ;

		if ( $fp = fopen( realpath( "$CONF[CONF_ROOT]/config.php" ), "w" ) )
		{
			fwrite( $fp, $conf_string, strlen( $conf_string ) ) ; fclose( $fp ) ;
			return true ;
		}
		else { return false ; }
	}

	FUNCTION Util_Vals_WriteToFile( $valname, $val )
	{
		global $CONF ;
		global $VALS ; $val = preg_replace( "/'/", "", $val ) ;

		if ( !isset( $VALS[$valname] ) ) { $VALS[$valname] = "" ; }

		$conf_vars = "\$VALS = Array() ; " ;
		foreach( $VALS as $key => $value )
		{
			if ( $key == $valname ) { $VALS[$key] = $val ; }
			$conf_vars .= " \$VALS['$key'] = '".$VALS[$key]."' ; " ;
		} $conf_vars = preg_replace( "/`/", "", $conf_vars ) ;

		$conf_string = "< php $conf_vars ?>" ;
		$conf_string = preg_replace( "/< php/", "<?php", preg_replace( "/  +/", " ", $conf_string ) ) ;

		if ( $fp = fopen( "$CONF[CONF_ROOT]/vals.php", "w" ) )
		{
			fwrite( $fp, $conf_string, strlen( $conf_string ) ) ; fclose( $fp ) ;
			return true ;
		}
		else { return false ; }
	}

	FUNCTION Util_Vals_WriteVersion( $version )
	{
		global $CONF ;

		$version_string = "< php \$VERSION = \"$version\" ; ?>" ;
		$version_string = preg_replace( "/< php/", "<?php", $version_string ) ;
		$fp = fopen( "$CONF[CONF_ROOT]/VERSION.php", "w" ) ;
		fwrite( $fp, $version_string, strlen( $version_string ) ) ; fclose( $fp ) ;
	}
?>