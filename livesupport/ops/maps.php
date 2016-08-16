<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;

	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses ) )
	{
		if ( !$opinfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
		$opinfo["theme"] = "default" ;
	}
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Hash.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/Util.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/GeoIP/get.php" ) ;

	$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;
	$vis_token = Util_Format_Sanatize( Util_Format_GetVar( "vis_token" ), "ln" ) ;
	$viewip = Util_Format_Sanatize( Util_Format_GetVar( "viewip" ), "n" ) ;
	$skip = Util_Format_Sanatize( Util_Format_GetVar( "skip" ), "n" ) ;

	if ( $skip ) { $geoip = 0 ; $geo_country = "Location Unknown" ; $geo_region = "-" ; $geo_city = "-" ; $geo_lat = 28.613459424004414 ; $geo_long = -40.4296875 ; }
	else
	{
		$countries = Util_Hash_Countries() ;
		LIST( $ip_num, $network ) = UtilIPs_IP2Long( $ip ) ;
		$geoinfo = GeoIP_get_GeoInfo( $dbh, $ip_num, $network ) ;

		$geo_country_code = ( isset( $geoinfo["country"] ) && $geoinfo["country"] ) ? $geoinfo["country"] : "unknown" ;
		$geo_country = ( $geo_country_code != "unknown" ) ? utf8_decode( $countries[$geo_country_code] ) : "Unknown" ;
		$geo_region = ( isset( $geoinfo["region"] ) && $geoinfo["region"] ) ? utf8_decode( $geoinfo["region"] ) : "unknown" ;
		$geo_city = ( isset( $geoinfo["city"] ) && $geoinfo["city"] ) ? utf8_decode( $geoinfo["city"] ) : "unknown" ;
		$geo_lat = ( isset( $geoinfo["latitude"] ) && $geoinfo["latitude"] ) ? $geoinfo["latitude"] : 28.613459424004414 ;
		$geo_long = ( isset( $geoinfo["longitude"] ) && $geoinfo["longitude"] ) ? $geoinfo["longitude"] : -40.4296875 ;

		if ( $geo_country_code == "unknown" ) { $geomap = 0 ; $geokey = "" ; }
	}
	$zoom = 3 ;
	if ( $geo_city != "unknown" ) { $zoom = 4 ; }
?>
<?php include_once( "$CONF[DOCUMENT_ROOT]/inc_doctype.php" ) ?>
<head>
<title> v.<?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/<?php echo $opinfo["theme"] ?>/style.css?<?php echo $VERSION ?>" id="stylesheet">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<?php if ( $geoip && $geomap ): ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $geokey ?>" type="text/javascript"></script>
<?php endif; ?>
<script type="text/javascript">
<!--
	$(document).ready(function()
	{
		<?php if ( $geoip && $geomap ): ?>
		draw_map() ;
		<?php else: ?>
		$('#map_default').show() ;
		<?php endif ; ?>

	});

	function draw_map()
	{
		var info_content = "<div class=\"info_box\"><b>Country:</b> <?php echo $geo_country ?><br><b>Region:</b> <?php echo $geo_region ?><br><b>City:</b> <?php echo $geo_city ?></div>" ;
		var infowindow = new google.maps.InfoWindow({
			content: info_content
		});

		var latlng = new google.maps.LatLng( <?php echo $geo_lat ?>, <?php echo $geo_long ?> ) ;
		var myOptions = {
			zoom: <?php echo $zoom ?>,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		} ;
		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions) ;
		var marker = new google.maps.Marker({
			animation: google.maps.Animation.DROP,
			position: latlng,
			title: "Country: <?php echo $geo_country ?>, Region: <?php echo $geo_region ?>, City: <?php echo $geo_city ?>"
		}) ;
		marker.setMap(map) ;
		infowindow.open(map, marker) ;
		//marker.addListener('click', function() {
		//	infowindow.open(map, marker) ;
		//});
 
		adjust_height() ;
	}

	function adjust_height()
	{
		var canvas_height = $(window).height() ;
		<?php if ( $geoip && $geomap ): ?>$('#map_canvas').css({'height': canvas_height}).show() ;<?php endif ; ?>
	}
//-->
</script>
</head>
<body id="chat_info_body" style="margin: 0px; border: 0px; padding: 0px;">
	<div id="map_canvas" style="display: none; height: 100%;"></div>
	<div id="map_default" style="display: none; height: 100%;">
		<?php if ( $skip ): ?>
		<div class="info_box" style="padding: 10px;">Location not available for this session.</div>

		<?php else: ?>

			<?php if ( $geoip && !$geomap ): ?>
			<div style="">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td width="80" class="chat_info_td_h"><b>Visitor ID</b></td>
					<td class="chat_info_td"><?php echo $vis_token ?></td>
				</tr>
				<?php if ( $viewip ): ?>
				<tr>
					<td width="80" class="chat_info_td_h"><b>IP</b></td>
					<td class="chat_info_td"><?php echo $ip ?></td>
				</tr>
				<?php endif ; ?>
				<tr>
					<td width="80" class="chat_info_td_h"><b>Country</b></td>
					<td class="chat_info_td"><img src="../pics/maps/<?php echo strtolower( $geo_country_code ) ?>.gif" width="18" height="12" border="0" alt="<?php echo $geo_country ?>" title="<?php echo $geo_country ?>"> <?php echo $geo_country ?></div></td>
				</tr>
				<tr>
					<td width="80" class="chat_info_td_h"><b>Region</b></td>
					<td class="chat_info_td"><?php echo $geo_region ?></td>
				</tr>
				<tr>
					<td width="80" class="chat_info_td_h"><b>City</b></td>
					<td class="chat_info_td"><?php echo $geo_city ?></td>
				</tr>
				</table>
			</div>
			<?php else: ?>
			<div class="chat_info_td" style="padding: 10px;">To enable GeoIP Location, contact the setup admin.  The setup admin will need to login to the setup area and access the top menu "Extras".  On the "Extras" page, click the "GeoIP" sub menu.</td>
			<?php endif ; ?>

		<?php endif ;?>
	</div>
</body>
</html>