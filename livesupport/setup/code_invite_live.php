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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$token = Util_Format_Sanatize( Util_Format_GetVar( "token" ), "ln" ) ;
	$now = time() ; $deptid = 0 ;

	/*************/
	/* HTML Code */
	$position_css = "" ;
	$vars = Util_Format_Get_Vars( $dbh ) ;
	if ( isset( $vars["code"] ) )
	{
		$proto = $vars["code"] ;
		switch ( $vars["position"] )
		{
			case 2:
				$position_css = " position: fixed; bottom: 0px; right: 0px; z-index: 1000;" ;
				break ;
			case 3:
				$position_css = " position: fixed; bottom: 0px; left: 0px; z-index: 1000;" ;
				break ;
			case 4:
				$position_css = " position: fixed; top: 0px; right: 0px; z-index: 1000;" ;
				break ;
			case 5:
				$position_css = " position: fixed; top: 0px; left: 0px; z-index: 1000;" ;
				break ;
			case 6:
				$position_css = " position: fixed; top: 50%; left: 0px; z-index: 1000;" ;
				break ;
			case 7:
				$position_css = " position: fixed; top: 50%; right: 0px; z-index: 1000;" ;
				break ;
			default:
				$position_css = "" ;
		}
	}

	$base_url = $CONF["BASE_URL"] ;
	$code = "&lt;span id=\"phplive_btn_$now\" onclick=\"phplive_launch_chat_$deptid(0)\" style=\"color: #0000FF; text-decoration: underline; cursor: pointer;$position_css\"&gt;&lt;/span&gt;-nl-&lt;script type=\"text/javascript\"&gt;-nl--nl-(function() {-nl-var phplive_e_$now = document.createElement(\"script\") ;-nl-phplive_e_$now.type = \"text/javascript\" ;-nl-phplive_e_$now.async = true ;-nl-phplive_e_$now.src = \"%%base_url%%/js/phplive_v2.js.php?v=$deptid|$now|$proto|%%text_string%%\" ;-nl-document.getElementById(\"phplive_btn_$now\").appendChild( phplive_e_$now ) ;-nl-})() ;-nl--nl-&lt;/script&gt;" ;

	if ( $proto == 1 ) { $base_url = preg_replace( "/(http:)|(https:)/", "http:", $base_url ) ; }
	else if ( $proto == 2 ) { $base_url = preg_replace( "/(http:)|(https:)/", "https:", $base_url ) ; }
	else { $base_url = preg_replace( "/(http:)|(https:)/", "", $base_url ) ; }

	$thecode = preg_replace( "/%%base_url%%/", $base_url, $code ) ;
	$code_html = preg_replace( "/&lt;/", "<", $thecode ) ;
	$code_html = preg_replace( "/&gt;/", ">", $code_html ) ;
	$code_html = preg_replace( "/-nl-/", "\r\n", $code_html ) ;
	/* HTML Code */
	/*************/

	LIST( $ip, $vis_token ) = Util_IP_GetIP( $token ) ;
	$ipinfo = IPs_get_IPInfo( $dbh, $vis_token, $ip ) ;

	$total_ops_online = Ops_get_itr_AnyOpsOnline( $dbh, 0 ) ;
	$initiate = ( isset( $VALS["auto_initiate"] ) && $VALS["auto_initiate"] ) ? unserialize( html_entity_decode( $VALS["auto_initiate"] ) ) : Array() ;

	if ( !$total_ops_online && file_exists( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) )
		unlink( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ;
	else if ( $total_ops_online && isset( $initiate["exin"] ) )
		touch( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ;
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
<script type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var si_timer ;
	var st_refresh ;
	var timer = 0 ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "html" ) ;

		<?php if ( $total_ops_online && isset( $initiate["exin"] ) ): ?>
		do_alert( 1, "Launching chat invitation..." ) ;
		setTimeout( function(){ init_chat() ; }, 5000 ) ;
		init_timer() ;
		<?php endif ; ?>
	});

	function init_timer()
	{
		si_timer = setInterval(function(){ start_timer() ; }, 1000) ;
	}

	function start_timer( thetimer )
	{
		timer += 1 ;
		if ( timer > 180 )
		{
			clearInterval( si_timer ) ;
		}
		else
		{
			$('#div_duration').html( timer ) ;
		}
	}

	function init_chat()
	{
		$('#div_chat').html( '<?php echo preg_replace( "/\r\n/", " ", preg_replace( "/%%text_string%%/", "", $code_html ) ) ?>' ) ;
	}

	function do_refresh()
	{
		$("#img_loading").show() ;
		if ( typeof( st_refresh ) != "undefined" ) { clearTimeout( st_refresh ) ; }
		st_refresh = setTimeout( function(){
			location.href = "code_invite_live.php?ses=<?php echo $ses ?>&<?php echo $now ?>" ;
		}, 1500 ) ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='code.php?ses=<?php echo $ses ?>'">HTML Code</div>
			<div class="op_submenu_focus" id="menu_code_auto">Automatic Chat Invitation</div>
			<div class="op_submenu" onClick="location.href='code_settings.php?ses=<?php echo $ses ?>'">Settings</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			<div>
				<div id="menu_sub_image" class="op_submenu2" onClick="location.href='code_invite.php?ses=<?php echo $ses ?>'">Chat Invitation Image</div>
				<div id="menu_sub_criteria" class="op_submenu2" onClick="location.href='code_invite.php?ses=<?php echo $ses ?>&jump=criteria'">Invitation Criteria</div>
				<div id="menu_sub_live" class="op_submenu_focus">Demo Invitation</div>
				<div style="clear: both"></div>
			</div>

			<div style="margin-top: 25px;">
				<?php if ( !$total_ops_online ): ?>
					<div class="info_error">Automatic chat invitation can only processes when an operator is Online.  <a href="../" target="_blank" style="color: #FFFFFF;">Login as an operator</a> and then <a href="JavaScript:void(0)" onClick="do_refresh()" style="color: #FFFFFF;">refresh this page to continue</a>.</div>
					<div id="img_loading" style="display: none; margin-top: 15px;"><img src="../pics/loading_ci.gif" width="16" height="16" border="0" alt=""></div>
				<?php elseif ( !isset( $initiate["exin"] ) ): ?>
					<div class="info_error">Automatic chat invitation is <a href="code_invite.php?ses=<?php echo $ses ?>&jump=criteria" style="color: #FFFFFF;">switched Off</a>.</div>
				<?php else: ?>

					Once the automatic chat invitation has been displayed OR when the chat request window has been opened, the <a href="code_invite.php?ses=<?php echo $ses ?>&jump=criteria">invitation criteria</a> will be reset.  The next automatic chat invitation will process after <?php echo $initiate["reset"] ?> hours.  (* the reset duration is to prevent the chat invitation from displaying during a possible chat session)

				<?php endif ; ?>

				<div id="div_chat" style="display: none; margin-top: 25px;"></div>
			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ?>