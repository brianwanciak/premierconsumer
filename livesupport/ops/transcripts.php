<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: ../setup/install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses ) ){ ErrorHandler( 602, "Invalid operator session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "n" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ;
	$auto = Util_Format_Sanatize( Util_Format_GetVar( "auto" ), "n" ) ;
	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$index = Util_Format_Sanatize( Util_Format_GetVar( "index" ), "n" ) ;

	$menu = "transcripts" ;
	$error = "" ;
	$theme = "default" ;

	$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;
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

	$text = Util_Format_Sanatize( Util_Format_GetVar( "text" ), "" ) ; $text = ( $text ) ? $text : "" ; $text_query = urlencode( $text ) ;
	$s_as = Util_Format_Sanatize( Util_Format_GetVar( "s_as" ), "ln" ) ;
	$transcripts = Chat_ext_get_OpDeptTrans( $dbh, $opinfo["opID"], $s_as, $text, $page, 20 ) ;

	$total_index = count($transcripts) - 1 ;
	$pages = Util_Functions_Page( $page, $index, 20, $transcripts[$total_index], "transcripts.php", "ses=$ses&s_as=$s_as&text=$text_query&opid=$opinfo[opID]" ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Transcripts </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
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
	var menu ;
	var reset_url = "transcripts.php?ses=<?php echo $ses ?>" ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		$("body").show() ;
		init_menu_op() ;
		toggle_menu_op( "trans", '<?php echo $ses ?>' ) ;

		$('#input_search').focus() ;
		$('#form_search').bind("submit", function() { return false ; }) ;
	});
	
	function open_transcript( theces, theopname )
	{
		var url = "./op_trans_view.php?ses=<?php echo $ses ?>&ces="+theces+"&id=<?php echo $opinfo["opID"] ?>&auth=operator&"+unixtime() ;

		$( '#op_trans' ).find('*').each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf("img_") != -1 )
				$(this).css({ 'opacity': 1 }) ;
		} );

		$('#img_'+theces).css({ 'opacity': '0.4' }) ;

		newwin = window.open( url, theces, "scrollbars=yes,menubar=no,resizable=1,location=no,width=<?php echo $VARS_CHAT_WIDTH+50 ?>,height=<?php echo $VARS_CHAT_HEIGHT ?>,status=0" ) ;

		if ( newwin )
			newwin.focus() ;
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

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ; ?>

		<div id="op_title" class="edit_title" style="margin-bottom: 15px;"></div>
		<div id="op_trans" style="display: none; margin: 0 auto;">

			<div style="margin-top: 15px;"><img src="../pics/icons/flag_blue.png" width="14" height="14" border="0" alt=""> Flag icon indicates the transcript includes the visitor's comment.  Click the "view" button to view the transcript and the comment.</div>

			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr><td colspan="10"><div class="page_top_wrapper"><?php echo $pages ?></div></td></tr>
				<tr>
					<td width="20" nowrap><div class="td_dept_header">&nbsp;</div></td>
					<td width="130" nowrap><div class="td_dept_header">Operator</div></td>
					<td width="130" nowrap><div class="td_dept_header">Visitor</div></td>
					<td width="95" nowrap><div class="td_dept_header">Rating</div></td>
					<td width="140"><div class="td_dept_header">Created</div></td>
					<td width="80"><div class="td_dept_header">Duration</div></td>
					<td><div class="td_dept_header">Question</div></td>
				</tr>
				<?php
					$opinfo["theme"] = "default" ;
					for ( $c = 0; $c < count( $transcripts )-1; ++$c )
					{
						$transcript = $transcripts[$c] ;

						// brute fix of rare bug
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
							$vname = ( $transcript["op2op"] ) ? $operators_hash[$transcript["opID"]] :  Util_Format_Sanatize( $transcript["vname"], "v" ) ;
							$rating = Util_Functions_Stars( "..", $transcript["rating"] ) ;
							$initiated = ( $transcript["initiated"] ) ?  "<img src=\"../pics/icons/info_initiate.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" title=\"Operator Initiated Chat\" alt=\"Operator Initiated Chat\" style=\"cursor: help;\"> " : "" ;
							$note = ( $transcript["noteID"] ) ?  "<div style='margin-top: 5px; text-align: left;'><img src=\"../pics/icons/flag_blue.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\" title=\"includes comment\" alt=\"includes comment\" style=\"cursor: help;\"></div>" : "" ;

							if ( $transcript["op2op"] )
								$question = "<div><img src=\"../pics/icons/agent.png\" width=\"16\" height=\"16\" border=\"0\" title=\"Operator to Operator Chat\" alt=\"Operator to Operator Chat\" style=\"cursor: help;\"></div>" ;
							$btn_view = "<div onClick=\"open_transcript('$transcript[ces]', '$operator')\" style=\"cursor: pointer;\" id=\"img_$transcript[ces]\"><img src=\"../pics/btn_view.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

							$td1 = "td_dept_td" ;

							print "<tr id=\"tr_$transcript[ces]\"><td class=\"$td1\">$btn_view</td><td class=\"$td1\" nowrap><div id=\"transcript_$transcript[ces]\">$initiated$operator</div></td><td class=\"$td1\" nowrap>$vname</td><td class=\"$td1\" align=\"center\">$rating$note</td><td class=\"$td1\" nowrap>$created_date<div style=\"font-size: 10px; margin-top: 3px;\">($created_time)</div></td><td class=\"$td1\" nowrap>$duration<div style=\"font-size: 10px; margin-top: 3px;\">($fsize)</div></td><td class=\"$td1\">$question</td></tr>" ;
						}
					}
					if ( $c == 0 )
						print "<tr><td colspan=7 class=\"td_dept_td\">Blank results.</td></tr>" ;
				?>
				</table>
			</div>

		</div>

<?php include_once( "./inc_footer.php" ) ; ?>
