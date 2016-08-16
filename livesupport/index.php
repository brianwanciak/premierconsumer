<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	if ( !is_file( "./web/config.php" ) ){ HEADER("location: setup/install.php") ; exit ; }
	include_once( "./web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ; 
	/* AUTO PATCH */
	$query = ( isset( $_SERVER["QUERY_STRING"] ) ) ? $_SERVER["QUERY_STRING"] : "" ;
	if ( !is_file( "$CONF[CONF_ROOT]/patches/$patch_v" ) )
	{
		HEADER( "location: patch.php?from=index&".$query ) ;
		exit ;
	}
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;
	/////////////////////////////////////////////
	if ( defined( "LANG_CHAT_WELCOME" ) || !isset( $LANG["CHAT_JS_CUSTOM_BLANK"] ) )
		ErrorHandler( 611, "Update to your custom language file is required ($CONF[lang]).  Copy an existing language file and create a new custom language file.", $PHPLIVE_FULLURL, 0, Array() ) ;
	$perm_web = is_writable( "$CONF[CONF_ROOT]" ) ; $perm_conf = is_writeable( "$CONF[CONF_ROOT]/config.php" ) ; $perm_chats = is_writeable( $CONF["CHAT_IO_DIR"] ) ; $perm_initiate = is_writeable( $CONF["TYPE_IO_DIR"] ) ; $perm_patches = is_writeable( "$CONF[CONF_ROOT]/patches" ) ;
	if ( !$perm_web || !$perm_conf || !$perm_chats || !$perm_initiate || !$perm_patches )
		ErrorHandler( 609, "Crucial files or directories are not writeable by the system.  Permission denied.", $PHPLIVE_FULLURL, 0, Array() ) ;

	$base_url = $CONF["BASE_URL"] ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$login = Util_Format_Sanatize( Util_Format_GetVar( "phplive_login" ), "e" ) ;
	$password = Util_Format_Sanatize( Util_Format_GetVar( "phplive_password" ), "ln" ) ;
	$from = Util_Format_Sanatize( Util_Format_GetVar( "from" ), "ln" ) ;
	$wp = ( Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) : 0 ;
	$auto = Util_Format_Sanatize( Util_Format_GetVar( "auto" ), "n" ) ;  if ( !$auto && $wp ) { $auto = 1 ; }
	$mapp = Util_Format_Sanatize( Util_Format_GetVar( "mapp" ), "n" ) ;
	$platform = Util_Format_Sanatize( Util_Format_GetVar( "platform" ), "n" ) ;
	$arn = Util_Format_Sanatize( Util_Format_GetVar( "arn" ), "url" ) ;
	$menu = ( Util_Format_Sanatize( Util_Format_GetVar( "menu" ), "ln" ) == "sa" ) ? "sa" : "operator" ;
	$wpress = Util_Format_Sanatize( Util_Format_GetVar( "wpress" ), "n" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	$open_status = Util_Format_Sanatize( Util_Format_GetVar( "open_status" ), "n" ) ;
	$remember = Util_Format_Sanatize( Util_Format_GetVar( "remember" ), "n" ) ;
	$v = Util_Format_Sanatize( Util_Format_GetVar( "v" ), "ln" ) ;
	$token_pass = md5($CONF['DOCUMENT_ROOT'].$CONF['TIMEZONE']) ;
	LIST( $ip, $vis_token ) = Util_IP_GetIP( "" ) ; $now = time() ;

	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;

	$error = $reload = $password_new = $auto_login_token = ""  ;
	$auto_login_token_ses = ( $ses ) ? $ses : "" ;
	if ( !isset( $CONF["screen"] ) ) { $CONF["screen"] = "same" ; }
	if ( $auto || $wp || $wpress || ( $query == "op" ) ) { $CONF["screen"] = "separate" ; }
	if ( !isset( $_COOKIE["phplive_cookie_check"] ) ) { setcookie( "phplive_cookie_check", 1, $now+(60*60*24*90), "/" ) ; }

	if ( !isset( $CONF["API_KEY"] ) )
	{
		$CONF["API_KEY"] = Util_Format_RandomString( 10 ) ;
		$error = ( Util_Vals_WriteToConfFile( "API_KEY", $CONF["API_KEY"] ) ) ? "" : "Could not write to config file." ;
	}
	if ( !isset( $CONF["SALT"] ) ) { Util_Vals_WriteToConfFile( "SALT", Util_Format_RandomString( 10 ) ) ; }
	if ( isset( $_COOKIE["phplive_auto_login_token"] ) && preg_match( "/\.\./", $_COOKIE["phplive_auto_login_token"] ) )
	{
		$auto_login_token_temp = Util_Format_Sanatize( $_COOKIE["phplive_auto_login_token"], "ln" ) ;
		LIST( $auto_login_token, $auto_login_token_ses ) = explode( "..", $auto_login_token_temp ) ;
	}

	if ( $action == "update_auto_login" )
	{
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		if ( $value )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

			$opinfo = Ops_get_OpInfoByID( $dbh, $_COOKIE["phplive_opID"] ) ;
			$auto_login_token = md5( "$opinfo[login]$opinfo[password]" )."..".$opinfo["ses"] ;
			setcookie( "phplive_auto_login_token", $auto_login_token, $now+(60*60*24*1095), "/" ) ;
		}
		else { setcookie( "phplive_auto_login_token", FALSE, -1, "/" ) ; }
		database_mysql_close( $dbh ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
		print $json_data ; exit ;
	}
	else if ( $action == "submit" )
	{
		$menu = Util_Format_Sanatize( Util_Format_GetVar( "menu" ), "ln" ) ;

		if ( !isset( $_COOKIE["phplive_cookie_check"] ) )
			$error = "Browser cookies must be enabled." ;
		else if ( $menu == "sa" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_ext.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_ext.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_ext.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/get.php" ) ;

			$admininfo = Setup_get_InfoByLogin( $dbh, $login ) ;
			if ( isset( $admininfo["adminID"] ) && ( $password === md5( $admininfo["password"].$token_pass ) ) )
			{
				$ses = md5( $now.$ip ) ;
				Ops_update_ext_AdminValue( $dbh, $admininfo["adminID"], "lastactive", $now ) ;
				Ops_update_ext_AdminValue( $dbh, $admininfo["adminID"], "ses", $ses ) ;
				setcookie( "phplive_adminID", $admininfo['adminID'], -1, "/" ) ;

				database_mysql_close( $dbh ) ;
				HEADER( "location: setup/?ses=$ses&$now" ) ;
				exit ;
			} else { $error = "Invalid login or password." ; }
		}
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_ext.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;

			$opinfo = Ops_get_ext_OpInfoByLogin( $dbh, $login ) ;

			if ( isset( $opinfo["opID"] ) && ( $password === md5( $opinfo["password"].$token_pass ) ) )
			{
				if ( $mapp && !$opinfo["mapper"] )
				{
					$error = "Account does not have Mobile App access." ;
				}
				else
				{
					$opvars = Ops_get_OpVars( $dbh, $opinfo["opID"] ) ;
					$op_sounds = isset( $VALS["op_sounds"] ) ? unserialize( $VALS["op_sounds"] ) : Array() ;
					if ( isset( $op_sounds[$opinfo["opID"]] ) ) { $op_sounds_vals = $op_sounds[$opinfo["opID"]] ; $opinfo["sound1"] = $op_sounds_vals[0] ; $opinfo["sound2"] = $op_sounds_vals[1] ; } else { $opinfo["sound1"] = "default" ; $opinfo["sound2"] = "default" ; }
					if ( !isset( $opvars["sound"] ) )
					{
						include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put.php" ) ;
						Ops_put_OpVars( $dbh, $opinfo["opID"] ) ;
					}

					// only one instance of console window per browser type since system uses cookies
					if ( $auto_login_token_ses )
					{
						if ( $opinfo["ses"] != $auto_login_token_ses )
						{
							setcookie( "phplive_auto_login_token", FALSE, -1, "/" ) ;
							database_mysql_close( $dbh ) ;
							HEADER( "location: logout.php?action=logout&dup=1&wp=$wp&auto=$auto&menu=operator&mapp=$mapp&wpress=$wpress&$now" ) ;
						}
						$remember = 1 ;
					}
					$ses = md5( $now.$ip ) ; Ops_update_OpValue( $dbh, $opinfo["opID"], "ses", $ses ) ;
					Ops_update_OpValue( $dbh, $opinfo["opID"], "lastactive", $now ) ;

					if ( $remember )
					{
						$auto_login_token = md5( "$opinfo[login]$opinfo[password]" )."..".$ses ;
						setcookie( "phplive_auto_login_token", $auto_login_token, $now+(60*60*24*1095), "/" ) ;
					} else { setcookie( "phplive_auto_login_token", FALSE, -1, "/" ) ; }
					if ( $mapp )
					{
						$mapp_opid = $opinfo["opID"] ;
						$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
						if ( $arn && $platform && ( !isset( $mapp_array[$mapp_opid] ) || ( $mapp_array[$mapp_opid]["a"] != $arn ) || ( $mapp_array[$mapp_opid]["p"] != $platform ) ) )
						{
							$mapp_array[$mapp_opid]["a"] = "$arn" ;
							$mapp_array[$mapp_opid]["p"] = $platform ; 
							Util_Vals_WriteToFile( "MAPP", serialize( $mapp_array ) ) ;
						}
						if ( is_file( "$CONF[TYPE_IO_DIR]/$mapp_opid.mapp" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$mapp_opid.mapp" ) ; }
						Ops_update_OpValue( $dbh, $opinfo["opID"], "mapp", 1 ) ;
						setcookie( "phplive_opID", $opinfo['opID'], $now+(60*60*24*90), "/" ) ;
					}
					else
					{
						Ops_update_OpValue( $dbh, $opinfo["opID"], "mapp", 0 ) ;
						setcookie( "phplive_opID", $opinfo['opID'], -1, "/" ) ;
					}
				}
			} else { $error = "Invalid login or password." ; }
		}
	}
	else if ( $action == "reset_password" )
	{
		if ( $ip && preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
			$json_data = "json_data = { \"status\": 0, \"error\": \"Could not process request at this time.\" };" ;
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ;
	
			if ( $menu == "sa" )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/get.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/update.php" ) ;

				$admininfo = Setup_get_InfoByLogin( $dbh, $login ) ;
				if ( isset( $admininfo["adminID"] ) )
				{
					if ( isset( $admininfo["error"] ) )
					{
						$json_data = "json_data = { \"status\": 0, \"error\": \"Multiple matched accounts found.\" };" ;
					}
					else if ( $admininfo["lastactive"] > ( $now-60 ) )
					{
						$time_left = $admininfo["lastactive"] - ( $now-60 ) ;
						$json_data = "json_data = { \"status\": 0, \"error\": \"Please try again in $time_left seconds.\" };" ;
					}
					else if ( $admininfo["status"] == -1 )
						$json_data = "json_data = { \"status\": 0, \"error\": \"Password reset is not available for this account.\" };" ;
					else
					{
						include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
						$departments = Depts_get_AllDepts( $dbh ) ;

						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$deptinfo = $departments[$c] ;
							if ( $deptinfo["smtp"] )
							{
								$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;
								break 1 ;
							}
						}

						$url = "$base_url/?adminid=$admininfo[adminID]&v=".md5( $now.$admininfo["password"] ) ;
						$message = "To reset the setup admin account password, please visit the following URL:\r\n==\r\n\r\n$url\r\n\r\n==\r\nIP: $ip\r\n" ;
						$error = Util_Email_SendEmail( "Setup Admin", $admininfo["email"], "Setup Admin", $admininfo["email"], "Setup Area Password Reset URL", $message, "" ) ;

						$email_partial = string_mask( $admininfo["email"], 4, strlen( $admininfo["email"] ) ) ;
						if ( !$error )
						{
							Setup_update_SetupValue( $dbh, $admininfo["adminID"], "lastactive", $now ) ;
							$json_data = "json_data = { \"status\": 1, \"message\": \"Email sent! Check your email address ($email_partial).\" };" ;
						}
						else
							$json_data = "json_data = { \"status\": 0, \"error\": \"$error\" };" ;
					}
				} else { $json_data = "json_data = { \"status\": 0, \"error\": \"Setup Admin login ($login) is invalid.\" };" ; }
			}
			else
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_ext.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;

				$opinfo = Ops_get_ext_OpInfoByLogin( $dbh, $login ) ;
				if ( isset( $opinfo["opID"] ) )
				{
					if ( $opinfo["lastactive"] > ( $now-60 ) )
					{
						$time_left = $opinfo["lastactive"] - ( $now-60 ) ;
						$json_data = "json_data = { \"status\": 0, \"error\": \"Please try again in $time_left seconds.\" };" ;
					}
					else
					{
						include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
						$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;

						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$deptinfo = $departments[$c] ;
							if ( $deptinfo["smtp"] )
							{
								$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;

								$CONF["SMTP_HOST"] = $smtp_array["host"] ;
								$CONF["SMTP_LOGIN"] = $smtp_array["login"] ;
								$CONF["SMTP_PASS"] = $smtp_array["pass"] ;
								$CONF["SMTP_PORT"] = $smtp_array["port"] ;
								$CONF["SMTP_API"] = isset( $smtp_array["api"] ) ? $smtp_array["api"] : "" ;
								$CONF["SMTP_DOMAIN"] = isset( $smtp_array["domain"] ) ? $smtp_array["domain"] : "" ;
								break 1 ;
							}
						}

						$url = "$base_url/?opid=$opinfo[opID]&v=".md5( $now.$opinfo["password"] ) ;
						$message = "To reset your operator account password, please visit the following URL:\r\n==\r\n\r\n$url\r\n\r\n==\r\nIP: $ip\r\n" ;
						$error = Util_Email_SendEmail( $opinfo["name"], $opinfo["email"], $opinfo["name"], $opinfo["email"], "Operator Password Reset URL", $message, "" ) ;

						$email_partial = string_mask( $opinfo["email"], 4, strlen( $opinfo["email"] ) ) ;
						if ( !$error )
						{
							Ops_update_OpValue( $dbh, $opinfo["opID"], "lastactive", $now ) ;
							$json_data = "json_data = { \"status\": 1, \"message\": \"Email sent! Check your email address ($email_partial).\" };" ;
						} else { $json_data = "json_data = { \"status\": 0, \"error\": \"$error\" };" ; }
					}
				}
				else { $json_data = "json_data = { \"status\": 0, \"error\": \"Operator login ($login) is invalid.\" };" ; }
			}
		}
		database_mysql_close( $dbh ) ; print $json_data ;
		exit ;
	}
	else if ( $v )
	{
		$adminid = Util_Format_Sanatize( Util_Format_GetVar( "adminid" ), "n" ) ;
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;

		if ( $adminid )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/update.php" ) ;

			$menu = "sa" ;
			$admininfo = Setup_get_InfoByID( $dbh, $adminid ) ;
			if ( isset( $admininfo["lastactive"] ) && ( $v === md5( $admininfo["lastactive"].$admininfo["password"] ) ) )
			{
				$login = $admininfo["login"] ;
				$password_new = Util_Format_RandomString( 6 ) ;
				Setup_update_SetupValue( $dbh, $admininfo["adminID"], "password", md5( $password_new ) ) ;
			} else { $error = "Password reset URL is invalid or has expired." ; }
		}
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;

			$menu = "operator" ;
			$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;
			if ( isset( $opinfo["lastactive"] ) && ( $v === md5( $opinfo["lastactive"].$opinfo["password"] ) ) )
			{
				$login = $opinfo["login"] ;
				$password_new = Util_Format_RandomString( 6 ) ;
				Ops_update_OpValue( $dbh, $opinfo["opID"], "password", md5( $password_new ) ) ;
			} else { $error = "Password reset URL is invalid or has expired." ; }
		}
	}

	$md5_password = "" ;
	if ( !$login && $auto_login_token && $auto_login_token_ses && ( $menu != "sa" ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

		$opinfo_ = Ops_get_OpInfoByToken( $dbh, $auto_login_token ) ;

		if ( isset( $opinfo_["opID"] ) )
		{
			$md5_password = md5( $opinfo_["password"].$token_pass ) ;
			$remember = 1 ;
		} else { setcookie( "phplive_auto_login_token", FALSE, -1, "/" ) ; }
	}
	// main one included at chat_actions_op_status.php
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	if ( !Ops_get_itr_AnyOpsOnline( $dbh, 0 ) )
	{
		$dir_files = glob( $CONF["TYPE_IO_DIR"]."/*", GLOB_NOSORT ) ;
		$total_dir_files = count( $dir_files ) ;
		if ( $total_dir_files )
		{
			for ( $c = 0; $c < $total_dir_files; ++$c )
			{
				if ( $dir_files[$c] && is_file( $dir_files[$c] ) && !preg_match( "/\.mapp/", $dir_files[$c] ) ) { unlink( $dir_files[$c] ) ; }
			}
		}
	}

	function string_mask( $string, $start, $end, $char_replace = '.' )
	{
		$middle = '' ;
		for ( $c = $start; $c < strlen( $string ); $c++ )
		{
			if ( $string[$c] == "@" ) { $middle .= "@" ; }
			else { $middle .= $char_replace ; }
		}
		return substr( $string, 0, $start ).$middle ;
	}
?>
<?php include_once( "./inc_doctype.php" ) ?>
<?php if ( isset( $CONF["KEY"] ) && ( $CONF["KEY"] == md5($KEY."-c615") ) ): ?><?php else: ?>
<!--
********************************************************************
* PHP Live! (c) OSI Codes Inc.
* www.phplivesupport.com
********************************************************************
-->
<?php endif ; ?>
<head>
<title> <?php if ( isset( $CONF["KEY"] ) && ( $CONF["KEY"] == md5($KEY."-c615") ) ): ?>Live Chat <?php else: ?>Premier Consumer Chat<?php endif ; ?> v.<?php echo $VERSION ?> </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "./inc_meta_dev.php" ) ; ?>
<meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height" />

<link rel="Stylesheet" href="./css/setup.css?<?php echo $VERSION ?>">
<?php if ( $mapp ): ?><script type="text/javascript" src="./mapp/js/mapp.js?<?php echo $VERSION ?>"></script><?php endif ; ?>
<script type="text/javascript" src="./js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/jquery_md5.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/winapp.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/modernizr.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	"use strict" ;
	var loaded = 1 ;
	var base_url = "." ;
	var widget = 0 ; var embed = 0 ; var mapp = <?php echo $mapp ?> ;
	var screen_ = ( typeof( phplive_wp ) != "undefined" ) ? "separate" : "<?php echo $CONF["screen"] ?>" ;
	var global_menu ;
	var mobile = ( <?php echo $mobile ?> ) ? is_mobile() : 0 ;
	var mapp_login = 0 ; // this method to ensure mapp vars are set
	var external_url = "" ;
	var forgot_attempts = 0 ;
	var sound_volume = 1 ;

	var audio_supported = HTML5_audio_support() ;
	var mp3_support = ( typeof( audio_supported["mp3"] ) != "undefined" ) ? 1 : 0 ;

	$(document).ready(function()
	{
		check_protocol() ;

		$("body").css({'background': '#FFFFFF'}) ;
		$("body").show() ;
		init_menu() ;

		<?php if ( $error ): ?>do_alert( 0, '<?php echo $error ?>' ) ;<?php endif ; ?>

		toggle_menu( "<?php echo $menu ?>" ) ;
		wp_total_visitors(0) ;

		<?php
			if ( $action ) { print "update_open_status( $open_status ) ;" ; }

			if ( $md5_password )
			{
				print "
				$('#phplive_password').val( '$md5_password' ) ;
				$('#phplive_login').val( '$opinfo_[login]' ) ;
				$('#ses').val( '$auto_login_token_ses' ) ; mapp_login = 1 ; if ( !mobile ) { \$('#theform').submit() ; }
				" ;
			}
			else if ( ( $action == "submit" ) && ( $menu == "operator" ) && !$error )
			{
				if ( $reload )
				{
					print "$('#div_reload').show() ;" ;
					print "setTimeout( function(){ $('#theform').submit() ; }, 15000 ) ;" ;
				}
				else
				{
					// play_sound( 0, \"login_op\", \"new_request_$opinfo[sound1]\" ) ;
					print "input_disable() ; $('#btn_login').attr('disabled', true).html('Logging in... <img src=\"pics/loading_fb.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\"> ') ;" ;
					if ( $wp || $auto )
						print "setTimeout( function(){ location.href='ops/operator.php?ses=$ses&auto=$auto&wp=$wp&mapp=$mapp&$now' ; }, 3000 ) ;" ;
					else
						print "setTimeout( function(){ location.href='ops/?ses=$ses&auto=$auto&wp=$wp&$now' ; }, 3000 ) ;" ;
				}
			}
			else if ( $password_new ) { print "$('#div_password').show() ;" ; }

			if ( $CONF["screen"] == "same" ) { print "$('#div_menus').show() ;" ; }
		?>

		$("#login_remember_text").click(function() {
			$( "input[name=remember]" ).prop( "checked", !$( "input[name=remember]" ).prop( "checked" ) ) ;
		});

		if ( <?php echo $mapp ?> ) { init_external_url() ; }
	});

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

	function check_protocol()
	{
		var base_url = "<?php echo $base_url ?>" ;
		var url = window.location.href ;
		var base_url_https = ( base_url.match( /^https:/i ) ) ? 1 : 0 ;
		var base_url_toggle = ( base_url.match( /^\//i ) ) ? 1 : 0 ; // one slash for absolute path (/phplive)
		var url_https = ( url.match( /^https:/i ) ) ? 1 : 0 ;

		if ( base_url_https && !url_https && !base_url_toggle )
		{
			location.href = base_url+"/<?php echo ( $query ) ? '?'.$query : '' ; ?>" ;
		}
		else if ( !base_url_https && url_https && !base_url_toggle )
		{
			base_url = base_url.replace( /^https:/g, "http:" ) ;
			location.href = base_url+"/<?php echo ( $query ) ? '?'.$query : '' ; ?>" ;
		} return true ;
	}

	function toggle_menu( themenu )
	{
		toggle_forgot(0) ;

		if ( !themenu )
		{
			if ( $('#btn_login').html() == "Login as Setup Admin" ) { themenu = "operator" ; }
			else { themenu = "sa" ; }
		}
		global_menu = themenu ;

		$('#div_forgot_error_sa').hide() ;
		$('#div_forgot_error_op').hide() ;

		if ( themenu == "sa" )
		{
			$('#login_remember').hide() ;
			$('#btn_login_forgot').html( "Reset Setup Password" ) ;
			$('#btn_login').html( "Login as Setup Admin" ) ;
			$('#href_forgot').html( "forgot setup admin password" ) ;
			$('#menu_operator').removeClass('info_menu_focus').addClass('info_menu_blank') ;
			$('#menu_sa').removeClass('info_menu_blank').addClass('info_menu_focus') ;
			$('#radio_login_sa').prop('checked', true) ;

			$('#div_info_operator').hide() ; $('#div_info_setup').show() ; $('#div_op_status').hide() ;
			$('#phplive_login').val( "" ) ;
			if ( screen_ == "same" ) { }
			$('#copyright').show() ;
		}
		else
		{
			if ( screen_ == "separate" ) { $('#login_remember').show() ; }
			else { $('#login_remember').prop('checked', false) ; }
			$('#btn_login_forgot').html( "Reset Operator Password" ) ;
			$('#btn_login').html( "Login as Operator" ) ;
			$('#href_forgot').html( "forgot operator password" ) ;
			$('#menu_sa').removeClass('info_menu_focus').addClass('info_menu_blank') ;
			$('#menu_operator').removeClass('info_menu_blank').addClass('info_menu_focus') ;
			$('#radio_login_operator').prop('checked', true) ;

			$('#div_info_setup').hide() ; $('#div_info_operator').show() ;  $('#div_op_status').show() ;
			$('#phplive_login').val( "<?php echo ( $login ) ? $login : "" ?>" ) ;
			if ( screen_ == "same" ) { $('#copyright').show() ; }
		}

		if ( !mapp && !mobile ) { $('#phplive_login').focus() ; }
		$('#menu').val( themenu ) ;
	}

	function do_login()
	{
		if ( $('#phplive_login').val() == "" )
			do_alert( 0, "Blank login is invalid." ) ;
		else if ( $('#phplive_password_temp').val() == "" )
			do_alert( 0, "Blank password is invalid." ) ;
		else
		{
			var md5_password = phplive_md5( phplive_md5( $('#phplive_password_temp').val() )+"<?php echo $token_pass ?>" ) ;
			$('#phplive_password').val( md5_password ) ;
			$('#theform').submit() ;
		}
	}

	function do_forgot()
	{
		var json_data = new Object ;
		var unique = unixtime() ;
		var login = $('#phplive_login').val() ;

		if ( !login )
			do_alert( 0, "Please provide the Login." ) ;
		else
		{
			$('#div_forgot_error_sa').hide() ;
			$('#div_forgot_error_op').hide() ;
			$('#btn_login_forgot').attr("disabled", true) ;

			$.ajax({
			type: "POST",
			url: "./index.php",
			data: "action=reset_password&menu="+global_menu+"&phplive_login="+login+"&unique="+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					forgot_attempts = 0 ;

					$('#email_partial').html( json_data.email_partial ) ;
					do_alert_div( ".", 1, json_data.message ) ;
				}
				else
				{
					++forgot_attempts ;

					if ( forgot_attempts > 3 )
					{
						if ( menu == "sa" ) { $('#div_forgot_error_sa').show() ; }
						else { $('#div_forgot_error_op').show() ; }
					}
					setTimeout( function(){ $('#btn_login_forgot').attr("disabled", false) ; }, 5000 ) ;
					do_alert( 0, json_data.error ) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error processing reset password.  Please reload the page and try again." ) ;
			} });
		}
	}

	function input_disable()
	{
		$("#theform :input").attr("disabled", true) ;
	}

	function input_text_listen( e )
	{
		var key = -1 ;
		var shift ;

		key = e.keyCode ;
		shift = e.shiftKey ;

		if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
			do_login() ;
	}

	function toggle_forgot( theflag )
	{
		$('#btn_login_forgot').attr("disabled", false) ;
		$('#div_alert').hide() ;
		if ( !mapp && !mobile ) { $('#phplive_login').focus() ; }
		
		if ( theflag )
		{
			$('#div_tr_password').hide() ;
			$('#div_btn_submit').hide() ;
			$('#div_btn_forgot').show() ;
			$('#div_op_status').hide() ;
		}
		else
		{
			$('#div_tr_password').show() ;
			$('#div_btn_forgot').hide() ;
			$('#div_btn_submit').show() ;
			if ( global_menu == "sa" ) { $('#div_op_status').hide() ; }
			else { $('#div_op_status').show() ; }
		}
	}

	function update_open_status( theflag )
	{
		$('#open_status_'+theflag).prop('checked', true) ;
	}
//-->
</script>
</head>
<body style="display: none; overflow: auto;">

<div id="body" style="padding-bottom: 20px;">
	<div style="width: 100%; padding-top: 25px;">
		<div style="width: 280px; margin: 0 auto;">
			<div style="font-size: 14px; color: #C4C4C3; text-shadow: 1px 1px #FFFFFF; text-align: center;"><img src="/assets/images/logo-small.png" /></div>
		</div>

		<div class="info_info" style="background: url( ./pics/clouds.png ) repeat-x #7CBDCD; background-position: bottom center; color: #FFFFFF; width: 280px; height: 350px; margin: 0 auto; margin-top: 20px; padding: 10px;">

			<div style="display: none; margin-bottom: 10px;" id="div_menus">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td width="50%" style="padding-right: 5px; text-align: center;"><div class="info_neutral" id="menu_operator" onClick="toggle_menu('operator')" style="padding: 10px; cursor: pointer;"><input type="radio" name="radio_login" id="radio_login_operator"> Chat Operator</div></td>
					<td width="50%" style="padding-left: 5px; text-align: center;"><div class="info_neutral" id="menu_sa" onClick="toggle_menu('sa')" style="padding: 10px; cursor: pointer;"><input type="radio" name="radio_login" id="radio_login_sa"> Setup Admin</div></td>
				</tr>
				</table>
			</div>

			<form method="POST" action="index.php?submit" id="theform">
			<input type="hidden" name="action" value="submit">
			<input type="hidden" name="auto" id="auto" value="<?php echo $auto ?>">
			<input type="hidden" name="wp" value="<?php echo $wp ?>">
			<input type="hidden" name="mapp" id="mapp" value="<?php echo $mapp ?>">
			<input type="hidden" name="platform" id="platform" value="">
			<input type="hidden" name="arn" id="arn" value="">
			<input type="hidden" name="menu" id="menu" value="">
			<input type="hidden" name="ses" id="ses" value="">
			<input type="hidden" name="wpress" id="wpress" value="<?php echo $wpress ?>">
			<input type="hidden" name="phplive_password" id="phplive_password" value="">
			<table cellspacing=0 cellpadding=5 border=0 style="width: 100%;">
			<tr>
				<td width="60"></td>
				<td>
					<div style="padding-bottom: 10px;">
						<div id="div_info_operator" style="display: none;"><img src="pics/icons/agent.png" width="16" height="16" border="0" alt=""> Chat Operator Login</div>
						<div id="div_info_setup" style="display: none;"><img src="pics/icons/settings.png" width="16" height="16" border="0" alt=""> Setup Admin Login</div>
					</div>
				</td>
			</tr>
			<tr>
				<td width="60"><span id="div_txt_login">Login</span></td>
				<td> <input type="text" class="input" name="phplive_login" id="phplive_login" size="15" maxlength="35" value="<?php echo ( $login ) ? $login : "" ?>" onKeyup="input_text_listen(event);"></td>
			</tr>
			<tr id="div_tr_password">
				<td width="60">Password</td>
				<td> <input type="password" class="input" name="phplive_password_temp" id="phplive_password_temp" size="15" maxlength="35" value="<?php echo ( isset( $password ) && $reload ) ? $password : "" ; ?>" onKeyPress="return noquotes(event)" onKeyup="input_text_listen(event);"></td>
			</tr>
			<tr>
				<td></td>
				<td colspan=3>
					<div id="div_btn_submit" style="padding-top: 15px;">
						<button type="button" id="btn_login" onClick="do_login()" class="btn"></button>
						<div style="margin-top: 25px;">&bull; <a href="JavaScript:void(0)" onClick="toggle_forgot(1)" id="href_forgot" style="color: #FFFFFF;"></a></div>
					</div>
					<div id="div_btn_forgot" style="display: none; margin-top: 10px;">
						<div class="info_error" style="display: none; text-shadow: none;" id="div_forgot_error_sa"></div>
						<div class="info_error" style="display: none; margin-top: 5px; text-shadow: none;" id="div_forgot_error_op">Please contact the Setup Admin to reset your login credentials.</div>
						<div id="div_alert" style="margin-top: 5px; text-shadow: none;"></div>
						<div style="margin-top: 15px;">
							<div><button type="button" id="btn_login_forgot" onClick="do_forgot()" class="btn"></button></div>
							<div style="margin-top: 25px;">&bull; <a href="JavaScript:void(0)" onClick="toggle_forgot(0)" style="color: #FFFFFF;">back to login</a></div>
						</div>
					</div>
				</td>
			</tr>
			</table>
			</form>

			<div id="div_sounds_login_op" style="width: 1px; height: 1px; overflow: hidden; opacity:0.0; filter: alpha(opacity=0);"></div>
			<audio id='div_sounds_audio_login_op'></audio>

		</div>
		<div style="background: url( ./pics/bg_fade_lite.png ) no-repeat; background-position: top center; width: 280px; height: 10px; margin: 0 auto; opacity:0.3; filter: alpha(opacity=30)" class="round_top">&nbsp;</div>
		<div style="padding-top: 5px;">
			<div style="width: 280px; margin: 0 auto; font-size: 10px; text-shadow: 1px 1px #FFFFFF; text-align: center;">
				<?php if ( ( isset( $CONF["KEY"] ) && ( $CONF["KEY"] == md5($KEY."-c615") ) ) || $mapp ): ?><?php else: ?>&copy; OSI Codes Inc. - powered by <a href="http://www.phplivesupport.com/?plk=osicodes-5-ykq-m" target="_blank">PHP Live! Support</a><?php endif ; ?>
			</div>
		</div>

	</div><?php if ( $mapp ) { include_once( "./mapp/inc_footer.php" ) ; } ?>
</div>

<div id="div_reload" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 2000px; background: url( ./pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">
	<div style="padding: 15px;">loading... <img src="pics/loading_fb.gif" width="16" height="11" border="0" alt=""></div>
</div>

<div id="div_password" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; padding-top: 80px; z-index: 50; background: url(./pics/bg_trans_white.png) repeat;">
	<div class="info_info" style="width: 280px; height: 250px; margin: 0 auto; padding: 10px; text-shadow: 1px 1px #FFFFFF;">
		<div class="edit_title">Your new password for account <span style="color: #ED933F;"><?php echo $login ?></span> is:</div>
		<div class="edit_title" style="margin-top:5px; color: #53BA4B;"><?php echo $password_new ?></div>
		<div style="margin-top: 15px;">Write the password down.  It will not be visible again once this window is closed.  After logging in, be sure to update your password.</div>
		<div style="margin-top: 25px;"><button type="button" onClick="$('#div_password').hide();" class="btn">Close Window and Login</button></div>
	</div>
</div>

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>
