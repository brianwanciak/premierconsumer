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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$theme = $opinfo["theme"] ;
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
	var global_ces_select ;
	var chat = new Object ;

	var stars = parent.stars ;

	$(document).ready(function()
	{
		var document_height = $(document).height() - 65 ;
		$('#canned_container').css({'height': document_height}) ;
		$('#canned_wrapper').show() ;

		toggle_menu_info( "info" ) ;
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

	function toggle_menu_info( themenu )
	{
		var divs = Array( "info", "transcripts", "transfer", "spam" ) ;

		for ( var c = 0; c < divs.length; ++c )
		{
			$('#div_info_'+divs[c]).hide() ;
			$('#menu_info_'+divs[c]).removeClass('menu_traffic_info_focus').addClass('menu_traffic_info') ;
		}

		if ( themenu == "transfer" )
		{
			if ( chat["op2op"] ) { $('#div_info_transfer').empty().html( "Chat transfer not available for this session." ) ; }
			else { parent.populate_ops(1) ; }
		}

		$('#div_info_'+themenu).show() ;
		$('#menu_info_'+themenu).removeClass('menu_traffic_info').addClass('menu_traffic_info_focus') ;
	}

	function populate_vinfo( theces )
	{
		if ( parent.chats[theces] != "undefined" )
		{
			global_ces = theces ;
			chat = parent.chats[theces] ;

			var spam_block_string = ( !chat["op2op"] ) ? " &nbsp;&nbsp; <button type=\"button\" class=\"input_button\" onClick=\"parent.spam_block(1, '"+chat["ip"]+"')\">Spam Block</button>" : "" ;

			$('#req_dept').html( parent.$('#req_dept').html() ) ;
			$('#req_email').empty().html( parent.$('#req_email').html() ) ;
			$('#req_request').empty().html( parent.$('#req_request').html() ) ;
			$('#req_onpage').empty().html( parent.$('#req_onpage').html() ) ;
			$('#req_refer').empty().html( parent.$('#req_refer').html() ) ;
			$('#req_market').empty().html( parent.$('#req_market').html() ) ;
			$('#req_resolution').empty().html( parent.$('#req_resolution').html() ) ;
			$('#req_ip').empty().html( parent.$('#req_ip').html() ) ;
			$('#req_custom').empty().html( parent.$('#req_custom').html() ) ;
			$('#req_t_ses').empty().html( parent.$('#req_t_ses').html() ).show() ;
			$('#req_ces').empty().html( parent.$('#req_ces').html() ) ;

			parent.populate_transcripts(1) ;
			init_external_url() ;
			if ( !chat["op2op"] && <?php echo $geoip ?> ) { fetch_geo( chat["ip"] ) ; }
		}
		toggle_menu_info( "info" ) ;
	}

	function fetch_geo( theip )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../wapis/geoip.php",
		data: " akey=<?php echo $CONF['API_KEY'] ?>&f=csv&ip="+theip+"&"+unique,
		success: function(data){
			// unknown,Location Unknown,-,-,28.613459424004,-40.4296875
			var geo_data = data.split(",") ;
			var country = ( typeof( geo_data[0] ) != "undefined" ) ? geo_data[0].toLowerCase()+".gif" : "unknown.gif" ;
			var country_name = ( typeof( geo_data[1] ) != "undefined" ) ? geo_data[1] : "unknown" ;

			$('#req_ip').append( " &nbsp; <img src=\"../pics/maps/"+country+"\" alt=\""+country_name+"\" title=\""+country_name+"\">" ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error loading Geo data.  Please reload the console and try again." ) ;
		} });
	}

	function populate_trans()
	{
		var transcripts_string = "" ;
		for ( var c = 0; c < parent.mapp_obj["transcripts"].length; ++c )
		{
			var transcript = parent.mapp_obj["transcripts"][c] ;
			var rating = ( parseInt( transcript["rating"] ) ) ? "<tr><td>Rating</td><td style=\"\">"+stars[transcript["rating"]]+"</td></tr>" : "" ; ;

			transcripts_string += " \
				<div class=\"info_neutral\" id='table_"+transcript["ces"]+"' style=\"padding: 10px; margin-bottom: 1px;\"> \
					<table cellspacing=0 cellpadding=2 border=0> \
					<tr> \
						<td>Operator</td> \
						<td style=\"\"><b>"+transcript["operator"]+"</b></td> \
					</tr> "+rating+" \
					<tr> \
						<td>Created</td> \
						<td style=\"\"><b>"+transcript["created"]+"</b> &nbsp; ("+transcript["duration"]+")</td> \
					</tr> \
					<tr> \
						<td><button type=\"button\" onClick=\"open_transcript('"+transcript["ces"]+"')\">select</button></td> \
					</tr> \
					</table> \
				</div> \
			" ;
		}

		if ( chat["op2op"] ) { transcripts_string = "Transcripts not available for this session." ; }
		else if ( ( typeof( parent.mapp_obj["transcripts"].length ) == "undefined" ) || ( parent.mapp_obj["transcripts"].length == 0 ) )
			transcripts_string = "<div class=\"info_neutral\">Blank results.</div>" ;

		$('#div_chats_trans_list').empty().html( transcripts_string ) ;
	}

	function open_transcript( theces )
	{
		var div_width = $('#canned_container').width() - 10 ;
		var div_height = $('#canned_container').height() - 10 ;
		var url = "../ops/op_trans_view.php?ses=<?php echo $ses ?>&ces="+theces+"&id=<?php echo $opinfo["opID"] ?>&auth=operator&back=1&mapp=1&"+unixtime() ;

		if ( global_ces_select != theces )
		{
			$('#table_'+theces).addClass('info_focus') ;
			if ( typeof( global_ces_select ) != "undefined" )
				$('#table_'+global_ces_select).removeClass('info_focus') ;
			global_ces_select = theces ;
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

	function populate_ops()
	{
		var departments = ( typeof( parent.mapp_obj["ops"] ) != "undefined" ) ? parent.mapp_obj["ops"] : Array() ;

		var ops_string = "" ;
		for ( var c = 0; c < departments.length; ++c )
		{
			ops_string += "<div class=\"chat_info_td_h\"><b>"+departments[c]["name"]+"</b></div>" ;
			for ( var c2 = 0; c2 < departments[c].operators.length; ++c2 )
			{
				var status = "offline" ;
				var status_bullet = "online_grey.png" ;
				var btn_transfer = "" ;
				var chatting_with = ( parent.nchats ) ? " chatting with "+departments[c].operators[c2]["requests"]+" visitors" : "" ;

				if ( departments[c].operators[c2]["status"] )
				{
					status = "online" ;

					status_bullet= "online_green.png" ;
					btn_transfer = "<button type=\"button\" class=\"input_button\" onClick=\"parent.transfer_chat( "+departments[c]["deptid"]+",'"+departments[c]["name"]+"',"+departments[c].operators[c2]["opid"]+",'"+departments[c].operators[c2]["name"]+"');$(this).attr('disabled', 'true');parent.toggle_extra( 'mapp_vinfo', '', '', 'Visitor Info' )\" style=\"font-size: 12px;\">transfer</button>" ;
				}

				if ( departments[c].operators[c2]["opid"] == parent.isop )
					ops_string += "<div class=\"chat_info_td\"><img src=\"../themes/<?php echo $theme ?>/"+status_bullet+"\" width=\"12\" height=\"12\" border=\"0\"> <b>(You)</b> are "+status+chatting_with+"</div>" ;
				else
					ops_string += "<div class=\"chat_info_td\"><img src=\"../themes/<?php echo $theme ?>/"+status_bullet+"\" width=\"12\" height=\"12\" border=\"0\"> "+btn_transfer+" "+departments[c].operators[c2]["name"]+" is "+status+chatting_with+"</div>" ;
			}
		}
		ops_string += "<div class=\"chat_info_end\"></div>" ;
		$('#div_info_transfer').empty().html( ops_string ) ;
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
					<div style="">
						<div id="menu_info_info" class="menu_traffic_info_focus" onClick="toggle_menu_info('info')">Visitor Info</div>
						<div id="menu_info_transcripts" class="menu_traffic_info" onClick="toggle_menu_info('transcripts')">Transcripts</div>
						<div id="menu_info_transfer" class="menu_traffic_info" onClick="toggle_menu_info('transfer')">Transfer</div>
						<div style="clear: both;"></div>
					</div>

					<div style="margin-top: 25px;">
						<div id="div_info_info" style="display: none;">
							<table cellspacing=0 cellpadding=0 border=0>
							<tr><td class="chat_info_td_h"><b>Department</b></td><td width="100%" class="chat_info_td"> <span id="req_dept"></span></td></tr>
							<tr><td class="chat_info_td_h" nowrap><b>Visitor Email</b></td><td class="chat_info_td"> <span id="req_email"></span></td></tr>
							<tr><td class="chat_info_td_h" nowrap><b>Chat Request</b></td><td class="chat_info_td"> <span id="req_request"></span></td></tr>
							<tr><td class="chat_info_td_h" nowrap><b>Clicked From</b></td><td class="chat_info_td"> <span id="req_onpage"></span></td></tr>
							<tr><td class="chat_info_td_h"><b>Refer URL</b></td><td class="chat_info_td"> <span id="req_refer"></span></td></tr>
							<tr><td class="chat_info_td_h"><b>Marketing</b></td><td class="chat_info_td"> <span id="req_market"></span></td></tr>
							<tr><td nowrap class="chat_info_td_h"><b>Resolution</b></td><td class="chat_info_td"> <span id="req_resolution"></span></td></tr>
							<?php if ( $opinfo["viewip"] ): ?><tr><td nowrap class="chat_info_td_h" nowrap><b>IP Address</b></td><td class="chat_info_td"> <span id="req_ip"></span></td></tr><?php endif ; ?>
							<tr><td nowrap class="chat_info_td_h" nowrap><b>Custom Vars</b></td><td class="chat_info_td"><div id="req_custom" style="max-height: 80px; overflow-y: auto; overflow-x: hidden;"></div></td></tr>
							<tr><td class="chat_info_td_h" style="opacity: 0.5; filter: alpha(opacity=50);"><b>Chat ID</b></td><td class="chat_info_td" style="opacity: 0.5; filter: alpha(opacity=50);"> <span id="req_ces"></span> &nbsp; <span id="req_t_ses" style="display: none;"></span></td></tr>
							</table>
						</div>

						<div id="div_info_transcripts" style="display: none; padding-bottom: 50px;">
							<div id="div_chats_trans_list"></div>
						</div>

						<div id="div_info_transfer" style="display: none; padding-bottom: 50px;">
						</div>

						<div id="div_info_spam" style="display: none;">
						</div>
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
