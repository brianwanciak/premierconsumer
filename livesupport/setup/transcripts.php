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
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;

	$error = "" ;
	$theme = "default" ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$jump = ( Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) : "list" ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$index = Util_Format_Sanatize( Util_Format_GetVar( "index" ), "n" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$operators = Ops_get_AllOps( $dbh ) ;

	// make hash for quick refrence
	$operators_hash = Array() ;
	for ( $c = 0; $c < count( $operators ); ++$c )
	{
		$operator = $operators[$c] ;
		$operators_hash[$operator["opID"]] = $operator["name"] ;
	}

	$dept_hash = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
	}

	if ( $action == "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove.php" ) ;

		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
		$created = Util_Format_Sanatize( Util_Format_GetVar( "created" ), "n" ) ;

		if ( !Chat_remove_Transcript( $dbh, $ces, $created ) )
			$error = "Transcript delete error: $dbh[error]" ;
	}

	$text = Util_Format_Sanatize( Util_Format_GetVar( "text" ), "" ) ; $text = ( $text ) ? $text : "" ; $text_query = urlencode( $text ) ;
	$s_as = Util_Format_Sanatize( Util_Format_GetVar( "s_as" ), "ln" ) ;
	$transcripts = Chat_ext_get_RefinedTranscripts( $dbh, $deptid, $opid, $s_as, $text, $page, 15 ) ;

	$total_index = count($transcripts) - 1 ;
	$pages = Util_Functions_Page( $page, $index, 15, $transcripts[$total_index], "transcripts.php", "ses=$ses&s_as=$s_as&text=$text_query&deptid=$deptid&opid=$opid" ) ;
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
	var global_ces ; var global_created ; var global_noteid ;
	var reset_url = "transcripts.php?ses=<?php echo $ses ?>" ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "trans" ) ;

		show_div( "<?php echo $jump ?>" ) ;

		$('#input_search').focus() ;
		$('#form_search').bind("submit", function() { return false ; }) ;

		<?php if ( $action && ( $action != "search" ) && !$error ): ?>do_alert( 1, "Delete Success" ) ;<?php endif ; ?>
	});

	function open_transcript( theces, theopname )
	{
		var url = "../ops/op_trans_view.php?ses=<?php echo $ses ?>&ces="+theces+"&id=<?php echo $admininfo["adminID"] ?>&auth=setup&"+unixtime() ;

		$( '#transcripts' ).find('*').each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf("img_") != -1 )
				$(this).css({ 'opacity': 1 }) ;
		} );

		$('#img_'+theces).css({ 'opacity': '0.4' }) ;
		
		newwin = window.open( url, theces, "scrollbars=yes,menubar=no,resizable=1,location=no,width=<?php echo $VARS_CHAT_WIDTH+50 ?>,height=<?php echo $VARS_CHAT_HEIGHT ?>,status=0" ) ;
		if ( newwin )
			newwin.focus() ;
	}

	function switch_dept( theobject )
	{
		location.href = "transcripts.php?ses=<?php echo $ses ?>&opid=<?php echo $opid ?>&deptid="+theobject.value ;
	}

	function switch_op( theobject )
	{
		location.href = "transcripts.php?ses=<?php echo $ses ?>&deptid=<?php echo $deptid ?>&opid="+theobject.value ;
	}

	function input_text_listen_search( e )
	{
		var key = -1 ;
		var shift ;

		key = e.keyCode ;
		shift = e.shiftKey ;

		if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
			$('#btn_page_search').click() ;
	}

	function show_div( thediv )
	{
		var divs = Array( "list", "encr", "export" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#transcripts_'+divs[c]).hide() ;
			$('#menu_trans_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#transcripts_'+thediv).show() ;
		$('#menu_trans_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function do_notice( thediv, theces, thecreated )
	{
		var pos = $('#tr_'+theces).position() ;
		var width = $('#tr_'+theces).outerWidth() ;
		var height = $('#tr_'+theces).outerHeight() - 8 ;

		global_ces = theces ; global_created = thecreated ;

		if ( $('#div_notice_'+thediv).is(':visible') )
			$('#div_notice_'+thediv).fadeOut( "fast", function() { show_div_notice(thediv, pos, width, height) ; }) ;
		else
			show_div_notice(thediv, pos, width, height) ;
	}

	function do_delete_doit()
	{
		if ( global_ces && global_created )
			location.href = "transcripts.php?ses=<?php echo $ses ?>&ces="+global_ces+"&created="+global_created+"&action=delete&index=<?php echo $index ?>&page=<?php echo $page ?>&deptid=<?php echo $deptid ?>&opid=<?php echo $opid ?>" ;
	}

	function show_div_notice( thediv, thepos, thewidth, theheight )
	{
		$('#div_notice_'+thediv).css({'top': thepos.top, 'left': thepos.left, 'width': thewidth, 'height': theheight}).fadeIn("fast") ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<!-- <div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="show_div('list')" id="menu_trans_list">Transcripts</div>
			<div class="op_submenu" onClick="show_div('encr')" id="menu_trans_encr">Encryption</div>
			<div class="op_submenu" onClick="show_div('export')" id="menu_trans_export">Export</div>
			<div style="clear: both"></div>
		</div> -->

		<div id="transcripts_list" style="">
			<div style="">
				<form method="POST" action="report_trans.php?submit" id="form_theform" style="">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td>
						<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept( this )">
						<option value="0">All Departments</option>
						<?php
							for ( $c = 0; $c < count( $departments ); ++$c )
							{
								$department = $departments[$c] ;
								$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
								print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
							}
						?>
						</select>
					</td> 
					<td><img src="../pics/space.gif" width="10" height=1></td>
					<td>
						<select name="opid" id="opid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_op( this )">
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
				</tr>
				</table>
				</form>
			</div>

			<div style="margin-top: 15px;"><img src="../pics/icons/flag_blue.png" width="14" height="14" border="0" alt=""> Flag icon indicates the transcript includes the visitor's comment.  Click the "view" button to view the transcript and the comment.</div>

			<table cellspacing=0 cellpadding=0 border=0 width="100%" id="transcripts" style="margin-top: 25px;">
			<tr><td colspan="10"><div class="page_top_wrapper"><?php echo $pages ?></div></td></tr>
			<tr>
				<td width="20" nowrap><div class="td_dept_header">&nbsp;</div></td>
				<td width="120" nowrap><div class="td_dept_header">Operator</div></td>
				<td width="120" nowrap><div class="td_dept_header">Visitor</div></td>
				<td width="95" nowrap><div class="td_dept_header">Rating</div></td>
				<td width="140"><div class="td_dept_header">Created</div></td>
				<td width="80"><div class="td_dept_header">Duration</div></td>
				<td><div class="td_dept_header">Question</div></td>
			</tr>
			<?php
				for ( $c = 0; $c < count( $transcripts )-1; ++$c )
				{
					$transcript = $transcripts[$c] ;
					if ( $transcript["opID"] )
					{
						// intercept nulled operator accounts that have been deleted
						if ( !isset( $operators_hash[$transcript["op2op"]] ) ) { $operators_hash[$transcript["op2op"]] = "&nbsp;" ; }
						if ( !isset( $operators_hash[$transcript["opID"]] ) ) { $operators_hash[$transcript["opID"]] = "&nbsp;" ; }

						$operator = ( $transcript["op2op"] ) ? $operators_hash[$transcript["op2op"]] : $operators_hash[$transcript["opID"]] ;
						$created_date = date( "M j, Y", $transcript["created"] ) ;
						$created_time = date( "g:i a", $transcript["created"] ) ;
						$duration = $transcript["ended"] - $transcript["created"] ;
						$duration = ( ( $duration - 60 ) < 1 ) ? " 1 min" : Util_Format_Duration( $duration ) ;
						$fsize = Util_Functions_Bytes( $transcript["fsize"] ) ;
						$question = $transcript["question"] ;
						$vname = ( $transcript["op2op"] ) ? $operators_hash[$transcript["opID"]] : Util_Format_Sanatize( $transcript["vname"], "v" ) ;
						$rating = Util_Functions_Stars( "..", $transcript["rating"] ) ;
						$initiated = ( $transcript["initiated"] ) ?  "<img src=\"../pics/icons/info_initiate.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" title=\"Operator Initiated Chat\" alt=\"Operator Initiated Chat\" style=\"cursor: help;\"> " : "" ;
						$note = ( $transcript["noteID"] ) ?  "<div style='margin-top: 5px; text-align: left;'><img src=\"../pics/icons/flag_blue.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\" title=\"includes comment\" alt=\"includes comment\" style=\"cursor: help;\"></div>" : "" ;

						if ( $transcript["op2op"] )
							$question = "<div><img src=\"../pics/icons/agent.png\" width=\"16\" height=\"16\" border=\"0\" title=\"Operator to Operator Chat\" alt=\"Operator to Operator Chat\" style=\"cursor: help;\"></div>" ;

						$btn_view = "<div onClick=\"open_transcript('$transcript[ces]', '$operator')\" style=\"cursor: pointer;\" id=\"img_$transcript[ces]\"><img src=\"../pics/btn_view.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;
						$btn_delete = "<div onClick=\"do_notice('delete', '$transcript[ces]', $transcript[created]);\" style=\"margin-top: 10px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

						$td1 = "td_dept_td" ;

						print "<tr id=\"tr_$transcript[ces]\"><td class=\"$td1\">$btn_view$btn_delete</td><td class=\"$td1\"><div id=\"transcript_$transcript[ces]\">$initiated$operator</div></td><td class=\"$td1\">$vname</td><td class=\"$td1\" align=\"center\">$rating$note</td><td class=\"$td1\" nowrap>$created_date<div style=\"font-size: 10px; margin-top: 3px;\">($created_time)</div></td><td class=\"$td1\" nowrap>$duration<div style=\"font-size: 10px; margin-top: 3px;\">($fsize)</div></td><td class=\"$td1\">$question</td></tr>" ;
					}
				}
				if ( $c == 0 )
					print "<tr><td colspan=9 class=\"td_dept_td\">Blank results.</td></tr>" ;
			?>
			<tr><td colspan="10"><div class="page_bottom_wrapper"><?php echo $pages ?></div></td></tr>
			</table>
		</div>

		<div id="transcripts_encr" style="display: none; margin-top: 25px;">
		</div>

		<div id="transcripts_export" style="display: none; margin-top: 25px;">
		</div>

		<div id="div_notice_delete" style="display: none; position: absolute;" class="info_error">
			<div style="padding: 10px;">
				<div class="edit_title">Delete this transcript permanently?</div>

				<div style="margin-top: 5px;"><button type="button" onClick="$(this).attr('disabled', true);do_delete_doit();">Delete</button> &nbsp; &nbsp; &nbsp; <a href="JavaScript:void(0)" style="color: #FFFFFF" onClick="$('#div_notice_delete').fadeOut('fast')">cancel</a></div>
			</div>
		</div>

		<div id="div_note" class="info_box" style="display: none; position: absolute; width: 200px; height: 150px; z-Index: 10;">ddd</div>

<?php include_once( "./inc_footer.php" ) ?>
