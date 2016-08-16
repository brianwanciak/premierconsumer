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

	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;

	if ( !isset( $CONF['foot_log'] ) ) { $CONF['foot_log'] = "on" ; }
	if ( !isset( $CONF['icon_check'] ) ) { $CONF['icon_check'] = "on" ; }
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> traffic monitor </title>

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
	var loaded = 1 ;
	var secondtime = 0 ;
	var map_left ;
	var global_div_width ;
	var global_div_height ;

	$(document).ready(function()
	{
		populate_traffic() ;
		init_depts() ;

		var div_height = parent.extra_wrapper_height - 55 ;
		$('#footprint_info_container').css({'min-height': div_height}) ;
		$('#canned_container').css({'min-height': div_height}) ;
		$('#canned_wrapper').fadeIn() ;

		//$(document).dblclick(function() {
		//	parent.close_extra( "traffic" ) ;
		//});

		parent.init_extra_loaded() ;
	});

	function init_depts()
	{
		var depts_select = "<select name=\"ini_deptid\" id=\"ini_deptid\" onChange=\"parent.initiate_deptid = this.value;\" style=\"min-width: 240px;\"><option value=0></option>" ;
		for ( var thisdeptid in parent.op_depts_hash )
		{
			var selected = "" ;
			if ( thisdeptid == parent.initiate_deptid )
				selected = "selected" ;

			depts_select += "<option value=\""+thisdeptid+"\" "+selected+">"+parent.op_depts_hash[thisdeptid]+"</option>" ;
		}
		depts_select += "</select>" ;

		$('#depts_select').html( depts_select ) ;
	}

	function init_trs( thetable )
	{
		$('#'+thetable+' tr:nth-child(2n+3)').addClass('chat_info_tr_traffic_row') ;
	}

	function populate_traffic()
	{
		var json_data = new Object ;
		var unique = unixtime() ;
		var image, image_info ;
		var footprints = new Object ;
		var vis_tokens = "" ;

		if ( "<?php echo $CONF['icon_check'] ?>" == "off" )
		{
			$('#canned_body').empty().html( "<div class='chat_info_td_traffic'>Traffic monitor has been switched off by the setup admin.</div>" ) ;
			return false ;
		}
		else if ( parent.automatic_offline_active )
		{
			$('#canned_body').empty().html( "<div class='chat_info_td_traffic'>Traffic Monitor is not available during offline hours.</div>" ) ;
			return false ;
		}

		if ( parent.extra == "traffic" )
		{
			for ( var thismd5 in parent.traffic_data ) { vis_tokens += "&vt[]="+thismd5.substring(0, 4) ; }

			$.ajax({
			type: "GET",
			url: "../ajax/chat_actions_op_itr_traffic.php",
			data: "action=traffic&unique="+unique+vis_tokens,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					var vis_exist = 0 ;
					var td_geoip = ( <?php echo $geoip ?> ) ? "<td width=\"50\"><div class=\"chat_info_td_t\">&nbsp;</div></td>" : "" ;
					var td_ip = ( parent.viewip ) ? "<td width=\"90\"><div class=\"chat_info_td_t\">IP</div></td>" : "" ;
					var td_market = ( parent.total_markets ) ? "<td width=\"80\"><div class=\"chat_info_td_t\">Campaign</div></td>" : "" ;
					var traffic_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\" id=\"table_trs_traffic\"><tr><td width=\"45\"><div class=\"chat_info_td_t\">&nbsp;</div></td><td width=\"80\"><div class=\"chat_info_td_t\">Duration</div></td>"+td_market+td_geoip+td_ip+"<td width=\"90\"><div class=\"chat_info_td_t\">Platform</div></td><td width=\"45\"><div class=\"chat_info_td_t\" style=\"margin-right: 0px; cursor: help;\" title=\"Total Footprints\" alt=\"Total Footprints\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/info.png\" width=\"10\" height=\"10\" border=\"0\" title=\"Total Footprints\" alt=\"Total Footprints\"> t-f</div></td><td width=\"45\"><div class=\"chat_info_td_t\" style=\"margin-right: 0px; cursor: help;\" title=\"Total Chat Requests\" alt=\"Total Chat Requests\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/info.png\" width=\"10\" height=\"10\" border=\"0\" title=\"Total Chat Requests\" alt=\"Total Chat Requests\"> t-c</div></td><td width=\"45\"><div class=\"chat_info_td_t\" style=\"margin-right: 0px; cursor: help;\" title=\"Total Operator Initiated Chats\" alt=\"Operator Initiated Chats\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/info.png\" width=\"10\" height=\"10\" border=\"0\" title=\"Total Operator Initiated Chats\" alt=\"Operator Initiated Chats\"> t-i</div></td><td><div class=\"chat_info_td_t\" style=\"margin-right: 0px; white-space: nowrap;\">On Page</div></td><td nowrap><div class=\"chat_info_td_t\">Refer</div></td></tr>" ;

					for ( var c = 0; c < json_data.traffics.length; ++c )
					{
						var vis_token = json_data.traffics[c]["vis_token"] ;
						if ( typeof( parent.traffic_data[vis_token] ) == "undefined" ) { parent.traffic_data[vis_token] = json_data.traffics[c] ; }
						else
						{
							for ( var key_name in parent.traffic_data[vis_token] )
							{
								if ( typeof( json_data.traffics[c][key_name] ) == "undefined" )
								{  json_data.traffics[c][key_name] = parent.traffic_data[vis_token][key_name] ; }
							}
						}

						var market_name = ( ( typeof( parent.markets[json_data.traffics[c]["marketid"]] ) != "undefined" ) && ( typeof( parent.markets[json_data.traffics[c]["marketid"]]["name"] ) != "undefined" ) ) ? parent.markets[json_data.traffics[c]["marketid"]]["name"] : "" ;
						var market_color = ( ( typeof( parent.markets[json_data.traffics[c]["marketid"]] ) != "undefined" ) && ( typeof( parent.markets[json_data.traffics[c]["marketid"]]["color"] ) != "undefined" ) ) ? "style=\"background: #"+parent.markets[json_data.traffics[c]["marketid"]]["color"]+"\"" : "" ;
						var market_td = ( parent.total_markets ) ? "<td class=\"chat_info_td_traffic\" "+market_color+">"+market_name+"</td>" : "" ;
						image = "actions.png" ; image_info = "&nbsp;" ;
						var td_map = ( <?php echo $geoip ?> ) ? "<td class=\"chat_info_td_traffic\" onClick=\"expand_map('"+json_data.traffics[c]["vis_token"]+"', '"+json_data.traffics[c]["ip"]+"', 1)\" id=\"footprint_map_"+json_data.traffics[c]["vis_token"]+"\" style=\"cursor: pointer;\"><span title=\"Country: "+parent.countries[json_data.traffics[c]["country"]]+", Region: "+json_data.traffics[c]["region"]+", City: "+json_data.traffics[c]["city"]+"\" alt=\"Country: "+parent.countries[json_data.traffics[c]["country"]]+", Region: "+json_data.traffics[c]["region"]+", City: "+json_data.traffics[c]["city"]+"\"><img src=\"../pics/maps/"+json_data.traffics[c]["country"].toLowerCase()+".gif\" width=\"18\" height=\"12\" border=0 id=\"map_"+json_data.traffics[c]["vis_token"]+"\"></span></td>" : "" ;
						var td_viewip = ( parent.viewip ) ? "<td class=\"chat_info_td_traffic\">"+json_data.traffics[c]["ip"]+"</td>" : "" ;

						if ( json_data.traffics[c]["vis_token"] == parent.vis_token )
							vis_exist = 1 ;

						if ( json_data.traffics[c]["chatting"] == 1 )
						{
							image = "chats.png" ;
							image_info = "title=\"requested chat\" alt=\"requested chat\"" ;
						}

						var url_raw = json_data.traffics[c]["onpage"] ;
						if ( url_raw == "livechatimagelink" )
							url_raw = "JavaScript:void(0)" ;

						traffic_string += "<tr style=\"\"><td class=\"chat_info_td_traffic\" title=\"View Visitor Details\" alt=\"View Visitor Details\" style=\"-moz-border-radius: 5px; border-radius: 5px;\" id=\"td_"+json_data.traffics[c]["vis_token"]+"\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/"+image+"\" border=\"0\" alt=\"\" style=\"cursor: pointer;\" "+image_info+" onClick=\"expand_footprint('"+json_data.traffics[c]["vis_token"]+"', '"+json_data.traffics[c]["duration"]+"', '"+market_name+"', '"+json_data.traffics[c]["ip"]+"', '"+json_data.traffics[c]["os"]+"', '"+json_data.traffics[c]["browser"]+"', '"+json_data.traffics[c]["resolution"]+"', '"+json_data.traffics[c]["t_footprints"]+"', '"+json_data.traffics[c]["t_requests"]+"', '"+json_data.traffics[c]["t_initiates"]+"', '"+json_data.traffics[c]["title"]+"', '"+json_data.traffics[c]["onpage"]+"', '"+json_data.traffics[c]["refer_snap"]+"', '"+json_data.traffics[c]["refer_raw"]+"', '"+json_data.traffics[c]["country"]+"', '"+json_data.traffics[c]["region"]+"', '"+json_data.traffics[c]["city"]+"' )\" id=\"footprint_"+json_data.traffics[c]["vis_token"]+"\"></td><td class=\"chat_info_td_traffic\" nowrap>"+json_data.traffics[c]["duration"]+"</td>"+market_td+td_map+td_viewip+"<td class=\"chat_info_td_traffic\" style=\"text-align: center;\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/os/"+json_data.traffics[c]["os"]+".png\" border=0 alt=\""+json_data.traffics[c]["os"]+"\" title=\""+json_data.traffics[c]["os"]+"\" width=\"14\" height=\"14\"> &nbsp; <img src=\"../themes/<?php echo $opinfo["theme"] ?>/browsers/"+json_data.traffics[c]["browser"]+".png\" border=0 alt=\""+json_data.traffics[c]["browser"]+"\" title=\""+json_data.traffics[c]["browser"]+"\" width=\"14\" height=\"14\"></td><td class=\"chat_info_td_traffic\"><div title=\"Total Footprints\" alt=\"Total Footprints\">"+json_data.traffics[c]["t_footprints"]+"</div></td><td class=\"chat_info_td_traffic\"><div title=\"Total Chat Requests\" alt=\"Total Chat Requests\">"+json_data.traffics[c]["t_requests"]+"</div></td><td class=\"chat_info_td_traffic\"><div title=\"Total Operator Initiated Chats\" alt=\"Operator Initiated Chats\">"+json_data.traffics[c]["t_initiates"]+"</div></td><td class=\"chat_info_td_traffic\" title=\""+json_data.traffics[c]["onpage"]+"\" alt=\""+json_data.traffics[c]["onpage"]+"\"><a href=\""+url_raw+"\" target=\"_blank\">"+json_data.traffics[c]["title"]+"</a></td><td class=\"chat_info_td_traffic\" nowrap><span title=\""+json_data.traffics[c]["refer_raw"]+"\" alt=\""+json_data.traffics[c]["refer_raw"]+"\"><a href=\""+json_data.traffics[c]["refer_raw"]+"\" target=\"_blank\">"+json_data.traffics[c]["refer_snap"]+"</a></span></td></tr>" ;

						footprints[vis_token] = 1 ;
					}
					traffic_string += "</table>" ;

					$('#canned_body').empty().html( traffic_string ) ;
					init_trs( 'table_trs_traffic' ) ;

					if ( $('#canned_wrapper').is(':visible') )
					{
						// set traffic info div for browser compatibility
						global_div_width = $('#canned_body').outerWidth() ;
						global_div_height = parent.$('#chat_extra_wrapper').outerHeight() - 200 ;
					}

					for ( var thismd5 in parent.maps_his_ )
					{
						if ( typeof( footprints[thismd5] ) == "undefined" )
							parent.delete_object( "map", thismd5 ) ;
					}
					for ( var thismd5 in parent.traffic_data )
					{
						if ( typeof( footprints[thismd5] ) == "undefined" )
							parent.delete_object( "traffic", thismd5 ) ;
					}

					if ( vis_exist )
					{
						// todo: open up the ip automatically
						// still needs to be hashed out
					}

					if ( !$('#canned_body').is(':visible') && !$('#footprint_info_container').is(':visible') )
					{ $('#canned_body').show() ; }
					
					if ( secondtime && !$('#footprint_info_container').is(':visible') )
						do_alert( 1, "Refresh Success" ) ;

					++secondtime ;
				}
				else { do_alert( 0, "Error loading traffic monitor.  Please reload the console and try again." ) ; }
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error loading traffic monitor.  Please reload the console and try again." ) ;
			} });
		}
	}

	function expand_footprint( thevis_token, theduration, themarket, theip, theos, thebrowser, theresolution, thet_footprints, thet_requests, thet_initiates, thetitle, theonpage, therefer_snap, therefer_raw, thecountry, theregion, thecity )
	{
		parent.vis_token = thevis_token ;

		select_footprint( thevis_token ) ;
		populate_footprint( thevis_token, theduration, themarket, theip, theos, thebrowser, theresolution, thet_footprints, thet_requests, thet_initiates, thetitle, theonpage, therefer_snap, therefer_raw, thecountry, theregion, thecity ) ;
	}

	function select_footprint( thevis_token )
	{
		$( '#canned_body' ).find('*').each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf("td_") != -1 )
				$(this).removeClass('chat_info_td_traffic_img') ;
		} );

		$('#td_'+thevis_token).addClass('chat_info_td_traffic_img') ;
	}

	function populate_footprint( thevis_token, theduration, themarket, theip, theos, thebrowser, theresolution, thet_footprints, thet_requests, thet_initiates, thetitle, theonpage, therefer_snap, therefer_raw, thecountry, theregion, thecity )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_footprints.php",
		data: "action=footprints&vis_token="+thevis_token+"&ip="+theip+"&unique="+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				var footprints_string = "<table cellspacing=0 cellpadding=0 border=0>" ;
				for ( var c = 0; c < json_data.footprints.length; ++c )
				{
					var url_raw = json_data.footprints[c]["onpage"] ;
					if ( url_raw == "livechatimagelink" )
						url_raw = "JavaScript:void(0)" ;

					footprints_string += "<tr><td width=\"30\" style=\"text-align: center\" class=\"chat_info_td\"><div style=\"font-weight: bold;\" title=\"Total\" alt=\"Total\">"+json_data.footprints[c]["total"]+"<div></td><td width=\"100%\" class=\"chat_info_td\"><div title=\""+json_data.footprints[c]["onpage"]+"\" alt=\""+json_data.footprints[c]["onpage"]+"\"><a href=\""+url_raw+"\" target=\"_blank\">"+json_data.footprints[c]["title"]+"</a></div></td></tr>" ;
				}
				footprints_string += "<tr><td colspan=2 class=\"chat_info_end\"></td></tr></table>" ;

				var url_raw = theonpage ; var refer_vis = therefer_raw ;
				if ( url_raw == "livechatimagelink" ) { url_raw = "JavaScript:void(0)" ; }

				if ( refer_vis.length > 80 ) { refer_vis = therefer_raw.substring(0,80)+"..." ; }

				$('#info_market').empty().html( themarket ) ;
				$('#info_duration').empty().html( theduration ) ;
				$('#info_requests').empty().html( thet_requests ) ;
				$('#info_initiates').empty().html( thet_initiates ) ;
				$('#info_platform').empty().html( "<img src=\"../themes/<?php echo $opinfo["theme"] ?>/os/"+theos+".png\" border=0 alt=\""+theos+"\" title=\""+theos+"\" alt=\""+theos+"\" width=\"14\" height=\"14\"> &nbsp; <img src=\"../themes/<?php echo $opinfo["theme"] ?>/browsers/"+thebrowser+".png\" border=0 alt=\""+thebrowser+"\" title=\""+thebrowser+"\" alt=\""+thebrowser+"\" width=\"14\" height=\"14\">" ) ;
				$('#info_resolution').empty().html( theresolution ) ;
				$('#info_vis_token').empty().html( thevis_token ) ;
				$('#info_onpage').empty().html( "<div title=\""+theonpage+"\" alt=\""+theonpage+"\"><a href=\""+url_raw+"\" target=\"_blank\">"+thetitle+"</a></div>" ) ;
				$('#info_refer').empty().html( "<div title=\""+therefer_raw+"\" alt=\""+therefer_raw+"\"><a href=\""+therefer_raw+"\" target=\"_blank\">"+refer_vis+"</a></div>" ) ;
				$('#info_footprints').empty().html( footprints_string ) ;

				<?php if ( $geoip ): ?>
				var pos = $('#map_'+thevis_token).position() ;
				map_left = pos.left + 25 ;
				<?php endif ; ?>
			
				toggle_menu_info( "info" ) ;
				$('#canned_body').hide() ;
				$('#footprint_info_container').show() ;
				populate_requestinfo( theip, thevis_token, thecountry, theregion, thecity ) ;
				populate_transcripts( thevis_token ) ;
			}
			else { do_alert( 0, "Error loading footprint information.  Please reload the console and try again." ) ; }
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error loading footprint information.  Please reload the console and try again." ) ;
		} });
	}

	function populate_requestinfo( theip, thevis_token, thecountry, theregion, thecity )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_reqinfo.php",
		data: "action=requestinfo&vis_token="+thevis_token+"&ip="+theip+"&unique="+unique,
		success: function(data){
			eval( data ) ;

			var span_map = ( <?php echo $geoip ?> ) ? "<span title=\"Country: "+parent.countries[thecountry]+", Region: "+theregion+", City: "+thecity+"\" alt=\"Country: "+parent.countries[thecountry]+", Region: "+theregion+", City: "+thecity+"\" onClick=\"expand_map('"+thevis_token+"', '"+theip+"', 0)\" style=\"cursor: pointer;\"><img src=\"../pics/maps/"+thecountry.toLowerCase()+".gif\" width=\"18\" height=\"12\" border=0></span> &nbsp; " : "" ;
			var span_ip = ( parent.viewip ) ? theip : "" ;

			$('#info_ip').empty().html( span_map+span_ip ) ;
			if ( json_data.status )
			{
				$('#info_duration').empty().html( " <span class=\"info_good\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/info_chats.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" style=\"cursor: help;\" title=\"currently chatting with "+json_data.name+"\" alt=\"currently in a chat session with "+json_data.name+"\"></span>" ) ;
			}
			$('#info_trans').empty().html( json_data.total_trans ) ;

			$('#chat_info_cans_select').empty().html( "<select id=\"canned_info_select\" style=\"min-width: 240px;\" onChange=\"select_canned()\"><option value=\"\"></option>"+parent.cans_string+"</select>" ) ;

			if ( typeof( parent.initiate_canid ) != "undefined" )
			{
				$('#canned_info_select').attr( 'selectedIndex', parent.initiate_canid ) ;
				select_canned() ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error loading traffic information.  Please reload the console and try again." ) ;
		} });
	}

	function select_canned()
	{
		$( "#chat_info_initiate_message" ).val( $('#canned_info_select').val().replace( /<br>/g, "\r" ) ) ;
		parent.initiate_canid = $('#canned_info_select' ).attr( 'selectedIndex' ) ;
	}

	function close_footprint_info( theflag )
	{
		parent.vis_token = undeefined ;

		$('#div_info_trans_list').hide() ;
		$('#div_info_trans_loading').show() ;
	
		if ( theflag )
		{
			$('#footprint_info_container').hide() ;
			$('#canned_body').show() ;
		}
		else
		{
			$('#footprint_info_container').hide() ;
			$('#chatting_with').empty() ;
			$('#canned_body').show() ;
			toggle_menu_info( "info" ) ;
		}

		$('#canned_wrapper').show() ;
	}

	function initiate_chat()
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var deptid = parseInt( $('#ini_deptid').val() ) ;
		var message = encodeURIComponent( $('#chat_info_initiate_message').val() ) ;
		
		if ( parent.vis_token.indexOf( "error" ) != -1 )
			parent.do_alert( 0, "Visitor has been blocked.  Request was not processed." ) ;
		else
		{
			if ( deptid && message )
			{
				$('#btn_initiate').html( "Connecting <img src=\"../themes/<?php echo $opinfo["theme"] ?>/loading_fb.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\">" ).attr("disabled", true) ;

				$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_initiate.php",
				data: "action=initiate&vis_token="+parent.vis_token+"&deptid="+deptid+"&question="+message+"&unique="+unique,
				success: function(data){
					eval( data ) ;

					if ( json_data.status )
					{
						// should automatically close, but as a safe measure close it again with timeout
						setTimeout( function(){
							parent.input_focus() ;
							parent.close_extra( "traffic" ) ;
							$('#btn_initiate').attr("disabled", false) ;
						}, 5000 ) ;
					}
					else
					{
						do_alert( 0, json_data.error ) ;
						$('#btn_initiate').html('Initiate Chat').attr('disabled', false) ;
					}
				},
				error:function (xhr, ajaxOptions, thrownError){
					do_alert( 0, "Error initiating chat.  Please reload the console and try again." ) ;
				} });
			}
			else if ( !message )
			{
				$('#chat_info_initiate_message').focus() ;
				do_alert( 0, "Blank Initiate Message is invalid." ) ;
			}
			else if ( !deptid )
			{
				$('#ini_deptid').focus() ;
				do_alert( 0, "Blank Department is invalid" ) ;
			}
		}
	}

	function expand_map( thevis_token, theip, theflag )
	{
		if ( theflag )
		{
			var pos = $('#map_'+thevis_token).position() ;
			map_left = pos.left + 25 ;
		}

		select_footprint( thevis_token ) ;
		parent.expand_map( map_left, thevis_token, theip ) ;
	}

	function toggle_menu_info( themenu )
	{
		var divs = Array( "info", "trans", "initiate" ) ;

		$('#div_traffic_info_info').css({'width': global_div_width, 'height': global_div_height}) ;
		$('#div_traffic_info_trans').css({'width': global_div_width, 'height': global_div_height}) ;
		$('#div_traffic_info_initiate').css({'width': global_div_width, 'height': global_div_height}) ;

		for ( var c = 0; c < divs.length; ++c )
		{
			$('#div_traffic_info_'+divs[c]).hide() ;
			$('#menu_traffic_info_'+divs[c]).removeClass('menu_traffic_info_focus').addClass('menu_traffic_info') ;
		}

		$('#div_traffic_info_'+themenu).show() ;
		$('#menu_traffic_info_'+themenu).removeClass('menu_traffic_info').addClass('menu_traffic_info_focus') ;

		if ( themenu == "trans" ) { init_trs( 'table_trs_trans' ) ; }
	}

	function populate_transcripts( thevis_token )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_trans.php",
		data: "action=transcripts&vis_token="+thevis_token+"&unique="+unique,
		success: function(data){
			eval( data ) ;

			$('#div_info_trans_loading').hide() ;
			$('#div_info_trans_list').show() ;
			if ( json_data.status )
			{
				var transcripts_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\" id=\"table_trs_trans\"> <tr> <td width=\"28\" nowrap><div class=\"chat_info_td_t\">&nbsp;</div></td> <td width=\"110\"><div class=\"chat_info_td_t\">Operator</div></td> <td width=\"110\"><div class=\"chat_info_td_t\">Visitor</div></td> <td width=\"80\"><div class=\"chat_info_td_t\">Rating</div></td> <td width=\"150\"><div class=\"chat_info_td_t\">Created</div></td> <td width=\"80\"><div class=\"chat_info_td_t\">Duration</div></td> <td><div class=\"chat_info_td_t\">Question</div></td> </tr>" ;
				for ( var c = 0; c < json_data.transcripts.length; ++c )
				{
					var rating = parseInt( json_data.transcripts[c]["rating"] ) ;
					var rating_stars = ( rating && ( typeof( parent.stars[rating] ) != "undefined" ) ) ? parent.stars[rating] : "&nbsp;" ;
					transcripts_string += "<tr><td class=\"chat_info_td_traffic\" width=\"16\" id=\"img_"+json_data.transcripts[c]["ces"]+"\" style=\"cursor: pointer; -moz-border-radius: 5px; border-radius: 5px;\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/view.png\" onClick=\"parent.open_transcript('"+json_data.transcripts[c]["ces"]+"')\" width=\"16\" height=\"16\" title=\"view transcript\" alt=\"view transcript\"></td><td class=\"chat_info_td_traffic\">"+json_data.transcripts[c]["operator"]+"</td><td class=\"chat_info_td_traffic\">"+json_data.transcripts[c]["vname"]+"</td><td class=\"chat_info_td_traffic\">"+rating_stars+"</td><td class=\"chat_info_td_traffic\" nowrap>"+json_data.transcripts[c]["created"]+"</td><td class=\"chat_info_td_traffic\" nowrap>"+json_data.transcripts[c]["duration"]+"</td><td class=\"chat_info_td_traffic\">"+json_data.transcripts[c]["question"]+"</td></tr>" ;
				}

				if ( json_data.transcripts.length == 0 )
					transcripts_string += "<tr><td colspan=7>Blank results.</td></tr>" ;

				transcripts_string += "</table>" ;
				$('#div_info_trans_list').empty().html( transcripts_string ) ;
			}
			else { do_alert( 0, "Error populating visitor transcripts.  Please reload the console and try again." ) ; }
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error populating visitor transcripts.  Please reload the console and try again." ) ;
		} });
	}

	function set_trans_img( theces )
	{
		if ( typeof( parent.ces_trans ) != "undefined" )
			$('#img_'+parent.ces_trans).removeClass('chat_info_td_traffic_img') ;

		parent.ces_trans = theces ;
		$('#img_'+theces).addClass('chat_info_td_traffic_img') ;
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
				<div id="canned_body" style="padding-bottom: 10px;"><img src="../themes/<?php echo $opinfo["theme"] ?>/loading_fb.gif" width="16" height="11" border="0"></div>
				<div id="footprint_info_container" style="display: none;">
					<div><span onClick="close_footprint_info(0)" style="cursor: pointer;"><img src="../themes/<?php echo $opinfo["theme"] ?>/close_extra.png" width="12" height="12" border="0" alt="close info" title="close info"> close</span></div>
					<div style="margin-top: 15px;">
						<div id="menu_traffic_info_info" class="menu_traffic_info_focus" onClick="toggle_menu_info('info')">Visitor Information</div>
						<div id="menu_traffic_info_trans" class="menu_traffic_info" onClick="toggle_menu_info('trans')">Visitor Transcripts (<span id="info_trans"></span>)</div>
						<?php if ( $CONF['icon_check'] == "on" ): ?>
						<div id="menu_traffic_info_initiate" class="menu_traffic_info" onClick="toggle_menu_info('initiate')">Initiate Chat with Visitor</div>
						<?php endif ; ?>
						<div style="clear: both;"></div>
					</div>
					<div id="div_traffic_info_info" style="margin-top: 15px;">
						<table cellspacing=0 cellpadding=0 border=0 width="100%">
						<tr>
							<td valign="top" width="30%">
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
								<tr>
									<td class="chat_info_td_traffic">Platform</td>
									<td class="chat_info_td_traffic_info" width="100%"><span id="info_resolution"></span> &nbsp; <span id="info_platform"></span></td>
								</tr>
								<tr>
									<td class="chat_info_td_traffic">Duration</td>
									<td class="chat_info_td_traffic_info"><span id="info_duration"></span></td>
								</tr>
								<tr>
									<td class="chat_info_td_traffic" nowrap>Requested Chat</td>
									<td class="chat_info_td_traffic_info"><span id="info_requests"></span></td>
								</tr>
								<tr>
									<td class="chat_info_td_traffic" nowrap>Op Initiated</td>
									<td class="chat_info_td_traffic_info"><span id="info_initiates"></span></td>
								</tr>
								<tr>
									<td class="chat_info_td_traffic">Location</td>
									<td class="chat_info_td_traffic_info"><div id="info_ip"></div></td>
								</tr>
								</table>
							</td>
							<td valign="top" width="70%">
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
								<tr>
									<td class="chat_info_td_traffic">Visitor ID</td>
									<td class="chat_info_td_traffic_info" width="100%"><div id="info_vis_token"></div></td>
								</tr>
								<tr>
									<td class="chat_info_td_traffic">On Page</td>
									<td class="chat_info_td_traffic_info" width="100%"><div id="info_onpage"></div></td>
								</tr>
								<tr>
									<td class="chat_info_td_traffic">Refer URL</td>
									<td class="chat_info_td_traffic_info"><div id="info_refer"></div></td>
								</tr>
								<tr>
									<?php if ( $CONF["foot_log"] == "on" ): ?>
									<td class="chat_info_td_traffic" valign="top" nowrap>Footprints<div style="font-size: 10px;">* recent <?php echo $VARS_FOOTPRINT_STATS_EXPIRE ?> days</div></td>
									<td class="chat_info_td_traffic">
										<div style="height: 180px; width: 100%; overflow: auto;" id="info_footprints"></div>
									</td>
									<?php endif;  ?>
								</tr>
								</table>
							</td>
						</tr>
						</table>
					</div>
					<div id="div_traffic_info_trans" style="display: none; margin-top: 15px; overflow: auto; overflow-x: hidden;">
						<div id="div_info_trans_loading"><img src="../themes/<?php echo $opinfo["theme"] ?>/loading_fb.gif" width="16" height="11" border="0" alt=""></div>
						<div id="div_info_trans_list" style="display: none;"></div>
					</div>
					<div id="div_traffic_info_initiate" style="display: none; margin-top: 15px; overflow: auto;">
						<form action="#">
						<div style="margin-top: 15px;">
							<table cellspacing=0 cellpadding=0 border=0>
							<tr>
								<td valign="top">
									<div>Chat Introduction Message:</div>
									<div style="margin-top: 5px;"><textarea id="chat_info_initiate_message" class="input_text" cols="40" rows="3" wrap="virtual" style="resize: none;"></textarea></div>
									<div style="margin-top: 5px;">* brief message (recommended: 25 or fewer words)</div>

									<div style="margin-top: 25px;">
										<div>or choose Canned Response:</div>
										<div style="margin-top: 5px;"><span id="chat_info_cans_select" style="padding-right: 10px;"></span></div>
									</div>
								</td>
								<td valign="top">
									<div style="padding-left: 25px;">
										<div>From Department:</div>
										<div style="margin-top: 5px;"><span id="depts_select"></span></div>
									</div>
								</td>
								<td valign="top">
									<div style="padding-left: 25px;">
										<div>&nbsp;</div>
										<div style="margin-top: 5px;"><button type="button" onClick="initiate_chat()" id="btn_initiate">Initiate Chat</button>
									</div>
								</td>
							</tr>
							</table>
						</div>
						</form>
					</div>
				</div>
			</div>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>
