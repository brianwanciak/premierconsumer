<?php
	include_once( "./web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;
	$proto = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "proto" ), "n" ) ) ;
	$url = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "url" ), "url" ) ) ;
	$error = "" ; $xframe = 0 ;

	if ( $url )
	{
		$proto_url = ( preg_match( "/^https/i", $url ) ) ? 1 : 0 ;
		if ( !function_exists( "curl_init" ) || !function_exists( "curl_exec" ) )
		{
			$error = "Server PHP does not support <a href='http://php.net/manual/en/book.curl.php' target='_blank' style='color: #FFFFFF;'>cURL</a>.  Contact your server admin to enable PHP cURL support to utilize the External URL feature.  Also check the 'curl_exec' function is not disabled in the php.ini file." ;
			$xframe = 1 ;
		}
		else
		{
			$request = curl_init( $url ) ;
			curl_setopt( $request, CURLOPT_RETURNTRANSFER, true ) ;
			curl_setopt( $request, CURLOPT_CUSTOMREQUEST, "GET" ) ;
			curl_setopt( $request, CURLOPT_HEADER, true ) ;
			curl_setopt( $request, CURLOPT_FOLLOWLOCATION, true ) ;
			curl_setopt( $request, CURLOPT_AUTOREFERER, true ) ;
			$response = curl_exec( $request ) ;
			$curl_errno = curl_errno( $request ) ;
			$header_size = curl_getinfo( $request, CURLINFO_HEADER_SIZE ) ;
			$header_string = substr( $response, 0, $header_size ) ;
			$status = curl_getinfo( $request, CURLINFO_HTTP_CODE ) ; 
			curl_close( $request ) ;

			$header_string = preg_replace( "/(\r\n)|(\r)|(\n)/", " ", $header_string ) ;

			preg_match( "/X\-Frame\-Options: (.*?) /i", $header_string, $matches ) ;
			if ( isset( $matches[0] ) && ( preg_match( "/(SAMEORIGIN)|(DENY)/i", $matches[0] ) ) ) { $xframe = 1 ; }
			else if ( !$header_size && !$status ) { $xframe = 0 ; }
			else if ( $status == 301 ) { $xframe = 1 ; }
			else if ( $proto && !$proto_url )
			{
				$error = "[ HTTP Protocol Mismatch ]  Cannot load an insecure HTTP URL ($url) from a secure HTTPS parent.  The URL being loaded must be secure HTTPS protocol." ;
				$xframe = 1 ;
			}
		}
	}
?>
<?php include_once( "./inc_doctype.php" ) ?>
<head>
<title> blank page </title>
<meta name="description" content="phplive_c615">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<?php include_once( "./inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="./themes/<?php echo $theme ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script language="JavaScript">
<!--
	var xframe = <?php echo $xframe ?> ;

	$(document).ready(function()
	{
		if ( <?php echo ( $error ) ? 1 : 0 ; ?> ) { $('#error_X-Frame-Options').html( "<?php echo $error ?>" ) ; }
	});

	function display_error( theerror, theextra )
	{
		if ( theerror = "XFrame" )
		{
			setTimeout( function(){ display_error_XFrame( theextra ) ; }, 3000 ) ;
		}
	}

	function display_error_XFrame( theurl )
	{
		$('#error_X-Frame-Options_url_txt').html( theurl ) ;
		$('#error_X-Frame-Options_url_href').html( "<a href=\""+theurl+"\" target=\"_blank\" style=\"color: #FFFFFF;\">"+theurl+"</a>" ) ;
		$('#error_X-Frame-Options').show() ;
	}
//-->
</script>
</head>
<body style="background: transparent;">
<!-- blank page for loading in various areas for iframe notices and errors -->
<div id="error_X-Frame-Options" style="display: none; padding: 25px;" class="info_error">

	Could not display <span id="error_X-Frame-Options_url_txt" style="font-weight: bold;"></span> because it can only be viewed from a new window.  This is due to the content restriction set at their website.  Click the link to access the page in a new window.
	<div style="margin-top: 15px; font-size: 14px; font-weight: bold;"><span id="error_X-Frame-Options_url_href" style="font-weight: bold;"></span></div>

</div>
</body>
</html>
