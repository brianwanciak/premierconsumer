<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "./web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/update.php" ) ;

	$height = Util_Format_Sanatize( Util_Format_GetVar( "height" ), "ln" ) ;
	$token = Util_Format_Sanatize( Util_Format_GetVar( "token" ), "ln" ) ;

	LIST( $ip, $vis_token ) = Util_IP_GetIP( $token ) ;
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;

	$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, "null", $vis_token ) ;
	if ( !isset( $requestinfo["ces"] ) )
	{
		database_mysql_close( $dbh ) ;
		HEADER( "location: blank.php" ) ;
		exit ;
	}

	$opinfo = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
	$deptinfo = Depts_get_DeptInfo( $dbh, $requestinfo["deptID"] ) ;

	$upload_dir = $CONF['CONF_ROOT'] ;
	$ces = $requestinfo["ces"] ;
	$theme = $CONF["THEME"] ;
	if ( $deptinfo["lang"] ) { $CONF["lang"] = $deptinfo["lang"] ; }

	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;

	// set log to indicate not picked up
	Chat_update_RequestLogValue( $dbh, $ces, "status", 0 ) ;
	Footprints_update_FootprintUniqueValue( $dbh, $vis_token, "chatting", 1 ) ;
	Footprints_update_FootprintUniqueValue( $dbh, $vis_token, "initiates", "initiates + 1" ) ;
?>
<?php include_once( "./inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta name="phplive" content="version: <?php echo $VERSION ?>">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>">
<?php include_once( "./inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="./themes/<?php echo $theme ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="./js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/modernizr.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var base_url = "." ; var base_url_full = "<?php echo $CONF["BASE_URL"] ?>" ;
	var isop = 0 ; var isop_ = <?php echo $requestinfo["opID"] ?> ; var isop__ = 0 ;
	var cname = "<?php echo $requestinfo["vname"] ?>" ;
	var ces = "<?php echo $requestinfo["ces"] ?>" ;
	var st_typing ;
	var chat_sound = 0 ; var console_blink_r = 0 ;
	var title_orig = document.title ;
	var loaded = 0 ;
	var focused = 1 ;
	var survey = "" ;
	var widget = 1 ; // used as logout flag and in p_engine.php to omit routing
	var embed = 0 ;
	var wp = 0 ;
	var mobile = <?php echo $mobile ?> ; var mapp = 0 ;
	var sound_new_text = "default" ;
	var theme = "<?php echo $theme ?>" ;
	var vclick = 0 ;
	
	var clicks = 0 ; // counter to track click to close the widget

	var chats = new Object ;
	chats[ces] = new Object ;

	var audio_supported = HTML5_audio_support() ;
	var mp3_support = ( typeof( audio_supported["mp3"] ) != "undefined" ) ? 1 : 0 ;

	$(document).ready(function()
	{
		loaded = 1 ;
		
		init_connect() ;

		$('#chat_body').css({'height': <?php echo $height ?>}).show() ;
	});
	$(window).resize(function() { init_divs(1) ; });

	function init_connect()
	{
		chats[ces] = new Object ;
		chats[ces]["requestid"] = <?php echo $requestinfo["requestID"] ?> ;
		chats[ces]["vname"] = cname ;
		chats[ces]["status"] = 1 ;
		chats[ces]["disconnected"] = 0 ;
		chats[ces]["tooslow"] = 0 ;
		chats[ces]["op2op"] = 0 ;
		chats[ces]["opid"] = <?php echo $requestinfo["opID"] ?> ;
		chats[ces]["deptid"] = <?php echo $requestinfo["deptID"] ?> ;
		chats[ces]["opid_orig"] = chats[ces]["opid"] ;
		chats[ces]["oname"] = "<?php echo $opinfo["name"] ?>" ;
		chats[ces]["mapp"] = <?php echo $opinfo["mapp"] ?> ;
		chats[ces]["ip"] = "<?php echo $requestinfo["ip"] ?>" ;
		chats[ces]["vis_token"] = "<?php echo $requestinfo["md5_vis"] ?>" ;
		chats[ces]["chatting"] = 0 ;
		chats[ces]["survey"] = 0 ;
		chats[ces]["timer"] = unixtime() ;
		chats[ces]["trans"] = "" 
		chats[ces]["t_ses"] = 1 ;
		chats[ces]["idle"] = 0 ;

		$('#chat_body').html( chats[ces]["trans"] ) ;
		$('#chat_vname').html( chats[ces]["oname"] ) ;
		$('textarea#input_text').val( "" ) ;

		$('#options_print').show() ;
	}

	function init_chats()
	{
	}

	function cleanup_disconnect( theces )
	{
		// nothing here for widget
	}

	function toggle_options( theflag )
	{
		return true ;
	}

	function leave_a_mesg(){}
//-->
</script>
</head>
<body>
<div onMouseOver="toggle_options(1)" onMouseOut="toggle_options(0)">
	<div id="chat_body" style="overflow-x: hidden; -moz-border-radius: 0px; border-radius: 0px;"></div>
	<div id="chat_widget_options" style="position: absolute; display: inline-block; bottom: 0px; margin-left: 1px; height: 25px;">
		<button type="button"><?php echo $LANG["CHAT_BTN_START_CHAT"] ?></button></td>
	</div>
	<span id="div_sounds_new_text"></span>
</div>
<iframe id="iframe_chat_engine" name="iframe_chat_engine" style="display: none; position: absolute; width: 100%; border: 0px; bottom: -100px; height: 20px;" src="ops/p_engine.php?ces=<?php echo $ces ?>" scrolling="no" frameBorder="0"></iframe>
</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>