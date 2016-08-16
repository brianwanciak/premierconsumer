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

	$error = $sub = "" ;

	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$option = Util_Format_Sanatize( Util_Format_GetVar( "option" ), "n" ) ;
	$sub = Util_Format_Sanatize( Util_Format_GetVar( "sub" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "n" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$auto_offline = ( isset( $VALS["AUTO_OFFLINE"] ) && $VALS["AUTO_OFFLINE"] ) ? unserialize( $VALS["AUTO_OFFLINE"] ) : Array() ;

	if ( $action == "update" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;

		$copy_all = Util_Format_Sanatize( Util_Format_GetVar( "copy_all" ), "n" ) ;
		$message = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "message" ), "" ) ) ;
		$message_busy = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "message_busy" ), "" ) ) ;

		if ( ( $sub == "greeting" ) || ( $sub == "offline" ) || ( $sub == "temail" ) )
		{
			$table_name = "" ;
			if ( $sub == "greeting" ) { $table_name = "msg_greet" ; }
			else if ( $sub == "offline" ) { $table_name = "msg_offline" ; }
			else if ( $sub == "temail" ) { $table_name = "msg_email" ; }

			$idle_disconnect_minutes_o = Util_Format_Sanatize( Util_Format_GetVar( "idle_disconnect_minutes_o" ), "n" ) ;
			$idle_disconnect_minutes_v = Util_Format_Sanatize( Util_Format_GetVar( "idle_disconnect_minutes_v" ), "n" ) ;
			$femail = Util_Format_Sanatize( Util_Format_GetVar( "femail" ), "n" ) ;

			if ( !$message )
				$error = "Blank input is invalid.  Message has been reset." ;
			else if ( ( $sub == "offline" ) && ( !$message_busy ) )
				$error = "Blank input is invalid.  Message has been reset." ;
			else if ( $message && $table_name )
			{
				if ( $copy_all )
				{
					for( $c = 0; $c < count( $departments ); ++$c )
					{
						Depts_update_UserDeptValue( $dbh, $departments[$c]["deptID"], $table_name, $message ) ;
						if ( $sub == "offline" ) { Depts_update_UserDeptValue( $dbh, $departments[$c]["deptID"], "msg_busy", $message_busy ) ; }
						else if ( $sub == "greeting" ) { Depts_update_UserDeptVarsValues( $dbh, $departments[$c]["deptID"], "idle_o", $idle_disconnect_minutes_o, "idle_v", $idle_disconnect_minutes_v ) ; }
						else if ( $sub == "temail" ) { Depts_update_UserDeptVarsValue( $dbh, $departments[$c]["deptID"], "trans_f_dept", $femail ) ; }
					}
				}
				else
				{
					Depts_update_UserDeptValue( $dbh, $deptid, $table_name, $message ) ;
					if ( $sub == "offline" ) { Depts_update_UserDeptValue( $dbh, $deptid, "msg_busy", $message_busy ) ; }
					else if ( $sub == "greeting" ) { Depts_update_UserDeptVarsValues( $dbh, $deptid, "idle_o", $idle_disconnect_minutes_o, "idle_v", $idle_disconnect_minutes_v ) ; }
					else if ( $sub == "temail" ) { Depts_update_UserDeptVarsValue( $dbh, $deptid, "trans_f_dept", $femail ) ; }
				}
			}
			else
				$error = "Invalid action: $sub" ;
		}
		else if ( $sub == "settings" )
		{
			$temail = Util_Format_Sanatize( Util_Format_GetVar( "temail" ), "n" ) ;
			$temaild = Util_Format_Sanatize( Util_Format_GetVar( "temaild" ), "n" ) ;
			$emailt = Util_Format_Sanatize( Util_Format_GetVar( "emailt" ), "e" ) ;
			$emailt_bcc = Util_Format_Sanatize( Util_Format_GetVar( "emailt_bcc" ), "n" ) ;

			if ( $copy_all )
			{
				for( $c = 0; $c < count( $departments ); ++$c )
				{
					Depts_update_UserDeptValues( $dbh, $departments[$c]["deptID"], "temail", $temail, "temaild", $temaild ) ;
					Depts_update_UserDeptValues( $dbh, $departments[$c]["deptID"], "emailt", $emailt, "emailt_bcc", $emailt_bcc ) ;
				}
			}
			else
			{
				Depts_update_UserDeptValues( $dbh, $deptid, "temail", $temail, "temaild", $temaild ) ;
				Depts_update_UserDeptValues( $dbh, $deptid, "emailt", $emailt, "emailt_bcc", $emailt_bcc ) ;
			}
		}
		else if ( $sub == "custom" )
		{
			$remail = Util_Format_Sanatize( Util_Format_GetVar( "remail" ), "ln" ) ;
			$rquestion = Util_Format_Sanatize( Util_Format_GetVar( "rquestion" ), "ln" ) ;
			$custom_field = preg_replace( "/'/", "", preg_replace( "/\"/", "", Util_Format_Sanatize( Util_Format_GetVar( "custom_field" ), "notags" ) ) ) ;
			$custom_field_required = Util_Format_Sanatize( Util_Format_GetVar( "custom_field_required" ), "n" ) ;
			$prechat = Util_Format_Sanatize( Util_Format_GetVar( "prechat" ), "n" ) ; $prechat = 1 ; // bug fix for just this version

			$custom_array = ( !$custom_field ) ? serialize( Array() ) : serialize( Array( "$custom_field", $custom_field_required ) ) ;

			if ( $copy_all )
			{
				for( $c = 0; $c < count( $departments ); ++$c )
				{
					Depts_update_UserDeptValues( $dbh, $departments[$c]["deptID"], "remail", $remail, "rquestion", $rquestion ) ;
					Depts_update_UserDeptValue( $dbh, $departments[$c]["deptID"], "custom", $custom_array ) ;
					Depts_update_UserDeptVarsValue( $dbh, $departments[$c]["deptID"], "prechat_form", $prechat ) ;
				}
			}
			else
			{
				Depts_update_UserDeptValues( $dbh, $deptid, "remail", $remail, "rquestion", $rquestion ) ;
				Depts_update_UserDeptValue( $dbh, $deptid, "custom", $custom_array ) ;
				Depts_update_UserDeptVarsValue( $dbh, $deptid, "prechat_form", $prechat ) ;
			}
		}
		else if ( $sub == "canned" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/put.php" ) ;

			$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "ln" ) ;
			$message = Util_Format_ConvertQuotes( Util_Format_Sanatize( Util_Format_GetVar( "message" ), "htmltags" ) ) ;
			$sub_deptid = Util_Format_Sanatize( Util_Format_GetVar( "sub_deptid" ), "n" ) ;

			$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
			if ( isset( $caninfo["opID"] ) )
				$opid = $caninfo["opID"] ;
			else
				$opid = 1111111111 ;

			if ( !$canid = Canned_put_Canned( $dbh, $canid, $opid, $deptid, $title, $message ) )
				$error = "Error processing canned message." ;
			$deptid = $sub_deptid ;
		}
		else if ( $sub == "hours" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

			$offline_hours_onoff = Util_Format_Sanatize( Util_Format_GetVar( "offline_hours_onoff" ), "n" ) ;
			$offline_hour = Util_Format_Sanatize( Util_Format_GetVar( "offline_hour" ), "n" ) ;
			$offline_min = Util_Format_Sanatize( Util_Format_GetVar( "offline_min" ), "n" ) ;
			$offline_ampm = Util_Format_Sanatize( Util_Format_GetVar( "offline_ampm" ), "ln" ) ;
			$offline_duration = Util_Format_Sanatize( Util_Format_GetVar( "offline_duration" ), "n" ) ;
			$offline_rewind = 0 ;

			if ( $offline_hours_onoff )
			{
				if ( ( $offline_ampm == "pm" ) && ( $offline_hour < 12 ) ) { $offline_hour += 12 ; }
				else if ( ( $offline_ampm == "am" ) && ( $offline_hour == 12 ) ) { $offline_hour = 0 ; }
				$hour_max = $offline_hour + $offline_duration ;
				if ( $hour_max >= 24 ) { $offline_rewind = $hour_max - 24 ; }
				if ( $copy_all )
				{
					for( $c = 0; $c < count( $departments ); ++$c )
					{
						$temp_deptid = $departments[$c]["deptID"] ;
						$auto_offline[$temp_deptid] = "$offline_hour,$offline_min,$offline_duration,$offline_rewind" ;
					}
				}
				else { $auto_offline[$deptid] = "$offline_hour,$offline_min,$offline_duration,$offline_rewind" ; }
			}
			else
			{
				if ( $copy_all )
				{
					for( $c = 0; $c < count( $departments ); ++$c )
					{
						$temp_deptid = $departments[$c]["deptID"] ;
						if ( isset( $auto_offline[$temp_deptid] ) ) { unset( $auto_offline[$temp_deptid] ) ; }
					}
				}
				else { unset( $auto_offline[$deptid] ) ; }
			}
			$error = ( Util_Vals_WriteToFile( "AUTO_OFFLINE", serialize( $auto_offline ) ) ) ? "" : "Could not write to vals file." ;
		}
		$action = $sub ;
	}
	else if ( $action == "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/remove.php" ) ;

		$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "n" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		Canned_remove_Canned( $dbh, $caninfo["opID"], $canid ) ;
		$action = $sub ; $canid = 0 ;
	}
	
	$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	$deptvars = Depts_get_DeptVars( $dbh, $deptid ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($deptinfo["lang"], "ln").".php" ) ;
	$deptname = $deptinfo["name"] ;

	switch ( $action )
	{
		case "greeting":
			$message = $deptinfo["msg_greet"] ;
			break ;
		case "offline":
			$message = $deptinfo["msg_offline"] ;
			break ;
		case "temail":
			$message = $deptinfo["msg_email"] ;
			break ;
		default:
			break ;
	}

	$offline_hours_hour = 6 ; $offline_hours_min = 30 ; $offline_hours_ampm = "pm" ; $offline_hours_duration = 12 ;
	if ( isset( $auto_offline[$deptid] ) )
	{
		LIST( $offline_hour, $offline_min, $offline_duration ) = explode( ",", $auto_offline[$deptid] ) ;
		if ( $offline_hour >= 12 )
		{
			$offline_hours_ampm = "pm" ;
			if ( $offline_hour > 12 ) { $offline_hour -= 12 ; }
		}
		else { $offline_hours_ampm = "am" ; }
		if ( !$offline_hour ) { $offline_hour = 12 ; }
		$offline_hours_hour = $offline_hour ; $offline_hours_min = $offline_min ; $offline_hours_duration = $offline_duration ;
	}

	$idle_disconnect_o = ( isset( $deptvars["idle_o"] ) ) ? $deptvars["idle_o"] : 0 ;
	$idle_disconnect_v = ( isset( $deptvars["idle_v"] ) ) ? $deptvars["idle_v"] : 0 ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var winname = unixtime() ;
	var option = <?php echo $option ?> ; // used to communicate with depts.php to toggle iframe

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;
		$("body").css({'background-color': '#FFFFFF'}) ;

		<?php if ( $action == "offline" ): ?>
			$('#div_offline_busy').show() ;
		<?php elseif ( $action == "custom" ): ?>
			toggle_prechat( <?php echo isset( $deptvars['prechat_form'] ) ? 1 : 1 ; ?> ) ;
		<?php endif ; ?>

		<?php if ( $sub && !$error ): ?>
		parent.do_alert( 1, "Success" ) ;
			<?php if ( $sub == "hours" ): ?>
				<?php if ( $offline_hours_onoff ): ?>
					<?php if ( $copy_all ): ?>
					$('*[id*=span_class_]', parent.document).each(function() {
						$(this).removeClass('info_clear').addClass('info_box') ;
					}) ;
					<?php else: ?>
					$('#span_class_<?php echo $deptid ?>', parent.document).removeClass('info_clear').addClass('info_box') ;
					<?php endif ; ?>
				<?php else: ?>
					<?php if ( $copy_all ): ?>
					$('*[id*=span_class_]', parent.document).each(function() {
						$(this).removeClass('info_box').addClass('info_clear') ;
					}) ;
					<?php else: ?>
					$('#span_class_<?php echo $deptid ?>', parent.document).removeClass('info_box').addClass('info_clear') ;
					<?php endif ; ?>
				<?php endif ; ?>
			<?php endif ; ?>
		<?php elseif ( $error ): ?>
		parent.do_alert( 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>

		<?php if ( isset( $auto_offline[$deptid] ) ): ?>
			$('#offline_hours_onoff_on').prop('checked', true) ;
			toggle_offline_hours( 1 ) ;
		<?php else: ?>
			$('#offline_hours_onoff_off').prop('checked', true) ;
			toggle_offline_hours( 0 ) ;
		<?php endif ; ?>

		<?php if ( $idle_disconnect_o ): ?>
			$('#idle_disconnect_o_onoff_on').prop('checked', true) ;
			toggle_idle_minutes_o( 1 ) ;
		<?php else: ?>
			$('#idle_disconnect_o_onoff_off').prop('checked', true) ;
			toggle_idle_minutes_o( 0 ) ;
		<?php endif ; ?>

		<?php if ( $idle_disconnect_v ): ?>
			$('#idle_disconnect_v_onoff_on').prop('checked', true) ;
			toggle_idle_minutes_v( 1 ) ;
		<?php else: ?>
			$('#idle_disconnect_v_onoff_off').prop('checked', true) ;
			toggle_idle_minutes_v( 0 ) ;
		<?php endif ; ?>

		<?php if ( $action == "hours" ): ?>setInterval(function(){ fetch_systime() ; }, 10000) ;<?php endif ; ?>

		if ( <?php echo $canid ?> )
		{
			var div_pos = $('#tr_div_'+<?php echo $canid ?>).position() ;
			var div_height = $('#tr_div_'+<?php echo $canid ?>).height() ;
			var scroll_to = div_pos.top + div_height - 200 ;

			$('html, body').animate({
				scrollTop: scroll_to
			}, 200) ;
			$('#tr_div_'+<?php echo $canid ?>).fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
		}
	});

	function fetch_systime()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../ajax/setup_actions_itr.php",
		data: " action=systime&ses=<?php echo $ses ?>&unique="+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
				$('#span_system_time').html( json_data.systime ) ;
			else
				$('#span_system_time').html( json_data.error ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error loading requested page.  Please reload the console and try again." ) ;
			$('#span_system_time').html( "systime error [e2]" ) ;
		} });
	}

	function do_edit( thecanid, thetitle, thedeptid, themessage )
	{
		$( "input#canid" ).val( thecanid ) ;
		$( "input#title" ).val( thetitle.replace( /&-#39;/g, "'" ) ) ;
		$( "#deptid" ).val( thedeptid ) ;
		$( "#message" ).val( themessage.replace(/<br>/g, "\r\n").replace( /&-#39;/g, "'" ) ) ;
		
		toggle_new(0) ;
	}

	function toggle_new( theflag )
	{
		// theflag = 1 means force show, not toggle
		if ( $('#canned_box_new').is(':visible') && !theflag )
		{
			$( "input#canid" ).val( "0" ) ;
			$( "input#title" ).val( "" ) ;
			$( "#deptid" ).val( <?php echo $deptid ?> ) ;
			$( "#message" ).val( "" ) ;

			$('body').css({ 'overflow': 'auto' }) ;
			$('#canned_box_new').hide() ;
		}
		else
		{
			$('body').css({ 'overflow': 'hidden' }) ;
			$('#canned_box_new').show() ;
		}

		$('#title').focus() ;
	}

	function do_delete( thecanid )
	{
		var unique = unixtime() ;

		if ( confirm( "Really delete this canned response?" ) )
			location.href = "iframe_edit.php?ses=<?php echo $ses ?>&action=delete&sub=canned&deptid=<?php echo $deptid ?>&canid="+thecanid+"&"+unique ;
	}

	function do_submit_settings()
	{
		var emailt = $('#emailt').val() ;

		if ( emailt && !check_email( emailt ) )
			parent.do_alert( 0, "Email format is invalid. (example: you@domain.com)" ) ;
		else
			$('#form_settings').submit() ;
	}

	function do_submit()
	{
		var canid = $('#canid').val() ;
		var title = $('#title').val() ;
		var deptid = $('#deptid').val() ;
		var message = $('#message').val() ;
		var emailt = $('#emailt').val() ;

		if ( title == "" )
			parent.do_alert( 0, "Please provide a Reference title." ) ;
		else if ( message == "" )
			parent.do_alert( 0, "Please provide a Message." ) ;
		else
			$('#theform').submit() ;
	}

	function do_redirect( theaction )
	{
		if ( theaction == "settings" )
		{
			parent.do_options( 5, <?php echo $deptid ?> ) ;
		}
	}

	function toggle_offline_hours( theflag )
	{
		if ( theflag )
		{
			var offline_hour_select = <?php echo $offline_hours_hour ?> ;
			var offline_min_select = "<?php echo $offline_hours_min ?>" ;
			var offline_ampm_select = "<?php echo $offline_hours_ampm ?>" ;
			var offline_duration_select = <?php echo $offline_hours_duration ?> ;
			$('#offline_hour').attr("disabled", false) ; $("#offline_hour option[value='-']").remove() ; $('#offline_hour').val( offline_hour_select ) ;
			$("#offline_min option[value='-']").remove() ; $('#offline_min').val( offline_min_select ).attr("disabled", false) ;
			$("#offline_ampm option[value='-']").remove() ; $('#offline_ampm').val( offline_ampm_select ).attr("disabled", false) ;
			$("#offline_duration option[value='-']").remove() ; $('#offline_duration').val( offline_duration_select ).attr("disabled", false) ;
		}
		else
		{
			$('#offline_hour').attr("disabled", true) ; $("#offline_hour").append("<option value='-'></option>") ; $("#offline_hour").val("-") ;
			$("#offline_min").append("<option value='-'></option>") ; $("#offline_min").val("-").attr("disabled", true) ;
			$("#offline_ampm").append("<option value='-'></option>") ; $("#offline_ampm").val("-").attr("disabled", true) ;
			$("#offline_duration").append("<option value='-'></option>") ; $("#offline_duration").val("-").attr("disabled", true) ;
		}
	}

	function toggle_idle_minutes_o( theflag )
	{
		if ( theflag )
		{
			var idle_disconnect_minutes_select = <?php echo ( $idle_disconnect_o ) ? $idle_disconnect_o : 15 ; ?> ;
			$('#idle_disconnect_minutes_o').attr("disabled", false) ;
			$("#idle_disconnect_minutes_o option[value='-']").remove() ;
			$('#idle_disconnect_minutes_o').val( idle_disconnect_minutes_select ) ;
		}
		else
		{
			$('#idle_disconnect_minutes_o').attr("disabled", true) ;
			$("#idle_disconnect_minutes_o").append("<option value='-'></option>") ;
			$("#idle_disconnect_minutes_o").val("-") ;
		}
	}

	function toggle_idle_minutes_v( theflag )
	{
		if ( theflag )
		{
			var idle_disconnect_minutes_select = <?php echo ( $idle_disconnect_v ) ? $idle_disconnect_v : 15 ; ?> ;
			$('#idle_disconnect_minutes_v').attr("disabled", false) ;
			$("#idle_disconnect_minutes_v option[value='-']").remove() ;
			$('#idle_disconnect_minutes_v').val( idle_disconnect_minutes_select ) ;
		}
		else
		{
			$('#idle_disconnect_minutes_v').attr("disabled", true) ;
			$("#idle_disconnect_minutes_v").append("<option value='-'></option>") ;
			$("#idle_disconnect_minutes_v").val("-") ;
		}
	}

	function toggle_prechat( theflag )
	{
		if ( theflag )
		{
			$('#div_prechat_skip').hide() ;
			$('#div_prechat').show() ;
		}
		else
		{
			$('#div_prechat').hide() ;
			$('#div_prechat_skip').show() ;
		}
	}

//-->
</script>
</head>
<body>

<div id="iframe_body" style="height: 350px;">
	<?php if ( $action != "canned" ): ?>
	<form action="iframe_edit.php?submit" id="form_settings" method="POST" accept-charset="<?php echo $LANG["CHARSET"] ?>">
	<input type="hidden" name="ses" value="<?php echo $ses ?>">
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="sub" value="<?php echo $action ?>">
	<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
	<input type="hidden" name="option" value="<?php echo $option ?>">
	<div style="">
		<div style="">
			<div id="info_greeting" style="display: none;">
				<div style="">The following message will be displayed to the visitor while being connected with an operator.</div>
				<div style="margin-top: 10px;"><b>%%visitor%%</b> = visitor's name</div>
			</div>
			<div id="info_offline" style="display: none;">
				<div style="font-weight: bold; font-size: 14px;">Standard Offline Message:</div>
				<div style="margin-top: 5px;">Display the following Offline Message when operators are offline.</div>
				<div style="margin-top: 10px;"></div>
			</div>
			<div id="info_temail" style="display: none;">
				<div style="">If <a href="JavaScript:void(0)" onClick="do_redirect('settings')">email transcript</a> is enabled, the following template will be used to generate the outgoing email transcript message sent to the visitor.</div>
				<div style="margin-top: 15px;">
					<img src="../pics/icons/email.png" width="16" height="16" border="0" alt=""> From: 
					<span class="info_neutral" style="cursor: pointer;" onclick="$('#femail_op').prop('checked', true)"><input type="radio" name="femail" id="femail_op" value="0" <?php echo ( !isset( $deptvars["trans_f_dept"] ) || !$deptvars["trans_f_dept"] ) ? "checked" : "" ; ?>> Operator Email Address</span>
					<span class="info_neutral" style="margin-left: 5px; cursor: pointer;" onclick="$('#femail_dept').prop('checked', true)"><input type="radio" name="femail" id="femail_dept" value="1" <?php echo ( isset( $deptvars["trans_f_dept"] ) && $deptvars["trans_f_dept"] ) ? "checked" : "" ; ?>> Department Email Address</span>
				</div>
			</div>
			<div id="info_hours" style="display: none;">
				<div style="">Automatically set department to go OFFLINE at a specific time.  The online operators will be automatically logged off and to limit accidental online status during offline hours, the system will also prevent the operator from going online during the "Offline Duration".</div>
				<div class="info_box" style="margin-top: 10px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> This feature is mainly to prevent an operator from leaving the console open during offline hours.  It's to ensure a global offline and logout time.</div>
			</div>
			<div id="info_smtp" style="display: none;">
				<div style="">As a default, the system will utilize the standard PHP mail() function using the web server mail settings to send out emails (transcripts, offline messages, etc).  However, if the department SMTP values are provided, emails will be sent using the external SMTP provider.</div>
			</div>
		</div>
		<div style="">
			<?php if ( $action == "temail" ): ?>
			<div style="padding-top: 15px; padding-bottom: 15px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" width="300" nowrap>
						<textarea type="text" cols="50" rows="10" id="message" name="message"><?php echo preg_replace( "/\"/", "&quot;", $message ) ?></textarea>
					</td>
					<td><img src="../pics/space.gif" width="55" height=1></td>
					<td valign="top" width="100%">
						<div class="info_box">
						<ul>
							<li style="margin-top: 5px;"> Dynamically populated variables:
								<ul style="margin-top: 10px;">
									<li> <b>%%transcript%%</b> = the chat transcript
									<li> <b>%%visitor%%</b> = visitor's name
									<li> <b>%%operator%%</b> = operator name
									<li> <b>%%op_email%%</b> = operator email
								</ul>
						</ul>
						</div>
					</td>
				</tr>
				</table>
			</div>
			<?php elseif ( $action == "hours" ): ?>
			<div style="padding-top: 15px; padding-bottom: 15px;">
				<div class="info_info">
					<table cellspacing=0 cellpadding=2 border=0>
					<tr>
						<td valign="top">
							<div style="font-size: 14px; font-weight: bold; text-align: center;">Automatic Offline</div>
							<div style="margin-top: 10px;">
								<div class="info_error" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onClick="$('#offline_hours_onoff_off').prop('checked',true);toggle_offline_hours(0);"><input type="radio" name="offline_hours_onoff" id="offline_hours_onoff_off" value=0 onClick=""> Off</div>
								<div class="info_good" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onClick="$('#offline_hours_onoff_on').prop('checked',true);toggle_offline_hours(1);"><input type="radio" name="offline_hours_onoff" id="offline_hours_onoff_on" value=1> On</div>
								<div style="clear: both;"></div>
							</div>
						</td>
						<td style="padding-left: 15px;">
							<div id="auto_offline_settings">
							<table cellspacing=0 cellpadding=2 border=0>
							<tr>
								<td>Offline Time</td>
								<td>
									<table cellspacing=0 cellpadding=0 border=0>
									<tr>
										<td>
											<select name="offline_hour" id="offline_hour">
											<?php for( $c = 1; $c <= 12; ++$c ) { print "<option value='$c'>$c</option>" ; } ?>
											</select>
										</td><td> : </td>
										<td>
											<select name="offline_min" id="offline_min">
											<?php $mins = Array( "00", "15", "30", "45" ) ; for( $c = 0; $c < count( $mins ); ++$c ) { $c_out = $mins[$c] ; print "<option value='$c_out'>$c_out</option>" ; } ?>
											</select>
										</td><td> &nbsp; </td>
										<td>
											<select name="offline_ampm" id="offline_ampm">
											<option value="pm">pm</option><option value="am">am</option>
											</select> today 
										</td><td> &nbsp; </td>
										<td style="padding-left: 25px;">
											<div class="info_neutral">the current <a href="interface.php?ses=<?php echo $ses ?>&jump=time" target="_parent">system time</a> is <span id="span_system_time" style="color: #79C2EB; font-size: 16px; font-weight: bold;"><?php echo date( "M j g:i a", time() ) ; ?></span></div>
										</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>Offline Duration</td>
								<td>
									<select name="offline_duration" id="offline_duration">
									<?php for( $c = 1; $c <= 17; ++$c ) { print "<option value='$c'>$c</option>" ; } ?>
									</select> hours
								</td>
							</tr>
							</table>
							</div>
						</td>
					</tr>
					</table>
				</div>
			</div>
			<?php elseif ( $action == "settings" ): ?>
			<div style="padding-bottom: 15px; text-align: justify;">
				<div style="float: left; min-height: 180px; width: 370px" class="info_info">
					<div style="font-weight: bold; font-size: 14px;">Email Transcript</div>
					<div style="margin-top: 5px;">Provide visitors an option to receive the chat transcript by email when chat session ends.</div>
					<div style="margin-top: 10px;">
						<div class="li_op round" style="cursor: pointer;" onclick="$('#temail_1').prop('checked', true)"><input type="radio" name="temail" id="temail_1" value="1" checked> Yes </div>
						<div class="li_op round" style="cursor: pointer;" onclick="$('#temail_0').prop('checked', true)"><input type="radio" name="temail" id="temail_0" value="0"> No</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div style="float: left; margin-left: 2px; min-height: 180px; width: 370px;" class="info_info">
					<div style="">Send a copy of the chat transcript to the department email address (<span style="color: #4285F4; font-weight: bold;"><?php echo $deptinfo["email"] ?></span>) when chat session ends?</div>
					<div style="margin-top: 10px;">
						<div class="li_op round" style="cursor: pointer;" onclick="$('#temaild_1').prop('checked', true)"><input type="radio" name="temaild" id="temaild_1" value="1" checked> Yes</div>
						<div class="li_op round" style="cursor: pointer;" onclick="$('#temaild_0').prop('checked', true)"><input type="radio" name="temaild" id="temaild_0" value="0"> No</div>
						<div style="clear: both;"></div>
					</div>

					<div style="margin-top: 15px;">Send a copy of the chat transcript to the following email address when chat session ends. (leave blank to inactivate)</div>
					<div style="margin-top: 10px;">
						<input type="text" style="width: 50%" id="emailt" name="emailt" maxlength="160" value="<?php echo $deptinfo["emailt"] ?>" onKeyPress="return justemails(event)">
					</div>
					<div style="display: none; margin-top: 10px;">
						<input type="checkbox" name="emailt_bcc" id="emailt_bcc" value=1 class="select" <?php echo ( $deptinfo["emailt_bcc"] ) ? "checked" : "" ; ?>> send as BCC
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
			<script language="JavaScript">
			<!--
				$( "input#temail_"+<?php echo $deptinfo["temail"] ?> ).prop( "checked", true ) ;
				$( "input#temaild_"+<?php echo $deptinfo["temaild"] ?> ).prop( "checked", true ) ;
			//-->
			</script>
			<?php
				elseif ( $action == "custom" ):
				$custom_field = ( $deptinfo["custom"] ) ? unserialize( $deptinfo["custom"] ) : Array() ;
			?>
			<div style="padding-bottom: 15px; text-align: justify;">
				<div style="margin-bottom: 15px; display: none;">
					<div class="li_op round"><input type="radio" name="prechat" id="prechat_1" value="1" onClick="toggle_prechat(1)" <?php echo ( !isset( $deptvars['prechat_form'] ) || $deptvars['prechat_form'] ) ? "checked" : "" ; ?> > Display the pre-chat form</div>
					<div class="li_op round"><input type="radio" name="prechat" id="prechat_0" value="0" onClick="toggle_prechat(0)" <?php echo ( isset( $deptvars['prechat_form'] ) && !$deptvars['prechat_form'] ) ? "checked" : "" ; ?> > Skip the pre-chat form</div>
					<div style="clear: both;"></div>
				</div>
				<div>
					<div id="div_prechat" style="display: none;">
						<div style="float: left; min-height: 180px; width: 370px;" class="info_info">
							<div>Should the email address be required before starting a chat session?  Selecting "Yes" will set the email field as required.  Selecting "No" will set the email field as optional.</div>
							<div style="margin-top: 10px;">
								<div class="li_op round" style="cursor: pointer;" onclick="$('#remail_1').prop('checked', true);"><input type="radio" name="remail" id="remail_1" value="1" checked> Yes</div>
								<div class="li_op round" style="cursor: pointer;" onclick="$('#remail_0').prop('checked', true);"><input type="radio" name="remail" id="remail_0" value="0"> No</div>
								<div style="clear: both;"></div>
							</div>

							<div style="margin-top: 15px;">A question required to chat?  Selecting "Yes" will set the question field as required.  Selecting "No" will hide the question field.</div>
							<div style="margin-top: 10px;">
								<div class="li_op round" style="cursor: pointer;" onclick="$('#rquestion_1').prop('checked', true);"><input type="radio" name="rquestion" id="rquestion_1" value="1" checked> Yes</div>
								<div class="li_op round" style="cursor: pointer;" onclick="$('#rquestion_0').prop('checked', true);"><input type="radio" name="rquestion" id="rquestion_0" value="0"> No</div>
								<div style="clear: both;"></div>
							</div>
						</div>
						<div style="float: left; margin-left: 2px; min-height: 180px; width: 370px;" class="info_info">
							<div>Add a custom field on the chat request window.</div>
							<div style="margin-top: 10px; text-shadow: none;" class="info_box">Current fields are Name, Email and Question.  Additional field could be "Login", "Phone", "Order Number", etc.</div>
							<div style="margin-top: 10px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td><input type="text" class="input" size="15" maxlength="20" id="custom_field" name="custom_field" value="<?php echo isset( $custom_field[0] ) ? $custom_field[0] : "" ; ?>"></td>
									<td style="padding-left: 10px;"><select name="custom_field_required" class="select"><option value=1>required to chat</option><option value=0 <?php echo ( isset( $custom_field[1] ) && !$custom_field[1] ) ? "selected" : "" ; ?>>optional</option></select></td>
								</tr>
								</table>
							</div>
						</div>
						<div style="clear: both;"></div>
					</div>
					<div id="div_prechat_skip" style="display: none;">
						Do not display the pre-chat form.  Route the chat request to available operators immediately after opening the chat request window.
					</div>
				</div>
			</div>
			<script type="text/javascript">
			<!--
				$( "input#remail_"+<?php echo $deptinfo["remail"] ?> ).prop( "checked", true ) ;
				$( "input#rquestion_"+<?php echo $deptinfo["rquestion"] ?> ).prop( "checked", true ) ;
			//-->
			</script>
			<?php elseif ( $action == "smtp" ): ?>
			<div style="padding-top: 15px; padding-bottom: 15px; text-align: justify;">
				<?php if ( is_file( "../addons/smtp/smtp.php" ) ): ?>
					<img src="../pics/icons/info.png" width="12" height="12" border="0" alt="enabled"> SMTP settings can be updated from the <a href="../addons/smtp/smtp.php?ses=<?php echo $ses ?>&deptid=<?php echo $deptid ?>" target="_parent">Extras-&gt;SMTP</a> area.
				<?php else: ?>
					<img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Currently, the department emails are sent using the standard PHP mail() function with the web server mail settings.  To send emails using an external SMTP server, visit the <a href="http://www.phplivesupport.com/r.php?r=smtp" target="_blank">SMTP documentations</a> page for more information about the SMTP addon.
				<?php endif ; ?>
			</div>
			<?php else: ?>
			<div style="margin-top: 15px; padding-bottom: 15px;"><input type="text" style="width: 95%" id="message" name="message" maxlength="155" value="<?php echo preg_replace( "/\"/", "&quot;", $message ) ?>"></div>

			<div id="div_offline_busy" style="display: none;">
				<div style="margin-top: 15px; font-weight: bold; font-size: 14px;">"Busy" Offline Message:</div>
				<div style="margin-top: 5px;">Display the following Offline Message when operators are online but were unable to accept the chat request.</div>
				<div style="margin-top: 15px; padding-bottom: 15px;"><input type="text" style="width: 95%" id="message_busy" name="message_busy" maxlength="155" value="<?php echo preg_replace( "/\"/", "&quot;", $deptinfo["msg_busy"] ) ?>"></div>
			</div>
			<?php endif ; ?>

			<?php if ( $action == "greeting" ): ?>
			<div style="margin-bottom: 15px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td width="50%" style="padding-right: 1px;">
						<div style="" class="info_neutral">
							<div style="font-size: 14px; font-weight: bold;">Operator Idle Chat Disconnect</div>
							<div style="margin-top: 5px;">
								<table cellspacing=0 cellpadding=2 border=0>
								<tr>
									<td valign="top" width="150">
										<div>
											<div class="info_error" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#idle_disconnect_o_onoff_off').prop('checked', true);toggle_idle_minutes_o(0);"><input type="radio" name="idle_disconnect_o_onoff" id="idle_disconnect_o_onoff_off" value=0> Off</div>
											<div class="info_good" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#idle_disconnect_o_onoff_on').prop('checked', true);toggle_idle_minutes_o(1);"><input type="radio" name="idle_disconnect_o_onoff" id="idle_disconnect_o_onoff_on" value=1> On</div>
											<div style="clear: both;"></div>
										</div>
									</td>
									<td style="padding-left: 15px;">
										<div>Automatically disconnect the chat session if the <b>operator</b> has not sent a chat response within:</div>
										<div style="margin-top: 5px;"><select id="idle_disconnect_minutes_o" name="idle_disconnect_minutes_o">
										<?php for( $c = 10; $c <= 30; ++$c ) { print "<option value=\"$c\">$c</option>" ; } ?>
										</select>
										minutes.</div>
									</td>
								</tr>
								</table>
							</div>
						</div>
					</td>
					<td width="50%" style="padding-left: 1px;">
						<div style="" class="info_neutral">
							<div style="font-size: 14px; font-weight: bold;">Visitor Idle Chat Disconnect</div>
							<div style="margin-top: 5px;">
								<table cellspacing=0 cellpadding=2 border=0>
								<tr>
									<td valign="top" width="150">
										<div>
											<div class="info_error" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#idle_disconnect_v_onoff_off').prop('checked', true);toggle_idle_minutes_v(0);"><input type="radio" name="idle_disconnect_v_onoff" id="idle_disconnect_v_onoff_off" value=0> Off</div>
											<div class="info_good" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#idle_disconnect_v_onoff_on').prop('checked', true);toggle_idle_minutes_v(1);"><input type="radio" name="idle_disconnect_v_onoff" id="idle_disconnect_v_onoff_on" value=1> On</div>
											<div style="clear: both;"></div>
										</div>
									</td>
									<td style="padding-left: 15px;">
										<div>Automatically disconnect the chat session if the <b>visitor</b> has not sent a chat response within:</div>
										<div style="margin-top: 5px;"><select id="idle_disconnect_minutes_v" name="idle_disconnect_minutes_v">
										<?php for( $c = 10; $c <= 30; ++$c ) { print "<option value=\"$c\">$c</option>" ; } ?>
										</select>
										minutes.</div>
									</td>
								</tr>
								</table>
							</div>
						</div>
					</td>
				</tr>
				</table>
			</div>
			<?php endif ; ?>

			<?php if ( ( count( $departments ) > 1 ) && !preg_match( "/(smtp)/", $action ) ) : ?>
			<div style=""><input type="checkbox" id="copy_all" name="copy_all" value=1> copy this update to all departments</div>
			<?php endif ; ?>

			<?php if ( !preg_match( "/(smtp)/", $action ) ): ?>
			<div style="padding-top: 5px;"><input type="button" value="Update" class="btn" onClick="do_submit_settings()"> &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="parent.do_options( <?php echo $option ?>, <?php echo $deptid ?> );">cancel</a></div>
			<?php endif ; ?>
		</div>
	</div>
	</form>
	<script type="text/javascript">$('#info_<?php echo $action ?>').show();</script>

	<?php
		else:
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;

		$departments = Depts_get_AllDepts( $dbh ) ;
		$operators = Ops_get_AllOps( $dbh ) ;

		// make hash for quick refrence
		$operators_hash = Array() ;
		$operators_hash[1111111111] = "<img src=\"../pics/icons/lock.png\" width=\"16\" height=\"16\" border=\"0\" title=\"created by Setup Admin\" title=\"created by Setup Admin\">" ;
		for ( $c = 0; $c < count( $operators ); ++$c )
		{
			$operator = $operators[$c] ;
			$operators_hash[$operator["opID"]] = $operator["name"] ;
		}

		// make hash for quick refrence
		$dept_hash = Array() ;
		$dept_hash[1111111111] = "All Departments" ;
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			$dept_hash[$department["deptID"]] = $department["name"] ;
		}

		$cans = Canned_get_DeptCanned( $dbh, $deptid, $page, 100 ) ;
	?>
	<div id="canned_list" style="overflow: auto;">
		<div><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Canned responses created here by the <b>Setup Admin</b> will be available to <a href="ops.php?ses=<?php echo $ses ?>&jump=assign" target="_parent">all operators that are assigned to this department</a></div>
		<div style="margin-top: 5px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Canned responses created by an operator is available only to the operator.</div>
		<div id="div_new_canned_<?php echo $deptid ?>" class="info_box" style="margin-top: 15px; width: 80px; padding-left: 25px; background: url( ../pics/icons/add.png ) no-repeat #FAFAA6; background-position: 5px 5px; border-bottom: 0px solid; cursor: pointer; border-bottom-left-radius: 0px; -moz-border-radius-bottomleft: 0px; border-bottom-right-radius: 0px; -moz-border-radius-bottomright: 0px;" onClick="parent.new_canned( <?php echo $deptid ?> )">new canned</div>

		<table cellspacing=0 cellpadding=0 border=0 width="100%">
		<tr>
			<td width="18" nowrap><div class="td_dept_header">&nbsp;</div></td>
			<td width="120" nowrap><div class="td_dept_header">Title</div></td>
			<td width="80" nowrap><div class="td_dept_header">Creator</div></td>
			<td width="180"><div class="td_dept_header">Department</div></td>
			<td><div class="td_dept_header">Message</div></td>
		</tr>
		<?php
			for ( $c = 0; $c < count( $cans )-1; ++$c )
			{
				$can = $cans[$c] ;
				$title = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", $can["title"] ) ) ;
				$title_display = preg_replace( "/\"/", "&quot;", $can["title"] ) ;

				$op_name = $operators_hash[$can["opID"]] ;
				$dept_name = $dept_hash[$can["deptID"]] ;
				$message = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $can["message"] ) ) ) ;
				$message_display = preg_replace( "/\"/", "&quot;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", Util_Format_ConvertTags( $can["message"] ) ) ) ;

				$td1 = "td_dept_td" ;

				print "<tr id=\"tr_div_$can[canID]\"><td class=\"$td1\" nowrap><div onClick=\"do_edit($can[canID], '$title', '$can[deptID]', '$message')\" style=\"cursor: pointer;\"><img src=\"../pics/btn_edit.png\" width=\"55\" height=\"20\" border=\"0\" alt=\"\"></div><div onClick=\"do_delete($can[canID])\" style=\"margin-top: 5px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"55\" height=\"20\" border=\"0\" alt=\"\"></div></td><td class=\"$td1\"><b>$title_display</b></td><td class=\"$td1\" nowrap>$op_name</td><td class=\"$td1\">$dept_name</td><td class=\"$td1\">$message_display</td></tr>" ;
			}
			if ( $c == 0 )
				print "<tr><td colspan=7 class=\"td_dept_td\">Blank results.</td></tr>" ;
		?>
		</table>
		<div class="chat_info_end"></div>
	</div>

	<div id="canned_box_new" style="display: none; position: fixed; top: 0px; left: 0px; background: #FFFFFF; height: 100%; padding: 5px; z-Index: 5;">
		<form method="POST" action="iframe_edit.php?<?php echo time() ?>" id="theform" accept-charset="<?php echo $LANG["CHARSET"] ?>">
		<table cellspacing=0 cellpadding=0 border=0 width="100%">
		<tr>
			<td width="380" style="padding: 5px;" nowrap>
				<input type="hidden" name="ses" value="<?php echo $ses ?>">
				<input type="hidden" name="action" value="update">
				<input type="hidden" name="sub" value="<?php echo $action ?>">
				<input type="hidden" name="sub_deptid" value="<?php echo $deptid ?>">
				<input type="hidden" name="option" value="<?php echo $option ?>">
				<input type="hidden" name="canid" id="canid" value="0">
				<div>
					Reference (example: "Welcome greeting", "Just a moment")<br>
					<input type="text" name="title" id="title" class="input" style="width: 98%; margin-bottom: 10px;" maxlength="25">
					Set this canned response available to Department:<br>
					<select name="deptid" id="deptid" style="width: 99%; margin-bottom: 10px;">
					<option value="1111111111">All Departments</option>
					<?php
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;
							if ( $department["deptID"] == $deptid )
							{
								$selected = ( $department["deptID"] == $deptid ) ? "selected" : "" ;
								print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
							}
						}
					?>
					</select>
					Canned Message<br>
					<textarea name="message" id="message" class="input" rows="5" style="width: 98%; margin-bottom: 10px;" wrap="virtual"></textarea>

					<button type="button" onClick="do_submit()" class="btn">Submit</button> &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="toggle_new(0)">cancel</a>
				</div>
			</td>
			<td><img src="../pics/space.gif" width="55" height=1></td>
			<td width="100%">
				<ul>
					<li> HTML will be converted to raw code.
					<li style="margin-top: 5px;"> Dynamically populated variables:
						<ul style="margin-top: 10px;">
							<li> <b>%%visitor%%</b> = visitor's name
							<li> <b>%%operator%%</b> = your name
							<li> <b>%%op_email%%</b> = your email
						</ul>
					<li style="margin-top: 10px;"> To display an image on chat, use the <b>image:</b> prefix
						<ul style="margin-top: 10px;">
							example:
							<li style=""> <b>image:</b><i>http://www.phplivesupport.com/pics/logo_small.png</i>
						</ul>
				</ul>
			</td>
		</tr>
		</table>
		</form>
	</div>

	<?php endif ; ?>
</div>

</body>
</html>
