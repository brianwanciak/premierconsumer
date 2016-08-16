<?php
	/************** DO NOT MODIFY BELOW */
	$var_microtime = ( function_exists( "gettimeofday" ) ) ? 1 : 0 ; $var_process_start = ( $var_microtime ) ? microtime(true) : time() ;
	$var_mem_start = ( function_exists( "memory_get_usage" ) ) ? memory_get_usage() : 0 ;
	if ( defined( 'API_Util_Vars' ) ) { return ; } define( 'API_Util_Vars', true ) ;
	$PHPLIVE_HOST = isset( $_SERVER["HTTP_HOST"] ) ? $_SERVER["HTTP_HOST"] : "unknown_host" ;
	$PHPLIVE_URI = isset( $_SERVER["REQUEST_URI"] ) ? $_SERVER["REQUEST_URI"] : "unknown_uri" ;
	$PHPLIVE_FULLURL = "$PHPLIVE_HOST/$PHPLIVE_URI" ;
	if ( isset( $CONF_EXTEND ) ) { $CONF['CONF_ROOT'] = "$CONF[DOCUMENT_ROOT]/web/$CONF_EXTEND" ; }
	else { $CONF['CONF_ROOT'] = "$CONF[DOCUMENT_ROOT]/web" ; }
	$CONF["UPLOAD_HTTP"] = "$CONF[BASE_URL]/web" ; $CONF["UPLOAD_DIR"] = $CONF['CONF_ROOT'] ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ; }
	include_once( "$CONF[CONF_ROOT]/vals.php" ) ; include_once( "$CONF[DOCUMENT_ROOT]/setup/KEY.php" ) ;
	if ( preg_match( "/patch\.php/", $PHPLIVE_URI ) ) { $VERSION = "PATCH_".time() ; } else { include_once( "$CONF[CONF_ROOT]/VERSION.php" ) ; }
	if ( is_file( "$CONF[CONF_ROOT]/system.lock" ) ) { HEADER( "location: $CONF[BASE_URL]/files/temp_offline.php?r=".preg_replace( "/[^phplive_embed]/i", "", $PHPLIVE_URI ) ) ; exit ; }
	$VARS_BROWSER = Array( 1=>"IE", 2=>"Firefox", 3=>"Chrome", 4=>"Safari", 5=>"Opera", 6=>"Other" ) ;
	$VARS_OS = Array( 1=>"Windows", 2=>"Mac", 3=>"Unix", 4=>"Other", 5=>"Mobile" ) ;
	$VARS_IP_CAPTURE = Array( "CF-Connecting-IP", "HTTP_X_FORWARDED_FOR", "REMOTE_ADDR" ) ;
	$geoip = ( isset( $CONF["geo"] ) && $CONF["geo"] ) ? 1 : 0 ;
	$geomap = ( isset( $CONF["geomap"] ) && ( strlen( $CONF["geomap"] ) == 39 ) ) ? 1 : 0 ; $geokey = ( $geomap ) ? $CONF["geomap"] : "" ;
	$patch_v = 140 ; $FAST_PATCH = 0 ; $VARS_JS_AUTOLINK_FILE = "min" ;
	/************** DO NOT MODIFY ABOVE */
	//
	// To change any of the below variables, create a new file API/Util_Extra.php (detailed at the end of this file)
	// Variable infomation located at: README/VARS.txt
	$CONF["CHAT_IO_DIR"] = "$CONF[CONF_ROOT]/chat_sessions" ;
	$CONF["TYPE_IO_DIR"] = "$CONF[CONF_ROOT]/chat_initiate" ;
	$VAR_DEBUG_OUT = 0 ;
	$VARS_SET_VERIFYPEER = 1 ;

	$VARS_JS_ROUTING = 3 ;
	$VARS_JS_REQUESTING = 3 ;
	$VARS_JS_FOOTPRINT_CHECK = 25 ;
	$VARS_JS_INVITE_CHECK = 13 ;
	$VARS_JS_FOOTPRINT_MAX_CYCLE = 100 ;
	$VARS_JS_RATING_FETCH = 25 ;
	$VARS_FOOTPRINT_U_EXPIRE = $VARS_JS_FOOTPRINT_CHECK * 2 ;
	$VARS_IP_LOG_EXPIRE = 30 ;
	$VARS_FOOTPRINT_STATS_EXPIRE = 15 ;
	$VARS_JS_OP_CONSOLE_TIMEOUT = 45 ;
	$VARS_CYCLE_VUPDATE = 10 ;
	$VARS_CYCLE_CLEAN = $VARS_JS_REQUESTING + 6 ;
	$VARS_EXPIRED_OPS = $VARS_CYCLE_CLEAN * 14 ;
	$VARS_EXPIRED_REQS = $VARS_CYCLE_VUPDATE * 33 ;
	$VARS_EXPIRED_OP2OP = $VARS_EXPIRED_REQS ;
	$VARS_EXPIRED_TFOOT = 90 ;
	$VARS_TRANSFER_BACK = 45 ;
	$VARS_SMS_BUFFER = 120 ;
	$VARS_MOBILE_CHAT_BUFFER = 300 ;
	$VARS_MAIL_SEND_BUFFER = 1 ;
	$VARS_MAX_EMBED_SESSIONS = 3 ;
	$VARS_MAX_IP_CHAT_REQUESTS = 5 ;
	$VARS_CHAT_WIDTH = 600 ;
	$VARS_CHAT_HEIGHT = 560 ;
	$VARS_CHAT_WIDTH_WIDGET = 420 ;
	$VARS_CHAT_HEIGHT_WIDGET = 520 ;
	$VARS_MYSQL_FREE_RESULTS = 0 ;
	$VARS_CHAT_WIDGET_SHADOW = 1 ;
	$VARS_MAX_CHAT_FILESIZE = 500000 ;

	/*****************************************************************************/
	/* To change a variable from above, create a new file Util_Extra.php within the directory phplive/API/ and place the variable changes there.
	// --- phplive/API/Util_Extra.php ---
	//
	// example:
	//	to change the variable $VARS_SMS_BUFFER, place the variable in the API/Util_Extra.php
	//	with your new value.  if new variables or values are introduced, your changes will not revert
	//	to the default values. */
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Extra.php" ) ; }
?>