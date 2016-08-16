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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "n" ) ;
	$flag = Util_Format_Sanatize( Util_Format_GetVar( "flag" ), "n" ) ;

	$error = "" ;

	if ( $action == "submit" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/put.php" ) ;

		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
		$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "ln" ) ;
		$message = Util_Format_Sanatize( Util_Format_GetVar( "message" ), "" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		if ( isset( $caninfo["opID"] ) )
			$opid = $caninfo["opID"] ;
		else
			$opid = $opinfo["opID"] ;

		if ( !$canid = Canned_put_Canned( $dbh, $canid, $opinfo["opID"], $deptid, $title, $message ) )
			$error = "Error processing canned message." ;
	}
	else if ( $action == "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/remove.php" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		if ( $caninfo["opID"] == $opinfo["opID"] )
			Canned_remove_Canned( $dbh, $opinfo["opID"], $canid ) ;
		$action = "submit" ; $canid = 0 ;
	}

	$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;
	$cans = Canned_get_OpCanned( $dbh, $opinfo["opID"], 0 ) ;

	// make hash for quick refrence
	$dept_hash = Array() ;
	$dept_hash[1111111111] = "All Departments" ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
	}
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> canned responses </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/<?php echo $opinfo["theme"] ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var global_auto_canid = parent.auto_canid ;

	$(document).ready(function()
	{
		if ( ( "<?php echo $action ?>" == "submit" ) && ( "<?php echo $error ?>" == "" ) )
		{
			if ( typeof( parent.isop ) != "undefined" )
				parent.populate_cans(0) ;
		}
		else if ( "<?php echo $action ?>" == "delete" )
		{
			if ( typeof( parent.isop ) != "undefined" )
				parent.populate_cans(0) ;
		}

		if ( "<?php echo $flag ?>" == "new_canned" )
			toggle_new(1) ;

		init_trs() ;
		if ( global_auto_canid ) { $('#auto_can_'+global_auto_canid).prop('checked', true) ; }

		var div_height = parent.extra_wrapper_height - 55 ;
		$('#canned_container').css({'min-height': div_height}) ;
		
		$('#canned_wrapper').fadeIn('fast', function() {
			if ( <?php echo $canid ?> )
			{
				var div_pos = $('#tr_div_'+<?php echo $canid ?>).position() ;
				var div_height = Math.round( $('#tr_div_'+<?php echo $canid ?>).height()/2 ) ;
				var scroll_to = div_pos.top - $(document).height() + div_height + 200 ;

				$('#canned_wrapper').animate({
					scrollTop: scroll_to
				}, 200) ;
				$('#tr_div_'+<?php echo $canid ?>).fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
			}
		}) ;

		<?php if ( ( ( $action == "submit" ) && !$error ) || ( $action == "success" ) ): ?>parent.do_alert( 1, "Success" ) ;<?php endif ; ?>

		parent.init_extra_loaded() ;
	});

	function init_trs()
	{
		$('#table_trs tr:nth-child(2n+3)').addClass('chat_info_tr_traffic_row') ;
	}

	function toggle_new( theflag )
	{
		// theflag = 1 means force show, not toggle
		if ( $('#canned_box_new').is(':visible') && !theflag )
		{
			$( "input#canid" ).val( "" ) ;
			$( "input#title" ).val( "" ) ;
			$( "#deptid" ).val( 1111111111 ) ;
			$( "#message" ).val( "" ) ;

			$('#canned_wrapper').show() ;
			$('#canned_box_new').hide() ;
			toggle_menu_info( "list" ) ;
		}
		else
		{
			$('#canned_box_new').show() ;
			$('#canned_wrapper').hide() ;
		}
	}

	function do_edit( thecanid, thetitle, thedeptid, themessage )
	{
		$( "input#canid" ).val( thecanid ) ;
		$( "input#title" ).val( thetitle.replace( /&-#39;/g, "'" ) ) ;
		$( "#deptid" ).val( thedeptid ) ;
		$( "#message" ).val( themessage.replace(/<br>/g, "\r\n").replace( /&-#39;/g, "'" ) ) ;
		
		toggle_new(0) ;
	}

	function do_delete( thiscanid )
	{
		if ( confirm( "Really delete this canned response?" ) )
			location.href = "op_canned.php?ses=<?php echo $ses ?>&action=delete&canid="+thiscanid ;
	}

	function do_submit()
	{
		var canid = $('#canid').val() ;
		var title = $('#title').val() ;
		var deptid = $('#deptid').val() ;
		var message = $('#message').val() ;

		if ( title == "" )
			do_alert( 0, "Please provide a Reference title." ) ;
		else if ( message == "" )
			do_alert( 0, "Please provide a Message." ) ;
		else
			$('#theform').submit() ;
	}

	function toggle_menu_info( themenu )
	{
		var divs = Array( "list", "settings" ) ;

		for ( var c = 0; c < divs.length; ++c )
		{
			$('#canned_'+divs[c]).hide() ;
			$('#menu_canned_'+divs[c]).removeClass('menu_traffic_info_focus').addClass('menu_traffic_info') ;
		}

		$('#canned_'+themenu).show() ;
		$('#menu_canned_'+themenu).removeClass('menu_traffic_info').addClass('menu_traffic_info_focus') ;
	}

	function select_auto_can( thecanid )
	{
		$('#div_confirm').show() ;
		$('#canned_list').find('*').each( function () {
			var div_name = this.id ;
			if ( div_name.indexOf( "auto_can_" ) == 0 )
				this.checked = false ;
		}) ;
		$('#auto_can_'+thecanid).prop('checked', true) ;

		$('#confirm_canid').val( thecanid ) ;
		if ( global_auto_canid != thecanid )
		{
			$('#confirm_value').val( 1 ) ;
			$('#confirm_title').html( "Automatically select and send this canned response immediately after accepting a chat?" ) ;
		}
		else
		{
			$('#confirm_value').val( 0 ) ;
			$('#confirm_title').html( "De-select this canned response?" ) ;
		}
	}

	function select_auto_can_doit( theoption )
	{
		var thecanid = parseInt( $('#confirm_canid').val() ) ;
		var thevalue = parseInt( $('#confirm_value').val() ) ;

		$('#div_confirm').hide() ;
		if ( theoption )
		{
			var unique = unixtime() ;
			var json_data = new Object ;

			$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_cans.php",
			data: "action=auto_canned&ses=<?php echo $ses ?>&canid="+thecanid+"&value="+thevalue+"&unique="+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					if ( thevalue )
						parent.auto_canid = global_auto_canid = thecanid ;
					else
					{
						parent.auto_canid = global_auto_canid = 0 ;
						$('#auto_can_'+thecanid).prop('checked', false) ;
					}
					do_alert( 1, "Success" ) ;
				}
				else
					do_alert( 0, "Error updating.  Please reload the console and try again." ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error updating.  Please reload the console and try again." ) ;
			} });
		}
		else if ( global_auto_canid )
		{
			$('#auto_can_'+thecanid).prop('checked', false) ;
			$('#auto_can_'+global_auto_canid).prop('checked', true) ;
		}
		else
			$('#auto_can_'+thecanid).prop('checked', false) ;
	}
//-->
</script>
</head>
<body>

<div id="canned_wrapper" style="display: none; height: 100%; overflow: auto;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">
			<div id="canned_container">
				<div style="padding-bottom: 15px;">
					<div class="menu_traffic_info_focus" style="font-weight: normal; border: 0px; cursor: default;"><span class="chat_info_td_t" onClick="toggle_new(1)" style="cursor: pointer;"><img src="../themes/<?php echo $opinfo["theme"] ?>/add.png" width="12" height="12" border="0" alt=""> Add New</span></div>
					<div style="clear: both;"></div>
				</div>
				<div id="canned_list">
					<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_trs">
					<tr>
						<td width="60" nowrap><div class="chat_info_td_t">&nbsp;</div></td>
						<td width="180" nowrap><div class="chat_info_td_t">Title</div></td>
						<td width="180"><div class="chat_info_td_t">Department</div></td>
						<td nowrap><div class="chat_info_td_t">Auto Select</div></td>
						<td width="100%"><div class="chat_info_td_t">Message</div></td>
					</tr>
					<?php
						for ( $c = 0; $c < count( $cans ); ++$c )
						{
							$can = $cans[$c] ;
							$title = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", $can["title"] ) ) ;
							$title_display = Util_Format_ConvertQuotes( $can["title"] ) ;

							if ( isset( $dept_hash[$can["deptID"]] ) )
							{
								$dept_name = $dept_hash[$can["deptID"]] ;
								$message = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $can["message"] ) ) ) ;
								$message_display = preg_replace( "/\"/", "&quot;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", Util_Format_ConvertTags( $can["message"] ) ) ) ;

								$auto_can_div = "<div style=\"text-align: center;\" class=\"chat_info_td_traffic\"><input type=\"checkbox\" id=\"auto_can_$can[canID]\" value=\"$can[canID]\" onClick=\"select_auto_can($can[canID])\"></div>" ;

								$delete_image = ( $can["opID"] == $opinfo["opID"] ) ? "<img src=\"../themes/$opinfo[theme]/delete.png\" style=\"cursor: pointer;\" onClick=\"do_delete($can[canID])\" title=\"delete\" alt=\"delete\" width=\"16\" height=\"16\" border=0>" : "<img src=\"../pics/space.gif\" width=\"16\" height=\"16\" border=0>" ;
								$edit_image = ( $can["opID"] == $opinfo["opID"] ) ? "<img src=\"../themes/$opinfo[theme]/edit.png\" style=\"cursor: pointer;\" onClick=\"do_edit($can[canID], '$title', '$can[deptID]', '$message')\" title=\"edit canned\"  alt=\"edit canned\" width=\"16\" height=\"16\" border=0>" : "<img src=\"../themes/$opinfo[theme]/lock.png\" width=\"16\" height=\"16\" border=0 title=\"created by Setup Admin\" alt=\"created by Setup Admin\">" ;

								print "<tr id=\"tr_div_$can[canID]\"><td class=\"chat_info_td_traffic\" nowrap>$delete_image &nbsp; $edit_image</td><td class=\"chat_info_td_traffic\" nowrap><button type=\"button\" style=\"font-size: 10px;\" onClick=\"parent.select_canned_pre('$title_display')\">select</button> <b>$title_display</b></td><td class=\"chat_info_td_traffic\" nowrap>$dept_name</td><td class=\"chat_info_td_traffic\">$auto_can_div</td><td class=\"chat_info_td_traffic\"><div id=\"canned_message_$can[canID]\">$message_display</div></td></tr>" ;
							}
						}
					?>
					</table>
					<div class="chat_info_end" style="padding: 10px;"></div>
				</div>
				<div id="canned_settings" style="display: none;">
				</div>
			</div>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

<div id="canned_box_new" style="display: none; height: 100%; overflow: auto;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td valign="top" nowrap width="100%">
					<div style=""><img src="../themes/<?php echo $opinfo["theme"] ?>/info.png" width="14" height="14" border="0" alt=""> Canned responses created here will be available to your account and are not visible to other operators.</div>

					<form method="POST" action="op_canned.php?<?php echo time() ?>" id="theform">
					<input type="hidden" name="ses" value="<?php echo $ses ?>">
					<input type="hidden" name="action" value="submit">
					<input type="hidden" name="canid" id="canid" value="0">
					<div style="margin-top: 25px;">
						Reference (example: "Greeting", "Just a moment")
						<div><input type="text" name="title" id="title" class="input_text" style="width: 98%; margin-bottom: 10px;" maxlength="25"></div>
						<div>Department</div>
						<div><select name="deptid" id="deptid" style="width: 99%; margin-bottom: 10px;">
							<option value="1111111111">All Departments</option>
							<?php
								for ( $c = 0; $c < count( $departments ); ++$c )
								{
									$department = $departments[$c] ;

									print "<option value=\"$department[deptID]\">$department[name]</option>" ;
								}
							?>
						</select></div>
						<div>Canned Message</div>
						<div><textarea name="message" id="message" class="input_text" rows="7" style="min-width: 98%; margin-bottom: 10px;" wrap="virtual"></textarea></div>

						<div><button type="button" onClick="do_submit()" class="input_button">Submit</button> &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="toggle_new(0)">cancel</span></div>
						</form>
					</div>
				</td>
				<td valign="center" nowrap style="padding-left: 25px;">
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
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

<div id="div_confirm" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">
	<div id="div_confirm_body" class="info_info" style="position: relative; width: 350px; margin: 0 auto; top: 100px;">
		<div class="info_box" style="padding: 25px;">
			<div id="confirm_title"></div>
			<form><input type="hidden" id="confirm_canid" name="confirm_canid" value=""><input type="hidden" id="confirm_value" name="confirm_value" value=""></form>
			<div style="margin-top: 15px;"><button type="button" onClick="select_auto_can_doit(1)" class="input_button">Yes</button> &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="select_auto_can_doit(0)">cancel</span></div>
		</div>
	</div>
</div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>