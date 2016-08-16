<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: ../setup/install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses ) ){ ErrorHandler( 602, "Invalid operator session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$error = "" ;

	if ( !isset( $CONF['foot_log'] ) ) { $CONF['foot_log'] = "on" ; }
	if ( !isset( $CONF['icon_check'] ) ) { $CONF['icon_check'] = "on" ; }
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Operator </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/<?php echo $opinfo["theme"] ?>/style.css?<?php echo $VERSION ?>">
<link rel="Stylesheet" href="../mapp/css/mapp.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../mapp/js/mapp.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var loaded = 1 ;
	var secondtime = 0 ;

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;

		var document_height = $(document).height() - 65 ;
		$('#canned_container').css({'height': document_height}) ;
		$('#canned_wrapper').show() ;

		populate_traffic() ;

		<?php if ( $action == "reload" ): ?>do_alert( 1, "Refresh Success" ) ;<?php endif ; ?>
	});

	function init_external_url()
	{
		$("a").click(function(){
			var temp_url = $(this).attr( "href" ) ;
			if ( !temp_url.match( /javascript/i ) )
			{
				parent.external_url = temp_url ;
				return false ;
			}
		});
	}

	function populate_traffic()
	{
		var json_data = new Object ;
		var unique = unixtime() ;
		var footprints = new Object ;

		if ( "<?php echo $CONF['icon_check'] ?>" == "off" )
		{
			$('#canned_container').empty().html( "<div class='chat_info_td_traffic'>Traffic monitor has been switched off by the setup admin.</div>" ) ;
			return false ;
		}
		else if ( parent.automatic_offline_active )
		{
			$('#canned_container').empty().html( "<div class='chat_info_td_traffic'>Traffic Monitor is not available during offline hours.</div>" ) ;
			return false ;
		}

		$.ajax({
		type: "GET",
		url: "../ajax/chat_actions_op_itr_traffic.php",
		data: "action=traffic&unique="+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				var vis_exist = 0 ;
				var traffic_string = "" ;

				var json_length = json_data.traffics.length ;
				for ( var c = 0; c < json_length; ++c )
				{
					var vis_token = json_data.traffics[c]["vis_token"] ;
					var market_name = ( ( typeof( parent.markets[json_data.traffics[c]["marketid"]] ) != "undefined" ) && ( typeof( parent.markets[json_data.traffics[c]["marketid"]]["name"] ) != "undefined" ) ) ? parent.markets[json_data.traffics[c]["marketid"]]["name"] : "" ;
					var market_color = ( ( typeof( parent.markets[json_data.traffics[c]["marketid"]] ) != "undefined" ) && ( typeof( parent.markets[json_data.traffics[c]["marketid"]]["color"] ) != "undefined" ) ) ? "style=\"background: #"+parent.markets[json_data.traffics[c]["marketid"]]["color"]+"\"" : "" ;
					var market_td = ( parent.total_markets ) ? "<td class=\"chat_info_td_traffic\" "+market_color+">"+market_name+"</td>" : "" ;
					
					var viewmap = ( <?php echo $geoip ?> ) ? "<img src=\"../pics/maps/"+json_data.traffics[c]["country"].toLowerCase()+".gif\" width=\"18\" height=\"12\" border=0 id=\"map_"+json_data.traffics[c]["vis_token"]+"\"> &nbsp; " : "" ;
					viewmap += "Region: "+json_data.traffics[c]["region"] ;
					var viewip = ( parent.viewip ) ? json_data.traffics[c]["ip"] : "" ;

					var url_raw = json_data.traffics[c]["onpage"] ;
					if ( url_raw == "livechatimagelink" )
						url_raw = "JavaScript:void(0)" ;

					/*
					json_data.traffics[c]["t_footprints"]
					json_data.traffics[c]["t_initiates"]
					json_data.traffics[c]["onpage"]
					json_data.traffics[c]["title"]
					json_data.traffics[c]["refer_raw"]
					json_data.traffics[c]["refer_snap"]
					*/

					traffic_string += "<div class='info_neutral' id='table_"+vis_token+"' style='padding: 10px; margin-bottom: 1px;'> \
							<table cellspacing=0 cellpadding=4 border=0> \
							<tr> \
								<td nowrap>On Page</td> \
								<td><a href='"+url_raw+"'><b>"+json_data.traffics[c]["title"]+"</b></a></td> \
							</tr> \
							<tr> \
								<td>Duration</td> \
								<td><b>"+json_data.traffics[c]["duration"]+"</b></td> \
							</tr> \
							<tr> \
								<td>Platform</td> \
								<td><b>"+viewip+"</b> &nbsp; <img src='../themes/<?php echo $opinfo["theme"] ?>/os/"+json_data.traffics[c]["os"]+".png' border=0 alt='"+json_data.traffics[c]["os"]+"' title='"+json_data.traffics[c]["os"]+"' alt='"+json_data.traffics[c]["os"]+"' width='14' height='14'> &nbsp; <img src='../themes/<?php echo $opinfo["theme"] ?>/browsers/"+json_data.traffics[c]["browser"]+".png' border=0 alt='"+json_data.traffics[c]["browser"]+"' title='"+json_data.traffics[c]["browser"]+"' alt='"+json_data.traffics[c]["browser"]+"' width='14' height='14'></td> \
							</tr> \
							<tr> \
								<td>Location</td> \
								<td><b>"+viewmap+"</b></td> \
							</tr> \
							</table> \
						</div>" ;

					footprints[vis_token] = 1 ;
				}
				if ( json_data.traffics.length != 0 ) { traffic_string += "<div style=\"padding: 50px;\">&nbsp;</div>" ; }
				else { traffic_string = "No website traffic at this time." ; }

				$('#canned_container').empty().html( traffic_string ) ;
				init_external_url() ;
				
				if ( secondtime )
					do_alert( 1, "Refresh Success" ) ;

				++secondtime ;
			}
			else { do_alert( 0, "Error loading traffic monitor.  Please reload the console and try again." ) ; }
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error loading traffic monitor.  Please reload the console and try again." ) ;
		} });
	}
//-->
</script>
</head>
<body style="">

<div id="canned_wrapper" style="display: none; height: 100%; overflow: auto;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">
			<div id="canned_container" style="overflow: auto;"></div>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

</body>
</html>
<?php
	if ( isset( $dbh ) && $dbh['con'] )
		database_mysql_close( $dbh ) ;
?>
