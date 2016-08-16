<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
	
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$apikey = Util_Format_Sanatize( Util_Format_GetVar( "apikey" ), "ln" ) ;
	$jump = ( Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) : "geoip" ;
	$success = Util_Format_Sanatize( Util_Format_GetVar( "success" ), "n" ) ;

	$error = "" ; $dev = 0 ;
	$VERSION_GEO = 0 ;


	/***************************************/
	// location of the addons/geo_data directory
	$geo_dir = "$CONF[DOCUMENT_ROOT]/addons/geo_data" ;
	/***************************************/


	if ( is_dir( $geo_dir ) && is_file( "$geo_dir/VERSION.php" ) )
		include_once( "$geo_dir/VERSION.php" ) ;

	LIST( $your_ip, $null ) = Util_IP_GetIP( "" ) ;

	if ( $action == "import_geo_files" )
	{
		$json_data = $next_file = $error = "" ;

		if ( !is_dir( $geo_dir ) )
			$json_data = "json_data = { \"status\": 0, \"error\": \"Directory addons/geo_data/ does not exist.\" };" ;
		else if ( !is_file( "$geo_dir/VERSION.php" ) )
			$json_data = "json_data = { \"status\": 0, \"error\": \"GeoIP Addon is invalid.  Download the latest GeoIP Addon.\" };" ;
		else
		{
			$index = Util_Format_Sanatize( Util_Format_GetVar( "index" ), "n" ) * 1 ;
			$index_next = $index + 1 ;

			// if first pass, empty out the database for the new inserts. truncate again for good measure
			if ( !$index )
			{
				if ( !$dev )
				{
					$query = "TRUNCATE TABLE p_geo_bloc" ;
					database_mysql_query( $dbh, $query ) ;

					$query = "TRUNCATE TABLE p_geo_loc" ;
					database_mysql_query( $dbh, $query ) ;
				}
			}

			if ( !$error )
			{
				$files = Array() ;
				$dir_files = glob( $geo_dir."/*", GLOB_NOSORT ) ;
				$total_dir_files = count( $dir_files ) ;
				if ( $total_dir_files )
				{
					for ( $c = 0; $c < $total_dir_files; ++$c )
					{
						if ( $dir_files[$c] && is_file( $dir_files[$c] ) && !preg_match( "/(README.txt)|(VERSION.php)|(COPYRIGHT.txt)/", $dir_files[$c] ) )
							$files[] = $dir_files[$c] ;
					}
				}

				$total_dir_files = count( $files ) ;
				for( $c = 0; $c < $total_dir_files; ++$c )
				{
					$file = $files[$c] ;
					if ( $c == $index )
					{
						if ( !$dev )
							import_file( $dbh, $file ) ;
						$percent = floor( $c/count($files) * 100 ) ;

						$file_display = str_replace( "$geo_dir", "", $file ) ;
						$json_data = "json_data = { \"status\": 0, \"index\": $index_next, \"total\": $total_dir_files, \"percent\": $percent, \"file\": \"$file_display\" };" ;
						break ;
					}
				}

				if ( !$json_data )
				{
					include_once( "$geo_dir/VERSION.php" ) ;

					Util_Vals_WriteToConfFile( "geo", 1 ) ;
					$CONF["geo"] = 1 ;
					$json_data = "json_data = { \"status\": 1 };" ;
				}
			}
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"$error\" };" ;
		}

		if ( isset( $dbh ) && isset( $dbh['con'] ) )
			database_mysql_close( $dbh ) ;

		print "$json_data" ;
		exit ;
	}
	else if ( $action == "update_api" )
	{
		if ( strlen( $apikey ) == 39 )
		{
			$error = ( Util_Vals_WriteToConfFile( "geomap", "$apikey" ) ) ? "" : "Could not write to config file." ;
			if ( !$error )
				$json_data = "json_data = { \"status\": 1 };" ;
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"Could not write to conf file.\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"API Key format is invalid.\" };" ;

		print "$json_data" ;
		exit ;
	}
	else if ( $action == "check_dir" )
	{
		if ( !is_dir( $geo_dir ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0 };" ;

		print "$json_data" ;
		exit ;
	}
	else if ( $action == "clear" )
	{
		if ( !$dev )
		{
			$query = "TRUNCATE TABLE p_geo_bloc" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "TRUNCATE TABLE p_geo_loc" ;
			database_mysql_query( $dbh, $query ) ;
		}

		Util_Vals_WriteToConfFile( "geo", "" ) ;
		database_mysql_close( $dbh ) ;
		HEADER( "location: ./extras_geo.php?ses=$ses&action=cleared" ) ;
		exit ;
	}
	else if ( $action == "clear_api" )
	{
		Util_Vals_WriteToConfFile( "geomap", "" ) ;
		HEADER( "location: ./extras_geo.php?ses=$ses&jump=geomap&action=cleared_api" ) ;
		exit ;
	}
	else if ( $action == "lookup" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Hash.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/Util.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/GeoIP/get.php" ) ;

		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		if ( preg_match( "/^\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b$/", $ip ) )
		{
			$countries = Util_Hash_Countries() ;
			LIST( $ip_num, $network ) = UtilIPs_IP2Long( $ip ) ;
			$geoinfo = GeoIP_get_GeoInfo( $dbh, $ip_num, $network ) ;
			database_mysql_close( $dbh ) ;
			
			$country = ( isset( $geoinfo["country"] ) && $geoinfo["country"] ) ? $geoinfo["country"] : "unknown" ;
			$country_name = ( isset( $geoinfo["country"] ) && $geoinfo["country"] ) ? $countries[$geoinfo["country"]] : "Location Unknown" ;
			$region = ( isset( $geoinfo["region"] ) && $geoinfo["region"] ) ? $geoinfo["region"] : "-" ;
			$city = ( isset( $geoinfo["city"] ) && $geoinfo["city"] ) ? $geoinfo["city"] : "-" ;
			$latitude = ( isset( $geoinfo["latitude"] ) && $geoinfo["latitude"] ) ? $geoinfo["latitude"] : 28.613459424004414 ;
			$longitude = ( isset( $geoinfo["longitude"] ) && $geoinfo["longitude"] ) ? $geoinfo["longitude"] : -40.4296875 ;

			$country = strtolower( $country ) ;

			$json_data = "json_data = { \"status\": 1, \"country\": \"$country\", \"country_name\": \"$country_name\", \"region\": \"$region\", \"city\": \"$city\", \"latitude\": \"$latitude\", \"longitude\": \"$longitude\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid IP address format.\" };" ;

		print $json_data ;
		exit ;
	}

	function import_file( $dbh, $thefile )
	{
		$queries = Array() ;

		$fh = fopen( $thefile, "rb" ) ;
		while( !feof( $fh ) )
		{
			$query = preg_replace( "/[`;]/", "", rtrim( fgets( $fh ) ) ) ;
			if ( $query )
				$queries[] = $query ;
		}
		fclose( $fh ) ;

		for ( $c = 0; $c < count( $queries ); ++$c )
		{
			$query = utf8_encode( $queries[$c] ) ;
			database_mysql_query( $dbh, $query ) ;
		}
	}
	
	$addon_emo = ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emo.php" ) ) ? 1 : 0 ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var st_geo_import ;
	var st_geo_import_cycle = 5 ; // seconds
	var global_ip ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "extras" ) ;
		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( !$geomap ): ?>
		$('#div_maps_steps').show() ;
		<?php else: ?>
		$('#div_maps_update').show() ;
		<?php endif; ?>

		if ( "<?php echo $error ?>" )
			do_alert( 0, "<?php echo $error ?>" ) ;
		else if ( ( "<?php echo $action ?>" == "update" ) || <?php echo $success ?> )
			do_alert( 1, "Success" ) ;
		else if ( "<?php echo $action ?>" == "cleared" )
			do_alert( 1, "GeoIP addon has been reset." ) ;
		else if ( "<?php echo $action ?>" == "cleared_api" )
			do_alert( 1, "Google Maps API Key has been cleared." ) ;
	});

	function confirm_import()
	{
		if ( confirm( "The import process may take up to 45 minutes. Continue?" ) )
		{
			$(window).scrollTop(0) ;
			check_geo_files(0, 0) ;
		}
	}

	function check_geo_files( theindex, thepercent )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$('#div_import').show() ;
		$('body').css({'overflow': 'hidden'}) ;
		$('#data_percent').html( thepercent+"% " ) ;

		if ( !theindex )
			$('#div_import_status').append("<div class=\"info_box\" style=\"margin-bottom: 10px;\">Beginning import...</div>") ;

		$.ajax({
		type: "POST",
		url: "./extras_geo.php",
		data: "action=import_geo_files&index="+theindex+"&ses=<?php echo $ses ?>&unique="+unique,
		success: function(data){
			eval( data ) ;

			if ( !parseInt( json_data.status ) && ( typeof( json_data.error ) == "undefined" ) )
			{
				var index_display = theindex + 1 ;
				$('#div_import_status').append("<li> Importing file: "+json_data.file+" ("+index_display+":"+json_data.total+")</li>") ;
				$('#div_import_status').prop( "scrollTop", $('#div_import_status').prop( "scrollHeight" ) ) ;

				if ( typeof( st_geo_import ) != "undefined" ) { clearTimeout( st_geo_import ) ; st_geo_import = undeefined ; }
				st_geo_import = setTimeout( function(){check_geo_files( theindex+1, json_data.percent ) ; }, st_geo_import_cycle*1000 ) ;
			}
			else if ( parseInt( json_data.status ) == 1 )
			{
				$('#data_percent').html( "100% complete " ) ;
				$('#img_loading').hide() ;
				$('#btn_close').show() ;
				do_alert( 1, "Import Success" ) ;
			}
			else
			{
				$('#div_import').hide() ;
				$('body').css({'overflow': 'visible'}) ;
				do_alert(0, json_data.error) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error importing process "+theindex+" index.  Please reload the page and try again." ) ;
		} });
	}

	function submit_key()
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var apikey = $('#apikey').val() ; apikey = apikey.replace( / /g, "" ) ;
		$('#apikey').val( apikey ) ;

		if ( !apikey )
			do_alert( 0, "Blank API Key is invalid." ) ;
		else if ( apikey.length != 39 )
			do_alert( 0, "API Key format is invalid." ) ;
		else if ( "<?php echo $geokey ?>" == apikey )
		{
			do_cancel() ;
			do_alert( 1, "Success" ) ;
		}
		else
		{
			$.ajax({
			type: "POST",
			url: "./extras_geo.php",
			data: "action=update_api&apikey="+apikey+"&ses=<?php echo $ses ?>&unique="+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
					location.href = "./extras_geo.php?ses=<?php echo $ses ?>&jump=geomap&success=1" ;
				else
				{
					do_alert( 0, jaon_data.error ) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error processing API Key.  Please reload the page and try again." ) ;
			} });
		}
	}

	function clear_key()
	{
		if ( confirm( "Clear API Key and deactivate Google Maps?" ) )
		{
			location.href = "./extras_geo.php?action=clear_api&ses=<?php echo $ses ?>&jump=<?php echo $jump ?>" ;
		}
	}

	function do_cancel()
	{
		$('#div_maps_steps').hide() ;
		$('#div_maps_update').show() ;
		$(window).scrollTop(0) ;
	}

	function reset_addon( theflag )
	{
		if ( theflag )
		{
			$('#div_reset_btn').hide() ;
			$('#div_reset').show() ;
		}
		else
		{
			$('#div_reset').hide() ;
			$('#div_reset_btn').show() ;
		}
	}

	function reset_addon_doit()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( confirm( "Reset and Clear the GeoIP addon?" ) )
		{
			$.ajax({
			type: "POST",
			url: "./extras_geo.php",
			data: "action=check_dir&ses=<?php echo $ses ?>&unique="+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
					location.href = "./extras_geo.php?ses=<?php echo $ses ?>&action=clear" ;
				else
				{
					do_alert( 0, "Error: The geo_data/ directory has not been deleted." ) ;
					$('#div_error').show() ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error processing reset request.  Please reload the page and try again." ) ;
			} });
		}
	}

	function show_div( thediv )
	{
		var divs = Array( "geoip", "geomap" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#div_'+divs[c]).hide() ;
			$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#div_'+thediv).show() ;
		$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function lookup_ip( theip )
	{
		var unique = unixtime() ;

		$('#geoip_output').html('<img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt="">') ;

		if ( !theip )
		{
			$('#geoip_output').html("") ;
			do_alert( 0, "Blank IP is invalid." ) ;
		}
		else
		{
			if ( global_ip != theip )
			{
				global_ip = theip ;
				$('#btn_lookup').attr("disabled", true) ;
				setTimeout( function(){ $('#btn_lookup').attr("disabled", false) ; }, 2000 ) ;

				$('#iframe_map').attr('src', "../ops/maps.php?ses=<?php echo $ses ?>&ip="+theip+"&vis_token=Quick+IP+Lookup&viewip=1&"+unique) ;
			}
			else { $('#iframe_map').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ; }
		}
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='extras.php?ses=<?php echo $ses ?>&jump=apis'" id="menu_apis">Dev APIs</div>
			<div class="op_submenu" onClick="location.href='marketing.php?ses=<?php echo $ses ?>'" id="menu_marketing">Marketing</div>
			<div class="op_submenu_focus" onClick="show_div('geoip')" id="menu_geoip">GeoIP</div>
			<div class="op_submenu" onClick="show_div('geomap')" id="menu_geomap">Google Maps</div>
			<div class="op_submenu" onClick="location.href='extras.php?ses=<?php echo $ses ?>&jump=external'" id="menu_external">External URLs</div>
			<?php if ( is_file( "../addons/smtp/smtp.php" ) ): ?><div class="op_submenu" onClick="location.href='../addons/smtp/smtp.php?ses=<?php echo $ses ?>'" id="menu_smtp">SMTP</div><?php endif ; ?>
			<?php if ( $addon_emo ): ?><div class="op_submenu" onClick="location.href='<?php echo $CONF["BASE_URL"] ?>/addons/emoticons/emo.php?ses=<?php echo $ses ?>'" id="menu_emoticons" id="menu_emoticons">Emoticons</div><?php endif ; ?>
			<div style="clear: both"></div>
		</div>

		<form method="POST" action="extras_geo.php?submit" enctype="multipart/form-data">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="ses" value="<?php echo $ses ?>">

		<div style="text-align: justify; margin-top: 25px;">
			<div id="div_geoip">
				<div>GeoIP is the identification of the real-world geographic location of an IP address. Enabling this feature will display a country flag, the region and city of an IP address throughout various areas of the system.  To enable this feature, complete the following step:</div>

				<?php if ( !isset( $CONF["geo"] ) || !$CONF["geo"] ): ?>
				<div style="margin-top: 15px;" class="edit_title">Activate the GeoIP addon:</div>
				<div style="margin-top: 15px;">
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td width="33%" valign="top" style="padding-right: 5px;">
							<div style="height: 210px;" class="info_info">
								<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 1:</span> Download</div>
								<div style="margin-top: 5px;">
									Login at the OSI Codes Inc. client area and download the compressed GeoIP Addon file.
									<div style="margin-top: 15px;">(~26 Megs GZ compressed)</div>

									<div style="margin-top: 15px;"><a href="http://www.phplivesupport.com/r.php?r=login" target="_blank">Login and Download the GeoIP Addon</a></div>
								</div>
							</div>
						</td>
						<td width="33%" valign="top" style="padding-right: 5px;">
							<div style="height: 210px;" class="info_info">
								<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 2:</span> Extract</div>
								<div style="margin-top: 5px;">
									The Addon file has been compress twice for minimal file size.  First decompression will produce the file <code>"geo_data.tar"</code>.  Second decompression of the decompressed file <code>"geo_data.tar"</code> will produce the actual GeoIP Addon data folder <code>"geo_data/"</code> containing all the data files.  Extract the entire <code>"geo_data/"</code> folder to your computer.

									<div style="margin-top: 15px;">File decompression software such as <a href="http://www.winzip.com" target="_blank">WinZip</a> or <a href="http://www.win-rar.com/" target="_blank">WinRar</a> is needed to decompress the GeoIP Addon file.</div>
								</div>
							</div>
						</td>
						<td width="33%" valign="top" style="">
							<div style="height: 210px;" class="info_info">
								<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 3:</span> FTP and Import</div>
								<div style="margin-top: 5px;">
									FTP the entire <code>"geo_data/"</code> folder to your server and place it inside the <code>addons/</code> directory of your PHP Live! system.
									
									<div style="margin-top: 15px;"><code>phplive/addons/geo_data/</code></div>

									<div style="margin-top: 15px;">Once the folder is uploaded, click the button to import the data.</div>
								</div>

								<div style="margin-top: 15px;"><button type="button" onClick="confirm_import()" class="btn">Folder is uploaded.  I'm ready to import.</button></div>
							</div>
						</td>
					</tr>
					</table>
				</div>

				<?php else: ?>
				<div style="margin-top: 15px;">
					<div class="edit_title"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""> GeoIP Is Enabled</div>

					<div id="div_geoip_lookup" style="margin-top: 15px;">
						<div class="op_submenu_focus" style="background: #F3F3F3;">Quick IP Lookup</div>
						<div style="clear: both;"></div>
						<div class="info_info">
							IP Address (your current IP: <span class="txt_orange"><?php echo $your_ip ?></span>)<br>
							<span style="font-size: 10px;">* support for IPv6 will be available in the near future</span>

							<div style="margin-top: 10px;">
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
								<tr>
									<td valign="top" nowrap><input type="text" name="ip_addy" id="ip_addy" size="20" maxlength="45" onKeyPress="return numbersonly(event)"> &nbsp; <input type="button" onClick="lookup_ip($('#ip_addy').val())" value="Lookup" class="btn" id="btn_lookup"></td>
									<td style="padding-left: 25px; width: 100%;" valign="top"><iframe id="iframe_map" name="iframe_map" style="width: 100%; height: 250px; border: 0px;" src="about:blank" scrolling="auto" border=0 frameborder=0 class="round"></iframe></td>
								</tr>
								</table>
							</div>
						</div>
						<div style="margin-top: 15px;"><img src="../pics/icons/reset.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="$('#div_geoip_reset').show(); $('#div_geoip_lookup').hide();">Reset GeoIP Addon</a></div>
					</div>
					<div id="div_geoip_reset" style="display: none; margin-top: 15px;">
						<div class="info_info">
							This action will clear the GeoIP data from the database and switch off the GeoIP feature (and Google Maps, if enabled).
							<ul style="margin-top: 10px;">
								<div style="padding: 5px;">Possible situations to reset the GeoIP addon:</div>
								<li> to update the GeoIP data to a newer version.
								<li> to save on disk space by deleting the GeoIP data from the database.
								<li> to import again due to errors.
							</ul>

							<div style="margin-top: 15px;" class="info_error"><b>Before Proceeding:</b><br>Manually delete the <code>"geo_data/"</code> directory (old data) from the <code>"phplive/addons/"</code> folder.</div>

							<div style="margin-top: 25px;"><input type="button" value="Reset GeoIP Addon" onClick="reset_addon_doit()" class="btn"> &nbsp; <a href="JavaScript:void(0)" onClick="$('#div_geoip_reset').hide();$('#div_geoip_lookup').show();">cancel</a></div>
						</div>
					</div>

					<div style="margin-top: 25px; text-align: right;">GeoIP Data v.<?php echo $VERSION_GEO ?>  <img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_geo&v=<?php echo base64_encode( $VERSION_GEO ) ?>&v_=<?php echo base64_encode( $VERSION ) ?>" target="_blank">Check for updates.</a></div>
				</div>

				<?php endif ; ?>
			</div>

			<div id="div_geomap" style="display: none; margin-top: 25px;">
				<div>Expand the GeoIP feature with Google Maps.  Google Maps will display the approximate location of an IP address on Google Maps.  Due to the <a href="https://developers.google.com/maps/terms" target="_blank">Google Maps API Terms of Service</a>, you'll want to signup for a Google API Key so that the integration is linked to your account.  API Key also enables the reporting features of the Google Maps requests.</div>

				<?php if ( !$geoip ): ?>
					<div style="margin-top: 25px;">
						<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Enable the <a href="JavaScript:void(0)" onClick="show_div('geoip')" style="color: #FFFFFF;">GeoIP Addon</a> before activating Google Maps.</span>
					</div>
				<?php else: ?>
					<div style="margin-top: 25px;">
						<?php if ( $geomap ): ?>
						<div class="edit_title" style="padding-bottom: 15px;"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt="">  Google Maps Is Enabled <span style="font-size: 12px; font-weight: normal;"> &bull; <a href="JavaScript:void(0)" onClick="show_div('geoip')">try it</a></span></div>
						<?php endif ; ?>
						<div id="div_maps_steps" style="display: none;">
							<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 1:</span> Enable the Google Maps JavaScript API</div>
							<div style="margin-top: 5px;">
								<ul>
									<li> Login to the <a href="https://console.developers.google.com/project" target="_blank">Google Code Console</a> and select a project (or create a new project if none exists).</li>
									<li> After selecting a project:</li>
										<ul>
											<li> For new projects: click the <span style="color: #3D7DE7; font-weight: bold;">BIG BLUE</span> box "Use Google APIs"</li>
											<li> For existing projects: click the link within the box <b>Explore other services</b>-&gt;"Enable APIs and get credentials like keys".</li>
										</ul>
									<li> On the list of APIs page, there should be "Google Maps APIs" section with a list of available APIs.  Enable the "<b>Google Maps JavaScript API</b>".</li>
								</ul>
							</div>

							<div style="margin-top: 15px;">
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
								<tr>
									<td width="33%" valign="top" style="padding: 5px;">
										<div style="height: 220px;" class="info_info">
											<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 2:</span> Credentials Menu</div>
											<div style="margin-top: 5px;">
												Click the left "Credentials" menu to access the credentials area.

												<div style="margin-top: 15px;"><div style="font-size: 10px; padding: 3px; background: #AAAAAB; color: #F4F4F4; width: 251px;" class="round_top">screenshot</div><img src="../pics/setup/api_menu_services.gif?<?php echo $VERSION ?>" width="255" height="125" style="border: 1px solid #DFDFDF" class="round" alt=""></div>
											</div>
										</div>
									</td>
									<td width="33%" valign="top" style="padding: 5px;">
										<div style="height: 220px;" class="info_info">
											<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 3:</span> Add Credential</div>
											<div style="margin-top: 5px;">
												Cick the "Add Credentials" button and select the "API key" selection from the dropdown menu.

												<div style="margin-top: 15px;"><div style="font-size: 10px; padding: 3px; background: #AAAAAB; color: #F4F4F4; width: 251px;" class="round_top">screenshot</div><img src="../pics/setup/api_menu_api_on.gif?<?php echo $VERSION ?>" width="255" height="125" style="border: 1px solid #DFDFDF" class="round" border="0" alt=""></div>
											</div>
										</div>
									</td>
									<td width="33%" valign="top" style="padding: 5px;">
										<div style="height: 220px;" class="info_info">
											<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 4:</span> Browser Key</div>
											<div style="margin-top: 5px;">
												Then, select the "Browser key" button to generate an API key.

												<div style="margin-top: 15px;"><div style="font-size: 10px; padding: 3px; background: #AAAAAB; color: #F4F4F4; width: 251px;" class="round_top">screenshot</div><img src="../pics/setup/api_menu_access.gif?<?php echo $VERSION ?>" width="255" height="125" style="border: 1px solid #DFDFDF" class="round" border="0" alt=""></div>
											</div>
										</div>
									</td>
								</tr>
								</table>
							</div>

							<div style="margin-top: 15px; padding: 5px;">
								<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Final Step:</span> Provide the Key</div>
								<div style="margin-top: 5px;">Copy the generated "Key" and provide the key below to activate Google Maps:</div>

								<div style="margin-top: 15px;"><input type="text" id="apikey" name="apikey" class="input" size="50" maxlength="55" value="<?php echo ( $geokey ) ? $geokey : "" ; ?>"></div>

								<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="14" height="14" border="0" alt=""> <b>NOTE:</b> it may take 15-45 minutes for Google to process the new API Key.  During processing, there may be an API error when trying to lookup an IP address.</div>
								<div style="margin-top: 15px;">
									<input type="button" value="Update Google API Key" onClick="submit_key()" class="btn"> &nbsp; 
									<span style="display: none;" id="text_cancel"> <input type="button" value="Clear Key" onClick="clear_key()" class="btn"> &nbsp; <a href="JavaScript:void(0)" onClick="do_cancel()">cancel</a></span>
								</div>
							</div>
						</div>
						<div id="div_maps_update" style="display: none;">
							<div class="info_info">Google API Key: <input type="text" id="apikey" name="apikey" class="input" size="50" maxlength="55" value="<?php echo ( $geokey ) ? $geokey : "" ; ?>" disabled="disabled"></div>
							<div style="margin-top: 15px;"><img src="../pics/icons/key.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="$('#div_maps_steps').show(); $('#div_maps_update').hide(); $('#text_cancel').show();">Update Google API Key</a></div>
						</div>
						
					</div>
				<?php endif ; ?>

			</div>

		</div>
		</form>

<div id="div_import" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">
	<div class="info_info" style="position: relative; width: 400px; margin: 0 auto; top: 130px;">
		<div style="background: url( ../pics/bg_trans_white.png ) repeat; padding: 25px;">
			<div style="font-size: 14px; font-weight: bold;">Importing GeoIP data: <span id="data_percent"></span><img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt="" id="img_loading"></div>

			<div id="div_import_status" class="round info_default" style="margin-top: 15px; height: 220px; overflow: auto;"></div>
			<div style="margin-top: 15px;"><button type="button" id="btn_close" onClick="location.href='extras_geo.php?ses=<?php echo $ses ?>'" style="display: none;">Continue</button></div>
		</div>
	</div>
</div>

<?php include_once( "./inc_footer.php" ) ?>
