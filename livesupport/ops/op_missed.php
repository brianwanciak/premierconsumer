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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$index = Util_Format_Sanatize( Util_Format_GetVar( "index" ), "n" ) ;
	$theme = $opinfo["theme"] ;

	$operators = Ops_get_AllOps( $dbh ) ;
	$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;

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
	$pages = Util_Functions_Page( $page, $index, 20, $transcripts[$total_index], "op_trans.php", "ses=$ses&s_as=$s_as&text=$text_query" ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> transcripts </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/<?php echo $opinfo["theme"] ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var reset_url = "op_trans.php?ses=<?php echo $ses ?>" ;
	var ces, newwin ;
	var widget ;

	$(document).ready(function()
	{
		init_trs() ;
		if ( !parent.mobile ) { }

		$('#input_search').focus() ;
		$('#form_search').bind("submit", function() { return false ; }) ;

		var div_height = parent.extra_wrapper_height - 55 ;
		$('#canned_container').css({'min-height': div_height}) ;
		$('#canned_wrapper').fadeIn() ;

		//$(document).dblclick(function() {
		//	parent.close_extra( "trans" ) ;
		//});
	});

	function init_trs()
	{
		$('#table_trs tr:nth-child(2n+3)').addClass('chat_info_tr_traffic_row') ;
	}

	function open_transcript( theces )
	{
		if ( typeof( ces ) != "undefined" )
			$('#img_'+ces).removeClass('chat_info_td_traffic_img') ;

		ces = theces ;
		$('#img_'+ces).addClass('chat_info_td_traffic_img') ;

		//$('#transcript_'+theces).html( "<img src=\"../themes/<?php echo $opinfo["theme"] ?>/loading_fb.gif\" border=\"0\" alt=\"\">" ) ;
		parent.open_transcript( theces ) ;
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
<body>

<div id="canned_wrapper" style="display: none; height: 100%; overflow: auto;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">
			<div id="canned_container">
				<div class="page_top_wrapper"><?php echo $pages ?></div>
				<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_trs">
				<tr>
					<td width="28"><div class="chat_info_td_t">&nbsp;</div></td>
					<td width="110"><div class="chat_info_td_t">Operator</div></td>
					<td width="110"><div class="chat_info_td_t">Visitor</div></td>
					<td width="80"><div class="chat_info_td_t">Rating</div></td>
					<td width="150"><div class="chat_info_td_t">Created</div></td>
					<td width="80"><div class="chat_info_td_t">Duration</div></td>
					<td width=""><div class="chat_info_td_t">Question</div></td>
				</tr>
				<?php
					for ( $c = 0; $c < count( $transcripts )-1; ++$c )
					{
						$transcript = $transcripts[$c] ;

						// filter out random bugs of no operator data
						if ( $transcript["opID"] )
						{
							// intercept nulled operator accounts that have been deleted
							if ( !isset( $operators_hash[$transcript["op2op"]] ) ) { $operators_hash[$transcript["op2op"]] = "&nbsp;" ; }
							if ( !isset( $operators_hash[$transcript["opID"]] ) ) { $operators_hash[$transcript["opID"]] = "&nbsp;" ; }

							$operator = ( $transcript["op2op"] ) ? $operators_hash[$transcript["op2op"]] : $operators_hash[$transcript["opID"]] ;
							$created = date( "M j (g:i a)", $transcript["created"] ) ;
							$duration = $transcript["ended"] - $transcript["created"] ;
							$duration = ( ( $duration - 60 ) < 1 ) ? " 1 min" : Util_Format_Duration( $duration ) ;
							$question = $transcript["question"] ;
							$vname = ( $transcript["op2op"] ) ? $operators_hash[$transcript["opID"]] : $transcript["vname"] ;
							$rating = Util_Functions_Stars( "..", $transcript["rating"] ) ;
							$initiated = ( $transcript["initiated"] ) ?  "<img src=\"../themes/$opinfo[theme]/info_initiate.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" title=\"Operator Initiated Chat\" alt=\"Operator Initiated Chat\" style=\"cursor: help;\"> " : "" ;

							if ( $transcript["op2op"] )
								$question = "<img src=\"../themes/$opinfo[theme]/agent.png\" width=\"16\" height=\"16\" border=\"0\" title=\"Operator to Operator Chat\" alt=\"Operator to Operator Chat\" style=\"cursor: help;\">" ;

							print "<tr><td class=\"chat_info_td_traffic\" id=\"img_$transcript[ces]\" style=\"text-align: center; -moz-border-radius: 5px; border-radius: 5px; cursor: pointer;\"><img src=\"../themes/$opinfo[theme]/view.png\" onClick=\"open_transcript('$transcript[ces]')\" width=\"16\" height=\"16\" title=\"view transcript\" alt=\"view transcript\"></td><td class=\"chat_info_td_traffic\" nowrap><b><div id=\"transcript_$transcript[ces]\">$initiated $operator</div></b></td><td class=\"chat_info_td_traffic\">$vname</td><td class=\"chat_info_td_traffic\" nowrap>$rating</td><td class=\"chat_info_td_traffic\" nowrap>$created</td><td class=\"chat_info_td_traffic\">$duration</td><td class=\"chat_info_td_traffic\">$question</td></tr>" ;
						}
					}
					if ( $c == 0 )
						print "<tr><td class=\"chat_info_td_traffic\">&nbsp;</td><td colspan=7 class=\"chat_info_td_traffic\">&nbsp;</td></tr>" ;
				?>
				</table>
				<div class="chat_info_end"></div>
				<?php if ( $total_index > 10 ): ?><div class="page_bottom_wrapper"><?php echo $pages ?></div><?php endif ; ?>
			</div>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>

	<div style="height: 25px; width: 10px;"></div>
</div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>