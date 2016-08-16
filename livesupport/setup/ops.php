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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }

	$https = "" ; $error = "" ;
	if ( isset( $_SERVER["HTTP_CF_VISITOR"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_CF_VISITOR"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTP_X_FORWARDED_PROTO"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_X_FORWARDED_PROTO"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTPS"] ) && preg_match( "/(on)/i", $_SERVER["HTTPS"] ) ) { $https = "s" ; }

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ; if ( !$jump ) { $jump = "main" ; }

	if ( $action == "submit" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_ext.php" ) ;

		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
		$rate = Util_Format_Sanatize( Util_Format_GetVar( "rate" ), "n" ) ;
		$sms = Util_Format_Sanatize( Util_Format_GetVar( "sms" ), "n" ) ;
		$op2op = Util_Format_Sanatize( Util_Format_GetVar( "op2op" ), "n" ) ;
		$traffic = Util_Format_Sanatize( Util_Format_GetVar( "traffic" ), "n" ) ;
		$viewip = Util_Format_Sanatize( Util_Format_GetVar( "viewip" ), "n" ) ;
		$maxc = Util_Format_Sanatize( Util_Format_GetVar( "maxc" ), "n" ) ;
		$maxco = Util_Format_Sanatize( Util_Format_GetVar( "maxco" ), "n" ) ;
		$login = Util_Format_Sanatize( Util_Format_GetVar( "login" ), "ln" ) ;
		$password = Util_Format_Sanatize( Util_Format_GetVar( "password" ), "ln" ) ;
		$name = Util_Format_Sanatize( Util_Format_GetVar( "name" ), "ln" ) ;
		$email = Util_Format_Sanatize( Util_Format_GetVar( "email" ), "e" ) ;
		$mapper = Util_Format_Sanatize( Util_Format_GetVar( "mapper" ), "n" ) ;
		$nchats = Util_Format_Sanatize( Util_Format_GetVar( "nchats" ), "n" ) ;

		$total_ops = Ops_get_TotalOps( $dbh ) ;
	
		if ( isset( $VARS_MAX_OPS ) && ( $total_ops >= $VARS_MAX_OPS ) && !$opid )
			$error = "Max allowed operators have been reached." ;
		else
		{
			$error = Ops_put_Op( $dbh, $opid, 1, $mapper, $rate, $sms, $op2op, $traffic, $viewip, $nchats, $maxc, $maxco, $login, $password, $name, $email ) ;
			if ( is_numeric( $error ) ) { $error = "" ; }
		}
	}
	else if ( $action == "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/remove.php" ) ;

		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
		$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;

		if ( isset( $opinfo["opID"] ) )
		{
			$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
			if ( $opinfo["mapp"] && is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;
				if ( isset( $mapp_array[$opid] ) ) { $arn = $mapp_array[$opid]["a"] ; $platform = $mapp_array[$opid]["p"] ; }
				if ( isset( $arn ) && $arn ) { Util_MAPP_Publish( $opid, "new_request", $platform, $arn, "Account not found.  You are Offline." ) ; }
			}
			Ops_remove_Op( $dbh, $opid ) ;
		}
	}
	else if ( $action == "submit_assign" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put.php" ) ;

		$opids = Util_Format_GetVar( "opids" ) ;
		$deptids = Util_Format_GetVar( "deptids" ) ;

		for ( $c = 0; $c < count( $opids ); ++$c )
		{
			$opid = Util_Format_Sanatize( $opids[$c], "n" ) ;
			for ( $c2 = 0; $c2 < count( $deptids ); ++$c2 )
			{
				$deptid = Util_Format_Sanatize( $deptids[$c2], "n" ) ;
				$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;
				$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
				Ops_put_OpDept( $dbh, $opid, $deptid, $deptinfo["visible"], $opinfo["status"] ) ;
			}
		}
	}

	Ops_update_itr_IdleOps( $dbh ) ;
	$operators = Ops_get_AllOps( $dbh ) ;
	$departments = Depts_get_AllDepts( $dbh ) ;

	$login_url = $CONF['BASE_URL'] ;
	if ( !preg_match( "/\/\//", $login_url ) ) { $login_url = "//$PHPLIVE_HOST$login_url" ; }
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
	var global_div_list_height ;
	var global_div_form_height ;
	var global_opid ;
	var st_rd ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;
		$("body").show() ;

		init_menu() ;
		toggle_menu_setup( "ops" ) ;
		init_divs() ;
		init_op_dept_list() ;

		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( $action && !$error ): ?>
		do_alert( 1, "Success" ) ;
		<?php elseif ( $error ): ?>
		do_alert( 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>

		$('#login').bind("paste",function(e) {
			e.preventDefault() ;
		});
	});

	function init_divs()
	{
		global_div_list_height = $('#div_list').outerHeight() ;
		global_div_form_height = $('#div_form').outerHeight() ;
	}

	function init_op_dept_list() { <?php for ( $c = 0; $c < count( $departments ); ++$c ) { $department = $departments[$c] ; if ( $department["name"] != "Archive" ) { print "op_dept_moveup( $department[deptID], 0 ) ;" ; } } ?> }

	function do_edit( theopid, thename, theemail, thelogin, therate, thesms, theop2op, thetraffic, theviewip, themaxc, themaxco, themapper, thenchats )
	{
		show_form() ;
		$( "input#opid" ).val( theopid ) ;
		$( "input#name" ).val( thename ) ;
		$( "input#email" ).val( theemail ) ;
		$( "input#login" ).val( thelogin ) ;
		$( "input#password_temp" ).val( "php-live-support" ) ;
		$( "select#maxc" ).val( themaxc ) ;
		$( "input#maxco_"+themaxco ).prop( "checked", true ) ;
		$( "input#rate_"+therate ).prop( "checked", true ) ;
		$( "input#sms_"+thesms ).prop( "checked", true ) ;
		$( "input#op2op_"+theop2op ).prop( "checked", true ) ;
		$( "input#traffic_"+thetraffic ).prop( "checked", true ) ;
		$( "input#viewip_"+theviewip ).prop( "checked", true ) ;
		$( "input#mapper_"+themapper ).prop( "checked", true ) ;
		$( "input#nchats_"+thenchats ).prop( "checked", true ) ;
	}

	function do_notice( thediv, theopid, thelogin )
	{
		if ( ( thediv == "disconnect" ) && ( typeof( st_rd ) != "undefined" ) ) { do_alert( 0, "Another disconnect in progress." ) ; return false ; }

		var pos = $('#div_tr_'+theopid).position() ;
		var width = $('#div_tr_'+theopid).outerWidth() ;
		var height = $('#div_tr_'+theopid).outerHeight() - 8 ;

		global_opid = theopid ;

		if ( $('#div_notice_'+thediv).is(':visible') )
			$('#div_notice_'+thediv).fadeOut( "fast", function() { show_div_notice(thediv, thelogin, pos, width, height) ; }) ;
		else
			show_div_notice(thediv, thelogin, pos, width, height) ;
	}

	function do_delete_doit()
	{
		location.href = "ops.php?ses=<?php echo $ses ?>&action=delete&opid="+global_opid ;
	}

	function show_div_notice( thediv, thelogin, thepos, thewidth, theheight )
	{
		$('#span_login_'+thediv).html( thelogin ) ;
		$('#div_notice_'+thediv).css({'top': thepos.top, 'left': thepos.left, 'width': thewidth, 'height': theheight}).fadeIn("fast") ;
	}

	function do_submit()
	{
		var name = encodeURIComponent( $( "input#name" ).val() ) ;
		var email = $( "input#email" ).val() ;
		var login = encodeURIComponent( $( "input#login" ).val() ) ;
		var password = phplive_md5( $( "input#password_temp" ).val() ) ; $( "input#password" ).val( password ) ;

		if ( name == "" )
			do_alert( 0, "Please provide a name." ) ;
		else if ( !check_email( email ) )
			do_alert( 0, "Please provide a valid email address." ) ;
		else if ( login == "" )
			do_alert( 0, "Please provide a login." ) ;
		else if ( password == "" )
			do_alert( 0, "Please provide a password." ) ;
		else if ( login == "<?php echo $admininfo["login"] ?>" )
			do_alert( 0, "Operator login cannot be the same as the setup admin login." ) ;
		else
		{
			email = encodeURIComponent( email ) ;
			$('#theform').submit() ;
		}
	}

	function show_div( thediv )
	{
		var divs = Array( "main", "assign", "report", "monitor", "online" ) ;

		if ( $('#div_form').is(':visible') )
			do_reset() ;

		for ( var c = 0; c < divs.length; ++c )
		{
			$('#edit').hide() ;

			$('#ops_'+divs[c]).hide() ;
			$('#menu_ops_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		if ( thediv == "main" )
			$('#edit').show() ;

		$('input#jump').val( thediv ) ;
		$('#ops_'+thediv).show() ;
		$('#menu_ops_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function op_dept_moveup( thedeptid, theopid )
	{
		var json_data = new Object ;
		$('#dept_ops_'+thedeptid).css({'opacity': 1, 'z-Index': -10}) ;

		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=moveup&deptid="+thedeptid+"&opid="+theopid+"&"+unixtime(),
			success: function(data){
				eval( data ) ;

				if ( json_data.ops != undefined )
				{
					var ops_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"92%\">" ;
					for ( var c = 0; c < json_data.ops.length; ++c )
					{
						var name = json_data.ops[c]["name"] ;
						var opid = json_data.ops[c]["opid"] ;
						var move_up = ( c ) ? "&nbsp; <a href=\"JavaScript:void(0)\" onClick=\"op_dept_moveup( "+thedeptid+", "+opid+" )\">move up</a>" : "&nbsp;" ;

						ops_string += "<tr><td class=\"td_dept_td\" nowrap><a href=\"JavaScript:void(0)\" onClick=\"op_dept_remove( "+thedeptid+", "+opid+" )\"><img src=\"../pics/icons/delete.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"delete\" title=\"delete\"></a> "+move_up+"</td><td class=\"td_dept_td\">"+name+"</td></tr>" ;
					}
					if ( c == 0 )
						ops_string += "<tr><td colspan=7 class=\"td_dept_td\">Blank results.</td></tr>" ;
				}
				ops_string += "</table>" ;
				$('#dept_ops_'+thedeptid).html( ops_string ) ;
				setTimeout(function(){ $('#dept_ops_'+thedeptid).css({'opacity': 1, 'z-Index': 10}) ; }, 500) ;
			}
		});
	}

	function op_dept_remove( thedeptid, theopid )
	{
		var json_data = new Object ;
		$('#dept_ops_'+thedeptid).css({'opacity': 1, 'z-Index': -10}) ;

		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=op_dept_remove&deptid="+thedeptid+"&opid="+theopid+"&"+unixtime(),
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
					op_dept_moveup( thedeptid, 0 ) ;
			}
		});
	}

	function remote_disconnect()
	{
		var json_data = new Object ;
		$('#remote_disconnect_button').hide() ;
		$('#remote_disconnect_notice').show() ;

		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=remote_disconnect&opid="+global_opid+"&"+unixtime(),
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
					check_op_status( global_opid ) ;
				else
				{
					$('#remote_disconnect_notice').hide() ;
					do_alert( 0, "Could not remote disconnect console.  Please try again." ) ;
				}
			}
		});
	}

	function check_op_status( theopid )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( typeof( st_rd ) != "undefined" ) { clearTimeout( st_rd ) ; }

		$.ajax({
		type: "POST",
		url: "../wapis/status_op.php",
		data: "opid="+theopid+"&jkey=<?php echo md5( $CONF['API_KEY'] ) ?>&"+unique,
		success: function(data){
			eval( data ) ;

			if ( !parseInt( json_data.status ) )
				location.href = 'ops.php?ses=<?php echo $ses ?>&action=success' ;
			else
				st_rd = setTimeout( function(){ check_op_status( theopid ) ; }, 2000 ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Lost connection to server.  Please reload the page and try again." ) ;
		} });
	}

	function launch_tools_op_status()
	{
		var url = "tools_op_status.php?ses=<?php echo $ses ?>&pop=1" ;

		if ( <?php echo count( $operators ) ?> > 0 )
			window.open( url, "Operators", "scrollbars=yes,menubar=no,resizable=1,location=no,width=550,height=550,status=0" ) ;
		else
		{
			if ( confirm( "Operator account does not exist.  Create an operator?" ) )
				location.href = "ops.php?ses=<?php echo $ses ?>" ;
		}
	}

	function check_all_ops( theobject )
	{
		if ( ( typeof( theobject ) != "undefined" ) && ( theobject.checked ) )
		{
			$( '#div_list_ops' ).find('*').each( function () {
				var div_name = this.id ;
				if ( div_name.indexOf( "ck_op_" ) == 0 )
					this.checked = true ;
			}) ;
		}
		else
		{
			$( '#div_list_ops' ).find('*').each( function () {
				var div_name = this.id ;
				if ( div_name.indexOf( "ck_op_" ) == 0 )
					this.checked = false ;
			}) ;
		}
	}

	function check_all_depts( theobject )
	{
		if ( ( typeof( theobject ) != "undefined" ) && ( theobject.checked ) )
		{
			$( '#div_list_depts' ).find('*').each( function () {
				var div_name = this.id ;
				if ( div_name.indexOf( "ck_dept_" ) == 0 )
					this.checked = true ;
			}) ;
		}
		else
		{
			$( '#div_list_depts' ).find('*').each( function () {
				var div_name = this.id ;
				if ( div_name.indexOf( "ck_dept_" ) == 0 )
					this.checked = false ;
			}) ;
		}
	}

	function do_assign()
	{
		var ok_ops = 0 ;
		var ok_depts = 0 ;

		$( '#div_list_ops' ).find('*').each( function () {
			var div_name = this.id ;
			if ( ( div_name.indexOf( "ck_op_" ) == 0 ) && this.checked )
				ok_ops = 1 ;
		}) ;
		$( '#div_list_depts' ).find('*').each( function () {
			var div_name = this.id ;
			if ( ( div_name.indexOf( "ck_dept_" ) == 0 ) && this.checked )
				ok_depts = 1 ;
		}) ;

		if ( !ok_ops )
			do_alert( 0, "An operator must be selected." ) ;
		else if ( !ok_depts )
			do_alert( 0, "A department must be selected." ) ;
		else
			$('#form_assign').submit() ;
	}

	function show_form()
	{
		$(window).scrollTop(0) ;
		$('#div_list').hide() ;
		$('#div_btn_add').hide() ;
		$('#div_form').show() ;
	}

	function do_reset()
	{
		$('#opid').val(0) ;
		$('#theform').each(function(){
			this.reset();
		});

		$(window).scrollTop(0) ;
		$('#div_form').hide() ;
		$('#div_btn_add').show() ;
		$('#div_list').show() ;
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus" onClick="show_div('main')" id="menu_ops_main">Chat Operators</div>
			<div class="op_submenu" onClick="show_div('assign')" id="menu_ops_assign">Assign Operator to Department</div>
			<div class="op_submenu" onClick="location.href='interface_op_pics.php?ses=<?php echo $ses ?>'">Profile Picture</div>
			<div class="op_submenu" onClick="location.href='ops_reports.php?ses=<?php echo $ses ?>'" id="menu_ops_report">Online Activity</div>
			<div class="op_submenu" onClick="show_div('monitor')" id="menu_ops_monitor">Operator Status Widget</div>
			<div class="op_submenu" onClick="location.href='ops.php?ses=<?php echo $ses ?>&jump=online'" id="menu_ops_online"><img src="../pics/icons/bulb.png" width="12" height="12" border="0" alt=""> Go ONLINE!</div>
			<div style="clear: both"></div>
		</div>

		<div id="ops_main">
			<div id="div_btn_add" class="edit_focus" style="margin-top: 25px;" onClick="show_form()">Add Chat Operator</div>
			<div id="div_list" style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td width="40"><div class="td_dept_header">&nbsp;</div></td>
					<td><div class="td_dept_header">Name</div></td>
					<td><div class="td_dept_header">Login</div></td>
					<td><div class="td_dept_header">Email</div></td>
					<td width="50" nowrap align="center"><div class="td_dept_header" style="cursor: help;" title="maximum concurrent active chat sessions" alt="maximum concurrent chat sessions">Max</div></td>
					<td width="60" nowrap align="center"><div class="td_dept_header">Rate</div></td>
					<td width="80" nowrap align="center"><div class="td_dept_header">Op2Op</div></td>
					<td width="60" nowrap align="center"><div class="td_dept_header">Traffic</div></td>
					<td width="80" nowrap align="center"><div class="td_dept_header">View IP</div></td>
					<td width="60" nowrap align="center"><div class="td_dept_header">Status</div></td>
				</tr>
				<?php
					$image_empty = "<img src=\"../pics/space.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">" ;
					$image_checked = "<img src=\"../pics/icons/check.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">";
					for ( $c = 0; $c < count( $operators ); ++$c )
					{
						$operator = $operators[$c] ;

						$login = $operator["login"] ;
						$email = $operator["email"] ;
						$maxc = ( $operator["maxc"] != -1 ) ? $operator["maxc"] : "&nbsp;" ;
						$rate = ( $operator["rate"] ) ? $image_checked : $image_empty ;
						$sms = ( $operator["sms"] ) ? $image_checked : $image_empty ;
						$op2op = ( $operator["op2op"] ) ? $image_checked : $image_empty ;
						$traffic = ( $operator["traffic"] ) ? $image_checked : $image_empty ;
						$viewip = ( $operator["viewip"] ) ? $image_checked : $image_empty ;
						$status = ( $operator["status"] ) ? "<b>Operator is Online</b><br>Click to disconnect console." : "Offline" ;
						$style = ( $operator["status"] ) ? "cursor: pointer" : "" ;
						$td_style = ( $operator["status"] ) ? "background: #AFFF9F; $style" : "" ;
						$js = ( $operator["status"] ) ? "onClick=\"do_notice('disconnect', $operator[opID], '$login')\"" : "" ;
						$sms_edit = ( $operator["sms"] ) ? 1 : 0 ;
						$profile_image = Util_Upload_GetLogo( "profile", $operator["opID"] ) ;
						$mapp_icon = ( $operator["mapper"] ) ? "<img src=\"../pics/icons/mobile.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"mobile app access\" title=\"mobile app access\" style=\"cursor: help;\"><br> " : "" ;
						$mapp_online_icon = ( $operator["mapp"] ) ? " &nbsp; <img src=\"../pics/icons/mobile.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"logged in on mobile\" title=\"logged in on mobile\" style=\"cursor: help;\">" : "" ;

						$status_img = ( $operator["status"] ) ? "<img src=\"../pics/icons/bulb.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"online\" title=\"online\">$mapp_online_icon<br>close" : "<img src=\"../pics/icons/bulb_off.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"offline\" title=\"offline\">" ;

						$edit_delete = "<div style=\"cursor: pointer;\" onClick=\"do_edit( $operator[opID], '$operator[name]', '$operator[email]', '$operator[login]', $operator[rate], $sms_edit, $operator[op2op], $operator[traffic], $operator[viewip], $operator[maxc], $operator[maxco], $operator[mapper], $operator[nchats] )\"><img src=\"../pics/btn_edit.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div><div onClick=\"do_notice('delete', $operator[opID], '$login')\" style=\"margin-top: 10px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

						$td1 = "td_dept_td" ;

						print "
						<tr id=\"div_tr_$operator[opID]\">
							<td class=\"$td1\" nowrap>$edit_delete</td>
							<td class=\"$td1\">
								<table cellspacing=0 cellpadding=2 border=0>
								<tr>
									<td align=\"center\" style=\"font-size: 10px;\"><a href=\"interface_op_pics.php?ses=$ses&opid=$operator[opID]\"><img src=\"$profile_image\" width=\"55\" height=\"55\" border=\"0\" alt=\"\" style=\"border: 1px solid #DFDFDF;\" class=\"round\"><br>[update]</a></td>
									<td>$mapp_icon$operator[name]</td>
								</tr>
								</table>
							</td>
							<td class=\"$td1\" nowrap>$login</td>
							<td class=\"$td1\">$email</td>
							<td class=\"$td1\" align=\"center\">$maxc</td>
							<td class=\"$td1\" align=\"center\">$rate</td>
							<td class=\"$td1\" align=\"center\">$op2op</td>
							<td class=\"$td1\" align=\"center\">$traffic</td>
							<td class=\"$td1\" align=\"center\">$viewip</td>
							<td class=\"$td1\" align=\"center\" style=\"$td_style\" $js>$status_img</td>
						</tr>
						" ;
					}
					if ( $c == 0 )
						print "<tr><td colspan=11 class=\"td_dept_td\">Blank results.</td></tr>" ;
				?>
				</table>
			</div>
		</div>

		<div style="display: none;" id="ops_assign">

			<div style="margin-top: 25px;">
				<?php if ( !count( $departments ) ): ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> A <a href="depts.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Department</a> must be created to continue.</span>
				<?php elseif ( !count( $operators ) ):  ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> An <a href="ops.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Operator</a> must be created to continue.</span>

				<?php else: ?>
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top">
						<form method="POST" action="ops.php?submit" id="form_assign">
						<input type="hidden" name="ses" value="<?php echo $ses ?>">
						<input type="hidden" name="action" value="submit_assign">
						<input type="hidden" name="jump" value="assign">
						
						<div style="max-height: 300px; overflow: auto;" id="div_list_ops">
							<table cellspacing=0 cellpadding=0 border=0 width="100%">
							<tr>
								<td width="20"><div class="td_dept_header"><input type="checkbox" onClick="check_all_ops(this)" id="ck_op_all"></div></td>
								<td width="10"><div class="td_dept_header">ID</div></td>
								<td><div class="td_dept_header">Operator Name</div></td>
								<td><div class="td_dept_header">Email</div></td>
							</tr>
							<?php
								for ( $c = 0; $c < count( $operators ); ++$c )
								{
									$operator = $operators[$c] ;

									$td1 = "td_dept_td" ;

									print "
									<tr>
										<td class=\"$td1\"><input type=\"checkbox\" id=\"ck_op_$operator[opID]\" name=\"opids[]\" value=\"$operator[opID]\"></td>
										<td class=\"$td1\">$operator[opID]</td>
										<td class=\"$td1\" nowrap>
											<div style=\"\">$operator[name]</div>
										</td>
										<td class=\"$td1\">$operator[email]</td>
									</tr>
									" ;
								}
							?>
							</table>
						</div>
						<div style="margin-top: 15px;" id="div_list_depts">
							<div class=""><img src="../pics/icons/arrow_top.png" width="15" height="16" border="0" alt=""> Assign the above checked operator(s) to the following departments <img src="../pics/icons/arrow_down.png" width="15" height="16" border="0" alt=""></div>

							<div style="margin-top: 15px; max-height: 300px; overflow: auto;">
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
								<tr>
									<td width="20"><div class="td_dept_header"><input type="checkbox" onClick="check_all_depts(this)" id="ck_dept_all"></div></td>
									<td width="10"><div class="td_dept_header">ID</div></td>
									<td><div class="td_dept_header">Department Name</div></td>
								</tr>
								<?php
									$ops_assigned = 0 ;
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;
										$ops = Depts_get_DeptOps( $dbh, $department["deptID"] ) ;
										if ( count( $ops ) )
											$ops_assigned = 1 ;

										$td1 = "td_dept_td" ;

										if ( $department["name"] != "Archive" )
										{
											print "
											<tr>
												<td class=\"$td1\"><input type=\"checkbox\" id=\"ck_dept_$department[deptID]\" name=\"deptids[]\" value=\"$department[deptID]\"></td>
												<td class=\"$td1\">$department[deptID]</td>
												<td class=\"$td1\" nowrap>
													<div style=\"\">$department[name]</div>
												</td>
											</tr>
											" ;
										}
									}
								?>
								</table>
							</div>

							<div style="margin-top: 10px;">
								<button type="button" style="padding: 10px;" onClick="do_assign()">Assign</button>
							</div>
						</div>
						</form>
					</td>
					<td valign="top" style="padding-left: 25px;" width="350">
						<div class="info_info">
							<div style="padding-bottom: 15px;"><img src="../pics/icons/vcard.png" width="16" height="16" border="0" alt=""> Department assignment and <span class="info_box">Defined Order</span></div>
							<div style="">
								<?php
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;

										if ( $department["name"] != "Archive" )
										{
											print "
												<div class=\"info_info\" style=\"margin-bottom: 5px;\">
													<div class=\"td_dept_header\">$department[name]</div>
													<div id=\"dept_ops_$department[deptID]\" style=\"min-height: 25px; max-height: 200px; overflow: auto;\"></div>
												</div>
											" ;
										}
									}
								?>
							</div>
						</div>
					</td>
				</tr>
				</table>
				<?php endif ; ?>
			</div>
		</div>


		<div style="display: none;" id="ops_monitor">
			<div style="margin-top: 25px;">
				The widget window can be left open on the desktop for quick view of operator online/offline status and real-time chat sessions.
			</div>

			<div style="margin-top: 25px;">
				<?php if ( !count( $operators ) ): ?>
				<div style="margin-top: 25px;"><span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> An <a href="ops.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Operator</a> must be created to continue.</span></div>
				<?php else: ?>
				<button type="button" onClick="launch_tools_op_status()" class="btn">Launch Operator Status Widget Window</button>
				<?php endif ; ?>
			</div>
		</div>

		<div style="display: none;" id="ops_online">
			<div style="margin-top: 25px;">
				<?php if ( !count( $departments ) ): ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> A <a href="depts.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Department</a> must be created to continue.</span>
				<?php elseif ( !count( $operators ) ): ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> An <a href="ops.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Operator</a> must be created to continue.</span>
				<?php elseif ( !$ops_assigned ): ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> An <a href="ops.php?ses=<?php echo $ses ?>&jump=assign" style="color: #FFFFFF;">operator must be assigned to a department</a> to continue.</span>
				<?php else: ?>

				<div class="info_info" style="text-shadow: 1px 1px #FFFFFF;">
					<div class="edit_title"><img src="../pics/icons/bulb.png" width="16" height="16" border="0" alt=""> Chat Operator Login URL:</div>
					<div style="margin-top: 5px;">Provide the following Operator Login URL to your <a href="ops.php?ses=<?php echo $ses ?>&jump=">Chat Operators</a>.</div>
					<div style="margin-top: 15px;">
						<table cellspacing=0 cellspacing=0 border=0>
						<tr>
							<td><div style="font-size: 32px; font-weight: bold; text-shadow: 1px 1px #FFFFFF;"><a href="<?php echo ( !preg_match( "/^(http)/", $login_url ) ) ? "http$https:$login_url" : $login_url ; ?>" target="_blank" style="color: #8DB173;" class="nounder"><?php echo ( !preg_match( "/^(http)/", $login_url ) ) ? "http$https:$login_url" : $login_url ; ?></a></div></td>
						</tr>
						</table>
					</div>
				</div>

				<div style="margin-top: 25px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Don't forget to copy/paste the chat icon <a href="./code.php?ses=<?php echo $ses ?>">HTML Code</a> onto your webpages.</div>
				<?php endif ; ?>
			</div>
		</div>

		<div id="div_form" style="display: none; margin-top: 25px;" id="a_edit">
			<form method="POST" action="ops.php?submit" id="theform">
			<input type="hidden" name="ses" value="<?php echo $ses ?>">
			<input type="hidden" name="action" value="submit">
			<input type="hidden" name="jump" id="jump" value="">
			<input type="hidden" name="opid" id="opid" value="0">
			<input type="hidden" name="password" id="password" value="">

			<div>
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td colspan=2 style="padding: 10px;" align="left"><img src="../pics/icons/arrow_left.png" width="16" height="15" border="0" alt=""> <a href="JavaScript:void(0)" onClick="do_reset()">back</a></td>
				</tr>
				<tr>
					<td nowrap><div class="tab_form_title">Operator Name</div></td>
					<td width="100%" style="padding-left: 10px;"><input type="text" name="name" id="name" size="30" maxlength="40" value="" onKeyPress="return noquotes(event)"></td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Operator Email</div></td>
					<td style="padding-left: 10px;"><input type="text" name="email" id="email" size="30" maxlength="160" value="" onKeyPress="return justemails(event)"></td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Login<div style="font-size: 10px; font-weight: normal;">* letters and numbers only</div></div></td>
					<td style="padding-left: 10px;"><input type="text" name="login" id="login" size="30" maxlength="15" value="" onKeyPress="return logins(event)"></td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Password</div></td>
					<td style="padding-left: 10px;"><input type="password" name="password_temp" id="password_temp" class="input" size="30" maxlength="15" value="" onKeyPress="return noquotes(event)"></td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Concurrent Active Chat Sessions<div style="font-size: 10px; font-weight: normal;">Does not apply to department <a href="./depts.php?ses=<?php echo $ses ?>&ftab=route">simultaneous routing type</a>.</div></div></td>
					<td style="padding-left: 10px;">Maximum number of <span class="info_box">concurrent active chat sessions</span>: 
						<select id="maxc" name="maxc">
							<option value="-1" selected>Unlimited</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>

						<div style="margin-top: 5px;">
							When the operator reaches the max:
							<div style="margin-top: 5px;">
								<div><input type="radio" name="maxco" id="maxco_0" value=0 checked> Automatically skip the operator for new chat requests until their concurrent active chats total is below the max.</div>
								<div style="margin-top: 5px;"><input type="radio" name="maxco" id="maxco_1" value=1> Automatically set the operator to OFFLINE status.  Automatically resume ONLINE when their concurrent active chats total is below the max.</div>
							</div>
						</div>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Rate Operator</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round" style="cursor: pointer;" onclick="$('#rate_1').prop('checked', true);"><input type="radio" name="rate" id="rate_1" value="1" checked> Yes</div><div class="li_op round" style="cursor: pointer;" onclick="$('#rate_0').prop('checked', true);"><input type="radio" name="rate" id="rate_0" value="0"> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">Enable visitors to leave chat performance rating after the chat session ends.</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Operator to Operator Chat</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round" style="cursor: pointer;" onclick="$('#op2op_1').prop('checked', true);"><input type="radio" name="op2op" id="op2op_1" value="1" checked> Yes</div><div class="li_op round" style="cursor: pointer;" onclick="$('#op2op_0').prop('checked', true);"><input type="radio" name="op2op" id="op2op_0" value="0"> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">Enable the operator to chat with other online operators from the console ("No" will hide the "Operators" footer menu).</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Traffic Monitor</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round" style="cursor: pointer;" onclick="$('#traffic_1').prop('checked', true);"><input type="radio" name="traffic" id="traffic_1" value="1" checked> Yes</div><div class="li_op round" style="cursor: pointer;" onclick="$('#traffic_0').prop('checked', true);"><input type="radio" name="traffic" id="traffic_0" value="0"> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">View website traffic (can perform initiate chat and other traffic related features).</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">View IP</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round" style="cursor: pointer;" onclick="$('#viewip_1').prop('checked', true);"><input type="radio" name="viewip" id="viewip_1" value="1"> Yes</div><div class="li_op round" style="cursor: pointer;" onclick="$('#viewip_0').prop('checked', true);"><input type="radio" name="viewip" id="viewip_0" value="0" checked> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">View website visitor IP and the <a href="extras_geo.php?ses=<?php echo $ses ?>">GeoIP</a> information.</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">View Chatting Number</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round" style="cursor: pointer;" onclick="$('#nchats_1').prop('checked', true);"><input type="radio" name="nchats" id="nchats_1" value="1"> Yes</div><div class="li_op round" style="cursor: pointer;" onclick="$('#nchats_0').prop('checked', true);"><input type="radio" name="nchats" id="nchats_0" value="0" checked> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">View <span class="info_box">other operator's current chatting number</span> on the operator console ("Transfer" tab and "Operators" footer menu).</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">SMS Alerts</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round" style="cursor: pointer;" onclick="$('#sms_1').prop('checked', true);"><input type="radio" name="sms" id="sms_1" value="1"> Yes</div><div class="li_op round" style="cursor: pointer;" onclick="$('#sms_0').prop('checked', true);"><input type="radio" name="sms" id="sms_0" value="0" checked> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">Receive new chat request mobile SMS alert. (configured by the operator at their operator area)</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title"><img src="../pics/icons/mobile.png" width="16" height="16" border="0" alt=""> <a href="../mapp/settings.php?ses=<?php echo $ses ?>">Mobile App Access</a></div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round" style="cursor: pointer;" onclick="$('#mapper_1').prop('checked', true);"><input type="radio" name="mapper" id="mapper_1" value="1"> Yes</div><div class="li_op round" style="cursor: pointer;" onclick="$('#mapper_0').prop('checked', true);"><input type="radio" name="mapper" id="mapper_0" value="0" checked> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">Enable the operator to login from the Mobile Application.</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title" style="background: #FFFFFF; border: 1px solid #FFFFFF; text-align: left; font-weight: normal;"><img src="../pics/icons/arrow_left.png" width="16" height="15" border="0" alt=""> <a href="JavaScript:void(0)" onClick="do_reset()">back</a></div></td>
					<td style="padding-left: 10px;"><button type="button" onClick="do_submit()" class="btn">Submit</button></td>
				</tr>
				</table>
			</div>

			</form>
		</div>

		<div id="div_notice_disconnect" style="display: none; position: absolute; text-align: right;" class="info_error">
			<div style="padding: 10px;">
				<div class="edit_title">Operator <span id="span_login_disconnect"></span> is <span class="info_good">ONLINE</span>.  Remote disconnect operator console and go offline?</div>

				<div style="margin-top: 15px;" id="remote_disconnect_button"><button type="button" onClick="remote_disconnect()">Yes. Disconnect</button> &nbsp; <a href="JavaScript:void(0)" style="color: #FFFFFF" onClick="$('#div_notice_disconnect').fadeOut('fast')">cancel</a></div>
				<div id="remote_disconnect_notice" style="display: none; margin-top: 15px;">Disconnecting console.  Just a moment... <img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt=""></div>
			</div>
		</div>

		<div id="div_notice_delete" style="display: none; position: absolute;" class="info_error">
			<div style="padding: 10px;">
				<div class="edit_title">Really delete operator account (<span id="span_login_delete"></span>)?</div>
				<div style="margin-top: 15px;">Deleting the operator account will also delete the operator's transcripts.</div>

				<div style="margin-top: 5px;"><button type="button" onClick="$(this).attr('disabled', true);do_delete_doit();">Delete</button> &nbsp; &nbsp; &nbsp; <a href="JavaScript:void(0)" style="color: #FFFFFF" onClick="$('#div_notice_delete').fadeOut('fast')">cancel</a></div>
			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ?>
