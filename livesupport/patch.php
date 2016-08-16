<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	if ( !is_file( "./web/config.php" ) ){ HEADER("location: ./setup/install.php") ; exit ; }
	include_once( "./web/config.php" ) ;

	if ( !isset( $CONF['SQLTYPE'] ) ) { $CONF['SQLTYPE'] = "SQL.php" ; }
	else if ( $CONF['SQLTYPE'] == "mysql" ) { $CONF['SQLTYPE'] = "SQL.php" ; }

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;

	$from = Util_Format_Sanatize( Util_Format_GetVar( "from" ), "ln" ) ;
	$patch = Util_Format_Sanatize( Util_Format_GetVar( "patch" ), "n" ) ;
	$patch_c = Util_Format_Sanatize( Util_Format_GetVar( "patch_c" ), "n" ) ;
	$patched = 0 ;
	$loopy = Util_Format_Sanatize( Util_Format_GetVar( "loopy" ), "n" ) ;

	$query = isset( $_SERVER["QUERY_STRING"] ) ? $_SERVER["QUERY_STRING"] : "" ;

	// basic check for permissions and gather ini settings
	$ini_open_basedir = ini_get("open_basedir") ;
	$ini_safe_mode = ini_get("safe_mode") ;
	$safe_mode = preg_match( "/on/i", $ini_safe_mode ) ? 1 : 0 ;

	if ( !is_file( "$CONF[DOCUMENT_ROOT]/blank.php" ) )
		ErrorHandler( 612, "\$CONF[DOCUMENT_ROOT] variable in config.php is invalid.", $PHPLIVE_FULLURL, 0, Array() ) ;
	else if ( !is_writeable( "$CONF[CONF_ROOT]/" ) )
		ErrorHandler( 609, "Permission denied on web/ directory. ($ini_open_basedir, $ini_safe_mode, $safe_mode)", $PHPLIVE_FULLURL, 0, Array() ) ;
	else if ( !is_writeable( "$CONF[CONF_ROOT]/config.php" ) )
		ErrorHandler( 609, "Permission denied on web/config.php directory. ($ini_open_basedir, $ini_safe_mode, $safe_mode)", $PHPLIVE_FULLURL, 0, Array() ) ;
	else if ( !is_writeable( "$CONF[CONF_ROOT]/patches/" ) )
		ErrorHandler( 609, "Permission denied on web/patches/ directory. ($ini_open_basedir, $ini_safe_mode, $safe_mode)", $PHPLIVE_FULLURL, 0, Array() ) ;
	else if ( !is_writeable( $CONF["CHAT_IO_DIR"] ) )
		ErrorHandler( 609, "Permission denied on web/chat_sessions directory. ($ini_open_basedir, $ini_safe_mode, $safe_mode)", $PHPLIVE_FULLURL, 0, Array() ) ;
	else if ( !is_writeable( $CONF["TYPE_IO_DIR"] ) )
		ErrorHandler( 609, "Permission denied on web/chat_initiate directory. ($ini_open_basedir, $ini_safe_mode, $safe_mode)", $PHPLIVE_FULLURL, 0, Array() ) ;

	if ( $from == "chat" )
		$url = "phplive.php?patched=1&".$query ;
	else if ( $from == "embed" )
		$url = "phplive_embed.php?patched=1&".$query ;
	else if ( $from == "setup" )
		$url = "setup/?patched=1&".$query ;
	else
		$url = "index.php?patched=1&".$query ;

	$fast_file = ( isset( $FAST_PATCH  ) && $FAST_PATCH  ) ? "_fast" : "" ;
	if ( $patch )
	{
		if ( !is_file( "$CONF[CONF_ROOT]/patches/$patch_v" ) )
		{
			if ( $patch_c <= 51 ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Patches/Util_Patches_1".$fast_file.".php" ) ; }
			else if ( $patch_c <= 85 ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Patches/Util_Patches_2".$fast_file.".php" ) ; }
			else { include_once( "$CONF[DOCUMENT_ROOT]/API/Patches/Util_Patches_3".$fast_file.".php" ) ; }
			$json_data = "json_data = { \"status\": 0, \"patch_c\": $patched };" ;
		}
		else
		{
			$json_data = "json_data = { \"status\": 1 };" ;
		}

		if ( isset( $dbh ) && isset( $dbh['con'] ) ) { database_mysql_close( $dbh ) ; }
		print "$json_data" ;
		exit ;
	}

?>
<?php include_once( "./inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "./inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="./css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="./js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var patch_c = 0 ;
	var loader_c ;
	var dev = 0 ; var dev_c = 0 ;

	var patch_interval = ( "$fast_file" == "_fast" ) ? 3000 : 1100 ;

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;

		$("body").css({'background': '#F7F7F7'}) ;
		auto_patch() ;
		init_loader_image() ;
	});

	function init_loader_image()
	{
		setInterval( function(){
			loader_c_temp = Math.floor(Math.random() * 3) + 1 ;
			if ( loader_c_temp != loader_c )
			{
				loader_c = loader_c_temp ;
				if ( loader_c == 1 )
				{
					$('#image_1').show() ;
					$('#image_2').hide() ;
					$('#image_3').hide() ;
				}
				else if ( loader_c == 2 )
				{
					$('#image_2').show() ;
					$('#image_1').hide() ;
					$('#image_3').hide() ;
				}
				else
				{
					$('#image_3').show() ;
					$('#image_1').hide() ;
					$('#image_2').hide() ;
				}
			}
		}, 1000 ) ;
	}

	function auto_patch()
	{
		var json_data = new Object ;
		var unique = unixtime() ;

		$('#loading').fadeTo("fast", .5) ;
		$.ajax({
		type: "POST",
		url: "./patch.php",
		data: "patch=1&patch_c="+patch_c+"&unique="+unique,
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				alert( err ) ;
				return false ;
			}

			patch_c = json_data.patch_c ;

			if ( json_data.status )
			{
				if ( dev && ( dev_c < 10 ) )
				{
					++dev_c ; patch_c = dev_c ;
					var percent = Math.round( ( patch_c/100 )*100 ) ;
					$('#status').html( percent ) ; $('#loading').fadeTo("fast", 1) ;
					setTimeout( function(){ patch_c += 1 ; auto_patch() ; }, patch_interval ) ;
				}
				else
				{
					$('#div_configure').hide() ;
					$('#div_success').show() ;

					if ( "<?php echo $from ?>" == "chat" )
					{
						setTimeout( function(){ do_redirect() ; }, 3000 ) ;
					}
					else
					{
						setTimeout( function(){ do_redirect() ; }, 45000 ) ;
					}
				}
			}
			else
			{
				var percent = Math.round( ( patch_c/<?php echo $patch_v ?> )*100 ) ;
				$('#status').html( percent ) ; $('#loading').fadeTo("fast", 1) ;
				setTimeout( function(){ patch_c += 1 ; auto_patch() ; }, patch_interval ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error patch "+patch_c+" process.  Please reload the page and try again." ) ;
		} });
	}

	function do_redirect()
	{
		location.href = "<?php echo $url ?>" ;
	}
//-->
</script>
</head>
<body style="overflow: hidden;">

<div style="width: 300px; margin: 0 auto; margin-top: 55px; padding: 10px; text-align: center;" class="info_box">

	<div id="div_configure">
		<table cellspacing=0 cellpadding=10 border=0 width="100%">
		<tr>
			<td width="52" align="center">
				<div id="loading" style="width: 48px; text-shadow: 1px 1px #FFFFFF;">
					<span id="image_1"><img src="pics/loading_patch_1.gif" width="38" height="38" border="0" alt="" style="background: #FBFBFB; -moz-border-radius: 5px; border-radius: 5px; padding: 2px; border: 1px solid #FFFFFF;"></span>
					<span id="image_2" style="display: none;"><img src="pics/loading_patch_2.gif" width="38" height="38" border="0" alt="" style="background: #FBFBFB; -moz-border-radius: 5px; border-radius: 5px; padding: 2px; border: 1px solid #FFFFFF;"></span>
					<span id="image_3" style="display: none;"><img src="pics/loading_patch_3.gif" width="38" height="38" border="0" alt="" style="background: #FBFBFB; -moz-border-radius: 5px; border-radius: 5px; padding: 2px; border: 1px solid #FFFFFF;"></span>
				</div>
			</td>
			<td><div style="">
				Updating and configuring the system...
				<div style="margin-top: 15px; font-size: 24px; font-weight: bold;"><span id="status">1</span>%</div></div>
			</td>
		</tr>
		</table>
	</div>
	<div id="div_success" style="display: none;">
		<div>Update complete.</div>
		<div style="margin-top: 15px; font-size: 24px; font-weight: bold;">100%</div>
		<div style="margin-top: 15px;"><button type="button" class="btn" onClick="do_redirect()">Continue</button></div>
	</div>

</div>

<!-- [winapp=4] -->

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>
