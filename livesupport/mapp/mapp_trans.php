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
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Mobile.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$index = Util_Format_Sanatize( Util_Format_GetVar( "index" ), "n" ) ;
	$mobile = Util_Mobile_Detect() ;
	$theme = $opinfo["theme"] ; $error = "" ;

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
	$transcripts = Chat_ext_get_OpDeptTrans( $dbh, $opinfo["opID"], $s_as, $text, $page, 50 ) ;

	$total_index = count($transcripts) - 1 ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Operator </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/<?php echo $opinfo["theme"] ?>/style.css?<?php echo $VERSION ?>">
<link rel="Stylesheet" href="../mapp/css/mapp.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../mapp/js/mapp.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var global_ces ;

	$(document).ready(function()
	{
		reset_mapp_div_height() ;
		$('#canned_wrapper').show() ;
		init_external_url() ;

		<?php if ( $action == "reload" ): ?>do_alert( 1, "Refresh Success" ) ;<?php endif ; ?>
	});

	function init_external_url()
	{
		$("a").click(function(){
			var temp_url = $(this).attr( "href" ) ;
			if ( !temp_url.match( /javascript/i ) )
			{
				parent.external_url = temp_url ;
				return false ;
			}
		});
	}

	function open_transcript( theces )
	{
		var div_width = $('#canned_container').width() - 10 ;
		var div_height = $('#canned_container').height() - 10 ;
		var url = "../ops/op_trans_view.php?ses=<?php echo $ses ?>&ces="+theces+"&id=<?php echo $opinfo["opID"] ?>&auth=operator&back=1&mapp=1&"+unixtime() ;

		if ( global_ces != theces )
		{
			$('#table_'+theces).addClass('info_focus') ;
			if ( typeof( global_ces ) != "undefined" )
				$('#table_'+global_ces).removeClass('info_focus') ;
			global_ces = theces ;
		}

		$('#div_cans').hide() ;
		$('#iframe_transcript').css({'height': div_height}).attr( 'src', url ).load(function (){
			$('#div_cans_iframe').show() ;
			setTimeout( function(){
				document.getElementById('iframe_transcript').contentWindow.init_chat_body_height(div_width, div_height) ;
			}, 200 ) ;
		});
	}

	function close_transcript( theces )
	{
		$('#div_cans_iframe').hide() ;
		$('#div_cans').show() ;

		var div_pos = $('#table_'+theces).position() ;
		var scroll_to = div_pos.top - 50 ;

		$('#canned_container').scroll() ;
		$('#canned_container').animate({
			scrollTop: scroll_to
		}, 200) ;
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
<body style="">

<div id="canned_wrapper" style="display: none; height: 100%; overflow: auto;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">
			<div id="canned_container" style="overflow: auto;">
				<div id="div_cans">

					<div id="div_chats_trans" style="">
						<div id="div_chats_trans_list">
							<div class="page_top_wrapper">
								<div style="float: left; padding-left: 10px;"><form method="POST" onSubmit="return false;" id="form_search"><input type="text" class="input_text_search" size="10" maxlength="255" style="font-size: 10px;" id="input_search" value="<?php echo $text ?>" onKeydown="input_text_listen_search(event);" autocorrect="off"> &nbsp; <select name="s_as" id="s_as" style="font-size: 10px;"><option value="text">text</option><option value="ces">chat ID</option><option value="vid">visitor ID</option></select> &nbsp; <input type="button" id="btn_page_search" style="" class="input_button" value="search" onClick="do_search('mapp_trans.php?ses=<?php echo $ses ?>')"> <input type="button" style="" class="input_button" value="reset" onClick="location.href='mapp_trans.php?ses=<?php echo $ses ?>&<?php echo time() ?>'"></form></div><script type="text/javascript">$('#s_as').val('text')</script>
								<div style="clear: both;"></div>
							</div>

							<div style="margin-top: 10px;">
							<?php if ( $text ): ?>
								<?php echo $transcripts[$total_index] ?> matching transcripts found.
							<?php else: ?>
								Displaying most recent 50 transcripts.
							<?php endif ; ?>
							</div>

							<div style="margin-top: 10px;">
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
											$created = date( "M j, Y (g:i a)", $transcript["created"] ) ;
											$duration = $transcript["ended"] - $transcript["created"] ;
											$duration = ( ( $duration - 60 ) < 1 ) ? " 1 min" : Util_Format_Duration( $duration ) ;
											$question = $transcript["question"] ;
											$vname = ( $transcript["op2op"] ) ? $operators_hash[$transcript["opID"]] : $transcript["vname"] ;
											$rating = ( $transcript["rating"] ) ? "<tr><td>Rating</td><td style=\"\">".Util_Functions_Stars( "..", $transcript["rating"] )."</td></tr>" : "" ;
											$initiated = ( $transcript["initiated"] ) ?  "<img src=\"../themes/$opinfo[theme]/info_initiate.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" title=\"Operator Initiated Chat\" alt=\"Operator Initiated Chat\" style=\"cursor: help;\"> " : "" ;

											if ( $mobile != 3 ) { $question = wordwrap( $transcript["question"], 37, "<br>", true ) ; }

											if ( $transcript["op2op"] )
												$question = "<img src=\"../themes/$opinfo[theme]/agent.png\" width=\"16\" height=\"16\" border=\"0\" title=\"Operator to Operator Chat\" alt=\"Operator to Operator Chat\" style=\"cursor: help;\">" ;

											print "
												<div class=\"info_neutral\" id='table_$transcript[ces]' style=\"padding: 10px; margin-bottom: 1px;\">
													<table cellspacing=0 cellpadding=2 border=0>
													<tr>
														<td>Operator</td>
														<td style=\"\">$initiated $operator</td>
													</tr>
													<tr>
														<td>Visitor</td>
														<td style=\"\"><b>$vname</b></td>
													</tr>$rating
													<tr>
														<td>Email</td>
														<td style=\"\"><b><a href=\"mailto:$transcript[vemail]\">$transcript[vemail]</a></b></td>
													</tr>
													<tr>
														<td>Created</td>
														<td style=\"\"><b>$created</b> &nbsp; ($duration)</td>
													</tr>
													<tr>
														<td><button type=\"button\" onClick=\"open_transcript('$transcript[ces]')\">select</button></td>
														<td>$question</td>
													</tr>
													</table>
												</div>
											" ;
											//$initiated $operator $vname $created $question
										}
									}
									if ( $c != 0 )
										print "<div style=\"padding: 50px;\">&nbsp;</div>" ;
									else
										print "<div class=\"info_neutral\">Blank results.</div>" ;
								?>
							</div>
						</div>
						<div id="div_chats_trans_content" style="display: none;"></div>
					</div>

				</div>
				<div id="div_cans_iframe" style="display: none;"><iframe id="iframe_transcript" name="iframe_transcript" style="width: 100%; border: 0px; height: 10px; -moz-border-radius: 5px; border-radius: 5px;" src="about:blank" scrolling="no" frameBorder="0"></iframe></div>
			</div>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

</body>
</html>
<?php
	if ( isset( $dbh ) && $dbh['con'] )
		database_mysql_close( $dbh ) ;
?>
