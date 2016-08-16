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

	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/remove.php" ) ;

	$now = time() ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "n" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$menu = Util_Format_Sanatize( Util_Format_GetVar( "menu" ), "ln" ) ;
	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ;
	$auto = Util_Format_Sanatize( Util_Format_GetVar( "auto" ), "n" ) ;
	$menu = ( $menu ) ? $menu : "reports" ;
	$error = "" ;

	$opvars = Ops_get_OpVars( $dbh, $opinfo["opID"] ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Operator </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
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
	"use strict" ;
	var global_total = 0 ;
	var global_accepted = 0 ;
	var global_c ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu_op() ;
		toggle_menu_op( "<?php echo $menu ?>" ) ;

		populate_timeline( 0, "today" ) ;

		if ( typeof( parent.isop ) != "undefined" ) { parent.init_extra_loaded() ; }
	});

	function populate_timeline( theunix, thetimeline )
	{
		var json_data = new Object ;

		$('#reports_timeline_body').empty().html( "<img src=\"../pics/loading_fb.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\">" ) ;
		$.ajax({
			type: "POST",
			url: "../ajax/op_actions_reports.php",
			data: "ses=<?php echo $ses ?>&action=fetch_request_timeline&timeline="+thetimeline+"&"+unixtime(),
			success: function(data){
				eval( data ) ;

				var string_hours = "<table cellspacing=0 cellpadding=0 border=0 style=\"height: 100px; width: 100%;\"><tr>" ;
				var hour, h1, meter, tooltip_display, string_cursor, string_js ;
				var hour_max = json_data.hour_max ;
				var hour_total_overall = json_data.total_overall ;
				var hour_total_accepted = json_data.total_accepted ;

				update_timeline_string( theunix, hour_total_overall, 0, hour_total_accepted ) ;
				global_total = hour_total_overall ;
				global_accepted = hour_total_accepted ;

				for ( var c = 0; c < json_data.timeline.length; ++c )
				{
					hour = json_data.timeline[c] ;
					tooltip_display = hour["total"]+" chat requests ("+hour["hour_"]+":00"+hour["ampm"]+" - "+hour["hour_"]+":59"+hour["ampm"]+")" ;

					if ( !parseInt( hour["total"] ) )
					{
						h1 = "0px" ;
						meter = "meter_v_clear.gif" ;
						string_cursor = string_js = tooltip_display = "" ;
					}
					else
					{
						h1 = Math.round( ( hour["total"]/hour_max ) * 100 ) + "px" ;
						meter = "meter_v_green.gif" ;
						string_cursor = " cursor: pointer;" ;
						string_js = "onClick=\"global_clicktype=2;update_timeline_string('"+hour["hour_display"]+"', "+hour["total"]+", "+c+", "+hour["accepted"]+")\"" ;
					}

					string_hours += "<td valign=\"bottom\" width=\"2%\" style=\"height: 100px;\"><div id=\"bar_v_requests_"+c+"\" title=\""+tooltip_display+"\" alt=\""+tooltip_display+"\" style=\"height: "+h1+"; background: url( ../pics/meters/"+meter+" ) repeat; border: 1px solid #4FD25B; border-top-left-radius: 5px 5px; -moz-border-radius-topleft: 5px 5px; border-top-right-radius: 5px 5px; -moz-border-radius-topright: 5px 5px;"+string_cursor+"\" "+string_js+"></div></td><td><img src=\"../pics/space.gif\" width=\"5\" height=1></td>" ;
				}
				string_hours += "</tr><tr>" ;

				for ( var c = 0; c < json_data.timeline.length; ++c )
				{
					hour = json_data.timeline[c] ;
					tooltip_display = hour["total"]+" requests ("+hour["hour_"]+":00"+hour["ampm"]+" - "+hour["hour_"]+":59"+hour["ampm"]+")" ;
					var ampm = ( hour["ampm"] == "am" ) ? "" : "pm" ;

					string_hours += "<td align=\"center\"><div id=\"requests_bg_day\" class=\"page_report\" style=\"margin: 0px; font-size: 10px; font-weight: bold;\" title=\""+tooltip_display+"\" id=\""+tooltip_display+"\" onClick=\"global_clicktype=2;update_timeline_string('"+hour["hour_display"]+"', "+hour["total"]+", "+c+", "+hour["accepted"]+")\">"+hour["hour_"]+ampm+"</div></td><td><img src=\"../pics/space.gif\" width=\"5\" height=1></td>" ;
				}
				string_hours += "</tr><tr>" ;

				for ( var c = 0; c < json_data.timeline.length; ++c )
				{
					hour = json_data.timeline[c] ;

					string_hours += "<td align=\"center\"><div id=\"requests_bg_total\" class=\"info_clear\" style=\"margin: 0px; font-size: 10px; font-weight: bold;\">"+hour["total"]+"</div></td><td><img src=\"../pics/space.gif\" width=\"5\" height=1></td>" ;
				}

				string_hours += "</tr></table>" ;
				$('#reports_timeline_body').empty().html( string_hours ) ;
			}
		});
	}

	function update_timeline_string( theunixtime, thetotal, thec, theaccepted )
	{
		var day_string = $('#stat_day_expand').html() ;
		var percent = ( parseInt( thetotal ) ) ? Math.round( ( parseInt(theaccepted)/parseInt(thetotal) ) * 100 ) : 0 ;
		var percent_display = ( percent ) ? "("+percent+"%)" : "" ;

		$( '#reports_timeline_body' ).find('*').each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf("bar_v_requests_") != -1 )
				$(this).css({'border': '1px solid #4FD25B'}) ;
		} );

		if ( typeof( theunixtime ) != "string" )
		{
			$('#stat_day_expand').html( "<span class=\"info_box\" style=\"font-weight: bold;\">12:00am - 11:59pm</span> &nbsp; "+thetotal+" total chat requests <span class=\"info_good\">"+theaccepted+"</span> accepted "+percent_display ) ;
		}
		else
		{
			if ( global_c == thec )
			{
				global_c = undeefined ;
				update_timeline_string( 0, global_total, 0, global_accepted ) ;
			}
			else
			{
				global_c = thec ;
				var day_string_output = theunixtime.replace( "%span%", "<span style=\"font-weight: bold;\" class=\"info_box\">" ) ;
				day_string_output = day_string_output.replace( "%span_%", " &nbsp; <span style=\"font-size: 12px; font-weight: normal;\"><a href=\"JavaScript:void(0)\" onClick=\"do_reset()\">reset</a></span> &nbsp;</span>" ) ;
				$('#stat_day_expand').html( day_string_output+" &nbsp; "+thetotal+" total chat requests <span class=\"info_good\">"+theaccepted+"</span> accepted "+percent_display ) ;

				if ( typeof( thec ) != "undefined" ) { $('#bar_v_requests_'+thec).css({'border': '1px solid #235D28'}) ; }
			}
		}
	}

	function toggle_timeline( thevalue )
	{
		populate_timeline( 0, thevalue ) ;
	}

	function do_reset()
	{
		global_c = undeefined ;
		update_timeline_string( 0, global_total, 0, global_accepted ) ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ); ?>

		<div id="op_title" class="edit_title" style="margin-bottom: 15px;"></div>

		<div id="op_reports" style="margin: 0 auto;">
			<div>
				<div class="info_box">
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td>
							Chat Requests Timeline &nbsp; <select name="timeline" onChange="toggle_timeline(this.value)">
								<option value="today">Today</option>
								<option value="7d">7 Days</option>
								<option value="14d">14 Days</option>
								<option value="1m">1 Month</option>
								<option value="2m">2 Months</option>
								<option value="3m">3 Months</option>
								<option value="6m">6 Months</option>
								<option value="1y">1 Year</option>
								<option value="2y">2 Year</option>
								<option value="3y">3 years</option>
							</select>
						</td>
						<td>
							<div style="text-align: right; text-shadow: none;">
								<span class="info_neutral">the current system time is <span id="span_system_time" style="color: #79C2EB; font-size: 16px; font-weight: bold;"><?php echo date( "M j, g:i a", $now ) ; ?></span></span>
							</div>
						</td>
					</tr>
					</table>
				</div>
			</div>
			<div id="overview_date_timeline" style="margin-top: 25px; font-weight: normal;">
				<div id="stat_day_expand"></div>
			</div>

			<div id="reports_timeline_body" style="margin-top: 15px; min-height: 300px; max-height: 350px; overflow: auto;"></div>
		</div>

<?php include_once( "./inc_footer.php" ); ?>
