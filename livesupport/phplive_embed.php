<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	if ( !is_file( "./web/config.php" ) ){ print "Error: Config file not found. [File: phplive_embed.php]" ; exit ; }
	include_once( "./web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	/* AUTO PATCH */
	if ( !is_file( "$CONF[CONF_ROOT]/patches/$patch_v" ) )
	{
		$query = ( isset( $_SERVER["QUERY_STRING"] ) ) ? $_SERVER["QUERY_STRING"] : "" ;
		HEADER( "location: patch.php?from=embed&".$query."&" ) ;
		exit ;
	}
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/get.php" ) ;

	$onpage = Util_Format_Sanatize( Util_Format_GetVar( "onpage" ), "url" ) ; $onpage = ( $onpage ) ? $onpage : "" ;
	$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "title" ) ; $title = ( $title ) ? $title : "" ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "n" ) ;
	$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;
	$widget = Util_Format_Sanatize( Util_Format_GetVar( "widget" ), "n" ) ;
	$js_name = Util_Format_Sanatize( Util_Format_GetVar( "js_name" ), "ln" ) ;
	$js_email = Util_Format_Sanatize( Util_Format_GetVar( "js_email" ), "e" ) ;
	$custom = Util_Format_Sanatize( Util_Format_GetVar( "custom" ), "url" ) ;
	$lang = Util_Format_Sanatize( Util_Format_GetVar( "lang" ), "ln" ) ;
	$marquee_test = Util_Format_ConvertQuotes( Util_Format_Sanatize( Util_Format_GetVar( "marquee_test" ), "notags" ) ) ;
	$dept_themes = ( isset( $VALS["THEMES"] ) ) ? unserialize( $VALS["THEMES"] ) : Array() ;
	if ( !$theme && isset( $dept_themes[$deptid] ) && $deptid ) { $theme = $dept_themes[$deptid] ; }
	else if ( !$theme ) { $theme = $CONF["THEME"] ; }
	else if ( $theme && !is_file( "$CONF[DOCUMENT_ROOT]/themes/$theme/style.css" ) ) { $theme = $CONF["THEME"] ; }

	$query = ( isset( $_SERVER["QUERY_STRING"] ) ) ? $_SERVER["QUERY_STRING"] : "" ;
	if ( !isset( $CONF["vsize"] ) ) { $width = $VARS_CHAT_WIDTH ; $height = $VARS_CHAT_HEIGHT ; }
	else { LIST( $width, $height ) = explode( "x", $CONF["vsize"] ) ; }

	$deptinfo = Array() ;
	if ( $deptid ) { $deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ; }

	if ( !isset( $CONF["lang"] ) ) { $CONF["lang"] = "english" ; }
	if ( isset( $deptinfo["lang"] ) ) { $CONF["lang"] = $deptinfo["lang"] ; }
	if ( $lang ) { $CONF["lang"] = $lang ; }

	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;
	$lang_db = Lang_get_Lang( $dbh, $CONF["lang"] ) ;
	if ( isset( $lang_db["lang"] ) )
	{
		$db_lang_hash = unserialize( $lang_db["lang_vars"] ) ;
		$LANG = array_merge( $LANG, $db_lang_hash ) ;
	}

	include_once( "$CONF[DOCUMENT_ROOT]/inc_cache.php" ) ;
?>
<?php include_once( "./inc_doctype.php" ) ?>
<!--
********************************************************************
* PHP Live! (c) OSI Codes Inc.
* www.phplivesupport.com
********************************************************************
-->
<head>
<title> - </title>
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="Stylesheet" href="./themes/<?php echo $theme ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="./js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/jquery_md5.js?<?php echo $VERSION ?>"></script>
<script language="JavaScript">
<!--
	"use strict" ;
	var status_minimize = 0 ;
	var status_maximize = 0 ;
	var status_popout = 0 ;
	var status_close = 0 ;
	var chat_connected = 0 ;
	var chat_disconnected = 0 ;

	var menu_height = 0 ;
	var iframe_height = 0 ;
	var win_minimized = 0 ;

	var phplive_mobile = 0 ;
	var phplive_userAgent = navigator.userAgent || navigator.vendor || window.opera ;
	if ( phplive_userAgent.match( /iPad/i ) || phplive_userAgent.match( /iPhone/i ) || phplive_userAgent.match( /iPod/i ) )
	{
		if ( phplive_userAgent.match( /iPad/i ) ) { phplive_mobile = 0 ; }
		else { phplive_mobile = 1 ; }
	}
	else if ( phplive_userAgent.match( /Android/i ) ) { phplive_mobile = 2 ; }

	var si_win_status ;
	var si_connected ;
	var si_disconnected ;
	var phplive_browser = navigator.appVersion ; var phplive_mime_types = "" ;
	var phplive_display_width = screen.availWidth ; var phplive_display_height = screen.availHeight ; var phplive_display_color = screen.colorDepth ; var phplive_timezone = new Date().getTimezoneOffset() ;
	if ( navigator.mimeTypes.length > 0 ) { for (var x=0; x < navigator.mimeTypes.length; x++) { phplive_mime_types += navigator.mimeTypes[x].description ; } }
	var phplive_browser_token = phplive_md5( phplive_display_width+phplive_display_height+phplive_display_color+phplive_timezone+phplive_browser+phplive_mime_types ) ;

	$(document).ready(function( )
	{
		var view_height = ( $(window).height( ) > $('body').height( ) ) ? $(window).height( ) : $('body').height( ) ;

		menu_height = $('#div_menu').outerHeight( ) ;
		iframe_height = view_height - menu_height ;
		if ( phplive_mobile )
			$('#td_popout').hide() ;

		si_connected = setInterval(function( ){
			if ( chat_connected == 1 )
			{
				toggle_show_close(0) ;
				clearInterval( si_connected ) ;
				start_disconnect_listner( ) ;
			}
		}, 80) ;
		start_status_listener( ) ;

		var location_string = window.location.href ;
		if ( location_string.match( /^https:/i ) ) { $('#img_lock').show( ) ; }

		$.ajaxSetup({
			beforeSend: function(jqXHR) {
				jqXHR.overrideMimeType( "text/html; charset=<?php echo $LANG["CHARSET"] ?>" ) ;
			}
		});
		create_iframe( "iframe_chat", "phplive.php?lang=<?php echo $lang ?>&embed=1&d=<?php echo $deptid ?>&marquee_test=<?php echo urlencode( $marquee_test ) ?>&onpage=<?php echo urlencode( Util_Format_URL( $onpage ) ) ?>&token="+phplive_browser_token+"&title=<?php echo urlencode( $title ) ?>&theme=<?php echo $theme ?>&js_name=<?php echo urlencode( $js_name ) ?>&js_email=<?php echo $js_email ?>&widget=<?php echo $widget ?>&custom=<?php echo urlencode( $custom ) ?>&" ) ;
	});

	function create_iframe( thename, theurl )
	{
		if ( document.getElementById(thename) ) { $('#'+thename).empty().remove() ; }
		var iframe_chat = document.createElement("iframe") ;
		iframe_chat.src = theurl ;
		iframe_chat.id = thename ; iframe_chat.name = thename ;
		iframe_chat.style.width = "100%" ;
		iframe_chat.style.height = iframe_height+"px" ;
		iframe_chat.style.border = 0 ;
		iframe_chat.scrolling = "no" ;
		iframe_chat.frameBorder = 0 ;
		$('#iframe_div').empty( ).html( iframe_chat ) ;
	}

	function start_disconnect_listner( )
	{
		si_disconnected = setInterval(function( ){
			if ( ( chat_disconnected == 1 ) && !win_minimized )
			{
				toggle_show_close(1) ;
				clearInterval( si_disconnected ) ;
				clearInterval( si_win_status ) ;
			}
		}, 90) ;
	}

	function start_status_listener( )
	{
		si_win_status = setInterval(function( ){
			var win_height = $('body').height( ) ;
			if ( win_height < 300 )
			{
				if ( !win_minimized )
				{
					if ( chat_connected ) { document.getElementById('iframe_chat').contentWindow.toggle_show_disconnect(0) ; }
					$('#menu_minimize').hide( ) ;
					$('#menu_popout').hide( ) ;
					$('#menu_close').hide( ) ;
					$('#menu_maximize').show( ) ;
				}
				win_minimized = 1 ;
			}
			else
			{
				if ( win_minimized )
				{
					if ( chat_connected && !chat_disconnected ) { document.getElementById('iframe_chat').contentWindow.toggle_show_disconnect(1) ; }
					else { $('#menu_close').show( ) ; }
					if ( typeof( document.getElementById('iframe_chat').contentWindow.clear_flash_console ) != "undefined" )
						document.getElementById('iframe_chat').contentWindow.clear_flash_console( ) ;
					document.getElementById('iframe_chat').contentWindow.init_scrolling() ;
					$('#menu_maximize').hide( ) ;
					$('#menu_popout').show( ) ;
					$('#menu_minimize').show( ) ;
				}
				win_minimized = 0 ;
			}
		}, 50) ;
	}

	function toggle_show_close( theflag )
	{
		if ( theflag )
			$('#menu_close').show( ) ;
		else
			$('#menu_close').hide( ) ;
	}

	function start_chat( theflag, thedeptid, theces )
	{
		var json_data = new Object ;
		var unique = unixtime( ) ;

		if ( theflag )
			create_iframe( "iframe_chat", "phplive_.php?deptid="+thedeptid+"&token="+phplive_browser_token+"&theme=<?php echo $theme ?>&ces="+theces+"&vname=null&vquestion=null&onpage=<?php echo urlencode( Util_Format_URL( $onpage ) ) ?>&"+unique ) ;
		else
		{
			var data_string = "postembed=1&" ;
			$("#iframe_chat").contents( ).find("#theform").find(':input').each(function( ){
				var thisvalue = $(this).val() ;
				if ( this.id == "onpage" ) { thisvalue = encodeURIComponent( thisvalue ) ; }
				else if ( this.id == "title" ) { thisvalue = encodeURIComponent( thisvalue ) ; }
				else if ( this.id == "custom" ) { thisvalue = encodeURIComponent( thisvalue ) ; }
				data_string += this.id+"="+thisvalue+"&" ;
			}) ;
			data_string += unique ;

			$.ajax({
			type: "POST",
			url: "./phplive_.php",
			data: data_string,
			success: function(data){
				try {
					eval(data) ;
				} catch(err) {
					do_alert( 0, err ) ;
					return false ;
				}

				if ( json_data.status )
				{
					create_iframe( "iframe_chat", "phplive_.php?embed=1&deptid="+json_data.deptid+"&token="+phplive_browser_token+"&theme=<?php echo $theme ?>&ces="+json_data.ces+"&vname=null&vquestion=null&onpage=<?php echo urlencode( Util_Format_URL( $onpage ) ) ?>&"+unique ) ;
				}
				else
					leave_a_message( decodeURIComponent( json_data.url_redirect ) ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error processing chat.  Please reload the page and try again." ) ;
			} });
		}
	}

	function leave_a_message( theurl )
	{
		create_iframe( "iframe_chat", theurl ) ;
	}

	function close_view() { } // dummy function needed for preview close
	function preview_text( thetxt_livechat, thetxt_header, thetxt_sub, thetxt_department, thetxt_select )
	{
		$('#chat_text_title').html( thetxt_livechat ) ;
		document.getElementById('iframe_chat').contentWindow.preview_text( thetxt_header, thetxt_sub, thetxt_department, thetxt_select ) ;
	}
//-->
</script>
</head>
<body>

<div id="div_menu" style="">
	<div id="chat_embed_header" style="width: <?php echo $VARS_CHAT_WIDTH_WIDGET ?>;">
		<table cellspacing=0 cellpadding=7 border=0 width="100%">
		<tr>
			<td style="width: 18px;"><img src="./themes/<?php echo $theme ?>/win_max.png?<?php echo $VERSION ?>" width='18' height='18' border='0' alt='' style='display: none; width: 18px; height: 18px;' id="menu_maximize"><img src="./themes/<?php echo $theme ?>/win_min.png?<?php echo $VERSION ?>" width='18' height='18' border='0' alt='' style='width: 18px; height: 18px;' id="menu_minimize"></td>
			<?php if ( !isset( $VALS["POPOUT"] ) || ( $VALS["POPOUT"] != "off" ) ): ?><td style="width: 18px;" id="td_popout"><img src="./themes/<?php echo $theme ?>/win_pop.png?<?php echo $VERSION ?>" width='18' height='18' border='0' alt='' style='width: 18px; height: 18px;' id="menu_popout"></td><?php endif ; ?>
			<td style="width: <?php echo ($VARS_CHAT_WIDTH_WIDGET-90) ?>px;">
				<span id="chat_embed_title" style="white-space: nowrap;"><img src="./themes/<?php echo $theme ?>/lock.png?<?php echo $VERSION ?>" width="14" height="14" border="0" alt="" id="img_lock" style="display: none;"> <span id="chat_text_title"><?php echo $LANG["TXT_LIVECHAT"] ?></span></div>
			</td>
			<td style="padding-right: 10px; width: 18px;"><img src='./themes/<?php echo $theme ?>/win_close.png?<?php echo $VERSION ?>' width='18' height='18' border='0' alt='' style='width: 18px; height: 18px;' id="menu_close"></td>
		</tr>
		</table>
	</div>
</div>
<div id="iframe_div"></div>

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>