var chat_http_error ; var st_http ; var process_throttle ;
function add_text( theces, thetext )
{
	if ( ( thetext != "" ) && ( typeof( chats[theces] ) != "undefined" ) )
	{
		thetext = init_timestamps( thetext.nl2br() ) ;
		chats[theces]["trans"] += thetext ;

		if ( theces == ces )
		{
			$('#chat_body').append( thetext.emos() ) ; if ( isop && mapp ) { init_external_url() ; }

			// on IE 8 (standard view) it's not remembering the srollTop... when any div takes focus away
			// scrolling goes back to ZERO... effects are minimal
			init_scrolling() ;
		}
	}
}

function add_text_prepare( theflag )
{
	var process_start = get_microtime() ;
	if ( typeof( process_throttle ) == "undefined" ) { process_throttle = process_start ; }
	else
	{
		var process_diff = process_start - process_throttle ;
		process_throttle = process_start ;

		// throttle check don't send but keep it in textarea
		if ( process_diff <= 500 )
			return true ;
	}

	var thetext = $( "textarea#input_text" ).val() ;
	thetext_temp = thetext.replace( / /g, "" ) ;
	//if ( !thetext_temp.slice(0, -1) ) { $( "textarea#input_text" ).val( "" ) ; return false ; }

	if ( isop )
		thetext = thetext.trimreturn().noreturns().tags().vars() ;
	else
		thetext = thetext.trimreturn().noreturns().tags() ;

	if ( isop && shortcut_enabled ) {
		if ( thetext.match( /^\// ) && !thetext.match( /^\/nolink / ) ) { process_shortcuts( thetext ) ; return true ; }
	}
	thetext = autolink_it( thetext ) ;

	if ( ( thetext != "" ) && ( typeof( chats[ces] ) != "undefined" ) )
	{
		var cdiv ;
		var now = unixtime() ;

		if ( isop )
		{
			var height_input_text = $("textarea#input_text").height() ;
			if ( parseInt( height_input_text ) != 75 )
			{
				//$("#chat_input").css({'bottom': "auto"}) ; $('textarea#input_text').css({'height': 75}) ;
				//$('#chat_body').css({'height': height_chat_body}) ;
			}

			if ( chats[ces]["op2op"] )
			{
				if ( parseInt( chats[ces]["op2op"] ) == parseInt( isop ) )
					cdiv = "co" ;
				else
					cdiv = "cv" ;
			}
			else
				cdiv = "co" ;
		}
		else
			cdiv = "cv" ;

		thetext = "<div class='"+cdiv+"'><span class='notranslate'><b>"+cname+"<timestamp_"+now+"_"+cdiv+">:</b></span> "+thetext+"</div>" ;

		if ( typeof( st_http ) != "undefined" ) { clearTimeout( st_http ) ; st_http = undeefined ; }
		st_http = setTimeout( function(){ $('#chat_processing').show() ; }, 5000 ) ;
		idle_reset( ces ) ; $('#idle_timer_notice').hide() ;
		if ( theflag ) { add_text( ces, thetext ) ; }
		http_text( thetext ) ;
	}

	$('button#input_btn').attr( "disabled", true ) ;
	$('textarea#input_text').val( "" ) ;

	if ( !mapp && !mobile ) { $('textarea#input_text').focus() ; }
}

function http_text( thetext )
{
	var json_data = new Object ;
	var unique = unixtime() ;

	var thesalt = ( typeof( salt ) != "undefined" ) ? salt : "nosalt" ;

	if ( typeof( chats[ces] ) != "undefined" )
	{
		$.ajax({
		type: "POST",
		url: base_url+"/ajax/chat_submit.php",
		data: "requestid="+chats[ces]["requestid"]+"&t_vses="+chats[ces]["t_ses"]+"&isop="+isop+"&isop_="+isop_+"&isop__="+isop__+"&op2op="+chats[ces]["op2op"]+"&ces="+ces+"&text="+encodeURIComponent( thetext )+"&salt="+thesalt+"&unique="+unique+"&",
		success: function(data){
			try {
				if ( chat_http_error ) { do_alert( 1, "Reconnect success!" ) ; chat_http_error = 0 ; }
				eval(data) ;
			} catch(err) {
				do_alert( 0, "Disconnected. Reconnecting..." ) ; chat_http_error = 1 ;
				setTimeout( function(){ http_text( thetext ) ; }, 6000 ) ;
				return false ;
			}
			if ( json_data.status ) {
				if ( typeof( st_http ) != "undefined" ) { clearTimeout( st_http ) ; st_http = undeefined ; }
				$('#chat_processing').hide() ;
				clearTimeout( st_typing ) ;
				st_typing = undeefined ;
			}
			else { do_alert( 0, "Error sending message.  Please reload the page and try again." ) ; }
		},
		error:function (xhr, ajaxOptions, thrownError){
			// keep trying and don't track timeout so it processes all dropped requests
			setTimeout( function(){ http_text( thetext ) ; }, 6000 ) ;
		} });
	}
}

function get_microtime()
{
	return new Date().getTime() ;
}

function input_text_listen( e )
{
	var key = e.keyCode ;
	var shift = e.shiftKey ;

	if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
	{
		add_text_prepare(1) ;
	}
	else if ( ( key == 8 ) || ( key == 46 ) )
	{
		if ( $( "textarea#input_text" ).val() == "" )
			$( "button#input_btn" ).attr( "disabled", true ) ;
	}
	else if ( $( "textarea#input_text" ).val() == "" )
		$( "button#input_btn" ).attr( "disabled", true ) ;
	else
		$( "button#input_btn" ).attr( "disabled", false ) ;
}

function input_text_typing( e )
{
	input_focus() ;
	if ( isop && shortcut_enabled )
	{
		var thetext = $( "textarea#input_text" ).val() ;
		if ( thetext.match( /^\// ) ) { return true ; }
	}

	if ( $( "textarea#input_text" ).val() )
	{
		if ( typeof( st_typing ) == "undefined" )
		{
			send_istyping() ;
			st_typing = setTimeout( function(){ clear_istyping() ; }, 5000 ) ;
		}
	}
}

function init_typing()
{
	si_typing = setInterval(function(){
		if ( typeof( chats[ces] ) != "undefined" )
		{
			if ( chats[ces]["istyping"] )
				$('#chat_vistyping').show() ;
			else
				$('#chat_vistyping').hide() ;
		}
	}, 1500) ;
}

function init_idle( theces )
{
	// if op2op chat skip idle (status 2 is transfer chat and op2op value is temp storage of original opID)
	if ( parseInt( chats[theces]["op2op"] ) && ( parseInt( chats[theces]["status"] ) != 2 ) ) { return true ; }
	if ( ( typeof( chats[theces] ) != "undefined" ) && ( typeof( chats[theces]["idle_counter"] ) != "undefined" ) && ( typeof( chats[theces]["idle_si"] ) == "undefined" ) && parseInt( chats[theces]["idle"] ) )
	{
		chats[theces]["idle_si"] = setInterval(function(){
			if ( typeof( chats[theces] ) != "undefined" )
			{
				if ( parseInt( chats[theces]["idle_counter_pause"] ) ) { idle_reset( theces ) ; }
				if ( parseInt( chats[theces]["idle_counter"] ) != -1 ) { ++chats[theces]["idle_counter"] ; }
				idle_check( theces, parseInt( chats[theces]["idle"] ) - 60 ) ;
			}
		}, 1000) ;
	}
}

function idle_check( theces, thecounter )
{
	if ( ( typeof( chats[theces] ) != "undefined" ) && chats[theces]["idle"] )
	{
		if ( parseInt( chats[theces]["idle_counter"] ) == parseInt( thecounter ) )
		{
			idle_alert( theces, 0 ) ;
		}
		else if ( parseInt( chats[theces]["idle_counter"] ) >= parseInt( chats[theces]["idle"] ) )
		{
			idle_disconnect( theces ) ;
		}
	}
}

function idle_alert( theces, theskip )
{
	if ( ( typeof( chats[theces] ) != "undefined" ) )
	{
		if ( !theskip )
		{
			if ( !chats[theces]["idle_alert"] )
			{
				chats[theces]["idle_alert"] = setInterval(function(){
					if ( ces == theces )
					{
						var idle_countdown = parseInt( chats[ces]["idle"] ) - parseInt( chats[ces]["idle_counter"] ) ;
						if ( ( idle_countdown > 0 ) && ( parseInt( chats[ces]["idle_counter"] ) != -1 ) ) { $('#idle_countdown').html( idle_countdown ) ; }
						else { $('#idle_countdown').html( "0" ) ; }
					}
				}, 1000) ;
			}

			if ( ces != theces ) { menu_blink( "green", theces ) ; }
			else { $('#idle_timer_notice').show() ; }

			if ( chats[theces]["status"] )
			{
				if ( chat_sound ) { play_sound( 0, "new_text", "new_text_"+sound_new_text ) ; }
				if ( !isop )
				{
					if ( embed && ( typeof( parent.win_minimized ) != "undefined" ) && parent.win_minimized ) { flash_console(0) ; }
					else if ( console_blink_r ) { flash_console(0) ; }
				}
				title_blink_init() ;
			}
		}
		else
		{
			if ( parseInt( chats[theces]["idle_alert"] ) && ( parseInt( chats[theces]["idle_counter"] ) != -1 ) ) { $('#idle_timer_notice').show() ; }
			else { $('#idle_timer_notice').hide() ; }
		}
	}
}

function idle_reset( theces )
{
	if ( chats[theces]["idle_alert"] ) { clearInterval( chats[theces]["idle_alert"] ) ; }
	chats[theces]["idle_alert"] = 0 ;
	chats[theces]["idle_counter"] = 0 ;
}

function idle_disconnect( theces )
{
	chats[theces]["idle_counter"] = -1 ; // flag to indicate idle disconnect processed
	if ( typeof( chats[theces]["idle_si"] ) != "undefined" ) { clearInterval( chats[theces]["idle_si"] ) ; chats[theces]["idle_si"] = undeefined ; }
	if ( typeof( chats[theces]["timer_si"] ) != "undefined" ) { clearInterval( chats[theces]["timer_si"] ) ; chats[theces]["timer_si"] = undeefined ; }
	if ( isop )
	{
		add_text( theces, "<div class=\"cn\">Operator chat is idle.  Session automatically disconnected.</div>" ) ;
		disconnect(0, 0, theces) ;
	} else { disconnect(0, 1, theces) ; }
}

function send_istyping()
{
	var json_data = new Object ;
	var unique = unixtime() ;

	if ( typeof( chats[ces] ) != "undefined" )
	{
		$.ajax({
		type: "GET",
		url: base_url+"/ajax/chat_actions_istyping.php",
		data: "a=t&isop="+isop+"&isop_="+isop_+"&c="+ces+"&f=1&"+unique+"&",
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				do_alert( 0, err ) ;
				return false ;
			}

			if ( json_data.status ) {
				return true ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			// suppress error to limit confusion... if error here, there will be error reporting in more crucial areas
		} });
	}
}

function clear_istyping()
{
	var json_data = new Object ;
	var unique = unixtime() ;

	if ( typeof( chats[ces] ) != "undefined" )
	{
		$.ajax({
		type: "GET",
		url: base_url+"/ajax/chat_actions_istyping.php",
		data: "a=t&isop="+isop+"&isop_="+isop_+"&c="+ces+"&f=0&"+unique+"&",
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				do_alert( 0, err ) ;
				return false ;
			}
			
			if ( json_data.status ) {
				clearTimeout( st_typing ) ;
				st_typing = undeefined ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			// suppress error to limit confusion... if error here, there will be error reporting in more crucial areas
		} });
	}
}

function init_scrolling()
{
	if ( ( typeof( chats ) != "undefined" ) && ( typeof( chats[ces] ) != "undefined" ) && ( parseInt( chats[ces]["status"] ) != 2 ) && !widget )
	{
		$('#chat_body').prop( "scrollTop", $('#chat_body').prop( "scrollHeight" ) ) ;
	}
}

function init_textarea()
{
	if ( typeof( chats[ces] ) != "undefined" )
	{
		if ( ( parseInt( chats[ces]["status"] ) == 1 ) && !parseInt( chats[ces]["disconnected"] ) )
		{
			if ( $('textarea#input_text').is(':disabled') ) { $('textarea#input_text').attr("disabled", false) ; }
		}
		else if ( parseInt( chats[ces]["op2op"] ) && ( chats[ces]["op2op"] == isop ) )
		{
			if ( $('textarea#input_text').is(':disabled') ) { $('textarea#input_text').attr("disabled", false) ; }
		}
		else if ( parseInt( chats[ces]["initiated"] ) && !parseInt( chats[ces]["disconnected"] ) )
		{
			if ( $('textarea#input_text').is(':disabled') ) { $('textarea#input_text').attr("disabled", false) ; }
		}
		else
		{
			if ( !$('textarea#input_text').is(':disabled') ) { $('textarea#input_text').val( "" ).attr("disabled", true) ; }
		}
	}
	else
	{
		if ( !$('textarea#input_text').is(':disabled') ) { $('textarea#input_text').val( "" ).attr("disabled", true) ; }
	}
}

function init_divs( theresize )
{
	if ( theresize > 1 ) { mapp = theresize ; theresize = 0 ; } // mapp intercept
	var chat_body_padding = $('#chat_body').css('padding-left') ;
	var chat_body_padding_diff = ( typeof( chat_body_padding ) != "undefined" ) ? 20 - ( chat_body_padding.replace( /px/, "" ) * 2 ) : 0 ;

	var browser_height = ( ( mapp && mobile ) && ( mapp != 1 ) ) ? mapp : $(window).height() ;
	var body_height = browser_height - $('#chat_footer').height() - 132 ;
	if ( ( typeof( isop ) != "undefined" ) && parseInt( isop ) )
	{ if ( ( mobile == 2 ) || ( mapp == 1 ) ) { body_height = browser_height - $('#chat_footer_mapp').height() - 152 ; } else { body_height = browser_height - $('#chat_footer').height() - 152 ; } }

	var browser_width = $(window).width() ;
	// seemse recent iOS updates caused this quirk line to be obsolete
	//if ( ( mobile == 1 ) || ( mobile == 3 ) ) { browser_width = screen.width ; } // iOS has quirk fix
	var body_width = ( ( typeof( isop ) != "undefined" ) && parseInt( isop ) ) ? browser_width - 450 : browser_width - 42 ;
	var mapp_buffer_menu = ( ( mobile == 1 ) || ( mobile == 3 ) ) ? 25 : 0 ;

	var chat_body_width = body_width + chat_body_padding_diff ;
	var chat_body_height = body_height + chat_body_padding_diff - $('#chat_options').outerHeight() ; chat_body_height -= mapp_buffer_menu ;
	var input_text_width = ( ( typeof( isop ) != "undefined" ) && parseInt( isop ) ) ? body_width + 17 : body_width - 100 ;
	var intro_top = ( ( typeof( isop ) != "undefined" ) && parseInt( isop ) ) ? 30 : 12 ; var intro_left = ( ( typeof( isop ) != "undefined" ) && parseInt( isop ) ) ? body_width + 40 : input_text_width + 30 ;
	var chat_btn_top, intro_width, intro_height ;
	var chat_btn_left = intro_left ;

	if ( widget ) { return true ; }
	else if ( ( typeof( isop ) != "undefined" ) && parseInt( isop ) )
	{
		extra_top = browser_height - $('#chat_footer').outerHeight() ; // css top val of footer
		intro_height = browser_height - 153 ; // tweak depending on footer height
		chat_btn_top = intro_height + 30 ;
		chat_btn_left += 5 ;
		var chat_info_body_height = intro_height - ( $('#chat_info_header').height() + $('#chat_info_menu_list').height() ) - 24 ;
		var chat_panel_left = intro_left + $('#chat_btn').outerWidth() ;
		var chat_status_offline_left = chat_panel_left ;
		var chat_status_offline_top = intro_height - 65 ;
		var chat_data_height = intro_height ;
		var chat_extra_wrapper_height = browser_height - 90 ;
		var chat_info_network_height = chat_info_body_height - 55 ;

		$('#chat_body').css({'height': chat_body_height, 'width': chat_body_width}) ;
		$('#chat_data').css({'top': intro_top, 'left': intro_left, 'height': chat_data_height, 'width': 410}) ;
		$('#chat_info_body').css({'max-height': chat_info_network_height}) ;
		$('#chat_info_wrapper_network').css({'height': chat_info_network_height}) ;

		$('#chat_panel').css({'top': chat_btn_top, 'left': chat_panel_left}) ;
		if ( mapp && prev_status ) { $('#chat_status_offline').css({'bottom': 0, 'left': 0}).show() ; }
		else { $('#chat_status_offline').css({'top': chat_status_offline_top, 'left': chat_status_offline_left}) ; }

		$('#chat_extra_wrapper').css({'height': chat_extra_wrapper_height}) ;
		$('#chat_extra_wrapper').hide() ;

		$("#chat_input").css({'bottom': "auto"}) ; $("textarea#input_text").css({'height': 75}) ;
		if ( theresize )
		{
			clearTimeout( st_resize ) ;
			st_resize = setTimeout( function(){ close_extra( extra ) ; }, 800 ) ;
		}
		else
			close_extra( extra ) ;
	}
	else
	{
		// only applies to op_trans_view.php
		if ( typeof( view ) != "undefined" )
		{
			if ( parseInt( view ) == 1 )
				chat_body_height -= 90 ; // lift it up so more stats show
			else
				chat_body_height += 50 ;
		}

		if ( mobile )
			chat_btn_top = browser_height - 85 ;
		else
			chat_btn_top = browser_height - 115 ;

		//chat_body_height -= 25 ; // visitor chat header height
		if ( ( typeof( socials ) != "undefined" ) && socials ) { chat_body_height -= 20 ; chat_btn_top -= 20 ;  }
		$('#chat_body').css({'height': chat_body_height, 'width': chat_body_width}) ;
	}

	if ( isop )
	{
		if ( mapp )
		{
			if ( profile_pic_enabled ) { input_text_width = browser_width - $('#div_profile_pic').width() - $('#chat_btn').width() - 50 ; }
			else { input_text_width = browser_width - $('#chat_btn').width() - 50 ; }
			$('#input_text').css({'width': input_text_width}) ;
		}
		else
		{
			if ( profile_pic_enabled )
			{
				input_text_width -= 65 ;
				$('#input_text').css({'width': input_text_width}) ;
			}
			else { $('#input_text').css({'width': input_text_width}) ; }
		}
	}
	else { $('#input_text').css({'width': input_text_width}) ; }

	if ( mapp )
	{
		if ( profile_pic_enabled ) { chat_btn_left = input_text_width + $('#div_profile_pic').width() + 25 ; }
		else { chat_btn_left = input_text_width + 25 ; }
		chat_btn_top -= 25 ;
		$('#chat_btn').css({'top': chat_btn_top, 'left': chat_btn_left}) ;
	} else { $('#chat_btn').css({'top': chat_btn_top, 'left': chat_btn_left}) ; }
}

function update_ces( thejson_data )
{
	var thisces = thejson_data["ces"] ;
	var orig_text = thejson_data["text"] ;
	var append_text = init_timestamps( thejson_data["text"] ) ;

	if ( ( typeof( chats[thisces] ) != "undefined" ) && orig_text )
	{
		chats[thisces]["chatting"] = 1 ;
		chats[thisces]["trans"] += append_text ;

		// parse for flags before doing functions
		if ( ( append_text.indexOf("</top>") != -1 ) && !parseInt( isop ) )
		{
			var regex_trans = /<top>(.*?)</ ;
			var regex_trans_match = regex_trans.exec( append_text ) ;
			
			chats[ces]["oname"] = regex_trans_match[1] ;
			$('#chat_vname').empty().html( regex_trans_match[1] ) ;

			var regex_opid = /<!--opid:(.*?)-->/ ;
			var regex_opid_match = regex_opid.exec( append_text ) ;
			isop_ = regex_opid_match[1] ;

			var regex_mapp = /<!--mapp:(.*?)-->/ ;
			var regex_mapp_match = regex_mapp.exec( append_text ) ;
			chats[ces]["mapp"] = regex_mapp_match[1] ;
		}

		if ( ( thejson_data["text"].indexOf( "<disconnected>" ) != -1 ) && !chats[thisces]["disconnected"] )
		{
			chats[thisces]["disconnected"] = unixtime() ;
			if ( thisces == ces ) { $('#idle_timer_notice').hide() ; }
			if ( typeof( chats[thisces]["idle_si"] ) != "undefined" ) { clearInterval( chats[thisces]["idle_si"] ) ; chats[thisces]["idle_si"] = undeefined ; }
			if ( isop )
			{
				var btn_close_chat = "<div style='margin-top: 5px;'><button onClick='cleanup_disconnect(ces)' style=''>close chat</button></div>" ;
				append_text += btn_close_chat ; chats[thisces]["trans"] += btn_close_chat ;
				clearInterval( chats[thisces]["timer_si"] ) ; chats[thisces]["timer_si"] = undeefined ;
			} else { document.getElementById('iframe_chat_engine').contentWindow.stopit(0) ; }
		}
		if ( ( thejson_data["text"].indexOf( "<restart_router>" ) != -1 ) && !isop )
		{
			chats[thisces]["status"] = 2 ;
			document.getElementById('iframe_chat_engine').contentWindow.routing() ;
		}
		if ( ( thejson_data["text"].indexOf( "<idle_start>" ) != -1 ) && parseInt( isop ) ) { init_idle( thisces ) ; }
		if ( ( thejson_data["text"].indexOf( "<idle_pause>" ) != -1 ) && !parseInt( isop ) && !parseInt( widget ) ) { chats[thisces]["idle_counter_pause"] = 1 ; }
		if ( ( thejson_data["text"].indexOf( "<idle_restart>" ) != -1 ) && !parseInt( isop ) && !parseInt( widget ) ) { chats[thisces]["idle_counter_pause"] = 0 ; }

		if ( ces == thisces )
		{
			$('#chat_body').append( append_text.emos() ) ; if ( isop && mapp ) { init_external_url() ; }
			init_scrolling() ;
			init_textarea() ;
			$('#chat_vistyping').hide() ;

			if ( document.getElementById('iframe_chat_engine').contentWindow.stopped )
			{
				if ( typeof( parent.chat_disconnected ) != "undefined" )
					parent.chat_disconnected = 1 ;
				if ( thisces == ces ) { $('#idle_timer_notice').hide() ; }
				chat_survey() ;
			}
		}

		var flash_console_on = 0 ;
		if ( isop )
		{
			chats[thisces]["recent_res"] = unixtime() ;
			if ( ces != thisces )
			{
				menu_blink( "green", thisces ) ;
			}
			else
			{
				toggle_last_response(1) ;
			}

			var reg = RegExp( chats[thisces]["vname"]+": ", "g" ) ;
			if ( ( typeof( dn_enabled_response ) != "undefined" ) && dn_enabled_response && chats[thisces]["status"] )
			{
				if ( wp )
					window.external.wp_incoming_chat( thisces, "Response: " + chats[thisces]["vname"], orig_text.replace( /<(.*?)>/g, '' ).replace( reg, ' ' ).replace( /\s+/g, ' ' ) ) ;
				else
					dn_show( 'new_response', thisces, "Response: " + chats[thisces]["vname"], orig_text.replace( /<(.*?)>/g, '' ).replace( reg, ' ' ).replace( /\s+/g, ' ' ), 45000 ) ;
			}
		}
		if ( console_blink_r ) { flash_console_on = 1 ; }

		if ( chats[thisces]["status"] || chats[thisces]["initiated"] )
		{
			if ( chat_sound )
			{
				play_sound( 0, "new_text", "new_text_"+sound_new_text ) ;
			}
			if ( !isop && embed )
			{
				if ( ( typeof( parent.win_minimized ) != "undefined" ) && parent.win_minimized ) { flash_console_on = 1 ; }
			}
			title_blink_init() ;
		}
		
		if ( flash_console_on ) { flash_console(0) ; }
	}
	if ( isop && !mapp ) { init_maxc() ; }
}

function disconnect( theunload, theclick, theces, thevclick )
{
	if ( typeof( widget ) == "undefined" ) { widget = 0 ; }
	if ( typeof( theces ) == "undefined" ) { theces = ces ; }
	if ( typeof( thevclick ) == "undefined" ) { thevclick = 0 ; } vclick = thevclick ;
	if ( theclick )
	{
		document.getElementById('info_disconnect')._onclick = document.getElementById('info_disconnect').onclick ;
		$('#info_disconnect').prop( "onclick", null ).html('<img src="'+base_url+'/pics/loading_fb.gif" width="16" height="11" border="0" alt="">') ;
		if ( mapp ) { $('#info_disconnect_mapp').prop( "onclick", null ).html('<img src="'+base_url+'/pics/loading_fb.gif" width="16" height="11" border="0" alt="">') ; }
	}

	if ( ( theces == ces ) && isop ) { $('#idle_timer_notice').hide() ; }
	else if ( theces == ces ) { $('#chat_vistyping').hide() ; }
	if ( ( ( typeof( theces ) != "undefined" ) && ( typeof( chats[theces] ) != "undefined" ) ) || widget )
	{
		var json_data = new Object ;
		var unique = unixtime() ;

		// limit multiple clicks during internet lag
		if ( !chats[theces]["disconnect_click"] )
		{
			chats[theces]["disconnect_click"] = theclick ;

			$.ajax({
			type: "POST",
			url: base_url+"/ajax/chat_actions_disconnect.php",
			data: "action=disconnect&isop="+isop+"&isop_="+isop_+"&isop__="+isop__+"&ces="+theces+"&vis_token="+chats[ces]["vis_token"]+"&ip="+chats[theces]["ip"]+"&widget="+widget+"&t_vses="+chats[theces]["t_ses"]+"&idle="+chats[theces]["idle_counter"]+"&vclick="+thevclick+"&unload="+theunload+"&unique="+unique+"&",
			success: function(data){
				try {
					eval(data) ;
				} catch(err) {
					do_alert( 0, "Error processing disconnect.  Please reload the page and try again." ) ;
					return false ;
				}

				if ( theclick )
				{
					document.getElementById('info_disconnect').onclick = document.getElementById('info_disconnect')._onclick ;
					if ( mapp ) { document.getElementById('info_disconnect_mapp').onclick = document.getElementById('info_disconnect')._onclick ; }
				}
				if ( json_data.status )
				{
					if ( parseInt( isop ) && ( parseInt( chats[theces]["idle_counter"] ) == -1 ) && !theclick )
					{
						// automatic process the idle disconnect but don't close the chat unless clicked disconnect
						chats[theces]["disconnect_click"] = 0 ;
						chats[theces]["disconnected"] = unixtime() ;
						if ( !$('textarea#input_text').is(':disabled') ) { $('textarea#input_text').val( "" ).attr("disabled", true) ; }
					}
					else
						cleanup_disconnect( json_data.ces ) ;

					if ( isop && !mapp ) { init_maxc() ; }
				}
				else { do_alert( 0, "Error processing disconnect.  Please reload the page and try again." ) ; }
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error processing disconnect.  Please reload the page and try again." ) ;
			} });
		}
	}
}

function init_disconnect()
{
	$('#info_disconnect').hover(
		function () {
			$(this).removeClass('info_disconnect').addClass('info_disconnect_hover') ;
		}, 
		function () {
			$(this).removeClass('info_disconnect_hover').addClass('info_disconnect') ;
		}
	);
}

function init_timer()
{
	if ( typeof( chats[ces] ) != "undefined" )
	{
		start_timer( chats[ces]["timer"] ) ;
		if ( ( ( parseInt( chats[ces]["status"] ) == 1 ) && !parseInt( chats[ces]["disconnected"] ) ) || ( parseInt( chats[ces]["initiated"] ) && !parseInt( chats[ces]["disconnected"] ) ) )
		{
			if ( typeof( chats[ces]["timer_si"] ) != "undefined" ) { clearInterval( chats[ces]["timer_si"] ) ; chats[ces]["timer_si"] = undeefined ; }
			chats[ces]["timer_si"] = setInterval(function(){ if ( typeof( chats[ces] ) != "undefined" ) { start_timer( chats[ces]["timer"] ) ; } }, 1000) ;
		}
	}
}

function start_timer( thetimer )
{
	var diff ;
	if ( chats[ces]["disconnected"] )
		diff = chats[ces]["disconnected"] - thetimer ;
	else
		diff = unixtime() - thetimer ;

	var hours = Math.floor( diff/3600 ) ;
	var mins =  Math.floor( ( diff - ( hours * 3600 ) )/60 ) ;
	var secs = diff - ( hours * 3600 ) - ( mins * 60 ) ;

	var display = pad( mins, 2 )+":"+pad( secs, 2 ) ;
	if ( hours ) { display = pad( hours, 2 )+":"+display ; }

	if ( chats[ces]["status"] || chats[ces]["initiated"] )
		$('#chat_vtimer').val( ""+display+"" ) ;
	else
		$('#chat_vtimer').val("00:00") ;
}

function init_marquees()
{
	start_marquees() ;
	setInterval( "start_marquees()", 10000 ) ;
}

function start_marquees()
{
	if ( typeof( marquees_messages[marquee_index] ) != "undefined" )
	{
		$('#chat_footer').empty().html( "<div class=\"marquee\">"+parse_marquee(marquees_messages[marquee_index])+"</div>" ) ;
		++marquee_index ;
		if ( marquee_index >= marquees.length )
			marquee_index = 0 ;
	}
}

function chat_survey()
{
	if ( !chats[ces]["survey"] )
	{
		chats[ces]["survey"] = 1 ;

		var survey_text = ( chats[ces]["rate"] ) ? survey_rate + survey : survey ;
		add_text( ces, survey_text ) ;
		if ( ( typeof( phplive_mobile ) != "undefined" ) && phplive_mobile && $('#chat_profile_pic').is(':visible') )
		{
			var chat_body_height = $('#chat_body').height() + 75 ;
			profile_pic_enabled = 0 ;

			$('#chat_profile_pic').hide() ;
			$('#chat_body').css({'height': chat_body_height}) ;
		}
	}
	window.onbeforeunload = null ;

	if ( !widget ) { $('#info_disconnect').hide() ; }
}

function submit_survey( theobject, thetexts )
{
	var json_data = new Object ;
	var unique = unixtime() ;

	if ( parseInt( chats[ces]["survey"] ) != 2 )
	{
		$.ajax({
		type: "POST",
		url: base_url+"/ajax/chat_actions_rating.php",
		data: "action=rating&requestid="+chats[ces]["requestid"]+"&ces="+ces+"&opid="+chats[ces]["opid"]+"&deptid="+chats[ces]["deptid"]+"&rating="+theobject.value+"&unique="+unique+"&",
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				do_alert( 0, err ) ;
				return false ;
			}

			if ( json_data.status )
			{
				chats[ces]["survey"] = 2 ;
				do_alert( 1, thetexts[0] ) ;

				$("input[name='rating']").each(function(i) {
					$(this).attr('disabled', true) ;
				});
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			// suppress error to limit confusion... if error here, there will be error reporting in more crucial areas
		} });
	}
}

function do_print( theces, thedeptid, theopid, thewidth, theheight )
{
	var winname = "Print"+theces ;
	var deptid = ( typeof( chats[theces]["deptid"] ) != "undefined" ) ? parseInt( chats[theces]["deptid"] ) : parseInt( thedeptid ) ;
	var opid = ( typeof( chats[theces]["opid"] ) != "undefined" ) ? parseInt( chats[theces]["opid"] ) : parseInt( theopid ) ;

	var url = base_url_full+"/ops/op_print.php?ces="+theces+"&deptid="+deptid+"&opid="+theopid+"&"+unixtime()+"&" ;

	if ( !wp )
		newwin_print = window.open( url, winname, "scrollbars=yes,menubar=no,resizable=1,location=no,width="+thewidth+",height="+theheight+",status=0" ) ;
	else
	{
		if ( typeof( isop ) != "undefined" ) { wp_new_win( url, winname, thewidth, theheight ) ; }
		else { location.href = url ; }
	}
}

function init_timestamps( thetranscript )
{
	var lines = thetranscript.split( "<>" ) ;

	var transcript = "" ;
	for ( var c = 0; c < lines.length; ++c )
	{
		var line = lines[c] ;
		var matches = line.match( /timestamp_(\d+)_/ ) ;
		
		var timestamp = "" ;
		if ( matches != null )
		{
			var time = extract_time( matches[1] ) ;
			timestamp = " (<span class='ct'>"+time+"</span>) " ;
			transcript += ( !widget ) ? line.replace( /<timestamp_(\d+)_((co)|(cv))>/, timestamp ) : line.replace( /<timestamp_(\d+)_((co)|(cv))>/, '' ) ;
		}
		else { transcript += line ; }
	}
	return transcript ;
}

function extract_time( theunixtime )
{
	var time_expanded = new Date( parseInt( theunixtime ) * 1000) ;
	var hours = time_expanded.getHours() ;
	if( hours >= 13 ) hours -= 12 ;
	var output = pad(hours,2)+":"+pad(time_expanded.getMinutes(), 2)+":"+pad(time_expanded.getSeconds(), 2) ;
	return output ;
}

function input_focus() { if ( !focused ) { focused = 1 ; } }

function play_sound( theloop, thediv, thesound )
{
	var unique = unixtime() ;

	var div_content = $('#div_sounds_'+thediv).html() ;
	if ( mp3_support )
	{
		var audio_obj = $("#div_sounds_audio_"+thediv) ;
		if ( ( ( thediv == "new_request" ) && !div_content && audio_obj[0].paused ) || ( thediv != "new_request" ) )
		{
			$('#div_sounds_'+thediv).html( "on" ) ;
			$("#div_sounds_audio_"+thediv).attr("src", base_url+"/media/"+thesound+'.mp3') ;
			if ( theloop && !mobile ) { audio_obj[0].loop = true ; } // mobile device play just once
			audio_obj[0].volume = sound_volume ;
			audio_obj[0].play() ;
		}
	}
	else
	{
		if ( ( ( thediv == "new_request" ) && !div_content ) || ( thediv != "new_request" ) )
			flashembed( "div_sounds_"+thediv, base_url+'/media/'+thesound+'.swf' ) ;
	}
}

function clear_sound( thediv )
{
	if ( mp3_support )
	{
		var audio_obj = $("#div_sounds_audio_"+thediv) ;
		audio_obj[0].pause() ;
	}
	$('#div_sounds_'+thediv).html("") ;
}

function title_blink_init()
{
	if ( mapp ) { return true ; }
	if ( ( typeof( title_orig ) != "undefined" ) && !parseInt( focused ) )
	{
		if ( typeof( si_title ) != "undefined" )
			clearInterval( si_title ) ;

		if ( ( typeof( embed ) != "undefined" ) && parseInt( embed ) ) {  }
		else { si_title = setInterval(function(){ title_blink( 1, title_orig, "Alert __________________ " ) ; }, 800) ; }
	}
}

function title_blink( theflag, theorig, thenew )
{
	if ( mapp ) { return true ; }
	if( !parseInt( focused ) && ( thenew != "reset" ) )
	{
		if ( ( si_counter % 2 ) && theflag ) { document.title = thenew ; }
		else { document.title = theorig ; }

		++si_counter ;
	}
	else
	{
		if ( typeof( si_title ) != "undefined" )
		{
			clearInterval( si_title ) ; si_title = undeefined ;
			document.title = theorig ;
		}
	}
}

function print_chat_sound_image( thetheme )
{
	if ( chat_sound )
		$('#chat_sound').attr('src', base_url+'/themes/'+thetheme+'/sound_on.png') ;
	else
		$('#chat_sound').attr('src', base_url+'/themes/'+thetheme+'/sound_off.png') ;
}

function flash_console( thecounter )
{
	if ( ( typeof( mapp ) != "undefined" ) && mapp ) { return true ; }
	++thecounter ;
	if ( ( thecounter % 2 ) )
		$('#chat_canvas').addClass('chat_canvas_alert') ;
	else
		$('#chat_canvas').removeClass('chat_canvas_alert') ;

	if ( typeof( st_flash_console ) != "undefined" )
		clearTimeout( st_flash_console ) ;
	st_flash_console = setTimeout( function(){ flash_console( thecounter ) ; }, 1000 ) ;
}

function clear_flash_console()
{
	if ( ( typeof( mapp ) != "undefined" ) && mapp ) { return true ; }
	$('#chat_canvas').removeClass('chat_canvas_alert') ;
	if ( typeof( st_flash_console ) != "undefined" )
	{
		clearTimeout( st_flash_console ) ;
		st_flash_console = undeefined ;
	}
	if ( typeof( title_orig ) != "undefined" ) { title_blink( 0, title_orig, "reset" ) ; }
}

function close_misc()
{
	if ( isop )
	{
		clear_flash_console() ;
		toggle_last_response(1) ;
		//clear_sound( "new_request" ) ;
	}
	if ( typeof toggle_emo_box == 'function' ) { toggle_emo_box(1) ; }
}

function textarea_listen()
{
	if ( typeof( si_textarea ) != "undefined" ) { clearInterval( si_textarea ) ; si_textarea == undeefined ; }
	si_textarea = setInterval(function(){
		var temp = $('textarea#input_text').val() ;
		temp = temp.replace( / /g, "" ) ;
		if ( temp ) { $('button#input_btn').attr( "disabled", false ) ; }
		else { $('button#input_btn').attr( "disabled", true ) ; }
	}, 200) ;
}

function webkit_version()
{
	// for browser webkit, not significant
	// but for iOS mapp webkit:
	// * <= 537.36 (iOS 9.2.1) var device_height = $(window).height() ;
	var result = /AppleWebKit\/([\d.]+)/.exec(navigator.userAgent) ;
	if ( result ) { return parseFloat(result[1]) ; }
	return 0 ;
}
