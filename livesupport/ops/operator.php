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
	if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses ) ){ ErrorHandler( 602, "Invalid operator session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Hash.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/External/get.php" ) ;

	/***** [ BEGIN ] BASIC CLEANUP *****/
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove_itr.php" ) ;

	Footprints_remove_itr_Expired_U( $dbh ) ;
	Chat_remove_itr_ExpiredOp2OpRequests( $dbh ) ;
	Chat_remove_itr_OldRequests( $dbh ) ;
	/***** [ END ] BASIC CLEANUP *****/

	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ;
	$auto = Util_Format_Sanatize( Util_Format_GetVar( "auto" ), "n" ) ;
	$mapp = Util_Format_Sanatize( Util_Format_GetVar( "mapp" ), "n" ) ;
	$mapp_reload = Util_Format_Sanatize( Util_Format_GetVar( "mapp_reload" ), "n" ) ;
	$nalert = Util_Format_Sanatize( Util_Format_GetVar( "nalert" ), "n" ) ;
	$pop = Util_Format_Sanatize( Util_Format_GetVar( "pop" ), "n" ) ;
	$reload = Util_Format_Sanatize( Util_Format_GetVar( "reload" ), "n" ) ;
	$open_status = Util_Format_Sanatize( Util_Format_GetVar( "open_status" ), "n" ) ; if ( $open_status != 1 ) { $open_status = 0 ; }
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;
	$theme = $opinfo["theme"] ; $now = time() ;

	if ( !isset( $CONF['foot_log'] ) ) { $CONF['foot_log'] = "on" ; }
	if ( !isset( $CONF['icon_check'] ) ) { $CONF['icon_check'] = "on" ; }

	$opid = $opinfo["opID"] ;
	$operators = Ops_get_AllOps( $dbh ) ;
	$departments = Depts_get_AllDepts( $dbh ) ;
	$departments_vars = Depts_get_AllDeptsVars( $dbh ) ;
	$op_depts = Depts_get_OpDepts( $dbh, $opid ) ;
	$externals = External_get_OpExternals( $dbh, $opid ) ;
	$vars = Util_Format_Get_Vars( $dbh ) ;
	$opvars = Ops_get_OpVars( $dbh, $opid ) ;

	$op_sounds = isset( $VALS["op_sounds"] ) ? unserialize( $VALS["op_sounds"] ) : Array() ;
	if ( isset( $op_sounds[$opid] ) ) { $op_sounds_vals = $op_sounds[$opid] ; $opinfo["sound1"] = $op_sounds_vals[0] ; $opinfo["sound2"] = $op_sounds_vals[1] ; } else { $opinfo["sound1"] = "default" ; $opinfo["sound2"] = "default" ; }

	$console_sound = ( !isset( $opvars["sound"] ) || ( isset( $opvars["sound"] ) && $opvars["sound"] ) ) ? 1 : 0 ;
	$console_blink = ( !isset( $opvars["blink"] ) || ( isset( $opvars["blink"] ) && !$opvars["blink"] ) ) ? 0 : 1 ;
	$console_blink_r = ( !isset( $opvars["blink_r"] ) || ( isset( $opvars["blink_r"] ) && !$opvars["blink_r"] ) ) ? 0 : 1 ;
	$charset = ( isset( $vars["char_set"] ) && $vars["char_set"] ) ? unserialize( $vars["char_set"] ) : Array(0=>"UTF-8") ;

	$depts_hash = "depts_hash[1111111111] = 'All Departments' ;" ;
	$depts_rtime_hash = "" ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$depts_hash .= "depts_hash[".$department["deptID"]."] = '$department[name]' ;" ;
		$depts_rtime_hash .= "depts_rtime_hash[".$department["deptID"]."] = '$department[rtime]' ;" ;
	}

	$op_depts_hash = $deptids = $depts_idle = "" ;
	for ( $c = 0; $c < count( $op_depts ); ++$c )
	{
		$department = $op_depts[$c] ;
		$op_depts_hash .= "op_depts_hash[".$department["deptID"]."] = '$department[name]' ;" ;
		$deptids .= "&d[]=".$department["deptID"] ;
		if ( isset( $departments_vars[$department["deptID"]] ) )
			$depts_idle .= "depts_idle[$department[deptID]] = ".$departments_vars[$department["deptID"]]["idle_o"] .";" ;
	}

	$LANG = Array() ; $LANG["CHAT_NOTIFY_DISCONNECT"] = "The party has left or disconnected.  Chat session has ended." ;

	$countries = Util_Hash_Countries() ;
	$country_hash = "" ;
	foreach( $countries as $country => $name )
		$country_hash .= "countries['$country'] = '".preg_replace( "/'/", "&#39;", $name )."' ;" ;

	$dept_emo = ( isset( $VALS["EMOS"] ) ) ? unserialize( $VALS["EMOS"] ) : Array() ;
	$dept_emos = "" ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$deptid = $department["deptID"] ;
		if ( isset( $dept_emo[$deptid] ) )
			$dept_emos .= "dept_emos[$deptid] = $dept_emo[$deptid] ;" ;
	}

	$profile_pic_url = Util_Upload_GetLogo( "profile", $opid ) ;
	if ( is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ; }
	$autolinker_js_file = ( isset( $VARS_JS_AUTOLINK_FILE ) && ( ( $VARS_JS_AUTOLINK_FILE == "min" ) || ( $VARS_JS_AUTOLINK_FILE == "src" ) ) ) ? "autolinker_$VARS_JS_AUTOLINK_FILE.js" : "autolinker_min.js" ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Operator Console </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $charset[0] ?>">
<?php include_once( "../inc_meta_dev.php" ) ; ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

<link rel="Stylesheet" href="../themes/<?php echo $theme ?>/style.css?<?php echo $VERSION ?>" id="stylesheet">
<?php if ( $mapp ): ?>
<link rel="Stylesheet" href="../mapp/css/mapp.css?<?php echo $VERSION ?>" id="stylesheet">
<script type="text/javascript" src="../mapp/js/mapp.js?<?php echo $VERSION ?>d"></script>
<?php endif ; ?>
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/winapp.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/dn.js?<?php echo $VERSION ?>"></script>
<?php if ( !preg_match( "/(MSIE 6)|(MSIE 7)|(MSIE 8)/i", $agent ) && !$mapp ): ?><script type="text/javascript" src="../js/sleep.js?<?php echo $VERSION ?>"></script><?php endif ; ?>
<script type="text/javascript" src="../js/modernizr.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/<?php echo $autolinker_js_file ?>?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	"use strict" ;
	var base_url = ".." ; var base_url_full = "<?php echo $CONF["BASE_URL"] ?>" ;
	var proto = ( location.protocol.match( /https/i ) ) ? 1 : 0 ;
	var isop = <?php echo $opid ?> ; var isop_ = 0 ; var isop__ = 0 ;
	var viewip = <?php echo $opinfo["viewip"] ?> ;
	var nchats = <?php echo $opinfo["nchats"] ?> ;
	var cname = "<?php echo $opinfo["name"] ?>" ; var cemail = "<?php echo $opinfo["email"] ?>" ;
	var ces, ces_trans, info, extra, extra_top ;
	var ck_his = new Array(), ex_his = new Array(), bk_his = new Array(), markets = new Array() ;
	var st_resize, st_typing, st_flash_console, st_logout ;
	var si_offline, si_title, si_typing, si_rating, si_automatic_offline, si_textarea ;
	var si_his = new Object, maps_his = new Object, maps_his_ = new Object, iframe_his = new Object, cl_his = new Object ;
	var tim_offline ;
	var prev_status = -1 ; var current_status = 0 ;
	var rupdated ; // flag to tell if chats were removed in DB
	var traffic = <?php echo $opinfo["traffic"] ?> ;
	var dn_enabled_response = <?php echo ( isset( $opvars["dn_response"] ) ) ? $opvars["dn_response"] : 0 ; ?> ;
	var dn_enabled_request = <?php echo ( isset( $opvars["dn_request"] ) ) ? $opvars["dn_request"] : 0 ; ?> ;
	var dn_always = <?php echo ( isset( $opvars["dn_always"] ) ) ? $opvars["dn_always"] : 0 ; ?> ;
	var dn_counter = 0 ; // keeps track of all the dn notices for a session, continous counter
	var prev_traffic = 0 ;
	var mapp = <?php echo $mapp ?> ; var mapp_c = <?php echo ( isset( $opvars["mapp_c"] ) ) ? $opvars["mapp_c"] : 0 ; ?> ; ;
	var mapp_obj = new Object ; var external_url = "" ;
	var mobile = ( <?php echo $mobile ?> ) ? is_mobile() : 0 ;
	var total_new_requests = 0 ;
	var traffic_sound = 0 ;
	var chat_sound = ( mapp ) ? 1 : <?php echo $console_sound ?> ; <?php if ( $mapp ): ?>chat_sound_mapp = chat_sound ;<?php endif ; ?>
	var console_blink = ( mapp ) ? 0 : <?php echo $console_blink ?> ;
	var console_blink_r = ( mapp ) ? 0 : <?php echo $console_blink_r ?> ;
	var sound_new_request = "<?php echo ( $opinfo["sound1"] ) ? $opinfo["sound1"] : "default" ; ?>" ;
	var sound_new_text = "<?php echo ( $opinfo["sound2"] ) ? $opinfo["sound2"] : "default" ; ?>" ;
	var sound_volume = 1 ;
	var theme = "<?php echo $theme ?>" ;
	var title_orig = document.title ;
	var si_counter = 0 ;
	var logout_timer = 60 ;
	var focused = 1 ;
	var fetch_rating_flag = 0 ;
	var reconnect_counter = 0 ; // reconnection flag so it runs once
	var network_counter = 0 ;
	var widget = 0 ; var embed = 0 ;
	var wp = ( ( typeof( window.external ) != "undefined" ) && ( typeof( window.external.wp_total_visitors ) != "undefined" ) ) ? 1 : 0 ;
	var ses = "<?php echo $ses ?>" ;
	var op_depts = <?php echo count( $op_depts ) ; ?> ;
	var divs = Array( "info", "footprints", "transcripts", "transfer", "maps", "spam" ) ;
	var total_markets = 0 ;
	var cans = new Object, missed_chats = new Object ;
	var auto_canid = <?php echo ( isset( $opvars["canID"] ) ) ? $opvars["canID"] : 0 ; ?> ;
	var vclick = 0 ;
	var shortcut_enabled = <?php echo ( isset( $opvars["shorts"] ) && !$mapp ) ? $opvars["shorts"] : 0 ; ?> ;
	var profile_pic_enabled = <?php echo ( $profile_pic_url && !$mapp ) ? 1 : 0 ; ?> ;
	var nsleep = <?php echo ( isset( $opvars["nsleep"] ) && !$mapp ) ? $opvars["nsleep"] : 0 ; ?> ;
	var maxc = <?php echo ( $opinfo["maxc"] == -1 ) ? 10 : $opinfo["maxc"] ?> ; var maxco = <?php echo $opinfo["maxco"] ?> ;
	var cache_v = "<?php echo $VERSION ?>" ;

	var status_update_flag = 0 ; // to indiate status is updating for timing of fetch rating
	var addon_emo = 0 ;
	var dept_emos = new Object ;
	<?php echo $dept_emos ?>

	var cans_string ; // global so op_traffic.php can reference
	var initiate_canid = 0 ; // global op_traffic.php
	var initiate_deptid = 0 ; // global op_traffic.php
	var height_chat_body ; // global for toggle_input_text()
	var prev_network_string ;
	var extra_wrapper_height ; // global for reference
	var global_ces_array = new Object ; // global to track switchboard

	var loaded = 0 ;
	var newwin, newwin_print ;

	var vis_token ; // for op_traffic.php
	var automatic_offline_active = 0 ;
	var ao = 0 ; // flag: auto offline
	var rd = 0 ; // flag: remote disconnect
	var dup = 0 ; // flag: duplicate login
	var mi = 0 ; // flag: mapp idle offline

	var chats = new Object ;
	var depts_hash = new Object ;
	var depts_rtime_hash = new Object ;
	var depts_idle = new Object ;
	var op_depts_hash = new Object ;
	var traffic_data = new Object ;

	var stars = new Object ;
	stars[5] = "<?php echo Util_Functions_Stars( "..", 5 ) ; ?>" ;
	stars[4] = "<?php echo Util_Functions_Stars( "..", 4 ) ; ?>" ;
	stars[3] = "<?php echo Util_Functions_Stars( "..", 3 ) ; ?>" ;
	stars[2] = "<?php echo Util_Functions_Stars( "..", 2 ) ; ?>" ;
	stars[1] = "<?php echo Util_Functions_Stars( "..", 1 ) ; ?>" ;
	stars[0] = "<?php echo Util_Functions_Stars( "..", 0 ) ; ?>" ;

	var countries = new Object ;
	var console_divs = new Array() ;
	<?php echo $country_hash ?>

	var audio_supported = HTML5_audio_support() ;
	var mp3_support = ( typeof( audio_supported["mp3"] ) != "undefined" ) ? 1 : 0 ;
	var autolinker = new Autolinker( { newWindow: true, stripPrefix: false } ) ;

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;

		$("body").fadeIn("slow") ;
		loaded = 1 ;
		init_divs(0) ;
		init_disconnect() ;
		fetch_markets() ;
		toggle_info( "info", 0 ) ;
		populate_cans(0) ;
		check_network(.0, 1, 0) ;
		init_typing() ;
		textarea_listen() ;
		print_chat_sound_image( theme ) ;

		<?php echo $depts_hash ?>

		<?php echo $depts_rtime_hash ?>

		<?php echo $depts_idle ?>
		
		<?php echo $op_depts_hash ?>

		<?php if ( $opinfo["traffic"] ): ?>update_traffic_counter( pad( prev_traffic, 2 ) ) ;<?php endif ; ?>

		$('#offline_timer').html( logout_timer+":00" ) ;
		$('#reconnect_notice').center() ;

		<?php if ( $reload && !$nalert ): ?>do_alert( 1, "Settings have been updated." ) ;<?php endif ; ?>

		if ( !op_depts )
		{
			logout_timer = 5 ;
			toggle_status(1) ;
			setTimeout(function(){
				clearInterval( si_rating ) ; check_network( 615, undeefined, undeefined ) ; $('#chat_panel').hide() ;
				$('#chat_body').append( '<div id="no_dept" class="info_error" style=\"padding: 10px;\">You are OFFLINE.  Account is not assigned to a department.<div style="margin-top: 15px;"><img src="../themes/hearts/alert.png" width="16" height="16" border="0" alt=""> Contact the Setup Admin to assign this account to a department.  Once assigned, <a href="JavaScript:void(0)" onClick="reload_console(0)" style="color: #FFFFFF;">refresh</a> this page to go online.</div></div>' ) ;
			}, 2000) ;
		}
		else
		{
			if ( !<?php echo $open_status ?> ) { toggle_status(0) ; }
			else { toggle_status(1) ; }
			document.getElementById('iframe_chat_engine').contentWindow.location.href = "./p_engine.php?ses=<?php echo $ses ?>&charset=<?php echo $charset[0] ?>&"+unixtime()+"&" ;
		}

		update_ratings() ;
		si_rating = setInterval( function(){
			if ( !document.getElementById('iframe_chat_engine').contentWindow.stopped )
				update_ratings() ;
		}, <?php echo $VARS_JS_RATING_FETCH ?> * 1000 ) ;
		if ( mapp ) { init_mapp_console() ; }
		prep_init() ;
	});
	$(window).resize(function() {
		// some devices triggers resize on various events, even at full screen
		if ( !mobile )
		{
			init_divs(1) ;
		}
		init_scrolling() ;
		init_maps_iframes() ;
		if ( !$('#chat_info_container').is(':visible') ) { toggle_slider(1) ; }
	});

	if ( !wp && !mapp ) { window.onbeforeunload = function() { pre_logout() ; return "You are about to exit the operator console." ; } }

	$(window).focus(function() {
		input_focus() ;
	});
	$(window).blur(function() {
		focused = 0 ;
	});

	function prep_init()
	{
		if ( !mapp )
		{ 
			$('#canned_select_btn').dblclick(function() { add_text_prepare(1) ; });
		}
		else
		{
			dn_enabled_response = dn_enabled_request = dn_always = 0 ;
			init_external_url() ;
			$('#options_expand').hide() ;
		}
		if ( profile_pic_enabled ) { $('#div_profile_pic').html('<img src="<?php echo $profile_pic_url ?>" width="55" height="55" border=0 class="profile_pic_img">').show() ; }
		if ( nsleep && ( typeof( sleep ) != "undefined" ) && !mapp && ( typeof( audio_supported["audio"] ) != "undefined" ) ) { sleep.prevent() ; }
	}

	var global_maxc_flag = 0 ; // indication of automatic maxc offline triggered
	function init_maxc()
	{
		var total_active_chats = 0 ;
		for ( var thisces in chats )
		{
			if ( ( parseInt( chats[thisces]["status"] ) == 1 ) && !chats[thisces]["disconnected"] )
				++total_active_chats ;
		}

		if ( maxco && total_active_chats && ( total_active_chats >= maxc ) )
		{
			global_maxc_flag = 1 ;

			if ( !$('#chat_status_offline').is(':visible') )
				toggle_status(1) ;
		}
		else if ( maxco && ( total_active_chats < maxc ) && global_maxc_flag )
		{
			global_maxc_flag = 0 ;

			if ( ( prev_status == 1 ) && $('#chat_status_offline').is(':visible') )
				toggle_status(0) ;
		}
	}

	function init_external_url()
	{
		$("a").click(function(){
			var temp_url = $(this).attr( "href" ) ;
			if ( !temp_url.match( /javascript/i ) )
			{
				external_url = temp_url ;
				return false ;
			}
		});
	}

	function pre_logout()
	{
		toggle_status(1) ;
	}

	function toggle_input_text()
	{
		var height_input_text = $("textarea#input_text").height() ;
		if ( height_input_text == 75 )
		{
			height_chat_body = $('#chat_body').height() ;

			var height_new = height_chat_body - (250-75) ;
			$('#chat_body').css({'height': height_new}) ;
			$("#chat_input").css({'bottom': -50}) ; $("textarea#input_text").css({'height': 250}) ;
			init_scrolling() ;
		}
		else
		{
			$("#chat_input").css({'bottom': "auto"}) ; $("textarea#input_text").css({'height': 75}) ;
			$('#chat_body').css({'height': height_chat_body}) ;
		}
	}

	function init_info()
	{
		$( '*', 'body' ).each( function(){
			var div_name = $( this ).attr('id') ;
			var class_name = $( this ).attr('class') ;
			if ( ( div_name != "info_menu_"+info ) && ( class_name == "chat_info_menu" ) && total_chats() )
			{
				$(this).hover(
					function () {
						$(this).removeClass('chat_info_menu').addClass('chat_info_menu_hover') ;
					}, 
					function () {
						$(this).removeClass('chat_info_menu_hover').addClass('chat_info_menu') ;
					}
				);
			}
		} );
	}

	function init_maps_iframes()
	{
		var height = $('#chat_body').height() ;
		for ( var thisces in maps_his )
			$('#iframe_maps_'+thisces).css({'height': height}) ;
		
		for ( var thisdiv in ex_his )
		{
			$( '*', '#'+thisdiv ).each( function(){
				var div_name = $( this ).attr('id') ;
				init_iframe( div_name ) ;
			} );
		}
	}

	function menu_blink( thecolor, theces )
	{
		if ( typeof( si_his[theces] ) == "undefined" )
		{
			if ( typeof( bk_his[theces] ) == "undefined" )
				bk_his[theces] = 1 ;

			if ( mapp && !$('#div_mapp_chat_bubble_red').is(':visible') )
			{
				$('#div_mapp_chat_bubble_red').show() ;
			}
			si_his[theces] = setInterval(function(){ menu_blink_doit( thecolor, theces ) ; }, 1000) ;
		}
	}

	function menu_blink_doit( thecolor, theces )
	{
		var offcolor ;
		if ( thecolor == "red" )
			offcolor = "green" ;
		else
			offcolor = "red" ;

		if ( !( bk_his[theces] % 2 ) )
			$('#menu_'+theces).removeClass('chat_switchboard_cell_bl_'+thecolor).removeClass('chat_switchboard_cell_bl_'+offcolor).addClass('chat_switchboard_cell') ;
		else
			$('#menu_'+theces).removeClass('chat_switchboard_cell').addClass('chat_switchboard_cell_bl_'+thecolor) ;

		bk_his[theces] += 1 ;
	}

	function new_chat( thejson_data, theflag )
	{
		var thisces = thejson_data["ces"] ;
		var is_in_his = is_ces_in_his( thisces ) ;
		rupdated = theflag ;

		// if 615 flag, visitor has improperly closed chat and the status is stuck on previous decline... bypass
		if ( thejson_data["vup"] == 615 ) { return true ; }

		// fixes random UI quirk
		if ( !mobile ) { $(window).scrollTop(0) ; }

		if ( typeof( chats[thisces] ) == "undefined" )
		{
			if ( !is_in_his && ( typeof( cl_his[thisces] ) == "undefined" ) )
			{
				chats[thisces] = new Object ;
				chats[thisces]["requestid"] = thejson_data["rid"] ;
				chats[thisces]["deptid"] = thejson_data["did"] ;
				chats[thisces]["opid"] = thejson_data["opid"] ;
				chats[thisces]["op2op"] = ( thejson_data["status"] != 2 ) ? thejson_data["op2op"] : 0 ;
				chats[thisces]["t_ses"] = thejson_data["tv"] ;
				chats[thisces]["opid_orig"] = <?php echo $opid ?> ;
				chats[thisces]["mapp"] = <?php echo $opinfo["mapp"] ?> ;
				chats[thisces]["status"] = thejson_data["status"] ;
				chats[thisces]["initiated"] = thejson_data["initiated"] ;
				chats[thisces]["auto_pop"] = thejson_data["auto_pop"] ;
				chats[thisces]["disconnected"] = 0 ;
				chats[thisces]["closed"] = 0 ;
				chats[thisces]["tooslow"] = 0 ;
				chats[thisces]["vname"] = thejson_data["vname"] ;
				chats[thisces]["os"] = thejson_data["os"] ;
				chats[thisces]["browser"] = thejson_data["browser"] ;
				chats[thisces]["resolution"] = thejson_data["resolution"] ;
				chats[thisces]["vemail"] = ( thejson_data["vemail"] != "null" ) ? thejson_data["vemail"] : "" ;
				chats[thisces]["requests"] = thejson_data["requests"] ;
				chats[thisces]["ip"] = thejson_data["ip"] ;
				chats[thisces]["country"] = thejson_data["country"] ;
				chats[thisces]["vis_token"] = thejson_data["vis_token"] ;
				chats[thisces]["onpage"] = thejson_data["onpage"] ;
				chats[thisces]["title"] = thejson_data["title"] ;
				chats[thisces]["marketid"] = thejson_data["marketid"] ;
				chats[thisces]["refer_raw"] = thejson_data["refer_raw"] ;
				chats[thisces]["refer_snap"] = thejson_data["refer_snap"] ;
				chats[thisces]["custom"] = thejson_data["custom"] ;
				chats[thisces]["question"] = thejson_data["question"] ;
				chats[thisces]["footprints"] = 0 ;
				chats[thisces]["transcripts"] = 0 ;
				chats[thisces]["timer"] = thejson_data["created"] ;
				chats[thisces]["istyping"] = 0 ;
				chats[thisces]["input"] = "" ;
				chats[thisces]["idle"] = ( typeof( depts_idle[chats[thisces]["deptid"]] ) != "undefined" ) ? depts_idle[chats[thisces]["deptid"]]*60 : 0 ;
				chats[thisces]["idle_counter"] = 0 ;
				chats[thisces]["idle_counter_pause"] = chats[thisces]["idle_alert"] = 0 ;
				chats[thisces]["recent_res"] = unixtime() ;

				if ( chats[thisces]["initiated"] )
				{
					input_focus() ;
					chats[thisces]["timer"] = unixtime() ;
					chats[thisces]["trans"] = "<div class=\"ca\"><div class=\"ctitle\">Initiated Chat.  <span id=\"trans_title\">Connecting</span>...</div></div>" ;
				}
				else if ( chats[thisces]["status"] == 1 )
				{
					init_idle( thisces ) ;
					chats[thisces]["trans"] = "" ;
				}
				else if ( chats[thisces]["op2op"] == isop )
				{
					// set status as picked up so the operator can send messages of important note prior to
					// receiving operator accept/decline
					chats[thisces]["status"] = 1 ;
					chats[thisces]["timer"] = unixtime() ;
					// <op2op> flag to indicate remove when picked up
					chats[thisces]["trans"] = "<c615><op2op><div class=\"ca\">Requesting Operator to Operator Chat. Connecting...</div></op2op></c615>" ;
				}
				else if ( chats[thisces]["op2op"] && ( chats[thisces]["status"] != 2 ) )
				{
					chats[thisces]["trans"] = "<c615><div class=\"ca\">Operator to Operator Chat Request from <b>"+chats[thisces]["vname"]+"</b></div><div class=\"ca\"><button type=\"button\" class=\"input_button\" style=\"font-size: 10px;\" onClick=\"$(this).attr('disabled', 'true');chat_accept();\">accept</button> &nbsp; or &nbsp;  <span onClick=\"chat_decline()\" style=\"text-decoration: underline; cursor: pointer;\">decline</span></div></c615>" ;
				}
				else if ( chats[thisces]["status"] == 2 )
				{
					chats[thisces]["timer"] = unixtime() ;
					chats[thisces]["trans"] = "<c615><div class=\"ca\"><div class=\"info_box\"><i>"+thejson_data["question"]+"</i></div> <div style=\"margin-top: 10px;\"><div class=\"ctitle\">Transferred Chat</div><div style=\"margin-top: 10px;\"><button type=\"button\" class=\"input_button\" style=\"font-size: 10px;\" onClick=\"$(this).attr('disabled', 'true');chat_accept();\">accept</button> &nbsp; or &nbsp; <span onClick=\"chat_decline()\" style=\"text-decoration: underline; cursor: pointer;\">decline</span></div></div></c615>" ;
				}
				else
					chats[thisces]["trans"] = "<c615><div class=\"ca\"><div class=\"info_box\"><i>"+thejson_data["question"]+"</i></div> <div style=\"margin-top: 10px;\"><div class=\"ctitle\">"+depts_hash[chats[thisces]["deptid"]]+"</div> <div style=\"margin-top: 10px;\"><button type=\"button\" style=\"font-size: 10px;\" class=\"input_button\" onClick=\"$(this).attr('disabled', 'true');chat_accept();\">accept</button> &nbsp; or &nbsp; <span onClick=\"chat_decline()\" style=\"text-decoration: underline; cursor: pointer;\">decline</span></div></div></c615>" ;

				chats[thisces]["chatting"] = 0 ;
				cl_his[thisces] = true ;

				if ( !chats[thisces]["initiated"] && ( chats[thisces]["op2op"] != isop ) )
				{
					if ( chat_sound && ( !chats[thisces]["status"] || ( chats[thisces]["status"] == 2 ) ) )
						play_sound( 1, "new_request", "new_request_<?php echo $opinfo["sound1"] ?>" ) ;
					if ( console_blink && ( !chats[thisces]["status"] || ( chats[thisces]["status"] == 2 ) ) )
						flash_console(0) ;

					title_blink_init() ;
				}

				// if console was reloaded
				if ( chats[thisces]["status"] != 1 )
				{
					if ( wp && !chats[thisces]["initiated"] && ( chats[thisces]["op2op"] != isop ) )
						window.external.wp_incoming_chat( thisces, chats[thisces]["vname"], thejson_data["question"].replace( /<br>/g, ' ' ) ) ;
					else if ( !chats[thisces]["initiated"] && ( chats[thisces]["op2op"] != isop ) )
					{
						if ( dn_enabled_request )
							dn_show( 'new_chat', thisces, chats[thisces]["vname"], thejson_data["question"].replace( /<br>/g, ' ' ), 45000 ) ;
					}
				}
			}
		}
		else if ( ( chats[thisces]["status"] == 3 ) && ( !thejson_data["status"] || ( thejson_data["status"] == 2 ) ) )
		{
			// transferred chat is transferred BACK to the original operator
			chats[thisces]["trans"] = "<c615><div class=\"ca\"><div class=\"info_box\"><i>"+chats[thisces]["question"]+"</i></div> <div style=\"margin-top: 10px;\"><div class=\"ctitle\">Transferred Chat</div><div style=\"margin-top: 10px;\"><button type=\"button\" class=\"input_button\" style=\"font-size: 10px;\" class=\"input_button\" onClick=\"$(this).attr('disabled', 'true');chat_accept();\">accept</button> &nbsp; or &nbsp; <span onClick=\"chat_decline()\" style=\"text-decoration: underline; cursor: pointer;\">decline</span></div></div></c615>" ;
			if ( thisces == ces )
			{
				var transcript = chats[thisces]["trans"] ;
				$('#chat_body').empty().html( transcript.emos() ) ;
				init_scrolling() ;
			}
			else
				menu_blink( "red", thisces ) ;

			// set status to transfer so it doesn't repeat the above message
			chats[thisces]["status"] = 2 ;
			chats[thisces]["chatting"] = 0 ;
			chats[thisces]["disconnected"] = 0 ;

			if ( chat_sound )
				play_sound( 1, "new_request", "new_request_<?php echo $opinfo["sound1"] ?>" ) ;
			else
				flash_console(0) ;

			title_blink_init() ;
		}
		else
			chats[thisces]["status"] = thejson_data["status"] ;

		if ( typeof( chats[thisces] ) != "undefined" )
		{
			chats[thisces]["rupdated"] = rupdated ;
			chats[thisces]["t_ses"] = thejson_data["tv"] ;
			if ( thisces == ces ) { $('#req_t_ses').empty().html( "("+chats[ces]["t_ses"]+")" ) ; }
		}
	}

	function init_chat_list( theflag )
	{
		var thisclass, thisces, thisces_temp, thisimage ;
		var list_string = "" ; var refresh_list_string = 0 ;
		var ces_array = new Array() ;

		clean_chats( theflag ) ;

		var t_chats = total_chats() ;

		for ( var thisces in chats )
		{
			if ( typeof( global_ces_array[thisces] ) == "undefined" ) { global_ces_array[thisces] = 1 ; refresh_list_string = 1 ; }
			ces_array.push( thisces ) ;
		}
		for ( var thisces in global_ces_array )
		{
			if ( typeof( chats[thisces] ) == "undefined" ) { delete global_ces_array[thisces] ; refresh_list_string = 1 ; }
		}

		ces_array.sort() ;

		var obj_length = ces_array.length ;
		for ( var c = 0; c < obj_length; ++c )
		{
			var thisces = ces_array[c] ;

			if ( thisces == ces )
			{
				thisclass = "chat_switchboard_cell_focus" ;
				thisimage = "online_green.png" ;
			}
			else
			{
				thisclass = "chat_switchboard_cell" ;
				thisimage = "online_grey.png" ;
			}

			list_string += "<div id=\"menu_"+thisces+"\" class=\""+thisclass+"\" style=\"float: left;\" onClick=\"activate_chat('"+thisces+"')\"><img src=\"../themes/<?php echo $theme ?>/"+thisimage+"\" border=\"0\" id=\"menu_img_"+thisces+"\"> "+chats[thisces]["vname"]+"</div>" ;
		}

		if ( t_chats )
		{
			list_string += "<div style=\"clear:both\"></div>" ;
			$('#options_print').show() ;
		}
		else
		{
			ces = undeefined ;
			toggle_info( "info", 0 ) ;
			reset_canvas() ;
			disconnect_showhide() ;
			$('#options_print').hide() ;
			title_blink( 0, title_orig, "reset" ) ;
		}

		if ( !total_chats() )
		{
			toggle_info_list(0) ;
			clear_sound( "new_request" ) ;
			if ( focused ) { clear_flash_console() ; }
		}
		else if ( !total_new_requests )
		{
			clear_sound( "new_request" ) ;
			if ( focused ) { clear_flash_console() ; }
		}

		if ( refresh_list_string ) { $('#chat_switchboard').empty().html( list_string ) ; }
		if ( ( t_chats == 1 ) && ( $('#req_ces').html() != thisces ) )
		{
			if ( ces != thisces )
			{
				activate_chat( thisces ) ;
			}
		}
		else if ( t_chats && ( typeof( ces ) == "undefined" ) )
		{
			activate_chat( get_chat_prev() ) ;
		}

		for ( var thisces in chats )
		{
			if ( ces != thisces )
			{
				if ( !chats[thisces]["status"] && !chats[thisces]["initiated"] )
				{
					menu_blink( "red", thisces ) ;
				}
			}
		}
		if ( isop && !mapp ) { init_maxc() ; }
	}

	function clean_chats( theflag )
	{
		rupdated = ( theflag ) ? theflag : rupdated ;
		for ( var thisces in chats )
		{
			if ( !chats[thisces]["initiated"] && ( chats[thisces]["rupdated"] != rupdated ) )
			{
				if ( !chats[thisces]["disconnected"] && !chats[thisces]["status"] && !chats[thisces]["op2op"] )
				{
					//missed_chats[thisces] = chats[thisces] ;
					delete_chat_session( thisces ) ;
				}
				else if ( chats[thisces]["op2op"] && !chats[thisces]["status"] )
					delete_chat_session( thisces ) ;
				else if ( ( chats[thisces]["status"] == 2 ) && ( rupdated > parseInt( chats[thisces]["rupdated"] + <?php echo $VARS_TRANSFER_BACK ?> ) ) )
					delete_chat_session( thisces ) ;
			}
		}
	}

	function init_iframe( theiframe )
	{
		extra_wrapper_height = $('#chat_extra_wrapper').outerHeight() - $('#chat_footer').outerHeight() - 26 ;
		$('#'+theiframe).css({'height': extra_wrapper_height}) ;
	}

	function init_extra()
	{
		var pos_footer = ( mapp ) ? $('#chat_footer_mapp').position() : $('#chat_footer').position() ;
		var chat_wrapper_top = pos_footer.top - $('#chat_extra_wrapper').outerHeight() + 6 ;

		$('#chat_extra_wrapper').css({'top': chat_wrapper_top}).show() ;
	}

	function init_extra_loaded() { $('#span_extra_close').html( "<img src=\"../pics/space.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\">" ) ; }

	function init_chats()
	{
		// empty - calls on each chat response check
	}

	function total_chats()
	{
		var total = 0 ;
		total_new_requests = 0 ;

		for ( var thisces in chats )
		{
			++total ;
			// transferred chats are considered new chats
			if ( ( !chats[thisces]["status"] || ( chats[thisces]["status"] == 2 ) ) && !chats[thisces]["initiated"] && ( chats[thisces]["op2op"] != isop ) )
				++total_new_requests ;
		}
		return total ;
	}

	function activate_chat( theces )
	{
		// close_extra( extra ) ;
		if ( mapp )
		{
			if ( $('#div_mapp_chat_bubble_red').is(':visible') ) { $('#div_mapp_chat_bubble_red').hide() ; }
		}
		if ( theces == ces ) { return true ; }
		// store text to memory to place back when focused
		if ( typeof( chats[ces] ) != "undefined" )
		{
			clear_istyping() ;
			chats[ces]["input"] = $( "textarea#input_text" ).val() ;
		}
		toggle_last_response(1) ;
		ces = theces ;

		if ( typeof( chats[ces] ) != "undefined" )
		{
			isop_ = chats[ces]["op2op"] ;
			isop__ = chats[ces]["opid"] ;

			if ( typeof( si_his[ces] ) != "undefined" ) { clearInterval( si_his[ces] ) ; delete bk_his[ces] ; delete si_his[ces] ; }
			if ( typeof( markets[chats[ces]["marketid"]]["name"] ) == "undefined" ) { fetch_markets() ; }
			
			if ( ( typeof( dept_emos[chats[ces]["deptid"]] ) != "undefined" ) && dept_emos[chats[ces]["deptid"]] && <?php echo ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) ) ? 1 : 0 ; ?> )
			{ $('#span_emoticons').show() ; addon_emo = 1 ; } else { $('#span_emoticons').hide() ; addon_emo = 0 ; }

			var transcript = chats[ces]["trans"] ;
			$('#chat_body').empty().html( init_timestamps( transcript.emos() ) ) ;
			
			$('textarea#input_text').val( chats[ces]["input"] ) ;
			if ( chats[ces]["input"] )
				$( "button#input_btn" ).attr( "disabled", false ) ;
			else
				$( "button#input_btn" ).attr( "disabled", true ) ;

			for ( var thisces in chats )
			{
				if ( thisces == ces )
					$('#menu_img_'+ces).attr( "src", "../themes/<?php echo $theme ?>/online_green.png" ) ;
				else
					$('#menu_img_'+thisces).attr( "src", "../themes/<?php echo $theme ?>/online_grey.png" ) ;
			}

			init_scrolling() ;
			ck_his.push( ces ) ;
			idle_alert( ces, 1 ) ;

			reset_chat_list_style() ;
			init_textarea() ;
			$('#menu_'+ces).removeClass('chat_switchboard_cell_bl_red').removeClass('chat_switchboard_cell_bl_green').removeClass('chat_switchboard_cell_bl_red').removeClass('chat_switchboard_cell').addClass('chat_switchboard_cell_focus') ;
			$('#chat_vname').empty().html( chats[ces]["vname"] ) ;

			// populate info section
			if ( !chats[ces]["op2op"] || ( chats[ces]["op2op"] && ( chats[ces]["status"] == 2 ) ) )
			{
				var req_auto_pop = ( chats[ces]["auto_pop"] ) ? "<img src=\"../themes/<?php echo $theme ?>/info_flag.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"pre-populated visitor information\" title=\"pre-populated visitor information\"> " : "" ;
				var req_email = ( chats[ces]["initiated"] && !chats[ces]["auto_pop"] ) ? "<i>initiated chat - email not available</i>" : "<a href=\"mailto:"+chats[ces]["vemail"]+"\" class=\"nounder\"><span class=\"chat_info_link\">"+chats[ces]["vemail"]+"</span></a>"+"&nbsp;"+req_auto_pop ;
				var marketing = ( typeof( markets[chats[ces]["marketid"]]["name"] ) != "undefined" ) ? markets[chats[ces]["marketid"]]["name"] : "" ;

				var custom_display = 0 ;
				var custom_raw = chats[ces]["custom"] ;
				custom_raw = custom_raw.split("-cus-") ;
				var custom_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\">" ;

				var obj_length = custom_raw.length ;
				for ( var c = 0; c < obj_length; ++c )
				{
					if ( custom_raw[c] != 0 )
					{
						var custom_val = custom_raw[c].split("-_-") ;
						if ( custom_val[1] )
						{
							var custom_value = decodeURIComponent( custom_val[1] ) ;
							if ( custom_value.match( /^http/ ) )
							{
								var custom_value_snap = ( custom_value.length > 50 ) ? custom_value.substring( 0, 20 ) + "..." + custom_value.substring( custom_value.length-20, custom_value.length ) : custom_value ;
								custom_string += "<tr><td><div><div class=\"chat_info_td_blank\" style=\"font-weight: bold;\">"+decodeURIComponent( custom_val[0] )+"</div><div style=\"padding-top: 0px;\" class=\"chat_info_td\" title=\""+custom_value+"\" alt=\""+custom_value+"\"><a href=\""+custom_value+"\" target=_blank>"+custom_value_snap+"</a></div></div></td></tr>" ;
							}
							else
								custom_string += "<tr><td><div><div class=\"chat_info_td_blank\" style=\"font-weight: bold;\">"+decodeURIComponent( custom_val[0] )+"</div><div style=\"padding-top: 0px;\" class=\"chat_info_td\">"+decodeURIComponent( custom_val[1] )+"</div></div></td></tr>" ;
						}
						custom_display = 1 ;
					}
				}
				custom_string += "</table>" ;

				var url_raw = chats[ces]["onpage"] ;
				if ( url_raw == "livechatimagelink" ) { url_raw = "JavaScript:void(0)" ; }

				var geomap_string = ( chats[ces]["country"] ) ? " &nbsp; <img src=\"../pics/maps/"+chats[ces]["country"]+".gif\" width=\"18\" height=\"12\" border=\"0\" alt=\""+chats[ces]["country"]+"\" title=\""+chats[ces]["country"]+"\">" : "" ;
				var geomap_string_display = ( mapp ) ? chats[ces]["ip"]+geomap_string : "<a href=\"JavaScript:void(0)\" onClick=\"toggle_info('maps',1)\">"+chats[ces]["ip"]+geomap_string+"</a>" ;

				$('#req_dept').empty().html( depts_hash[chats[ces]["deptid"]]+"&nbsp;" ) ; 
				$('#req_email').empty().html( req_email ) ;
				$('#req_request').empty().html( chats[ces]["requests"] + " time(s)"+"&nbsp;" ) ;
				$('#req_onpage').empty().html( "<div title=\""+chats[ces]["onpage"]+"\" alt=\""+chats[ces]["onpage"]+"\"><a href=\""+url_raw+"\" target=\"_blank\">"+chats[ces]["title"]+"</a></div>" ) ;
				$('#req_refer').empty().html( "<a href=\""+chats[ces]["refer_raw"]+"\" target=\"_blank\">"+chats[ces]["refer_snap"]+"</a>"+"&nbsp;" ) ;
				$('#req_market').empty().html( marketing+"&nbsp;" ) ;
				$('#req_resolution').empty().html( chats[ces]["resolution"]+" &nbsp; <img src=\"../themes/<?php echo $theme ?>/os/"+chats[ces]["os"]+".png\" border=0 alt=\""+chats[ces]["os"]+"\" title=\""+chats[ces]["os"]+"\" alt=\""+chats[ces]["os"]+"\" width=\"10\" height=\"10\"> &nbsp; <img src=\"../themes/<?php echo $theme ?>/browsers/"+chats[ces]["browser"]+".png\" border=0 alt=\""+chats[ces]["browser"]+"\" title=\""+chats[ces]["browser"]+"\" alt=\""+chats[ces]["browser"]+"\" width=\"10\" height=\"10\">" ) ;
				$('#req_ip').empty().html( geomap_string_display ) ;
				if ( custom_display ) { $('#req_custom').empty().html(custom_string).show() ; } else { $('#req_custom').empty().html( "&nbsp;" ).show() ; }
				$('#req_t_ses').empty().html( "("+chats[ces]["t_ses"]+")" ).show() ;
				$('#req_ces').empty().html( ces ) ;
			}
			else
			{
				$('#req_dept').empty().html( "Operator 2 Operator Chat" ) ; 
				$('#req_email').empty() ;
				$('#req_request').empty() ;
				$('#req_onpage').empty() ;
				$('#req_refer').empty() ;
				$('#req_market').empty() ;
				$('#req_resolution').empty() ;
				$('#req_ip').empty() ;
				$('#req_custom').empty().hide() ;
				$('#req_t_ses').empty().hide() ;
				$('#req_ces').empty().html( ces ) ;
			}

			if ( !mapp && !mobile ) { $('#input_text').focus() ; }

			toggle_info( "info", 0 ) ;
			init_timer() ;

			populate_cans( chats[ces]["deptid"] ) ;
		}
		else { populate_cans(0) ; }

		disconnect_showhide() ;
	}

	function chat_accept()
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var wname = encodeURIComponent( cname ) ;

		clear_flash_console() ;
		if ( ( typeof( ces ) != "undefined" ) && ( typeof( chats[ces] ) != "undefined" ) )
		{
			$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_accept.php",
			data: "action=accept&requestid="+chats[ces]["requestid"]+"&ces="+ces+"&t_vses="+chats[ces]["t_ses"]+"&unique="+unique+"&",
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					fetch_rating_flag = 1 ;
					input_focus() ;
					if ( wp )
						wp_hide_tray( ces ) ;
					else
						dn_close( ces ) ;

					total_chats() ;
					if ( !total_new_requests && ( mobile == 2 ) )
					{
						clear_sound( "new_request" ) ;
						if ( focused ) { clear_flash_console() ; }
					}

					// if transferred, keep the same created time
					if ( chats[ces]["status"] != 2 )
						chats[ces]["timer"] = unixtime() ;

					chats[ces]["disconnected"] = 0 ; // reset it here as safe measure
					if ( json_data.tooslow )
					{
						chats[ces]["status"] = 1 ;
						chats[ces]["disconnected"] = 1 ;
						chats[ces]["tooslow"] = 1 ;
						$('#chat_body').append( "<div class='cl'>Chat session no longer exists.</div>" ) ;
					}
					else
					{
						if ( parseInt( chats[ces]["status"] ) == 2 )
						{
							$('#chat_body').empty() ;
							var string = chats[ces]["trans"] ;
							chats[ces]["trans"] = string.c615() ;
						}
						else { chats[ces]["trans"] = "" ; }

						// set status to picked up always
						chats[ces]["status"] = 1 ;
						var transcript = chats[ces]["trans"] ;
						if ( auto_canid && ( chats[ces]["status"] != 2 ) && !chats[ces]["op2op"] )
						{
							setTimeout( function() {
								select_canned_pre( auto_canid ) ;
								add_text_prepare(0) ;
							}, 1000 ) ;
						}

						// only set the idle timer on visitor chats (op2op chats should not auto disconnect)
						if ( !parseInt( chats[ces]["op2op"] ) ) { init_idle( ces ) ; }

						$('#chat_body').empty().html( transcript.emos() ) ;
						init_scrolling() ;
						init_textarea() ;
						init_chat_list(0) ;
						toggle_info( "info", 0 ) ;
						disconnect_showhide() ;
						init_timer() ;
					}
				}
				else { do_alert( 0, "Error accepting chat.  Please reload the console and try again." ) ; }
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error accepting chat.  Please reload the console and try again." ) ;
			} });
		}
	}

	function chat_decline()
	{
		var unique = unixtime() ;
		var theces = ces ;
		var json_data = new Object ;
		var wname = encodeURIComponent( cname ) ;

		clear_flash_console() ;
		if ( chats[theces]["tooslow"] )
			cleanup_disconnect( theces ) ;
		else if ( !chats[theces]["status"] || chats[theces]["disconnected"] || ( chats[theces]["status"] == 2 ) )
		{
			var requestid = chats[theces]["requestid"] ;
			var op2op = chats[theces]["op2op"] ;
			var status = chats[theces]["status"] ;
			cleanup_disconnect( theces ) ;

			$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_decline.php",
			data: "action=decline&requestid="+requestid+"&isop="+isop+"&isop_="+isop_+"&isop__="+isop__+"&ces="+theces+"&op2op="+op2op+"&status="+status+"&unique="+unique+"&",
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					setTimeout( function() { if ( typeof( cl_his[theces] ) != "undefined" ) { delete cl_his[theces] ; } }, <?php echo ( $VARS_JS_ROUTING * 3 ) ?> * 1000 ) ;
				}
				else
					do_alert( 0, "Error declining chat.  Please reload the console and try again." ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error declining chat.  Please reload the console and try again." ) ;
			} });
		}
	}

	function populate_cans( thedeptid )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_cans.php",
		data: "action=cans&opid="+isop+"&deptid="+thedeptid+"&"+unique+"&",
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				var deptid = 0 ;
				cans = new Object ;
				cans_string = "" ;

				var json_length = json_data.cans.length ;
				for ( var c = 0; c < json_length; ++c )
				{
					if ( !deptid || ( deptid != json_data.cans[c]["deptid"] ) )
					{
						deptid = json_data.cans[c]["deptid"] ;
						var dept_name = depts_hash[deptid] ;
						cans_string += "<optgroup label=\""+dept_name+"\">" ;
					}
					cans[json_data.cans[c]["title"]] = json_data.cans[c]["canid"] ;
					cans_string += "<option value=\""+json_data.cans[c]["message"]+"\">"+json_data.cans[c]["title"]+"</option>" ;
				}

				$('#chat_cans_select').empty().html( "<select id=\"canned_select\" style=\"width: 120px;\"><option></option>"+cans_string+"</select>" ) ;
			}
			else
				do_alert( 0, "Could not load canned responses. Please reload the console and try again." ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Lost connection to server.  Please reload the console and try again. [Error: 1031]" ) ;
		} });
	}

	function get_chat_prev( theces )
	{
		var thisces ;
		var obj_length = ck_his.length-1 ;
		for ( var c = obj_length; c >= 0; --c )
		{
			if ( ck_his[c] != "undefined" )
			{
				thisces = ck_his[c] ;
				if ( typeof( chats[thisces] ) != "undefined" )
				{
					if ( typeof( theces ) != "undefined" )
					{
						if ( theces != thisces ) { return thisces ; }
					}
					else { return thisces ; }
				}
			}
		}

		// otherwise activate the first chat request
		for ( var thisces in chats )
		{
			if ( theces != thisces ) { return thisces ; }
		}
	}

	function get_chat_next( theces )
	{
		var the_index = 0, ces_index = 0, next_index = 0 ;
		var chats_array = new Array() ;
		for ( var thisces in chats )
		{
			if ( thisces == theces ) { ces_index = the_index ; }
			chats_array.push( thisces ) ;
			++the_index ;
		}
		var chats_index_length = ( chats_array.length ) ? chats_array.length - 1 : 0 ;
		next_index = ces_index + 1 ;
		if ( typeof( chats_array[next_index] ) == "undefined" )
			return chats_array[0] ;
		else
			return chats_array[next_index] ;
	}

	function is_ces_in_his( theces )
	{
		var temp_ces ;
		var obj_length = ck_his.length-1 ;
		for ( var c = obj_length; c >= 0; --c )
		{
			temp_ces = ck_his[c] ;
			if ( temp_ces == theces )
				return true ;
		}
		return false ;
	}

	function reset_chat_list_style()
	{
		for ( var thisces in chats )
			$('#menu_'+thisces).removeClass('chat_switchboard_cell_focus').addClass('chat_switchboard_cell') ;
	}

	function reset_canvas()
	{
		var chat_body_content = $('#chat_body').html() ;

		if ( typeof( ces ) == "undefined" )
		{
			if ( !prev_status )
			{
				if ( !chat_body_content.match( /notice_online_tag/i ) )
					$('#chat_body').html("<notice_online_tag><div class='info_box' style=\"padding: 10px;\">You are <span class=\"info_good\">ONLINE</span>.<div style='margin-top: 10px;'>To receive visitor chat requests, keep this window open or minimized.</div></div>") ;
			}
			else
			{
				if ( automatic_offline_active && !chat_body_content.match( /notice_automatic_offline/i ) )
				{
					$('#chat_body').html("<notice_automatic_offline><div class='info_error'>You are OFFLINE.</div><div class='info_error' style='margin-top: 15px;' id='div_automatic_offline'><img src='../themes/<?php echo $theme ?>/alert.png' width='16' height='16' border='0' alt=''> <span style='font-size: 16px; font-weight: bold;'>Automatic Offline</span><div style='margin-top: 10px;'>It is past regular chat support hours.  The system is set to automatically logout shortly and will be available again during regular chat support hours.  <span onClick='toggle_status(3)' style='color: #FFFFFF; text-decoration: underline; cursor: pointer;'>Continue and logout.</span></div></div></notice_automatic_offline>") ;
				}
				else if ( !chat_body_content.match( /offline/i ) )
					$('#chat_body').html("<div class='info_error'>You are OFFLINE.</div>") ;
			}
		}

		if ( $('#req_dept').html().length )
		{
			$('#chat_vname').empty() ;
			$('#chat_vtimer').empty() ;
			$('#info_info').find('*').each( function(){
				var div_name = this.id ;
				if ( div_name.indexOf( "req_" ) == 0 )
					$(this).empty() ;
			} );
		}
	}

	function pre_disconnect()
	{
		$('#idle_timer_notice').hide() ;
		if ( typeof( chats[ces] ) != "undefined" )
		{
			if ( chats[ces]["tooslow"] )
				cleanup_disconnect( ces ) ;
			else if ( ( chats[ces]["status"] != 1 ) && !chats[ces]["initiated"] )
				chat_decline() ;
			else
			{
				chats[ces]["closed"] = 1 ;
				disconnect(0, 1, ces) ;
			}
		}
	}

	function cleanup_disconnect( theces )
	{
		delete_chat_session( theces ) ;

		$('#chat_vname').empty() ; $('#chat_vtimer').empty() ;
		if ( theces == ces ) { $('#idle_timer_notice').hide() ; }
		init_chat_list(0) ;
		init_textarea() ;
		activate_chat( get_chat_prev() ) ;
		close_extra( extra ) ;

		total_chats() ;
		if ( !total_new_requests )
		{
			clear_sound( "new_request" ) ;
			if ( focused ) { clear_flash_console() ; }
		}
	}

	function delete_chat_session( theces )
	{
		if ( wp )
			wp_hide_tray( theces ) ;
		else
			dn_close( theces ) ;

		if ( typeof( chats[theces] ) != "undefined" )
		{
			if ( typeof( chats[theces]["idle_si"] ) != "undefined" ) { clearInterval( chats[theces]["idle_si"] ) ; chats[theces]["idle_si"] = undeefined ; }

			// if transferred chat, delete from history incase it is routed back
			if ( ( chats[theces]["status"] == 3 ) || ( chats[theces]["status"] == 2 ) )
				delete cl_his[theces] ;
			else if ( !chats[theces]["status"] )
			{
				// add some buffer so it does not pop up immediately if loop
				setTimeout( function() { if ( typeof( cl_his[theces] ) != "undefined" ) { delete cl_his[theces] ; } }, <?php echo ( $VARS_JS_ROUTING * 2 ) ?> * 1000 ) ;
			}

			delete chats[theces] ;
			delete maps_his[theces] ;
			$('#iframe_maps_'+theces).remove() ;

			delete_from_his_ck( theces ) ;

			// if chat was focused, set it to undefined so the list resets
			if ( ces == theces ) { ces = undeefined ; }
		}
	}

	function delete_from_his_ck( theces )
	{
		var temp_ces ;
		var obj_length = ck_his.length-1 ;
		for ( var c = obj_length; c >= 0; --c )
		{
			temp_ces = ck_his[c] ;
			if ( temp_ces == theces )
			{
				ck_his[c] = undeefined ;
			}
		}
	}

	function toggle_info( thediv, theclick )
	{
		var obj_length = divs.length ;
		for ( var c = 0; c < obj_length; ++c )
		{
			$('#info_menu_'+divs[c]).removeClass('chat_info_menu_focus').addClass('chat_info_menu') ;
			$('#info_'+divs[c]).hide() ;
			if ( divs[c] == "transcripts" )
				$('#info_'+divs[c]).removeClass('info_transcripts') ;

			if ( divs[c] != "info" && divs[c] != "maps" )
				$('#info_'+divs[c]).empty().html( "<img src=\"../themes/<?php echo $theme ?>/loading_fb.gif\" border=\"0\" alt=\"\">" ) ;
		}

		if ( ( typeof( ces ) != "undefined" ) && ( typeof( chats[ces] ) != "undefined" ) )
		{
			info = thediv ;
			toggle_info_list(1) ;

			$('#info_menu_'+thediv).removeClass('chat_info_menu').addClass('chat_info_menu_focus') ;
			$('#info_'+thediv).show() ;
			$('#chat_info_body').css({'overflow': 'auto'}) ;

			if ( thediv == "maps" )
			{
				for ( var thisces in maps_his )
					$('#iframe_maps_'+thisces).hide() ;

				populate_maps() ;
			}
			else if ( thediv == "transfer" )
			{
				if ( ( chats[ces]["status"] == 1 ) && !chats[ces]["op2op"] && !chats[ces]["disconnected"] )
					populate_ops(0) ;
				else if ( !chats[ces]["status"] && !chats[ces]["op2op"] )
					$('#info_transfer').empty().html( "<div class=\"\">A chat session must be active.</div>" ) ;
				else
					$('#info_transfer').empty().html( "<div class=\"info_box\">Chat transfer not available for this session.<ul style=\"margin-top: 5px;\"><span style=\"font-weight: bold;\">Possible reasons:</span> <li>Chat has not been accepted.</li><li>Chat has ended.</li><li>Operator to Operator Chat</li></ul></div>" ) ;
			}
			else if ( thediv == "footprints" )
				populate_footprints() ;
			else if ( thediv == "transcripts" )
				populate_transcripts(mapp) ;
			else if ( thediv == "spam" )
				populate_spam() ;
		}
		else
		{
			if ( theclick && ( thediv != "info" ) )
			{
				toggle_info_list(0) ;
				do_alert( 0, "A chat session must be active." ) ;
			}
			$('#info_menu_info').removeClass('chat_info_menu').addClass('chat_info_menu_focus') ;
			$('#info_info').show() ;
		}

		init_info() ;
		disconnect_showhide() ;
	}

	function toggle_info_list( theflag )
	{
		if ( theflag && !$('#info_menu_maps').is(':visible') )
		{
			$('#info_menu_maps').show() ;
			$('#info_menu_footprints').show() ;
			$('#info_menu_transcripts').show() ;
			$('#info_menu_transfer').show() ;
			$('#info_menu_spam').show() ;
		}
		else if ( !theflag && $('#info_menu_maps').is(':visible') )
		{
			$('#info_menu_maps').hide() ;
			$('#info_menu_footprints').hide() ;
			$('#info_menu_transcripts').hide() ;
			$('#info_menu_transfer').hide() ;
			$('#info_menu_spam').hide() ;
		}
	}

	function disconnect_showhide()
	{
		if ( ( typeof( ces ) != "undefined" ) && ( typeof( chats[ces] ) != "undefined" ) )
		{
			if ( !chats[ces]["closed"] )
			{
				$('#info_disconnect').css({"padding": "3px"}).empty().html( "<img src=\"../themes/<?php echo $theme ?>/close_extra.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\"> close chat with <b>"+chats[ces]["vname"]+"</b>" ) ;
				if ( mapp ) { $('#info_disconnect_mapp').empty().html( "<img src=\"../themes/<?php echo $theme ?>/close_extra.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\"> close chat with <b>"+chats[ces]["vname"]+"</b>" ).show() ; $('#options_mapp_vinfo').show() ; }
			}
		}
		else
		{
			$('#info_disconnect').css({"padding": "0px"}).empty() ;
			if ( mapp ) { $('#info_disconnect_mapp').empty().hide() ; $('#options_mapp_vinfo').hide() ; }
		}
	}

	function populate_ops( themapp )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_deptops.php",
		data: "action=deptops&unique="+unique+"&",
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				if ( themapp ) { mapp_obj["ops"] = new Object ; mapp_obj["ops"] = json_data.departments ; populate_mapp_ops() ; }
				var ops_string = "" ;
				var json_length = json_data.departments.length ;
				for ( var c = 0; c < json_length; ++c )
				{
					ops_string += "<div class=\"chat_info_td_h\"><b>"+json_data.departments[c]["name"]+"</b></div>" ;
					var json_length2 = json_data.departments[c].operators.length ;
					for ( var c2 = 0; c2 < json_length2; ++c2 )
					{
						var status = "offline" ;
						var status_bullet = "online_grey.png" ;
						var btn_transfer = "" ;
						var chatting_with = ( nchats ) ? " chatting with "+json_data.departments[c].operators[c2]["requests"]+" visitors" : "" ;

						if ( json_data.departments[c].operators[c2]["status"] )
						{
							status = "online" ;

							status_bullet= "online_green.png" ;
							btn_transfer = "<button type=\"button\" class=\"input_button\" onClick=\"transfer_chat( "+json_data.departments[c]["deptid"]+",'"+json_data.departments[c]["name"]+"',"+json_data.departments[c].operators[c2]["opid"]+",'"+json_data.departments[c].operators[c2]["name"]+"');$(this).attr('disabled', 'true');\" style=\"font-size: 12px;\">transfer</button>" ;
						}

						if ( json_data.departments[c].operators[c2]["opid"] == isop )
							ops_string += "<div class=\"chat_info_td\" style=\"padding-left: 15px;\"><img src=\"../themes/<?php echo $theme ?>/"+status_bullet+"\" width=\"12\" height=\"12\" border=\"0\"> <b>(You)</b> are "+status+chatting_with+"</div>" ;
						else
							ops_string += "<div class=\"chat_info_td\" style=\"padding-left: 15px;\"><img src=\"../themes/<?php echo $theme ?>/"+status_bullet+"\" width=\"12\" height=\"12\" border=\"0\"> "+btn_transfer+" "+json_data.departments[c].operators[c2]["name"]+" is "+status+chatting_with+"</div>" ;
					}
				}
				ops_string += "<div class=\"chat_info_end\"></div>" ;
				$('#info_transfer').empty().html( ops_string ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error loading requested page.  Please reload the console and try again." ) ;
		} });
	}

	function populate_footprints()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( chats[ces]["op2op"] ) { $('#info_footprints').empty().html( "<div class=\"info_box\">Footprints not available for this session.</div>" ) ; }
		else
		{
			if ( chats[ces]["footprints"] == 0 )
			{
				$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_footprints.php",
				data: "action=footprints&vis_token="+chats[ces]["vis_token"]+"&unique="+unique+"&",
				success: function(data){
					eval( data ) ;

					if ( json_data.status )
					{
						var footprints_string = "<table cellspacing=0 cellpadding=0 border=0>" ;
						var json_length = json_data.footprints.length ;
						for ( var c = 0; c < json_length; ++c )
						{
							var url_raw = json_data.footprints[c]["onpage"] ;
							if ( url_raw == "livechatimagelink" )
								url_raw = "JavaScript:void(0)" ;
							footprints_string += "<tr><td width=\"30\" style=\"text-align: center\" class=\"chat_info_td_h\"><b>"+json_data.footprints[c]["total"]+"</b></td><td width=\"100%\" class=\"chat_info_td\"><div title=\""+json_data.footprints[c]["onpage"]+"\" alt=\""+json_data.footprints[c]["onpage"]+"\"><a href=\""+url_raw+"\" target=\"_blank\">"+json_data.footprints[c]["title"]+"</a></div></tr>" ;
						}
						footprints_string += "<tr><td colspan=2 class=\"chat_info_end\"></td></tr></table>" ;

						if ( json_data.footprints.length == 0 )
							footprints_string = "<div class=\"chat_info_td\">Visitor has no footprint record.</div>" ;

						chats[ces]["footprints"] = footprints_string ;
						$('#info_footprints').empty().html( chats[ces]["footprints"] ) ;
					}
				},
				error:function (xhr, ajaxOptions, thrownError){
					do_alert( 0, "Error loading footprints.  Please reload the console and try again." ) ;
				} });
			}
			else
			{
				$('#info_footprints').empty().html( chats[ces]["footprints"] ) ;
			}
		}
	}

	function populate_maps()
	{
		if ( typeof( maps_his[ces] ) == "undefined" )
		{
			var info_maps_width = $('#info_maps').width() - 18 ;
			var unique = unixtime() ;
			var iframe_map = document.createElement( "iframe" ); 
			iframe_map.setAttribute( "src", "./maps.php?ses=<?php echo $ses ?>&ip="+chats[ces]["ip"]+"&vis_token="+chats[ces]["vis_token"]+"&viewip="+viewip+"&skip="+chats[ces]["op2op"]+"&"+unique+"&" ) ; 
			iframe_map.style.display = "none" ;
			iframe_map.border = 0 ;
			iframe_map.frameBorder = 0 ;
			iframe_map.setAttribute( "id", "iframe_maps_"+ces ) ;
			maps_his[ces] = iframe_map ;

			$('#info_maps').append( iframe_map ) ;
			$("#iframe_maps_"+ces).css({"border": "5px", "width": info_maps_width, "overflow": "hidden"}) ;
			init_maps_iframes() ;
			$('#iframe_maps_'+ces).show() ;
		}
		else
		{
			init_maps_iframes() ;
			$('#iframe_maps_'+ces).show() ;
		}
	}

	function populate_transcripts( themapp )
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		mapp_obj["transcripts"] = new Object ;

		if ( chats[ces]["op2op"] )
		{
			if ( mapp ) { populate_mapp_trans_vinfo() ; }
			else { $('#info_transcripts').empty().html( "<div class=\"info_box\">Transcripts not available for this session.</div>" ) ; }
		}
		else
		{
			if ( chats[ces]["transcripts"] == 0 )
			{
				$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_trans.php",
				data: " action=transcripts&vis_token="+chats[ces]["vis_token"]+"&unique="+unique+"&",
				success: function(data){
					eval( data ) ;

					if ( json_data.status )
					{
						if ( themapp ) { mapp_obj["transcripts"] = json_data.transcripts ; populate_mapp_trans_vinfo() ; }
						else
						{
							var transcripts_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\">" ;
							var json_length = json_data.transcripts.length ;
							for ( var c = 0; c < json_length; ++c )
							{
								transcripts_string += "<tr><td width=\"16\" class=\"chat_info_td_h\" title=\"view transcript\" alt=\"view transcript\" onClick=\"open_transcript('"+json_data.transcripts[c]["ces"]+"')\" id=\"transcript_"+json_data.transcripts[c]["ces"]+"\" style=\"width: 16px; cursor: pointer;\"><img src=\"../themes/<?php echo $theme ?>/view.png\" width=\"16\" height=\"16\"></td><td class=\"chat_info_td\">"+json_data.transcripts[c]["operator"]+"</td><td width=\"50\" class=\"chat_info_td\">"+json_data.transcripts[c]["duration"]+"</td><td width=\"150\" class=\"chat_info_td\">"+json_data.transcripts[c]["created"]+"</td></tr>" ;
							}
							transcripts_string += "</table>" ;

							if ( json_data.transcripts.length == 0 )
								transcripts_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\"><tr><td class=\"chat_info_td\">Blank results.</td></tr></table>" ;

							chats[ces]["transcripts"] = transcripts_string ;
							$('#info_transcripts').empty().html( chats[ces]["transcripts"] ) ;
						}
					}
				},
				error:function (xhr, ajaxOptions, thrownError){
					do_alert( 0, "Error loading transcripts.  Please reload the console and try again." ) ;
				} });
			}
			else
			{
				$('#info_transcripts').empty().html( chats[ces]["transcripts"] ) ;
			}
		}
	}

	function populate_spam()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( chats[ces]["op2op"] ) { $('#info_spam').empty().html( "<div class=\"info_box\">Block not available for this session.</div>" ) ; }
		else
		{
			$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_spam.php",
			data: " action=spam_check&ip="+chats[ces]["ip"]+"&unique="+unique+"&",
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					var spam_string = "<div class=\"chat_info_td round\" style=\"margin-bottom: 10px;\"><ul style=\"padding-left: 15px;\"><li> Block the visitor's IP address from future chat requests.</li><li> Blocked visitors will always see an offline status.</li></ul></div>" ;

					if ( json_data.exist == 0 )
						spam_string += "<div id=\"info_spam_action\"><button type=\"button\" class=\"input_button\" onClick=\"spam_block(1, '"+chats[ces]["ip"]+"')\">Block</button></div>" ;
					else
						spam_string += "<div id=\"info_spam_action\" class=\"chat_info_link\" onClick=\"spam_block(0, '"+chats[ces]["ip"]+"')\">Visitor has been blocked.  Click to unblock visitor.</div>" ;

					$('#info_spam').empty().html( spam_string ) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error loading requested page.  Please reload the console and try again." ) ;
			} });
		}
	}

	function spam_block( theflag, theip )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$('#info_spam_action').html( "<img src=\"../themes/<?php echo $theme ?>/loading_fb.gif\" border=\"0\" alt=\"\">" ) ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_spam.php",
		data: "action=spam_block&flag="+theflag+"&ip="+theip+"&unique="+unique+"&",
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				var spam_string = "" ;
				if ( !theflag )
					spam_string = "<div id=\"info_spam_action\"><button type=\"button\" class=\"input_button\" onClick=\"spam_block(1, '"+chats[ces]["ip"]+"')\">Block</button></div>" ;
				else
					spam_string = "<div id=\"info_spam_action\" class=\"chat_info_link\" onClick=\"spam_block(0, '"+chats[ces]["ip"]+"')\">Visitor has been blocked.  Click to unblock visitor.</div>" ;

				$('#info_spam_action').html( spam_string ) ;
			}
			else
			{
				do_alert( 0, json_data.error ) ;
				$('#info_spam_action').html("") ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error processing block.  Please reload the console and try again." ) ;
		} });
	}

	function transfer_chat( thedeptid, thedeptname, theopid, theopname )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$('#idle_timer_notice').hide() ;
		// only transfer if chat has not been transferred already
		if ( chats[ces]["status"] != 3 )
		{
			$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_transfer.php",
			data: "action=transfer&requestid="+chats[ces]["requestid"]+"&ces="+ces+"&deptid="+thedeptid+"&t_vses="+chats[ces]["t_ses"]+"&deptname="+thedeptname+"&opid="+theopid+"&opname="+theopname+"&unique="+unique+"&",
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					isop_ = 0 ; isop__ = 0 ;
					chats[ces]["initiated"] = 0 ; // reset to register it as a normal chat

					// delete chat AND remove it from history list so it can repopulate if transferred back to same op
					chats[ces]["status"] = 3 ; // set it to 3, for AFTER transfer (used only here)
					chats[ces]["disconnected"] = unixtime() ;
					var trans_to = ( theopname ) ? theopname : thedeptname ;
					var trans_string = "<c615><div class='cl'>Chat has been transferred to <b>"+trans_to+"</b>. Chat session has ended.<div style='margin-top: 5px;'><button onClick='cleanup_disconnect(ces)' style=''>close chat</button></div></div></c615>" ;
					chats[ces]["trans"] += trans_string  ;
					$('#chat_body').append( trans_string ) ; if ( isop && mapp ) { init_external_url() ; }
					init_scrolling() ;
					init_textarea() ;
				}
				else { do_alert( 0, "Transfer error.  Please reload the console and try again." ) ; }
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Transfer error.  Please reload the console and try again." ) ;
			} });
		}
		else
		{
			// todo: display message instead of disabling the buttons
		}
	}

	function fetch_markets()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_campaigns.php",
		data: "action=campaigns&unique="+unique+"&",
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				total_markets = json_data.markets.length ;
				for ( var c = 0; c < total_markets; ++c )
				{
					var marketid = json_data.markets[c].marketid ;
					var name = json_data.markets[c].name ;
					var color = json_data.markets[c].color ;

					markets[marketid] = new Object ;
					markets[marketid]["name"] = name ;
					markets[marketid]["color"] = color ;
				}
				// add the dummy ZERO
				markets["0"] = new Object ;
			}
			else { do_alert( 0, "Error fetching campaigns.  Please reload the console and try again." ) ; }
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error fetching campaigns.  Please reload the console and try again." ) ;
		} });
	}

	function populate_ops_op2op()
	{
		$('#iframe_op2op').attr('src', "op_op2op.php?ses=<?php echo $ses ?>&"+unixtime()+"&" ).ready(function() {
			init_iframe( 'iframe_op2op' ) ;
		});
		$('#chat_extra_body_op2op').fadeIn("fast") ;
	}

	function populate_traffic()
	{
		$('#iframe_traffic').attr('src', "op_traffic.php?ses=<?php echo $ses ?>&mapp="+mapp+"&"+unixtime()+"&" ).ready(function() {
			init_iframe( 'iframe_traffic' ) ;
		});
		$('#chat_extra_body_traffic').fadeIn("fast") ;
	}

	function populate_canned( theflag )
	{
		$('#iframe_canned').attr('src', "op_canned.php?ses=<?php echo $ses ?>&flag="+theflag+"&"+unixtime()+"&" ).ready(function() {
			init_iframe( 'iframe_canned' ) ;
		});
		$('#chat_extra_body_canned').fadeIn("fast") ;
	}

	function populate_trans()
	{
		$('#iframe_trans').attr('src', "op_trans.php?ses=<?php echo $ses ?>&"+unixtime()+"&" ).ready(function() {
			init_iframe( 'iframe_trans' ) ;
		});
		$('#chat_extra_body_trans').fadeIn("fast") ;
	}

	function populate_settings( themenu )
	{
		var theurl = "about:blank" ;

		if ( themenu == "themes" )
			theurl = "./index.php?menu="+themenu+"&auto=<?php echo $auto ?>&wp=1&console=1&ses=<?php echo $ses ?>&"+unixtime()+"&" ;
		else if ( themenu == "sounds" )
			theurl = "./notifications.php?auto=<?php echo $auto ?>&wp=1&console=1&ses=<?php echo $ses ?>&"+unixtime()+"&" ;
		else if ( themenu == "settings" )
			theurl = "./settings.php?auto=<?php echo $auto ?>&wp=1&console=1&ses=<?php echo $ses ?>&"+unixtime()+"&" ;

		$('#iframe_settings').attr('src', theurl ).ready(function() {
			init_iframe( 'iframe_settings' ) ;
		});

		if ( typeof( iframe_his["settings"] ) == "undefined" )
		{
			$('#chat_extra_body_settings').fadeIn("fast") ;
			iframe_his["settings"] = 1 ;
		}
		else { $('#chat_extra_body_settings').show() ; }
	}

	function populate_ext( thediv, theurl )
	{
		var temp = "chat_extra_body_ext_"+thediv ;

		if ( typeof( ex_his[temp] ) == "undefined" )
		{
			$('#'+temp).empty().html( "<iframe id=\"iframe_ext_"+thediv+"\" name=\"iframe_ext_"+thediv+"\" style=\"width: 100%; border: 0px;\" src=\"../blank_.php?theme=<?php echo $opinfo["theme"] ?>&url="+encodeURIComponent(theurl)+"&proto="+proto+"\" scrolling=\"auto\" border=0 frameborder=0 onLoad=\"init_iframe_errors( '"+thediv+"','XFrame', '"+theurl+"'); init_iframe( 'iframe_ext_"+thediv+"' );\"></iframe>" ).show() ;
			ex_his[temp] = 1 ;
		}
		else
			$('#'+temp).show() ;
	}

	function init_reload_traffic()
	{
		document.getElementById('iframe_traffic').contentWindow.close_footprint_info(1) ;
		reload_traffic("iframe_traffic") ;
	}

	function init_iframe_errors( thediv, theerror, theurl )
	{
		if ( ( typeof( document.getElementById('iframe_ext_'+thediv).contentWindow.xframe ) != "undefined" ) && document.getElementById('iframe_ext_'+thediv).contentWindow.xframe )
		{
			document.getElementById('iframe_ext_'+thediv).contentWindow.display_error( theerror, theurl ) ;
		}
		else if ( ( typeof( document.getElementById('iframe_ext_'+thediv).contentWindow.xframe ) != "undefined" ) && !document.getElementById('iframe_ext_'+thediv).contentWindow.xframe )
		{
			setTimeout( function(){ $('#iframe_ext_'+thediv).attr('src', theurl ) ; }, 800 ) ;
		}
	}

	function toggle_extra( thediv, theflag, theurl, thename, themenu )
	{
		// the flag is used for various triggers for the div
		reset_footer() ;
		toggle_last_response(1) ;
		total_chats() ;

		// mapp pre actions
		if ( mapp ) { $('#chat_switchboard').css({'top': -1000}) ; }

		if ( extra == thediv )
		{
			close_extra( thediv ) ;
			if ( !mapp && !mobile ) { $( "textarea#input_text" ).focus() ; }
		}
		else
		{
			extra = thediv ;

			if ( typeof( thediv ) == "number" )
				$('#chat_footer_cell_ext_'+thediv).removeClass('chat_footer_cell').addClass('chat_footer_cell_focus') ;
			else
				$('#chat_footer_cell_'+thediv).removeClass('chat_footer_cell').addClass('chat_footer_cell_focus') ;

			var span_text = ( mapp || ( typeof( thediv ) == "number" ) ) ? "<img src=\"../pics/space.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\">" : "<img src=\"../pics/loading_fb.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\">" ;
			$('#chat_extra_title').html( "<button type=\"button\" style=\"width: 60px;\" onClick=\"close_extra( '"+extra+"' )\">close</button> <span id=\"span_extra_close\">"+span_text+"</span> " + thename ) ;
			if ( thediv == "op2op" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				setTimeout( function(){ populate_ops_op2op() ; }, 300 ) ; // add a little delay to fix bug
			}
			else if ( thediv == "traffic" )
			{
				// todo: REFINE onClick= event
				$('#chat_extra_title').append( "<span id=\"div_traffic_sound\" class=\"sound_box_on\" style=\"margin-left: 20px; font-weight: normal; font-size: 10px; cursor: pointer;\" onClick=\"toggle_traffic_sound()\"></span> &nbsp; <span style=\"font-size: 10px; font-weight: normal;\">Traffic monitor will auto refresh when there is change in website traffic. <button type='botton' style='font-size: 10px;' onClick='init_reload_traffic(\"iframe_traffic\")'>refresh now</button></span>" ) ;
				print_traffic_sound_text() ;
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_traffic() ;
			}
			else if ( thediv == "canned" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_canned( theflag ) ;
			}
			else if ( thediv == "trans" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_trans() ;
			}
			else if ( thediv == "settings" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_settings( themenu ) ;
			}
			else if ( thediv == "mapp_chats" )
			{
				if ( !total_new_requests && $('#div_mapp_chat_bubble_red').is(':visible') ) { $('#div_mapp_chat_bubble_red').hide() ; }
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_chats( "<?php echo $ses ?>" ) ;
				toggle_mapp_icon( thediv, 1 ) ;
			}
			else if ( thediv == "mapp_traffic" )
			{
				$('#chat_extra_title').append( "&nbsp; <span style=\"float: right; margin-right: 10px; font-size: 10px; font-weight: normal;\"><button type='botton' style='' onClick='init_reload_mapp_traffic(\"<?php echo $ses ?>\")'>refresh</button></span><span style=\"clear: both;\"></span>" ) ;
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_traffic( "<?php echo $ses ?>" ) ;
				toggle_mapp_icon( thediv, 1 ) ;
			}
			else if ( thediv == "mapp_themes" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_themes( "<?php echo $ses ?>" ) ;
			}
			else if ( thediv == "mapp_prefs" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_prefs( "<?php echo $ses ?>" ) ;
			}
			else if ( thediv == "mapp_sounds" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_sounds( "<?php echo $ses ?>" ) ;
			}
			else if ( thediv == "mapp_operators" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_operators( "<?php echo $ses ?>" ) ;
			}
			else if ( thediv == "mapp_trans" )
			{
				$('#chat_extra_title').append( "&nbsp; <span style=\"float: right; margin-right: 10px; font-size: 10px; font-weight: normal;\"><button type='botton' style='' onClick='init_reload_mapp_trans(\"<?php echo $ses ?>\")'>refresh</button></span><span style=\"clear: both;\"></span>" ) ;
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_trans( "<?php echo $ses ?>" ) ;
			}
			else if ( thediv == "mapp_power" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_power( "<?php echo $ses ?>" ) ;
				toggle_mapp_icon( thediv, 1 ) ;
			}
			else if ( thediv == "mapp_vinfo" )
			{
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_vinfo( ces ) ;
			}
			else if ( thediv == "mapp_cans" )
			{
				$('#chat_extra_title').append( "&nbsp; <span style=\"float: right; margin-right: 10px; font-size: 10px; font-weight: normal;\"><button type='botton' style='' onClick='init_reload_mapp_cans(\"<?php echo $ses ?>\")'>refresh</button></span><span style=\"clear: both;\"></span>" ) ;
				hide_extra( "chat_extra_body_"+thediv ) ;
				populate_mapp_cans( "<?php echo $ses ?>" ) ;
				toggle_mapp_icon( thediv, 1 ) ;
			}
			else
			{
				hide_extra( "chat_extra_body_ext_"+thediv ) ;
				populate_ext( thediv, theurl ) ;
			}

			init_extra() ;
		}
	}

	function reset_footer()
	{
		var divs = Array( "op2op", "traffic", "canned", "trans", "extras" ) ;
		var obj_length = divs.length ;
		for ( var c = 0; c < obj_length; ++c )
			$('#chat_footer_cell_'+divs[c]).removeClass('chat_footer_cell_focus').addClass('chat_footer_cell') ;

		$('div').filter(function() {
			return this.id.match(/chat_footer_cell_ext_\d/) ;
		}).removeClass('chat_footer_cell_focus').addClass('chat_footer_cell') ;

		$('#div_geomap').hide() ;
	}

	function close_extra( thediv )
	{
		var pos_footer = $('#chat_footer').position() ;
		var chat_extra_wrapper_top = pos_footer.top - 1 ;

		if ( typeof( extra ) == "undefined" )
		{
			// nothing for now
		}
		else
		{
			if ( ( thediv == "settings" ) && ( typeof( document.getElementById('iframe_'+thediv).contentWindow ) != "undefined" ) )
			{
				if ( typeof( document.getElementById('iframe_'+thediv).contentWindow.clear_sound ) != "undefined" )
					document.getElementById('iframe_'+thediv).contentWindow.clear_sound('new_request') ;
			}
			else if ( thediv == "mapp_chats" ) { $('#chat_switchboard').css({'top': -1000}) ; }
			else if ( ( thediv == "mapp_sounds" ) && ( typeof( document.getElementById('iframe_mapp_sounds').contentWindow.demo_sound1 ) != "undefined" ) ) { document.getElementById('iframe_mapp_sounds').contentWindow.demo_sound1(0) ; }

			if ( mapp ) { $('#chat_extra_wrapper').hide() ; toggle_mapp_icon(thediv, 0) ; close_extra_doit( thediv ) ; }
			else { $('#chat_extra_wrapper').fadeOut('fast', function() { close_extra_doit( thediv ) ; }) ; }

			if ( thediv == "settings" )
			{
				$('#chat_footer').animate({
					bottom: "-2"
				}, 500, function() {
					// nothing as of yet
				});
			}
		}
	}

	function close_extra_doit( thediv )
	{
		if ( typeof( thediv ) != "number" )
		{
			if ( ( thediv == "op2op" ) || ( thediv == "canned" ) || ( thediv == "trans" ) || ( thediv == "traffic" ) || ( thediv == "settings" ) || ( thediv == "mapp_themes" ) || ( thediv == "mapp_prefs" ) || ( thediv == "mapp_sounds" ) || ( thediv == "mapp_operators" ) || ( thediv == "mapp_power" ) || ( thediv == "mapp_traffic" ) ) { iframe_blank( thediv ) ; }
		}
		extra = undeefined ;
		reset_footer() ;
	}

	function hide_extra( thediv )
	{
		var divs = Array( "chat_extra_body_op2op", "chat_extra_body_traffic", "chat_extra_body_canned", "chat_extra_body_trans", "chat_extra_body_extras", "chat_extra_body_settings", "chat_extra_body_mapp_chats", "chat_extra_body_mapp_traffic", "chat_extra_body_mapp_themes", "chat_extra_body_mapp_prefs", "chat_extra_body_mapp_sounds", "chat_extra_body_mapp_operators", "chat_extra_body_mapp_trans", "chat_extra_body_mapp_power", "chat_extra_body_mapp_vinfo", "chat_extra_body_mapp_cans" ) ;

		var obj_length = divs.length ;
		for ( var c = 0; c < obj_length; ++c )
		{
			if ( divs[c] != thediv )
			{
				var regp = /chat_extra_body_(.*)/gi ;
				var regm = regp.exec( divs[c] ) ;
				var thisdiv = regm[1] ;

				if ( ( thisdiv == "op2op" ) || ( thisdiv == "canned" ) || ( thisdiv == "trans" ) || ( thisdiv == "traffic" ) || ( thisdiv == "settings" ) || ( thisdiv == "mapp_themes" ) || ( thisdiv == "mapp_prefs" ) || ( thisdiv == "mapp_sounds" ) || ( thisdiv == "mapp_operators" ) || ( thisdiv == "mapp_power" ) || ( thisdiv == "mapp_traffic" ) ) { iframe_blank( thisdiv ) ; }
				if ( mapp && ( thisdiv == "mapp_sounds" ) && ( typeof( document.getElementById('iframe_mapp_sounds').contentWindow.demo_sound1 ) != "undefined" ) ) { document.getElementById('iframe_mapp_sounds').contentWindow.demo_sound1(0) ; }
				if ( mapp && thisdiv.match(/mapp_/) ) { toggle_mapp_icon(thisdiv, 0) ; }

				$('#'+divs[c]).hide() ; 
			}
		}

		$('div').filter(function() {
			return this.id.match(/chat_extra_body_ext_\d/) ;
		}).hide() ;
	}

	function iframe_blank( thediv )
	{
		if ( ( $('#iframe_'+thediv).length != 0 ) && ( typeof( document.getElementById('iframe_'+thediv).contentWindow ) != "undefined" ) )
		{
			// mobile = 1/3 (iOS) about:blank does not process - bug fix
			if ( mapp && ( ( mobile == 1 ) || ( mobile == 3 ) ) )
			{
				var href = document.getElementById('iframe_'+thediv).contentWindow.location.href ;
				if ( !href.match( /blank/ ) ) { $('#iframe_'+thediv).attr( 'src', "../blank.php" ) ; } // about:blank doesn't work on iOS mapp
			}
			else { $('#iframe_'+thediv).attr( 'src', "about:blank" ) ; }
		}
	}

	function select_canned_pre( theauto_canid )
	{
		input_focus() ;
		close_extra( extra ) ;

		var thetitle ;
		if ( typeof( theauto_canid ) == "number" )
		{
			for ( var thistitle in cans )
			{
				if ( cans[thistitle] == theauto_canid ) { thetitle = thistitle ; break ; }
			}
		}
		else { thetitle = theauto_canid ; }

		if ( typeof( thetitle ) != "undefined" )
		{
			$('#canned_select option').filter(function () {
				if ( $(this).html() == thetitle.replace( /&-#39;/g, "'" ) )
					this.selected = true ;
			}) ;
			select_canned() ;
		}
	}

	function select_canned()
	{
		var response_text = $('#canned_select').val() ;

		if ( typeof( extra ) != "undefined" ) { close_extra( extra ) ; }
		if ( response_text && total_chats() && ( typeof( ces ) != "undefined" ) && chats[ces]["status"] && !chats[ces]["disconnected"] )
		{
			$( "textarea#input_text" ).val( response_text.replace( /<br>/g, "\r" ) ) ;
			if ( !mapp && !mobile ) { $( "textarea#input_text" ).focus() ; }
			$( "button#input_btn" ).attr( "disabled", false ) ;
		}
		else if ( !response_text ) { do_alert( 0, "Blank canned response is invalid." ) ; }
		else { do_alert( 0, "A chat session must be active." ) ; }
	}

	function check_network( thetotal, theunixtime, theserver )
	{
		if ( typeof( theunixtime ) != "undefined" )
		{
			var network_duration = thetotal - theserver ;
			if ( network_duration < 0 ) { network_duration = 0.001 ; }
			update_network( thetotal.toFixed(3), network_duration.toFixed(3), theserver.toFixed(3) ) ;
		}
		else { update_network( "[error] "+thetotal, "-", "-" ) ; }
	}

	var global_network_max = 0 ; var global_prev_network = 0 ; var global_prev_server = 0 ;
	var diff_network_string = "" ; var diff_server_string = "" ; var network_string = "" ; var server_string = "" ;
	function update_network( thetotal, thenetwork, theserver )
	{
		var meter_length = 0 ;

		if ( !thetotal.match( /error/i ) ) { thetotal = parseFloat( thetotal ).toFixed(3) ; }
		if ( thetotal && !global_network_max ) { global_network_max = thetotal*3 ; }
		if ( global_network_max && !mapp ) { meter_length = Math.round( ( thetotal/global_network_max ) * 100 ) ; }

		var temp_network = parseFloat( thenetwork ).toFixed(3) ;
		var temp_server = parseFloat( theserver ).toFixed(3) ;
		
		var diff_network = parseFloat( temp_network - global_prev_network ).toFixed(3) ;
		var diff_server = parseFloat( temp_server - global_prev_server ).toFixed(3) ;

		var diff_network_display = ( diff_network < 0 ) ? diff_network * -1 : diff_network ;
		var diff_server_display = ( diff_server < 0 ) ? diff_server * -1 : diff_server ;

		diff_network_string = ( ( diff_network > 0 ) ) ? " <span class='info_error' style='padding: 2px;'>-"+parseFloat( diff_network_display ).toFixed(3)+'</span>' : "<span class='info_good' style='padding: 2px;'>+"+parseFloat( diff_network_display ).toFixed(3)+"</span>" ;

		diff_server_string = ( ( diff_server > 0 ) ) ? " <span class='info_error' style='padding: 2px;'>-"+parseFloat( diff_server_display ).toFixed(3)+'</span>' : "<span class='info_good' style='padding: 2px;'>+"+parseFloat( diff_server_display ).toFixed(3)+"</span>" ;

		if ( diff_network == 0 ) { diff_network_string = "" ; }
		if ( diff_server == 0 ) { diff_server_string = "" ; }

		network_string = ( !mapp ) ? "<table cellspacing=0 cellpadding=0 border=0><tr><td>"+thenetwork+"</td><td style='padding-left: 5px;'>"+diff_network_string+"</td></tr></table>" : thenetwork ;
		server_string = ( !mapp ) ? "<table cellspacing=0 cellpadding=0 border=0><tr><td>"+theserver+"</td><td style='padding-left: 5px;'>"+diff_server_string+"</td></tr></table>" : theserver ;

		update_network_log( "<tr id='div_network_his_"+network_counter+"' style='display: none'><td class='chat_info_td'>"+network_string+"</td><td class='chat_info_td'>"+server_string+"</td><td class='chat_info_td'>"+thetotal+" <div style=\"width: "+meter_length+"px; background: #3399FF; height: 2px;\">&nbsp;</div></td></tr>" ) ;

		global_prev_network = temp_network ;
		global_prev_server = temp_server ;

		if ( thetotal <= 1 )
		{
			if ( mapp ) { update_mapp_network( 5 ) ; }
			else { $('#chat_network_img').css({'background-position': '0px bottom'}) ; }
		}
		else if ( thetotal <= 1.3 )
		{
			if ( mapp ) { update_mapp_network( 4 ) ; }
			else { $('#chat_network_img').css({'background-position': '0px -152px'}) ; }
		}
		else if ( thetotal <= 1.6 )
		{
			if ( mapp ) { update_mapp_network( 3 ) ; }
			else { $('#chat_network_img').css({'background-position': '0px -114px'}) ; }
		}
		else if ( thetotal <= 2 )
		{
			if ( mapp ) { update_mapp_network( 2 ) ; }
			else { $('#chat_network_img').css({'background-position': '0px -76px'}) ; }
		}
		else if ( thetotal <= 50 )
		{
			if ( mapp ) { update_mapp_network( 1 ) ; }
			else { $('#chat_network_img').css({'background-position': '0px -38px'}) ; }
		}
		else
		{
			if ( mapp ) { update_mapp_network( 0 ) ; }
			else { $('#chat_network_img').css({'background-position': '0px 0px'}) ; }

			// disconnected by calling p_engine.php stop() function (attempt to reconnect)
			if ( op_depts ) { reconnect() ; }
		}
	}

	function update_network_log( thestring )
	{
		var total_display = 100 ;
		var now_string = thestring.replace(/his_\d+'/g, "") ;

		if ( prev_network_string != now_string )
		{
			if ( mapp ) { update_mapp_network_log( network_counter, thestring ) ; }
			else
			{
				$('#chat_info_network_info tbody tr:nth-child(2)').after( thestring ) ;
				$('#div_network_his_'+network_counter).fadeIn("fast") ;
			}

			if ( thestring.match( /(\[error\])|(disco)/gi ) )
			{
				var localtime = new Date() ;
				var tempstring = thestring.replace( /id='(.*?)' style=/g, "id='log_$1' style=" ) ;
				tempstring = tempstring.replace( /none'><td class='chat_info_td'>(.*?)</g, "none'><td class='chat_info_td'>"+localtime.toLocaleString()+"<" ) ;

				if ( mapp ) { }
				else
				{
					tempstring = tempstring.replace( /<span class=(.*?)<\/span>/g, "" ) ;
					$('#chat_info_network_log tbody tr:nth-child(2)').after( tempstring ) ;
					$('#log_div_network_his_'+network_counter).show() ;
				}
			}
			++network_counter ;
			prev_network_string = now_string ;

			if ( network_counter > total_display )
			{
				var div_delete = network_counter - total_display - 1 ;
				$('#div_network_his_'+div_delete).fadeOut( "slow", function() {
					$('#div_network_his_'+div_delete).remove() ;
				});
			}
		}
	}

	function toggle_status( thestatus )
	{
		var unique = unixtime() ;

		// make it active if status is online to hide logout div
		if ( prev_status != thestatus )
		{
			if ( typeof( st_logout ) != "undefined" )
			{
				clearTimeout( st_logout ) ;
				st_logout = undeefined ;
			}

			$('#chat_status_logout').hide() ;

			if ( ( automatic_offline_active || global_maxc_flag ) && ( thestatus == 0 ) )
			{
				if ( !$('#status_1').is(':checked') ) { $('#status_1').prop( "checked", true ) ; }
				if ( !$('#chat_status_offline').is(':visible') )
				{
					if ( typeof( si_offline ) != "undefined" ) { clearInterval( si_offline ); si_offline = undeefined ; $('#offline_timer').html( logout_timer+":00" ) ; }
					start_offline_timer( logout_timer*60 ) ;
					$('#chat_status_offline').show() ;
				}
				else if ( global_maxc_flag )
					toggle_offline_timer_showhide(1) ;

				if ( $('#div_automatic_offline').is(':visible') )
					$('#div_automatic_offline').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
				else
					$('#chat_status_offline').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
			}
			else
			{
				if ( typeof( si_offline ) != "undefined" ) { clearInterval( si_offline ); si_offline = undeefined ; $('#offline_timer').html( logout_timer+":00" ) ; }
				if ( thestatus == 0 )
				{
					$('#chat_status_offline_radio').css({'background': ''}) ; $('#chat_status_offline').hide() ;
					if ( !$('#status_0').is(':checked') ) { $('#status_0').prop( "checked", true ) ; }

					if ( typeof( prev_status ) != "undefined" ) { prev_status = thestatus ; update_status(1) ; } // do not log cancel logout revert
					else { prev_status = thestatus ; }
				}
				else if ( thestatus == 1 )
				{
					toggle_offline_timer_showhide( global_maxc_flag ) ;
					if ( !$('#status_1').is(':checked') ) { $('#status_1').prop( "checked", true ) ; }
					if ( !$('#chat_status_offline').is(':visible') ) { $('#chat_status_offline').show() ; }

					var color = $('#chat_status_offline').css("background-color") ;
					$('#chat_status_offline_radio').css({'background-color': color}) ;

					$('#div_automatic_offline').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;

					start_offline_timer( logout_timer*60 ) ;
					if ( typeof( prev_status ) != "undefined" ) { prev_status = thestatus ; update_status(0) ; }
					else { prev_status = thestatus ; }
				}
				else if ( thestatus == 2 )
				{
					$('#chat_status_logout').show() ;
					$('#chat_status_offline_radio').css({'background': ''}) ; $('#chat_status_offline').hide() ;
					if ( !$('#status_2').is(':checked') ) { $('#status_2').prop( "checked", true ) ; }

					// another layer of check for auto logout
					st_logout = setTimeout( function(){ toggle_status( 3 ) ; }, 300000 ) ;
				}
				else if ( ( thestatus == 3 ) || ( thestatus == 4 ) )
				{
					window.onbeforeunload = null ;
					location.href = base_url+"/logout.php?action=logout&ao="+ao+"&rd="+rd+"&dup="+dup+"&mi="+mi+"&auto=1&wp="+wp+"&pop=<?php echo $pop ?>&mapp="+mapp+"&"+unique+"&" ;
				}
				else
				{
					$('input:radio[name=status]')[prev_status].checked = true ;
					$('#chat_status_logout').hide() ;

					if ( ( thestatus == 1000 ) && ( prev_status == 1 ) ) { prev_status = undeefined ; toggle_status( 1 ) ; }
					else { var temp = prev_status ; prev_status = undeefined ; toggle_status( temp ) ; }
				}
			}
		}
		else
		{
		}
	}

	function toggle_offline_timer_showhide( theflag )
	{
		if ( theflag )
		{
			$('#chat_status_offline_text').html( "You are OFFLINE" ) ;
			$('#offline_timer_description').html( "You have reached the max concurrent chat limit.  System will automatically resume ONLINE status when your concurrent active chats total is below the max ("+maxc+")." ) ;
			$('#offline_timer').hide() ;
			$('#offline_timer_btn').hide() ;
		}
		else
		{
			$('#chat_status_offline_text').html( "You are OFFLINE." ) ;
			$('#offline_timer_description').html( "automatically log out in: &nbsp; " ) ;
			$('#offline_timer').show() ;
			$('#offline_timer_btn').show() ;
		}
	}

	function update_status( thestatus )
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		status_update_flag = 1 ;

		current_status = thestatus ;
		$('#chat_status_logout').hide() ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_status.php",
		data: "action=status&opid="+isop+"&status="+thestatus+"&mapp="+mapp+"&unique="+unique+"&",
		success: function(data){
			eval( data ) ;
			status_update_flag = 0 ;

			if ( json_data.status )
			{
				// success action
			}
			else{ do_alert( 0, "Error updating status.  Please reload the console and try again." ) ; }
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error updating status.  Please refresh the console to reset the status. [e2]" ) ;
		} });
	}

	function update_ratings()
	{
		if ( status_update_flag )
		{
			var si_status_update = setInterval(function(){
				if ( !status_update_flag )
				{
					clearInterval( si_status_update ) ;
					update_ratings_doit() ;
				}
			}, 200) ;
		}
		else { update_ratings_doit() ; }
	}

	function update_ratings_doit()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( !total_chats() )
			++fetch_rating_flag ;
		else
			fetch_rating_flag = 1 ;

		// if no chats, save query resources
		var rating_flag = ( fetch_rating_flag >= 4 ) ? 0 : 1 ;

		var of = ( rd || mi || dup || ao ) ? 1 : 0 ; // for future feature

		$.ajax({
		type: "GET",
		url: "../ajax/chat_actions_op_itr_ratings.php",
		data: "a=fr&se=<?php echo $ses ?>&st="+current_status+"&f="+rating_flag+"&m="+mapp+"&"+unique+"<?php echo $deptids ?>",
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				// suppress error, system checks in intervals
				return false ;
			}

			if ( json_data.status )
			{
				if ( rating_flag )
				{
					$('#rating_recent').html( stars[json_data.rt_r] ).unbind('click').bind('click', function() {
						if ( json_data.ces )
							open_transcript( json_data.ces ) ;
					});
					$('#rating_overall').html( stars[json_data.rt_o] ) ;

					$('#chats_today').html( json_data.c_t+" accepted" ) ;
					$('#chats_overall').html( json_data.c_o+" accepted" ) ;
				}
			}

			if ( parseInt( json_data.rst ) == 1 )
			{
				// restart requesting process (most likely comp went to idle/sleep mode)
				document.getElementById('iframe_chat_engine').contentWindow.restart_requesting() ;
			}

			if ( parseInt( json_data.signal ) == 1 )
			{
				rd = 1 ;
				toggle_status(3) ;
			}
			else if ( parseInt( json_data.signal ) == 4 )
			{
				mi = 1 ;
				toggle_status(3) ;
			}
			else if ( parseInt( json_data.signal ) == 3 )
			{
				dup = 1 ;
				toggle_status(3) ;
			}
			else if ( parseInt( json_data.signal ) == 2 )
			{
				if ( typeof( si_automatic_offline ) != "undefined" ) { clearInterval( si_automatic_offline ) ; }
				si_automatic_offline = setInterval(function(){
					if ( !total_chats() )
					{
						clearInterval( si_automatic_offline ) ; si_automatic_offline = undeefined ;
						logout_timer = 10 ; automatic_offline_active = 1 ;
						toggle_status(1) ;
					}
				}, 1000) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			// suppress error, system checks in intervals
		} });
	}

	function start_offline_timer( thestart )
	{
		tim_offline = thestart ;
		si_offline = setInterval( "offline_timer()", 1000 ) ;
	}

	function reset_offline_timer()
	{
		if ( typeof( si_offline ) != "undefined" )
		{
			clearInterval( si_offline ) ; si_offline = undeefined ;
			start_offline_timer( logout_timer*60 ) ;
		}
	}

	function offline_timer()
	{
		if ( tim_offline )
		{
			var mins = Math.floor( tim_offline/60 ) ;
			var secs = pad( tim_offline - ( mins * 60 ), 2 ) ;
			var display = mins+":"+secs ;

			$('#offline_timer').html( display ) ;
			--tim_offline ;
		}
		else if ( global_maxc_flag )
		{
			clearInterval( si_offline ) ; si_offline = undeefined ;
		}
		else if ( typeof ( si_offline ) != "undefined" )
		{
			if ( automatic_offline_active ) { ao = 1 ; }
			clearInterval( si_offline ) ; si_offline = undeefined ;
			toggle_status(3) ;
		}
	}

	function launch_settings( themenu )
	{
		$('#chat_footer').animate({
			bottom: "-50"
		}, 500, function() {
			toggle_extra( "settings", "", "", "Settings", themenu ) ;
		});
	}

	function open_transcript( theces )
	{
		var url = "<?php echo $CONF["BASE_URL"] ?>/ops/op_trans_view.php?ses=<?php echo $ses ?>&ces="+theces+"&id="+isop+"&wp="+wp+"&auth=op&"+unixtime()+"&" ;

		if ( !wp )
			window.open( url, "Transcript_"+theces, "scrollbars=yes,menubar=no,resizable=1,location=no,width=<?php echo $VARS_CHAT_WIDTH+50 ?>,height=<?php echo $VARS_CHAT_HEIGHT ?>,status=0" ) ;
		else
			wp_new_win( url, "Transcript_"+theces, <?php echo $VARS_CHAT_WIDTH+50 ?>, <?php echo $VARS_CHAT_HEIGHT ?> ) ;

		if ( extra == "traffic" )
			document.getElementById('iframe_'+extra).contentWindow.set_trans_img( theces ) ;
	}

	function update_traffic_counter( thecounter )
	{
		if ( !mapp && ( prev_traffic != thecounter ) && ( extra == "traffic" ) && ( typeof( document.getElementById('iframe_traffic').contentWindow.loaded ) != "undefined" ) ) { reload_traffic("iframe_traffic") ; }
		else if ( mapp && ( prev_traffic != thecounter ) && ( extra == "mapp_traffic" ) && ( typeof( document.getElementById('iframe_mapp_traffic').contentWindow.loaded ) != "undefined" ) ) { reload_traffic("iframe_mapp_traffic") ; }

		if ( prev_traffic != thecounter )
		{
			$('#chat_footer_traffic_counter').empty().html( thecounter ) ;
			if ( mapp ) { $('#chat_footer_traffic_counter_mapp').empty().html( thecounter ) ; }
			if ( thecounter && traffic_sound )
				play_sound( 0, "new_traffic", "new_traffic" ) ;
		}

		if ( wp )
			wp_total_visitors( thecounter )

		prev_traffic = thecounter ;
	}

	function reload_traffic( theiframe )
	{
		document.getElementById(theiframe).contentWindow.populate_traffic() ;
	}

	function toggle_traffic_sound()
	{
		if ( traffic_sound )
		{
			traffic_sound = 0 ;
			clear_sound( "new_traffic" ) ;
		}
		else
		{
			traffic_sound = 1 ;
			play_sound( 0, "new_traffic", "new_traffic" ) ;
		}

		print_traffic_sound_text() ;
	}

	function print_traffic_sound_text()
	{
		if ( traffic_sound )
			$('#div_traffic_sound').empty().html( "<img src=\"../themes/<?php echo $theme ?>/bell_start.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\"> traffic sound is ON" ).removeClass('sound_box_off').addClass('sound_box_on') ;
		else
			$('#div_traffic_sound').empty().html( "<img src=\"../themes/<?php echo $theme ?>/bell_stop.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">  traffic sound is OFF" ).removeClass('sound_box_on').addClass('sound_box_off') ;
	}

	function reload_console( theflag )
	{
		var unique = unixtime() ;
		var open_status = ( parseInt( current_status ) ) ? 0 : 1 ;

		window.onbeforeunload = null ;
		location.href = base_url_full+"/ops/operator.php?reload=1&ses=<?php echo $ses ?>&wp=<?php echo $wp ?>&open_status="+open_status+"&auto=<?php echo $auto ?>&mapp="+mapp+"&nalert="+theflag+"&"+unique+"&" ;
	}

	function reconnect()
	{
		++reconnect_counter ;
		if ( reconnect_counter == 1 ) { $('#idle_timer_notice').hide() ; for ( var thisces in chats ) { idle_reset( thisces ) ; } }

		// ~5 minutes to try to reconnect
		if ( reconnect_counter > 15 )
		{
			document.getElementById('iframe_chat_engine').contentWindow.stopit(0) ;
			var a_message = ( wp ) ? "Please exit WinApp and try again." : "Please try refreshing this window." ;
			a_message = ( mapp ) ? "<button type=\"button\" class=\"\" onClick=\"reconnect_mapp()\" id=\"btn_reconnect\">Try to Reconnect</button>" : a_message ;
			$('#reconnect_status').empty().html("<img src=\"../themes/<?php echo $theme ?>/alert.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\"> Could not reconnect.  "+a_message) ;

			if ( chat_sound )
				play_sound( 1, "new_request", "new_request_<?php echo $opinfo["sound1"] ?>" ) ;

			if ( dn_enabled_request )
				dn_show( 'new_chat', "null", "System Alert", "Operator console could not establish a connection to the server.", 45000 ) ;
			flash_console(0) ;
			title_blink_init() ;
			if ( !mapp )
			{
				open_network_status() ;
				toggle_network_infolog() ;
			}
		}
		else
		{
			if ( reconnect_counter == 1 ) { update_network_log( "<tr id='div_network_his_"+network_counter+"' style='display: none'><td class='chat_info_td'>disconnected</td><td class='chat_info_td'>&nbsp;</td><td class='chat_info_td'>disconnected</td></tr>" ) ; }

			document.getElementById('iframe_chat_engine').contentWindow.stopit(1) ;

			if ( mapp && ( reconnect_counter < 2 ) )
			{
				// for Mobile Apps, some devices pauses network at pause/resume.  add some buffer so the disconnect message is only
				// displayed on actual network disconnect
			}
			else{ $('#reconnect_notice').show() ; }
			$('#reconnect_attempt').empty().html( "&bull; reconnect attempt: "+reconnect_counter ) ;

			// only need to call requesting() as the function restarts chatting()
			document.getElementById('iframe_chat_engine').contentWindow.requesting() ;
		}
	}

	function reconnect_success()
	{
		reconnect_counter = 0 ;
		if ( $('#reconnect_notice').is(':visible') )
		{
			var localtime = new Date() ;
			toggle_status( prev_status ) ;

			$('#reconnect_notice').hide() ;
			$('#chat_info_network_log tbody tr:nth-child(2)').after( "<td class='chat_info_td'>"+localtime.toLocaleString()+"</td><td class='chat_info_td'></td><td class='chat_info_td'><img src='../themes/<?php echo $theme ?>/online_green.png' width='10' height='10' border=0> reconnected</td>" ) ;
		}
	}

	function toggle_network_infolog()
	{
		if ( $('#chat_info_wrapper_network_info').is(':visible') )
		{
			$('#chat_info_wrapper_network_info').hide() ;
			$('#chat_info_wrapper_network_log').show() ;
		}
		else
		{
			$('#chat_info_wrapper_network_log').hide() ;
			$('#chat_info_wrapper_network_info').show() ;
		}
	}

	function expand_map( theleft, themd5, theip )
	{
		for ( var thismd5 in maps_his_ )
			$('#info_maps_footprint_'+thismd5).hide() ;

		if ( !<?php echo $geoip ?> )
			return true ;

		var unique ; // indication of first load of the map
		if ( typeof( maps_his_[themd5] ) == "undefined" )
		{
			unique = unixtime() ;
			var iframe_map = document.createElement( "iframe" ); 
			iframe_map.setAttribute( "src", "./maps.php?ses=<?php echo $ses ?>&ip="+theip+"&vis_token="+themd5+"&viewip="+viewip+"&"+unique+"&" ) ; 
			iframe_map.style.display = "none" ;
			iframe_map.border = 0 ;
			iframe_map.frameBorder = 0 ;
			iframe_map.setAttribute( "id", "info_maps_footprint_"+themd5 ) ;
			maps_his_[themd5] = iframe_map ;

			$('#info_maps_').append( iframe_map ) ;
			$("#info_maps_footprint_"+themd5).css({"width": "100%", "overflow": "hidden"}).fadeIn(500) ;
		}
		else
			$('#info_maps_footprint_'+themd5).fadeIn(500) ;

		var pos = $('#chat_extra_wrapper').position() ;
		var top = pos.top + 69 ;
		var width = parseInt( $('#chat_extra_wrapper').width() ) - theleft - 20 ;
		var height = parseInt( $('#chat_extra_wrapper').height() ) - 89 ;
		width = ( width > 815 ) ? 815 : width ;
		$('#div_geomap').css({'top': top, 'left': theleft, 'width': width, 'height': height}).show() ;

		var height_ = height - 25 ;
		$("#info_maps_footprint_"+themd5).css({"height": height_}) ;

		if ( typeof( unique ) == "undefined" )
			document.getElementById('info_maps_footprint_'+themd5).contentWindow.adjust_height() ;
	}

	function delete_object( thetype, themd5 )
	{
		if ( thetype == "map" )
		{
			if ( typeof( maps_his_[themd5] ) != "undefined" )
			{
				delete maps_his_[themd5] ;
				$('#info_maps_footprint_'+themd5).remove() ;
			}
		}
		else if ( thetype == "traffic" )
		{
			if ( typeof( traffic_data[themd5] ) != "undefined" )
				delete traffic_data[themd5] ;
		}
	}

	function open_network_status()
	{
		if ( $('#chat_info_wrapper_network').is(':visible') )
		{
			$('#chat_info_wrapper_network').hide() ;
			$('#chat_info_wrapper_info').show() ;
		}
		else
		{
			$('#chat_info_wrapper_info').hide() ;
			$('#chat_info_wrapper_network').show() ;

			if ( !$('#chat_info_container').is(':visible') ) { toggle_slider(0) ; }
		}
	}

	function toggle_slider( theforce )
	{
		var browser_width = $(window).width() ;

		if ( $('#chat_info_container').is(':visible') || theforce )
		{
			var chat_body_width = browser_width - ( 35 + ( parseInt( $('#chat_body').css('padding-left').replace( /px/, "" ) ) * 2 ) ) ;

			$('#chat_info_container').hide() ;
			$('#chat_body').css({'width': chat_body_width}) ;
			$('#icons_slider').attr( 'src', '../themes/<?php echo $theme ?>/slider_left.png' ) ;
			$('#chat_info_wrapper_network').hide() ;
		}
		else
		{
			var chat_body_padding = $('#chat_body').css('padding-left') ;
			var chat_body_padding_diff = ( typeof( chat_body_padding ) != "undefined" ) ? 20 - ( chat_body_padding.replace( /px/, "" ) * 2 ) : 0 ;
			var body_width = browser_width - 450 ;
			var chat_body_width = body_width + chat_body_padding_diff ;

			$('#chat_body').css({'width': chat_body_width}) ;
			$('#chat_info_container').show() ;
			$('#icons_slider').attr( 'src', '../themes/<?php echo $theme ?>/slider_right.png' ) ;

			if ( !$('#chat_info_wrapper_info').is(':visible') )
				$('#chat_info_wrapper_network').show() ;
		}
		init_scrolling() ;
	}

	function process_shortcuts( thetext )
	{
		if ( ( thetext == "/t" ) || ( thetext == "/n" ) ) { $('textarea#input_text').val( "" ) ; activate_chat( get_chat_next( ces ) ) ; }
		else if ( ( thetext == "/exit" ) || ( thetext == "/close" ) ) { pre_disconnect(); }
		$('textarea#input_text').val( "" ) ;
	}

	function toggle_last_response( theforce_close )
	{
		if ( $('#div_last_response').is(':visible') || theforce_close )
		{
			$('#div_last_response').hide() ;
		}
		else
		{
			if ( typeof( chats[ces] ) != "undefined" )
			{
				if ( chats[ces]["disconnected"] )
				{
					do_alert( 0, "Chat session has ended." ) ;
				}
				else
				{
					var diff = unixtime() - chats[ces]["recent_res"] ;
					var mins =  Math.floor( diff/60 ) ; if ( !mins ) { mins = 1 ; }

					var pos_options = $('#chat_options').position() ;
					var pos_chat = $('#chat_body').position() ;
					var top = pos_options.top - 170 ;
					var left = pos_chat.left + 25 ;

					$('#last_response_timer_vname').html( chats[ces]["vname"] ) ;
					$('#last_response_timer').html( mins ) ;
					$('#div_last_response').css({'top': top, 'left': left}).show() ;
				}
			}
		}
	}
//-->
</script>
</head>
<body style="display: none;">

<div id="chat_canvas" style="min-height: 100%; width: 100%;" class="chat_canvas_op">
	<div id="chat_switchboard" style="position: relative; height: 19px; padding-left: 10px; z-Index: 103;"></div>
</div>
<div style="position: absolute; top: 20px; padding: 10px; z-Index: 2;" onClick="clear_flash_console();input_focus();dn_close();<?php if ( !$mapp ): ?>close_extra( extra );<?php endif ; ?>">
	<div id="chat_body" style="overflow: auto;<?php echo ( $mobile || $mapp ) ? " padding: 5px;": "" ; ?>" onClick="close_misc()"></div>
	<div id="chat_options" style="padding: 5px;">
		<div style="height: 16px;">
			<div id="options_settings" style="float: left; cursor: pointer;" onClick="launch_settings('themes')" id="chat_settings"><img src="../themes/<?php echo $theme ?>/vcard.png" width="16" height="16" border="0" alt=""> <span style="position: relative; top: -2px;">settings</span></div>
			<div id="options_sound" style="float: left; padding-left: 15px;"><img src="../themes/<?php echo $theme ?>/sound_on.png" width="16" height="16" border="0" alt="" onClick="launch_settings('sounds')" id="chat_sound" title="toggle sound" alt="toggle sound" style="cursor: pointer;"></div>
			<div id="options_expand" style="float: left; padding-left: 15px;"><img src="../themes/<?php echo $theme ?>/slider_v.png" width="16" height="16" border="0" alt="" onClick="toggle_input_text()" id="chat_sound" title="toggle sound" alt="toggle sound" style="cursor: pointer;"></div>
			<div id="options_print" style="display: none; float: left;">
				<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) ; } ?>
				<span style="padding-left: 15px;" id="chat_printer"><img src="../themes/<?php echo $theme ?>/printer.png" width="16" height="16" border="0" alt="" onClick="do_print(ces, 0, isop, <?php echo $VARS_CHAT_WIDTH ?>, <?php echo $VARS_CHAT_HEIGHT ?>)" title="print transcript" alt="print transcript" style="cursor: pointer;"></span>
				<span id="chat_vtimer_wrapper" style="position: relative; top: -2px; padding-left: 15px;"><input type="text" style="text-align: center; font-weight: normal; cursor: pointer;" value="00:00" id="chat_vtimer" size=8 maxlength=10 readonly class="input_timer" onClick="toggle_last_response(0)"></span>
				<span id="options_mapp_vinfo" style="position: relative; display: none; padding-left: 15px; cursor: pointer;" onClick="toggle_extra( 'mapp_vinfo', '', '', 'Visitor Info' )"><img src="../themes/<?php echo $theme ?>/info.png" width="12" height="12" border="0" alt=""> visitor info</span>
				<span id="chat_processing" style="display: none; padding-left: 15px;"><img src="../themes/<?php echo $theme ?>/loading_chat.gif" width="16" height="16" border="0" alt="loading..." title="loading..."></span>
				<span id="chat_vname" style="position: relative; top: -2px; padding-left: 15px;"></span>
				<span id="chat_vistyping" style="display: none; position: relative; top: -2px;">&nbsp;<?php echo ( $mapp ) ? "typing..." : "is typing..." ?></span>
			</div>
			<div id="options_expand" style="float: right"><span style="cursor: pointer;"><img src="../themes/<?php echo $theme ?>/slider_right.png" width="16" height="16" border="0" onClick="toggle_slider(0)" id="icons_slider" name="icons_slider"></span></div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<div id="chat_input" style="margin-top: 8px;">
		<div style="display: none; float: left; width: 65px; cursor: pointer;" id="div_profile_pic" onClick="launch_settings('settings')"></div>
		<div style="float: left;" id="div_input_text" onClick="clear_flash_console()"><textarea id="input_text" rows="3" style="padding: 2px; height: 75px; resize: none;" wrap="virtual" onFocus="dn_close();" onKeyup="input_text_listen(event);" onKeydown="input_text_typing(event);" disabled="disabled"></textarea></div>
		<div style="clear:both;"></div>
	</div>
</div>
<div id="chat_data" style="position: absolute; overflow: hidden;">
	<div class="chat_info_wrapper" style="margin-right: 8px;">
		
		<div id="chat_info_container">
			<div id="chat_info_header" style="margin-bottom: 5px;">
				<?php if ( $opinfo["rate"] ): ?>
				<div style="float: left; margin-right: 25px; ">
					<div class="rating_title">recent rating:</div>
					<div id="rating_recent" style="cursor: pointer"></div>
				</div>
				<div style="float: left; margin-right: 25px;">
					<div class="rating_title">overall rating:</div>
					<div id="rating_overall"></div>
				</div>
				<?php endif ; ?>
				<div style="float: left; margin-right: 10px;">
					<div class="rating_title">chats today:<div id="chats_today" style=""></div></div>
				</div>
				<div style="float: left;">
					<div class="rating_title">chats overall:<div id="chats_overall" style=""></div></div>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div id="chat_info_wrapper_info">
				<div id="chat_info_menu_list">
					<div id="info_menu_info" class="chat_info_menu" onClick="toggle_info('info',1)">Visitor Info</div>
					<div id="info_menu_maps" class="chat_info_menu" onClick="toggle_info('maps',1)" style="display: none;">Location</div>
					<?php if ( $CONF["foot_log"] == "on" ): ?><div id="info_menu_footprints" class="chat_info_menu" onClick="toggle_info('footprints',1)" style="display: none;">Footprints</div><?php endif ; ?>
					<div id="info_menu_transcripts" class="chat_info_menu" onClick="toggle_info('transcripts',1)" style="display: none;">Transcripts</div>
					<div id="info_menu_transfer" class="chat_info_menu" onClick="toggle_info('transfer',1)" style="display: none;">Transfer</div>
					<div id="info_menu_spam" class="chat_info_menu" onClick="toggle_info('spam',1)" style="display: none; margin-right: 0px;">Block</div>
					<div style="clear: both"></div>
				</div>
				<div id="chat_info_body" style="overflow: auto;">
					<div id="info_info" style="display: none; padding: 10px; text-align: justify;">
						<div id="info_info_body" style="">
							<table cellspacing=0 cellpadding=0 border=0>
							<tr><td class="chat_info_td_h" style="opacity: 0.5; filter: alpha(opacity=50);">Department</td><td width="100%" class="chat_info_td" style="opacity: 0.5; filter: alpha(opacity=50);"> <span id="req_dept"></span></td></tr>
							<tr><td class="chat_info_td_h" nowrap>Visitor Email</td><td class="chat_info_td"> <span id="req_email"></span></td></tr>
							<tr><td class="chat_info_td_h" nowrap>Chat Request</td><td class="chat_info_td"> <span id="req_request"></span></td></tr>
							<tr><td class="chat_info_td_h" nowrap>Clicked From</td><td class="chat_info_td"> <span id="req_onpage"></span></td></tr>
							<tr><td class="chat_info_td_h">Refer URL</td><td class="chat_info_td"> <span id="req_refer"></span></td></tr>
							<tr><td class="chat_info_td_h">Marketing</td><td class="chat_info_td"> <span id="req_market"></span></td></tr>
							<tr><td nowrap class="chat_info_td_h">Resolution</td><td class="chat_info_td"> <span id="req_resolution"></span></td></tr>
							<?php if ( $opinfo["viewip"] ): ?><tr><td nowrap class="chat_info_td_h" nowrap>IP Address</td><td class="chat_info_td"> <span id="req_ip"></span></td></tr><?php endif ; ?>
							<tr><td nowrap class="chat_info_td_h" nowrap>Custom Vars</td><td class="chat_info_td"><div id="req_custom" style="max-height: 80px; overflow-y: auto; overflow-x: hidden;"></div></td></tr>
							<tr><td class="chat_info_td_h" style="opacity: 0.5; filter: alpha(opacity=50);">Chat ID</td><td class="chat_info_td" style="opacity: 0.5; filter: alpha(opacity=50);"> <span id="req_ces"></span> &nbsp; <span id="req_t_ses" style="display: none;"></span></td></tr>
							</table>
						</div>
					</div>
					<div id="info_maps" style="display: none; padding: 10px;"></div>
					<div id="info_footprints" style="display: none; padding: 10px;"><img src="../themes/<?php echo $theme ?>/loading_fb.gif" border="0" alt=""></div>
					<div id="info_transcripts" style="display: none; padding: 10px; overflow-x: hidden;"><img src="../themes/<?php echo $theme ?>/loading_fb.gif" border="0" alt=""></div>
					<div id="info_transfer" style="display: none; padding: 10px;"><img src="../themes/<?php echo $theme ?>/loading_fb.gif" border="0" alt=""></div>
					<div id="info_spam" style="display: none; padding: 10px;"></div>
				</div>
			</div>
		</div>
		<div id="chat_info_wrapper_network" style="display: none; overflow: auto; overflow-x: hidden;" class="info_content">
			<div id="chat_info_wrapper_network_info">
				<table cellspacing=0 cellpadding=2 border=0 width="100%" id="chat_info_network_info">
				<tbody>
				<tr><td colspan=3 class="chat_info_td"><div class="info_box">
					<table cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td width="285">Network Status | <span onClick="toggle_network_infolog()" style="text-decoration: underline; cursor: pointer;">Log</span></td>
						<td><button type="button" style="" onClick="open_network_status()">close</button></td>
					</tr>
					</table>
				</div></td></tr>
				<tr>
					<td class="chat_info_td_h" width="37%" nowrap><b>Network Speed</b><div style="font-size: 10px;">(seconds)</div></td>
					<td class="chat_info_td_h" width="37%" nowrap><b>Server Response</b><div style="font-size: 10px;">(seconds)</div></td>
					<td class="chat_info_td_h" width="24%" nowrap><b>Total</b><div style="font-size: 10px;">(seconds)</div></td>
				</tr>
				</tbody>
				</table>
			</div>
			<div id="chat_info_wrapper_network_log" style="display: none;">
				<table cellspacing=0 cellpadding=2 border=0 width="100%" id="chat_info_network_log">
				<tbody>
				<tr><td colspan=3 class="chat_info_td"><div class="info_box">
					<table cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td width="285"><span onClick="toggle_network_infolog()" style="text-decoration: underline; cursor: pointer;">Network Status</span> | Log</td>
						<td><button type="button" style="" onClick="open_network_status()">close</button></td>
					</tr>
					</table>
				</div></td></tr>
				<tr>
					<td class="chat_info_td_h" width="37%"><b>Time</b></td>
					<td class="chat_info_td_h" width="37%"><b>&nbsp;</b></td>
					<td class="chat_info_td_h" width="24%"><b>Error Code</b></td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
		<div id="sounds" style="width: 1px; height: 1px; overflow: hidden; opacity:0.0; filter:alpha(opacity=0);">
			<span id="div_sounds_new_request"></span>
			<span id="div_sounds_new_text"></span>
			<span id="div_sounds_new_traffic"></span>
			<span id="div_sounds_new_liner"></span>
			<audio id='div_sounds_audio_new_request'></audio>
			<audio id='div_sounds_audio_new_text'></audio>
			<audio id='div_sounds_audio_new_traffic'></audio>
			<audio id='div_sounds_audio_new_liner'></audio>
		</div>
	</div>
</div>
<div id="chat_btn" style="position: absolute; padding-right: 10px; z-Index: 10"><button id="input_btn" type="button" OnClick="add_text_prepare(1)" disabled="disabled">Submit</button><div style="margin-top: 5px; font-size: 10px;" id="chat_text_login">chat operator:<div><?php echo $opinfo["login"] ?></div></div></div>

<div id="chat_panel" style="position: absolute; z-Index: 10">
	<div id="chat_status" style="float: left; height: 75px; padding-left: 10px;">
		Status
		<div id="chat_status_status" style="margin-top: 5px;">
			<form>
			<table cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td><div style="padding: 4px; cursor: pointer;" onClick="toggle_status(0);"><input type="radio" name="status" id="status_0" value=0 checked> online </div></td>
				<td style="padding-left: 5px;"><div style="padding: 4px; cursor: pointer;" onClick="toggle_status(1);" id="chat_status_offline_radio" class="round"><input type="radio" name="status" id="status_1" value=1> offline </div></td>
				<td style="padding-left: 5px;"><div style="padding: 4px; cursor: pointer;" onClick="toggle_status(2);"><input type="radio" name="status" id="status_2" value=2> logout </div></td>
			</tr>
			</table>
			</form>
		</div>
	</div>
	<div id="chat_network" style="float: left; height: 75px; padding-left: 15px;">
		Network
		<div id="chat_network_img" style="margin-top: 5px; width: 50px; height: 38px; cursor: pointer;" title="network status" alt="network status" onClick="open_network_status()"></div>
	</div>
	<div style="clear: both;"></div>
</div>
<div id="chat_status_offline" style="position: absolute; display: none; padding: 5px; width: 235px; height: 80px; z-Index: 98;">
	<div id="chat_status_offline_text" style="padding: 2px; font-weight: bold;" class="round">OFFLINE</div>
	<div style="margin-top: 5px;">
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td><div id="offline_timer_description" style="text-align: justify;">automatically log out in: &nbsp; </div></td>
			<td width="50" id="offline_timer" style="font-family: Impact, Serif; font-size: 18px;"></td>
			<td><div id="offline_timer_btn"><form><button type="button" style="font-size: 10px;" onClick="reset_offline_timer();">Reset</button></form></div></td>
		</tr>
		</table>
	</div>
</div>

<div id="chat_footer" style="position: absolute; width: 100%; bottom: 0px; height: 34px; z-Index: 102;">

	<?php if ( $opinfo["op2op"] ): ?>
	<div class="chat_footer_cell_noclick"><img src="../themes/<?php echo $theme ?>/divider.png" border="0" alt=""></div>
	<div id="chat_footer_cell_op2op" class="chat_footer_cell" onClick="toggle_extra( 'op2op', '', '', 'Operators' )">Operators</div>
	<?php endif ; ?>

	<div class="chat_footer_cell_noclick"><img src="../themes/<?php echo $theme ?>/divider.png" border="0" alt=""></div>
	<div id="chat_footer_cell_canned" class="chat_footer_cell" onClick="toggle_extra( 'canned', '', '', 'Create/Edit Canned' )">Canned Responses</div>
	<div class="chat_footer_cell_noclick" style="padding-top: 1px;"><span id="chat_cans_select"></span> <button type="button" id="canned_select_btn" onClick="select_canned()">select</button></div>

	<div class="chat_footer_cell_noclick"><img src="../themes/<?php echo $theme ?>/divider.png" border="0" alt=""></div>
	<div id="chat_footer_cell_trans" class="chat_footer_cell" onClick="toggle_extra( 'trans', '', '', 'Transcripts' )">Transcripts</div>

	<?php if ( $opinfo["traffic"] && ( $CONF['icon_check'] == "on" ) ): ?>
	<div class="chat_footer_cell_noclick"><img src="../themes/<?php echo $theme ?>/divider.png" border="0" alt=""></div>
	<div id="chat_footer_cell_traffic" class="chat_footer_cell" onClick="toggle_extra( 'traffic', '', '', 'Traffic Monitor' )">Traffic Monitor <span id="chat_footer_traffic_counter">00</span></div>
	<?php endif; ?>

	<?php
		for ( $c = 0; $c < count( $externals ); ++$c )
		{
			$external = $externals[$c] ;

			print "
				<div class=\"chat_footer_cell_noclick\"><img src=\"../themes/$theme/divider.png\" border=\"0\" alt=\"\"></div>
				<div id=\"chat_footer_cell_ext_$external[extID]\" class=\"chat_footer_cell\" onClick=\"toggle_extra( $external[extID], '', '$external[url]', '$external[name]' )\">$external[name]</div>
			" ;
		}
	?>

	<div class="chat_footer_cell_noclick"><img src="../themes/<?php echo $theme ?>/divider.png" border="0" alt=""></div>
	<div style="clear: both;"></div>
</div>
<div id="chat_extra_wrapper" style="position: absolute; display: none; margin-top: 30px; width: 100%; overflow: auto; z-Index: 99;">
	<div id="chat_extra_title" style="font-size: 16px; font-weight: bold; padding: 2px; padding-left: 10px;"></div>
	<div id="chat_extra_body_op2op" style="display: none;"><iframe id="iframe_op2op" name="iframe_op2op" style="width: 100%; border: 0px;" src="about:blank" scrolling="auto" border=0 frameborder=0></iframe></div>
	<div id="chat_extra_body_traffic" style="display: none;"><iframe id="iframe_traffic" name="iframe_traffic" style="width: 100%; border: 0px;" src="about:blank" scrolling="auto" border=0 frameborder=0></iframe></div>
	<div id="chat_extra_body_canned" style="display: none;"><iframe id="iframe_canned" name="iframe_canned" style="width: 100%; border: 0px;" src="about:blank" scrolling="auto" border=0 frameborder=0></iframe></div>
	<div id="chat_extra_body_trans" style="display: none;"><iframe id="iframe_trans" name="iframe_trans" style="width: 100%; border: 0px;" src="about:blank" scrolling="auto" border=0 frameborder=0></iframe></div>
	<div id="chat_extra_body_extras" style="display: none;"><iframe id="iframe_extras" name="iframe_extras" style="width: 100%; border: 0px;" src="about:blank" scrolling="auto" border=0 frameborder=0></iframe></div>
	<?php
		for ( $c = 0; $c < count( $externals ); ++$c )
		{
			$external = $externals[$c] ;
			print "<div id=\"chat_extra_body_ext_$external[extID]\" style=\"display: none;\"></div>" ;
		}
	?>
	<div id="chat_extra_body_settings" style="display: none;"><iframe id="iframe_settings" name="iframe_settings" style="width: 100%; border: 0px;" src="about:blank" scrolling="auto" border=0 frameborder=0></iframe></div>
	<?php if ( $mapp ) { include_once( "../mapp/mapp_iframes.php" ) ; } ?>
</div>
<div id="info_disconnect" class="info_disconnect" style="position: absolute; top: 1px; right: 0px; text-align: right; z-Index: 104;" onClick="pre_disconnect();"></div>
<div id="chat_status_logout" style="position: absolute; display: none; width: 100%; bottom: 0px; height: 80px; z-Index: 103;">
	<div id="chat_status_logout_confirm" style="position: absolute; bottom: 0px; right: 0px; padding-bottom: 15px; padding-right: 20px; ">
		<form>
		<table cellspacing=0 cellpadding=5 border=0>
		<tr>
			<td nowrap><div class="info_error"><img src="../themes/<?php echo $theme ?>/alert.png" width="16" height="16" border="0" alt=""> Really logout and go offline?  <button type="button" onClick="toggle_status(3)">Yes, Log Out.</button></div></td>
			<td><div style="padding-left: 15px;"> <button type="button" onClick="toggle_status(1000)">Cancel</button></div></td>
		</tr>
		</table>
		</form>
	</div>
</div>
<?php if ( $mapp ) { include_once( "../mapp/mapp_footer_menu.php" ) ; } ?>
<iframe id="iframe_chat_engine" name="iframe_chat_engine" style="display: none; position: absolute; width: 100%; border: 0px; bottom: -250px; height: 10px; z-Index: 110;" src="about:blank" scrolling="no" frameBorder="0"></iframe>

<div id="reconnect_notice" class="info_warning" style="display: none; position: absolute; z-Index: 1000;">
	<div id="reconnect_status">Operator console disconnected.  Reconnecting... <img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt=""></div>
	<div id="reconnect_attempt" style="margin-top: 15px;">&nbsp;</div>
</div>

<div id="div_geomap" class="info_neutral" style="display: none; position: absolute; z-Index: 1000;">
	<div class="info_error" style="text-align: center; cursor: pointer;" onClick="$('#div_geomap').hide();">close</div>
	<div id="info_maps_"></div>
</div>

<div id="idle_timer_notice" class="info_content" style="display: none; position: absolute; top: 40px; left: 25px; width: 310px; padding: 10px; z-Index: 10;">
	<div style="font-weight: bold; font-size: 14px;">Chat is idle.  Please send a response.</div>
	<div style="margin-top: 10px;">Chat session will close <span class="info_neutral" id="idle_countdown">60</span> seconds.</div>
</div>

<div style="display: none; position: absolute; top: 0px; left: 0px; padding: 45px; z-Index: 102; cursor: pointer;" class="info_box" id="div_last_response" onClick="toggle_last_response(0)">
	<div style="font-weight: bold; font-size: 14px;" id="last_response_timer_vname"></div>
	<div style="margin-top: 5px;">Last response sent <span id="last_response_timer"></span>&nbsp; minutes ago</div>
</div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>
