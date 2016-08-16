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
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;

	$error = "" ; $now = time() ;
	$theme = "default" ;
	$deptinfo = $opinfo = Array() ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
	$m = Util_Format_Sanatize( Util_Format_GetVar( "m" ), "n" ) ;
	$d = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "n" ) ;
	$y = Util_Format_Sanatize( Util_Format_GetVar( "y" ), "n" ) ;
	if ( !$m ) { $m = date( "m", $now ) ; }
	if ( !$d ) { $d = date( "j", $now ) ; }
	if ( !$y ) { $y = date( "Y", $now ) ; }

	$today = mktime( 0, 0, 1, $m, $d, $y ) ;
	$stat_start = mktime( 0, 0, 1, $m, 1, $y ) ;
	$stat_end = mktime( 0, 0, 1, $m+1, 0, $y ) ;
	$stat_end_day = date( "j", $stat_end ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$operators = Ops_get_AllOps( $dbh ) ;
	if ( $deptid ) { $deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ; }
	if ( $opid ) { $opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ; }
	
	$rating_none = Util_Functions_Stars( "..", 0 ) ;

	if ( $action == "reset" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove.php" ) ;

		Chat_remove_ResetReports( $dbh ) ;
		database_mysql_close( $dbh ) ;
		HEADER( "location: reports_chat.php?action=success&ses=$ses&" ) ; exit ;
	}

	$requests_timespan = Chat_get_ext_RequestsRangeHash( $dbh, $stat_start, $stat_end, $operators ) ;
	$month_stats = Array() ;
	$month_total_requests = $month_total_taken = $month_total_declined = $month_total_message = $month_total_initiated = $month_total_initiated_ = 0 ;
	$month_max = 0 ; $js_stat_depts = $js_stat_ops = "" ;
	foreach ( $requests_timespan as $sdate => $deptop )
	{
		// todo: filter for invalid dates (should be fixed with timezone reset)
		if ( isset( $deptop["depts"] ) )
		{
			foreach ( $deptop["depts"] as $key => $value )
			{
				if ( !isset( $month_stats[$sdate] ) )
				{
					$month_stats[$sdate] = Array() ;
					$month_stats[$sdate]["requests"] = $month_stats[$sdate]["taken"] = $month_stats[$sdate]["declined"] = $month_stats[$sdate]["message"] = $month_stats[$sdate]["initiated"] = $month_stats[$sdate]["initiated_"] = 0 ;
				}

				$month_stats[$sdate]["requests"] += $value["requests"] ;
				$month_stats[$sdate]["taken"] += $value["taken"] ;
				$month_stats[$sdate]["declined"] += $value["declined"] ;
				$month_stats[$sdate]["message"] += $value["message"] ;
				$month_stats[$sdate]["initiated"] += $value["initiated"] ;
				$month_stats[$sdate]["initiated_"] += $value["initiated_"] ;

				if ( $sdate )
				{
					$month_total_requests += $value["requests"] ;
					$month_total_taken += $value["taken"] ;
					$month_total_declined += $value["declined"] ;
					$month_total_initiated += $value["initiated"] ;
					$month_total_initiated_ += $value["initiated_"] ;
					$month_total_message += $value["message"] ;
				}

				$rating = ( $value["rateit"] ) ? round( $value["ratings"]/$value["rateit"] ) : 0 ;

				$js_stat_depts .= "stat_depts[$sdate][$key]['requests'] = $value[requests] ; stat_depts[$sdate][$key]['taken'] = $value[taken] ; stat_depts[$sdate][$key]['declined'] = $value[declined] ; stat_depts[$sdate][$key]['message'] = $value[message] ; stat_depts[$sdate][$key]['initiated'] = $value[initiated] ; stat_depts[$sdate][$key]['initiated_'] = $value[initiated_] ; stat_depts[$sdate][$key]['rating'] = $rating ; " ;
			}
		}
		if ( isset( $deptop["ops"] ) )
		{
			foreach ( $deptop["ops"] as $key => $value )
			{
				$rating = ( $value["rateit"] ) ? round( $value["ratings"]/$value["rateit"] ) : 0 ;

				$js_stat_ops .= "stat_ops[$sdate][$key]['requests'] = $value[requests] ; stat_ops[$sdate][$key]['taken'] = $value[taken] ; stat_ops[$sdate][$key]['declined'] = $value[declined] ; stat_ops[$sdate][$key]['message'] = $value[message] ; stat_ops[$sdate][$key]['initiated'] = $value[initiated] ; stat_ops[$sdate][$key]['initiated_'] = $value[initiated_] ; stat_ops[$sdate][$key]['rating'] = $rating ; " ;
			}
		}

		if ( isset( $month_stats[$sdate]["requests"] ) && ( $month_stats[$sdate]["requests"] > $month_max ) && $sdate )
			$month_max = $month_stats[$sdate]["requests"] ;
	}
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
	"use strict"
	var stat_depts = new Object ;
	var stat_ops = new Object ;
	var stat_start = <?php echo $stat_start ?> ;
	var global_stat_time = 0 ;
	var global_div = "ops" ;
	var global_timeline_unix = 0 ;
	var global_deptid = 0 ;
	var global_opid = 0 ;
	var global_total = 0 ;
	var global_overall_c ;
	var global_timeline_c ;
	var global_accepted = 0 ;

	var stars = new Object ;
	stars[5] = "<?php echo Util_Functions_Stars( "..", 5 ) ; ?>" ;
	stars[4] = "<?php echo Util_Functions_Stars( "..", 4 ) ; ?>" ;
	stars[3] = "<?php echo Util_Functions_Stars( "..", 3 ) ; ?>" ;
	stars[2] = "<?php echo Util_Functions_Stars( "..", 2 ) ; ?>" ;
	stars[1] = "<?php echo Util_Functions_Stars( "..", 1 ) ; ?>" ;

	<?php
		for ( $c = 1; $c <= $stat_end_day; ++$c )
		{
			$stat_day = mktime( 0, 0, 1, $m, $c, $y ) ;
			print "stat_depts[$stat_day] = new Object; stat_ops[$stat_day] = new Object; " ;

			for ( $c2 = 0; $c2 < count( $departments ); ++$c2 )
			{
				$department = $departments[$c2] ;
				print "stat_depts[$stat_day][$department[deptID]] = new Object; " ;
			}
			for ( $c3 = 0; $c3 < count( $operators ); ++$c3 )
			{
				$operator = $operators[$c3] ;
				print "stat_ops[$stat_day][$operator[opID]] = new Object; " ;
			}
		}
	?>

	<?php echo $js_stat_depts ?>
	<?php echo $js_stat_ops ?>

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "rchats" ) ;

		<?php if ( $action && !$error ): ?>do_alert(1, "Success") ;<?php endif ; ?>

		reset_date() ;
	});

	function close_iframe()
	{
		$('#iframe_wrapper').hide() ;
	}

	function reset_date()
	{
		select_date( 0, "<?php echo date( "M j, Y", $stat_start ) ?> - <?php echo date( "M j, Y", $stat_end ) ?>" ) ;
	}

	function select_date( theunix, thedayexpand, thec )
	{
		var stat_total_requests = 0, stat_total_taken = 0, stat_total_declined = 0, stat_total_message = 0, stat_total_initiated = 0, stat_total_initiated_ = 0 ;

		global_timeline_unix = theunix ;
		global_timeline_c = undeefined ;
		$('#stat_day_expand').html( thedayexpand ) ;

		$( '#div_timeline' ).find('*').each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf("bar_v_overall_") != -1 )
				$(this).css({'border': '1px solid #4FD25B'}) ;
		} );

		if ( theunix )
		{
			if ( global_overall_c == thec )
			{
				global_overall_c = undeefined ;
				reset_date() ;
				if ( global_div == "timeline" )
				{
					global_stat_time = 0 ; global_div = undeefined ;
					show_div('timeline') ;
				}
				else if ( global_div == "urls" )
				{
					global_stat_time = 0 ; global_div = undeefined ;
					show_div('urls') ;
				}
				else
				{
					if ( global_div != "info" ) { $('#reports_'+global_div).hide().fadeIn("fast") ; }
				}
			}
			else
			{
				global_overall_c = thec ;
				if ( global_div != "info" ) { $('#reports_'+global_div).hide() ; }
				for ( var deptid in stat_depts[theunix] )
				{
					if ( typeof( stat_depts[theunix][deptid]["requests"] ) != "undefined" )
					{
						populate_stats( "dept", deptid, stat_depts[theunix][deptid]["requests"], stat_depts[theunix][deptid]["taken"], stat_depts[theunix][deptid]["declined"], stat_depts[theunix][deptid]["initiated"], stat_depts[theunix][deptid]["initiated_"],  stat_depts[theunix][deptid]["message"], stars[stat_depts[theunix][deptid]["rating"]] ) ;

						stat_total_requests += stat_depts[theunix][deptid]["requests"] ;
						stat_total_taken += stat_depts[theunix][deptid]["taken"] ;
						stat_total_declined += stat_depts[theunix][deptid]["declined"] ;
						stat_total_initiated += stat_depts[theunix][deptid]["initiated"] ;
						stat_total_initiated_ += stat_depts[theunix][deptid]["initiated_"] ;
						stat_total_message += stat_depts[theunix][deptid]["message"] ;
					}
					else
						populate_stats( "dept", deptid, 0, 0, 0, 0, 0, 0, "<?php echo $rating_none ?>" ) ;
				}

				for ( var opid in stat_ops[theunix] )
				{
					if ( typeof( stat_ops[theunix][opid]["requests"] ) != "undefined" )
						populate_stats( "op", opid, stat_ops[theunix][opid]["requests"], stat_ops[theunix][opid]["taken"], stat_ops[theunix][opid]["declined"], stat_ops[theunix][opid]["initiated"], stat_ops[theunix][opid]["initiated_"], stat_ops[theunix][opid]["message"], stars[stat_ops[theunix][opid]["rating"]] ) ;
					else
						populate_stats( "op", opid, 0, 0, 0, 0, 0, 0, "<?php echo $rating_none ?>" ) ;
				}

				// populate both because the menu tabs is toggle, not loading of data
				populate_urls( theunix ) ;
				populate_timeline( theunix, global_opid ) ;

				if ( global_div != "info" ) { $('#reports_'+global_div).fadeIn("fast") ; }
				if ( typeof( thec ) != "undefined" ) { $('#bar_v_overall_'+thec).css({'border': '1px solid #235D28'}) ; }
			}
		}
		else
		{
			var depts = new Object ;
			var ops = new Object ;
			for ( var sdate in stat_depts )
			{
				for ( var deptid in stat_depts[sdate] )
				{
					if ( typeof( depts[deptid] ) == "undefined" )
					{
						depts[deptid] = new Object ;
						depts[deptid]["requests"] = depts[deptid]["taken"] = depts[deptid]["declined"] = depts[deptid]["initiated"] = depts[deptid]["initiated_"] = depts[deptid]["message"] = depts[deptid]["rating"] = depts[deptid]["rating_c"] = 0 ;
					}

					if ( typeof( stat_depts[sdate][deptid]["requests"] ) != "undefined" )
					{
						depts[deptid]["requests"] += stat_depts[sdate][deptid]["requests"] ;
						depts[deptid]["taken"] += stat_depts[sdate][deptid]["taken"] ;
						depts[deptid]["declined"] += stat_depts[sdate][deptid]["declined"] ;
						depts[deptid]["initiated"] += stat_depts[sdate][deptid]["initiated"] ;
						depts[deptid]["initiated_"] += stat_depts[sdate][deptid]["initiated_"] ;
						depts[deptid]["message"] += stat_depts[sdate][deptid]["message"] ;
						depts[deptid]["rating"] += stat_depts[sdate][deptid]["rating"] ;
						depts[deptid]["rating_c"] += ( stat_depts[sdate][deptid]["rating"] ) ? 1 : 0 ;
					}
				}

				for ( var opid in stat_ops[sdate] )
				{
					if ( typeof( ops[opid] ) == "undefined" )
					{
						ops[opid] = new Object ;
						ops[opid]["requests"] = ops[opid]["taken"] = ops[opid]["declined"] = ops[opid]["initiated"] = ops[opid]["initiated_"] = ops[opid]["message"] = ops[opid]["rating"] = ops[opid]["rating_c"] = 0 ;
					}

					if ( typeof( stat_ops[sdate][opid]["requests"] ) != "undefined" )
					{
						ops[opid]["requests"] += stat_ops[sdate][opid]["requests"] ;
						ops[opid]["taken"] += stat_ops[sdate][opid]["taken"] ;
						ops[opid]["declined"] += stat_ops[sdate][opid]["declined"] ;
						ops[opid]["initiated"] += stat_ops[sdate][opid]["initiated"] ;
						ops[opid]["initiated_"] += stat_ops[sdate][opid]["initiated_"] ;
						ops[opid]["message"] += stat_ops[sdate][opid]["message"] ;
						ops[opid]["rating"] += stat_ops[sdate][opid]["rating"] ;
						ops[opid]["rating_c"] += ( stat_ops[sdate][opid]["rating"] ) ? 1 : 0 ;
					}
				}
			}

			for ( var deptid in depts )
			{
				var stars_ = Math.round( depts[deptid]["rating"]/depts[deptid]["rating_c"] ) ;
				populate_stats( "dept", deptid, depts[deptid]["requests"], depts[deptid]["taken"], depts[deptid]["declined"], depts[deptid]["initiated"], depts[deptid]["initiated_"], depts[deptid]["message"], stars[stars_] ) ;

				stat_total_requests += depts[deptid]["requests"] ;
				stat_total_taken += depts[deptid]["taken"] ;
				stat_total_declined += depts[deptid]["declined"] ;
				stat_total_initiated += depts[deptid]["initiated"] ;
				stat_total_initiated_ += depts[deptid]["initiated_"] ;
				stat_total_message += depts[deptid]["message"] ;
			}

			for ( var opid in ops )
			{
				var stars_ = Math.round( ops[opid]["rating"]/ops[opid]["rating_c"] ) ;
				populate_stats( "op", opid, ops[opid]["requests"], ops[opid]["taken"], ops[opid]["declined"], ops[opid]["initiated"], ops[opid]["initiated_"], ops[opid]["message"], stars[stars_] ) ;
			}
		}

		$('#stat_total_requests').html( stat_total_requests ) ;
		$('#stat_total_taken').html( stat_total_taken ) ;
		$('#stat_total_declined').html( stat_total_declined ) ;
		$('#stat_total_message').html( stat_total_message ) ;
		$('#stat_total_initiated').html( stat_total_initiated ) ;
		$('#stat_total_initiated_').html( stat_total_initiated_ ) ;
	}

	function populate_stats( thestat, theid, therequests, thetaken, thedeclined, theinitiated, theinitiated_, themessage, therating )
	{
		$('#stat_req_'+thestat+'_requests_'+theid).html( therequests ) ;
		$('#stat_req_'+thestat+'_taken_'+theid).html( thetaken ) ;
		$('#stat_req_'+thestat+'_declined_'+theid).html( thedeclined ) ;
		$('#stat_req_'+thestat+'_message_'+theid).html( themessage ) ;
		$('#stat_req_'+thestat+'_initiated_'+theid).html( theinitiated ) ;
		$('#stat_req_'+thestat+'_initiated__'+theid).html( theinitiated_ ) ;
		$('#stat_req_'+thestat+'_rating_'+theid).html( therating ) ;
	}

	function populate_urls( theunix )
	{
		var json_data = new Object ;
		var day_string = $('#stat_day_expand').html() ;

		$('#reports_urls').empty().html( "<img src=\"../pics/loading_fb.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\">" ) ;
		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions_reports.php",
			data: "ses=<?php echo $ses ?>&action=fetch_request_urls&sdate="+theunix+"&stat_start=<?php echo $stat_start ?>&stat_end=<?php echo $stat_end ?>&"+unixtime(),
			success: function(data){
				eval( data ) ;

				var string_urls = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\"><tr><td width=\"16\"><div class=\"td_dept_header\">Total</div></td><td width=\"100%\"><div class=\"td_dept_header\">URL the visitor was viewing when they requested live chat on <span class=\"info_box\">"+day_string+"</span></div></td></tr>" ;
				var url ;
				for ( var c = 0; c < json_data.urls.length; ++c )
				{
					url = json_data.urls[c] ;
					string_urls += "<tr><td class=\"td_dept_td\" width=\"16\">"+url["total"]+"</td><td class=\"td_dept_td\" width=\"100%\"><a href=\""+url["url"]+"\" target=_blank>"+url["url"]+"</a></td></tr>" ;
				}
				if ( !c )
					string_urls += "<tr><td class=\"td_dept_td\" colspan=2>Blank results.</td></tr>" ;

				string_urls += "</table>" ;
				$('#reports_urls').empty().html( string_urls ) ;
			}
		});
	}

	function populate_timeline( theunix, theopid )
	{
		var json_data = new Object ;

		$('#reports_timeline_body').empty().html( "<img src=\"../pics/loading_fb.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\">" ) ;
		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions_reports.php",
			data: "ses=<?php echo $ses ?>&action=fetch_request_timeline&deptid="+global_deptid+"&opid="+theopid+"&sdate="+theunix+"&stat_start=<?php echo $stat_start ?>&stat_end=<?php echo $stat_end ?>&"+unixtime(),
			success: function(data){
				eval( data ) ;

				var string_hours = "<table cellspacing=0 cellpadding=0 border=0 style=\"height: 100px; width: 100%;\"><tr>" ;
				var hour, h1, meter, tooltip_display, string_cursor, string_js ;
				var hour_max = json_data.hour_max ;
				var hour_total_overall = json_data.total_overall ;
				var hour_total_accepted = json_data.total_accepted ;

				global_stat_time = theunix ;
				update_timeline_string( theunix, hour_total_overall, 0, hour_total_accepted ) ;
				global_total = hour_total_overall ;
				global_accepted = hour_total_accepted ;

				for ( var c = 0; c < json_data.timeline.length; ++c )
				{
					hour = json_data.timeline[c] ;
					tooltip_display = hour["hour_"]+":00"+hour["ampm"]+" - "+hour["hour_"]+":59"+hour["ampm"]+" ("+hour["total"]+" chat requests)" ;

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
						string_js = "onClick=\"update_timeline_string('"+hour["hour_display"]+"', "+hour["total"]+", "+c+", "+hour["accepted"]+")\"" ;
					}

					string_hours += "<td valign=\"bottom\" width=\"2%\" style=\"height: 100px;\"><div id=\"bar_v_requests_"+c+"\" title=\""+tooltip_display+"\" alt=\""+tooltip_display+"\" style=\"height: "+h1+"; background: url( ../pics/meters/"+meter+" ) repeat; border: 1px solid #4FD25B; border-top-left-radius: 5px 5px; -moz-border-radius-topleft: 5px 5px; border-top-right-radius: 5px 5px; -moz-border-radius-topright: 5px 5px;"+string_cursor+"\" "+string_js+"></div></td><td><img src=\"../pics/space.gif\" width=\"5\" height=1></td>" ;
				}
				string_hours += "</tr><tr>" ;

				for ( var c = 0; c < json_data.timeline.length; ++c )
				{
					hour = json_data.timeline[c] ;
					tooltip_display = hour["total"]+" requests ("+hour["hour_"]+":00"+hour["ampm"]+" - "+hour["hour_"]+":59"+hour["ampm"]+")" ;
					var ampm = ( hour["ampm"] == "am" ) ? "" : "pm" ;

					string_hours += "<td align=\"center\"><div id=\"requests_bg_day\" class=\"page_report\" style=\"margin: 0px; font-size: 10px; font-weight: bold;\" title=\""+tooltip_display+"\" id=\""+tooltip_display+"\" onClick=\"update_timeline_string('"+hour["hour_display"]+"', "+hour["total"]+", "+c+", "+hour["accepted"]+")\">"+hour["hour_"]+ampm+"</div></td><td><img src=\"../pics/space.gif\" width=\"5\" height=1></td>" ;
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
			$('#stat_timeline_expand').html( "<span class=\"info_box\" style=\"font-weight: bold;\">12:00am - 11:59pm</span> &nbsp; "+thetotal+" total chat requests <span class=\"info_good\">"+theaccepted+"</span> accepted "+percent_display ) ;
		else
		{
			if ( global_timeline_c == thec )
			{
				global_timeline_c = undeefined ;
				update_timeline_string( 0, global_total, 0, global_accepted ) ;
			}
			else
			{
				global_timeline_c = thec ;
				var day_string_output = theunixtime.replace( "%span%", "<span style=\"font-weight: bold;\" class=\"info_box\">" ) ;
				day_string_output = day_string_output.replace( "%span_%", " &nbsp; <span style=\"font-size: 12px; font-weight: normal;\"><a href=\"JavaScript:void(0)\" onClick=\"do_reset()\">reset</a></span> &nbsp;</span>" ) ;
				$('#stat_timeline_expand').html( day_string_output+" &nbsp; "+thetotal+" total chat requests <span class=\"info_good\">"+theaccepted+"</span> accepted "+percent_display ) ;

				if ( typeof( thec ) != "undefined" ) { $('#bar_v_requests_'+thec).css({'border': '1px solid #235D28'}) ; }
			}
		}
	}

	function do_reset()
	{
		global_timeline_c = undeefined ;
		update_timeline_string( 0, global_total, 0, global_accepted ) ;
	}

	function show_div( thediv )
	{
		if ( global_div != thediv )
		{
			var divs = Array( "depts", "ops", "timeline", "urls", "info" ) ;
			for ( var c = 0; c < divs.length; ++c )
			{
				$('#reports_'+divs[c]).hide() ;
				$('#sub_menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu2') ;
			}

			global_div = thediv ;
			if ( thediv == "urls" )
				populate_urls( global_stat_time ) ;
			else if ( thediv == "timeline" )
			{
				$('#deptid').val(global_deptid) ;
				switch_dept(global_deptid) ;
			}

			$('#reports_'+thediv).fadeIn("fast") ;
			$('#sub_menu_'+thediv).removeClass('op_submenu2').addClass('op_submenu_focus') ;
		}
	}

	function do_reset_reports()
	{
		if ( confirm( "Reset Chat Reports data?  This action cannot be undone." ) )
			location.href = "reports_chat.php?action=reset&ses=<?php echo $ses ?>" ;
	}

	function switch_dept( thedeptid )
	{
		var opid = $('#opid').val() ;

		global_deptid = thedeptid ;
		global_stat_time = undeefined ;
		global_timeline_c = undeefined ;
		populate_timeline( global_stat_time, opid ) ;
	}

	function switch_ops( theopid )
	{
		global_opid = theopid ;
		global_stat_time = undeefined ;
		global_timeline_c = undeefined ;
		populate_timeline( global_stat_time, theopid ) ;
	}

	function department_timeline( thedeptid )
	{
		show_div( "timeline" ) ;
		$('#deptid').val( thedeptid ) ;
		switch_dept( thedeptid ) ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus">Chat Reports</div>
			<div class="op_submenu" onClick="location.href='reports_chat_active.php?ses=<?php echo $ses ?>'">Active Chats</div>
			<div class="op_submenu" onClick="location.href='reports_chat_missed.php?ses=<?php echo $ses ?>'">Missed Chats</div>
			<div class="op_submenu" onClick="location.href='reports_chat_msg.php?ses=<?php echo $ses ?>'">Offline Messages</div>
			<div style="clear: both"></div>
		</div>

		<table cellspacing=0 cellpadding=0 border=0 width="100%" style="margin-top: 25px;">
		<tr>
			<td><div class="td_dept_header">Timeline</div></td>
			<td width="80"><div class="td_dept_header">Requests</div></td>
			<td width="60"><div class="td_dept_header">Accepted</div></td>
			<td><div class="td_dept_header">Declined</div></td>
			<td nowrap><div class="td_dept_header">Op Initiated</div></td>
			<td nowrap><div class="td_dept_header">Op Initiate Accept</div></td>
			<td width="110"><div class="td_dept_header">Email Form</div></td>
		</tr>
		<tr>
			<td class="td_dept_td"><?php include_once( "./inc_select_cal.php" ) ; ?></td>
			<td class="td_dept_td"><?php echo $month_total_requests ?></td>
			<td class="td_dept_td"><?php echo $month_total_taken ?></td>
			<td class="td_dept_td"><?php echo $month_total_declined ?></td>
			<td class="td_dept_td"><?php echo $month_total_initiated ?></td>
			<td class="td_dept_td"><?php echo $month_total_initiated_ ?></td>
			<td class="td_dept_td"><?php echo $month_total_message ?></td>
		</tr>
		</table>

		<div style="margin-top: 25px; width: 100%;" id="div_timeline">
			<table cellspacing=0 cellpadding=0 border=0 style="height: 100px; width: 100%;">
			<tr>
				<?php
					$tooltips = Array() ; $stat_day_totals = Array() ;
					for ( $c = 1; $c <= $stat_end_day; ++$c )
					{
						$stat_day = mktime( 0, 0, 1, $m, $c, $y ) ;
						$stat_day_expand = date( "l, M j, Y", $stat_day ) ;

						$h1 = "0px" ; $meter = "meter_v_green.gif" ;
						$tooltip = "$stat_day_expand" ;
						$tooltips[$stat_day] = $tooltip ;
						$tooltip_display = "" ;
						if ( isset( $month_stats[$stat_day] ) )
						{
							$stat_day_totals[$c] = $month_stats[$stat_day]["requests"] ;
							$tooltip_display = "$stat_day_expand" ;
							if ( $month_max )
								$h1 = round( ( $month_stats[$stat_day]["requests"]/$month_max ) * 100 ) . "px" ;
						}
						else if ( ( $c == $stat_end_day ) && !$month_max )
						{
							$stat_day_totals[$c] = 0 ;
							$h1 = "0px" ;
							$meter = "meter_v_clear.gif" ;
						}
						else
							$stat_day_totals[$c] = 0 ;

						print "
							<td valign=\"bottom\" width=\"2%\" style=\"height: 100px;\"><div id=\"bar_v_overall_$c\" title=\"$tooltip_display\" alt=\"$tooltip_display\" style=\"height: $h1; background: url( ../pics/meters/$meter ) repeat-y; border: 1px solid #4FD25B; border-top-left-radius: 5px 5px; -moz-border-radius-topleft: 5px 5px; border-top-right-radius: 5px 5px; -moz-border-radius-topright: 5px 5px; cursor: pointer;\" OnClick=\"select_date( $stat_day, '$stat_day_expand', $c );\"></div></td>
							<td><img src=\"../pics/space.gif\" width=\"5\" height=1 border=0></td>
						" ;
					}
				?>
			</tr>
			<tr>
				<?php
					for ( $c = 1; $c <= $stat_end_day; ++$c )
					{
						$stat_day = mktime( 0, 0, 1, $m, $c, $y ) ;
						$stat_day_expand = date( "l, M j, Y", $stat_day ) ;
						print "
							<td align=\"center\"><div id=\"requests_bg_day\" class=\"page_report\" style=\"min-width: 12px; margin: 0px; font-size: 10px; font-weight: bold;\" title=\"$tooltips[$stat_day]\" id=\"$tooltips[$stat_day]\" OnClick=\"select_date( $stat_day, '$stat_day_expand', $c );\">$c</div></td>
							<td><img src=\"../pics/space.gif\" width=\"5\" height=1 border=0></td>
						" ;
					}
				?>
			</tr>
			<tr>
				<?php
					for ( $c = 1; $c <= $stat_end_day; ++$c )
					{
						$stat_day = mktime( 0, 0, 1, $m, $c, $y ) ;
						$stat_day_expand = date( "l, M j, Y", $stat_day ) ;
						$total = $stat_day_totals[$c] ;
						if ( $total > 999 ) { $total = "+" ; }
						print "
							<td align=\"center\"><div id=\"requests_bg_total_$c\" class=\"info_clear\" style=\"margin: 0px; font-size: 10px; font-weight: bold;\">$total</div></td>
							<td><img src=\"../pics/space.gif\" width=\"5\" height=1 border=0></td>
						" ;
					}
				?>
			</tr>
			</table>
		</div>

		<div id="overview_day_chart" style="margin-top: 25px;">
			<div style="text-align: right;">
				<span class="info_neutral">the current <a href="interface.php?ses=<?php echo $ses ?>&jump=time" target="_parent">system time</a> is <span id="span_system_time" style="color: #79C2EB; font-size: 16px; font-weight: bold;"><?php echo date( "M j, g:i a", time() ) ; ?></span></span>
			</div>
			<div id="overview_date_title" style="margin-top: 8px;"><div id="stat_day_expand" class="info_box"></div></div>

			<div class="op_submenu_wrapper">
				<div class="op_submenu_focus" onClick="show_div('ops')" id="sub_menu_ops">Operators</div>
				<div class="op_submenu2" onClick="show_div('depts')" id="sub_menu_depts">Departments</div>
				<div class="op_submenu2" onClick="show_div('timeline')" id="sub_menu_timeline">Chat Request Timeline</div>
				<div class="op_submenu2" onClick="show_div('urls')" id="sub_menu_urls">Chat Request URLs</div>
				<div class="op_submenu2" onClick="show_div('info')" id="sub_menu_info"><span class="info_box"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Information About the Chat Reports</span></div>
				<div style="clear: both"></div>
			</div>

			<div id="overview_data_container" style="margin-top: 15px;">
				<div id="reports_depts" style="display: none; min-height: 300px; max-height: 350px; overflow: auto; overflow-x: hidden;">
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td><div class="td_dept_header">Department Name</div></td>
						<td width="100"><div class="td_dept_header" style="text-align: center;">Ave Rating</div></td>
						<td width="80"><div class="td_dept_header" style="text-align: center;">Requests</div></td>
						<td width="60"><div class="td_dept_header" style="text-align: center;">Accepted</div></td>
						<td><div class="td_dept_header" style="text-align: center;">Declined</div></td>
						<td nowrap><div class="td_dept_header" style="text-align: center;">Op Initiated</div></td>
						<td nowrap><div class="td_dept_header" style="text-align: center;">Op Initiate Accept</div></td>
						<td width="110"><div class="td_dept_header" style="text-align: center;"><a href="reports_chat_msg.php?ses=<?php echo $ses ?>">Email Form</a></div></td>
					</tr>
					<?php
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;
							$td1 = "td_dept_td" ;

							print "
								<tr>
									<td class=\"$td1\" nowrap>$department[name]</td>
									<td class=\"$td1\"><div id=\"stat_req_dept_rating_$department[deptID]\">$rating_none</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_requests_$department[deptID]\" class=\"report_numbox\" title=\"Requests\" alt=\"Requests\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_taken_$department[deptID]\" class=\"report_numbox\" title=\"Accepted\" alt=\"Accepted\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_declined_$department[deptID]\" class=\"report_numbox\" title=\"Declined\" alt=\"Declined\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_initiated_$department[deptID]\" class=\"report_numbox\" title=\"Op Initiated\" alt=\"Op Initiated\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_initiated__$department[deptID]\" class=\"report_numbox\" title=\"Op Initiate Accept\" alt=\"Op Initiate Accept\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_message_$department[deptID]\" class=\"report_numbox\" title=\"Email Form\" alt=\"Email Form\">0</div></td>
								</tr>
							" ;
						}
					?>
					<tr>
						<td class="td_dept_td"><b>Total</b></td>
						<td class="td_dept_td"><div id="stat_total_rating" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_requests" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_taken" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_declined" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_initiated" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_initiated_" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_message" style="font-weight: bold; text-align: center;"></div></td>
					</tr>
					</table>
				</div>

				<div id="reports_ops" style="min-height: 300px; max-height: 350px; overflow: auto;">
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td><div class="td_dept_header">Operator Name</div></td>
						<td width="100"><div class="td_dept_header" style="text-align: center;">Ave Rating</div></td>
						<td width="80"><div class="td_dept_header" style="text-align: center;">Requests</div></td>
						<td width="60"><div class="td_dept_header" style="text-align: center;">Accepted</div></td>
						<td><div class="td_dept_header" style="text-align: center;">Declined</div></td>
						<td nowrap><div class="td_dept_header" style="text-align: center;">Op Initiated</div></td>
						<td nowrap><div class="td_dept_header" style="text-align: center;">Op Initiate Accept</div></td>
						<td width="110"><div class="td_dept_header" style="text-align: center;">&nbsp;</div></td>
					</tr>
					<?php
						for ( $c = 0; $c < count( $operators ); ++$c )
						{
							$operator = $operators[$c] ;
							$td1 = "td_dept_td" ;

							print "
								<tr>
									<td class=\"$td1\">$operator[name]</td>
									<td class=\"$td1\"><div id=\"stat_req_op_rating_$operator[opID]\">$rating_none</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_requests_$operator[opID]\" class=\"report_numbox\" title=\"Requests\" alt=\"Requests\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_taken_$operator[opID]\" class=\"report_numbox\" title=\"Accepted\" alt=\"Accepted\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_declined_$operator[opID]\" class=\"report_numbox\" title=\"Declined\" alt=\"Declined\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_initiated_$operator[opID]\" class=\"report_numbox\" title=\"Op Initiated\" alt=\"Op Initiated\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_initiated__$operator[opID]\" class=\"report_numbox\" title=\"Op Initiate Accept\" alt=\"Op Initiate Accept\">0</div></td>
								</tr>
							" ;
						}
					?>
					</table>
				</div>

				<div id="reports_timeline" style="display: none;">
					<form>
					<table cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td>
							<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept( this.value )">
							<option value="0">All Departments</option>
							<?php
								if ( count( $departments ) > 1 )
								{
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;

										if ( $department["name"] != "Archive" )
										{
											$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
											print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
										}
									}
								}
							?>
							</select>
						</td>
						<td><img src="../pics/space.gif" width="10" height=1></td>
						<td>
							<select name="opid" id="opid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_ops( this.value )">
							<option value="0">All Operators</option>
							<?php
								for ( $c = 0; $c < count( $operators ); ++$c )
								{
									$operator = $operators[$c] ;
									$selected = ( $opid == $operator["opID"] ) ? "selected" : "" ;
									print "<option value=\"$operator[opID]\" $selected>$operator[name]</option>" ;
								}
							?>
							</select>
						</td>
						<td style="padding-left: 15px;">
							<div id="overview_date_timeline" style="font-weight: normal;"><div id="stat_timeline_expand"></div></div>
						</td>
					</tr>
					</table>
					</form>
					<div style="min-height: 300px; max-height: 350px; overflow: auto;">
						<div id="reports_timeline_body" style="margin-top: 15px;"></div>
					</div>
				</div>

				<div id="reports_urls" style="display: none; min-height: 300px; max-height: 350px; overflow: auto;">
				</div>

				<div id="reports_info" style="display: none; min-height: 300px; max-height: 350px; overflow: auto;">
					<div>
						<ul style="">
							<div style="font-size: 14px; font-weight: bold;">For one chat request, the following report will be stored:</div>
							<li style="margin-top: 15px;"> For every operator that receives the chat request, their <b>"Requests"</b> value will increment by one.
							<li> For every operator that does not accept the chat request, their <b>"Declined"</b> value will increment by one.
							<li> One chat request to a department could count towards several department <b>"Declined"</b> depending on the number of operators assigned to the department.
							<li> If the chat request is routed to the "Leave a Message" form, the department <b>"Email Form"</b> value will increment by one.
							<li> If the department <a href="depts.php?ses=<?php echo $ses ?>&div=rloop">"Chat Routing Loop"</a> is set to more then once, it is still considered one chat request and, if declined, one decline for each operator.
							<li> <b>"Op Initiated"</b> (Operator initiated chat) are not counted towards the <b>"Requests"</b> total.
						</ul>
					</div>

					<div style="margin-top: 15px;" class="info_info">
						<div><img src="../pics/icons/reset.png" width="16" height="16" border="0" alt=""> <b>Chat Reports Reset</b></div>
						<div style="margin-top: 5px;">The chat reports can be reset, clearing the data and setting the values to zero across all departments and operators.  The <a href="JavaScript:void(0)" onClick="show_div('timeline')">Chat Request Timeline</a>, <a href="JavaScript:void(0)" onClick="show_div('urls')">Chat Request URLs</a> and the <a href="reports_chat_missed.php?ses=<?php echo $ses ?>">Missed Chats</a> data will also be cleared.  Chat reports reset does not affect the chat transcripts, offline messages, or any other portion of the system.</div>

						<div style="margin-top: 15px;"><button type="button" onClick="do_reset_reports()">Reset Chat Reports</button></div>
					</div>
				</div>

			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ?>
