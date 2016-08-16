/********************************************
* Mapp functions
********************************************/

function init_mapp_set_arn( theplatform, thearn, themapp )
{
	$('#platform').val(theplatform) ;
	$('#arn').val(thearn) ;
	$('#auto').val(1) ;
	$('#mapp').val(themapp) ;
	if ( mapp_login ) { setTimeout( function(){ $('#theform').submit() ; }, 500 ) ; } ;
}

var chat_sound_mapp ;
function init_mapp_pause()
{
	if ( typeof( isop ) == "undefined" ) { return false ; }
	var json_data = new Object ;
	var unique = unixtime() ;

	if ( chat_sound ) { chat_sound = 0 ; chat_sound_mapp = 1 ; } // Android device fix so it doesn't play twice in background
	var confirm = ( typeof( mapp_c ) != "undefined" ) ? mapp_c : 0 ;

	$.ajax({
	type: "POST",
	url: base_url+"/mapp/ajax/mapp_actions.php",
	data: "action=pause&opid="+isop+"&confirm="+confirm+"&unique="+unique+"&",
	success: function(data){
		try {
			eval(data) ;
		} catch(err) {
			// suppress for now until error reporting
			// should never reach here unless a script error
		}

		if ( json_data.status )
		{
			//
		}
	},
	error:function (xhr, ajaxOptions, thrownError){
		// sometimes devices throw this error when shutdown
	} });
}

var si_mapp_resume ;
function init_mapp_resume()
{
	if ( typeof( isop ) == "undefined" ) { return false ; }
	var json_data = new Object ;
	var unique = unixtime() ;

	if ( chat_sound_mapp )
	{
		chat_sound = 1 ;
	}

	$.ajax({
	type: "POST",
	url: base_url+"/mapp/ajax/mapp_actions.php",
	data: "action=resume&opid="+isop+"&unique="+unique+"&",
	success: function(data){
		if ( typeof( si_mapp_resume ) != "undefined" ) { clearInterval( si_mapp_resume ) ; si_mapp_resume = undeefined ; }
		try {
			eval(data) ;
		} catch(err) {
			// suppress for now until error reporting
			// should never reach here unless a script error
		}

		if ( json_data.status )
		{
			//
		}
	},
	error:function (xhr, ajaxOptions, thrownError){
		// keep trying so it removes the mapp file
		if ( typeof( si_mapp_resume ) != "undefined" ) { clearInterval( si_mapp_resume ) ; }
		si_mapp_resume = setInterval(function(){
			init_mapp_resume() ;
		}, 5000) ;
	} });
}

function init_mapp_console()
{
	toggle_slider(0) ;
	$('#options_settings').hide() ;
	$('#options_sound').hide() ;
	$('#icons_slider').hide() ;
	$('#chat_printer').hide() ;
	$('#chat_panel').hide() ;
	$('#chat_footer').hide() ;
	$('#chat_switchboard').css({'top': -1000}) ; // needs to be visible for clone
	$('#chat_vname').hide() ;
	$('#chat_vistyping').hide() ;
	$('#info_disconnect').hide() ;
	$('#chat_vtimer').css({'top': 0}) ;
	$('#chat_footer_mapp').show() ;
	$('#chat_info_header').hide() ;
	$('#chat_info_menu_list').hide() ;
}

function reset_mapp_div_height()
{
	var document_height = $(document).height() - 65 ;
	$('#canned_container').css({'height': document_height}).show() ;
}

function populate_mapp_chats( theses )
{
	init_iframe( 'iframe_mapp_chats' ) ;
	$('#chat_extra_body_mapp_chats').show() ;

	var switchboard_top = 120 ;

	$('#chat_switchboard').css({'top': switchboard_top}) ;
	setTimeout( function(){
		document.getElementById('iframe_mapp_chats').contentWindow.reset_mapp_div_height() ;
		document.getElementById('iframe_mapp_chats').contentWindow.display_chats() ;
	}, 100 ) ;
}

function populate_mapp_traffic( theses )
{
	$('#iframe_mapp_traffic').attr('src', base_url+"/mapp/mapp_traffic.php?ses="+theses+"&"+unixtime()+"&" ).ready(function() {
		init_iframe( 'iframe_mapp_traffic' ) ;
	});
	$('#chat_extra_body_mapp_traffic').show() ;
}

function populate_mapp_themes( theses )
{
	$('#iframe_mapp_themes').attr('src', base_url+"/mapp/mapp_themes.php?ses="+theses+"&"+unixtime()+"&" ).ready(function() {
		init_iframe( 'iframe_mapp_themes' ) ;
	});
	$('#chat_extra_body_mapp_themes').show() ;
}

function populate_mapp_prefs( theses )
{
	$('#iframe_mapp_prefs').attr('src', base_url+"/mapp/mapp_prefs.php?ses="+theses+"&"+unixtime()+"&" ).ready(function() {
		init_iframe( 'iframe_mapp_prefs' ) ;
	});
	$('#chat_extra_body_mapp_prefs').show() ;
}

function populate_mapp_sounds( theses )
{
	$('#iframe_mapp_sounds').attr('src', base_url+"/mapp/mapp_sounds.php?ses="+theses+"&"+unixtime()+"&" ).ready(function() {
		init_iframe( 'iframe_mapp_sounds' ) ;
	});
	$('#chat_extra_body_mapp_sounds').show() ;
}

function populate_mapp_operators( theses )
{
	$('#iframe_mapp_operators').attr('src', base_url+"/mapp/mapp_operators.php?ses="+theses+"&"+unixtime()+"&" ).ready(function() {
		init_iframe( 'iframe_mapp_operators' ) ;
	});
	$('#chat_extra_body_mapp_operators').show() ;
}

function populate_mapp_trans( theses )
{
	init_iframe( 'iframe_mapp_trans' ) ;
	$('#chat_extra_body_mapp_trans').show() ;
	setTimeout( function(){
		document.getElementById('iframe_mapp_trans').contentWindow.reset_mapp_div_height() ;
	}, 100 ) ;
}

function init_reload_mapp_traffic( theses )
{
	$('#iframe_mapp_traffic').attr('src', base_url+"/mapp/mapp_traffic.php?ses="+theses+"&action=reload&"+unixtime()+"&" ).ready(function() {
		//
	});
}


function init_reload_mapp_trans( theses )
{
	$('#iframe_mapp_trans').attr('src', base_url+"/mapp/mapp_trans.php?ses="+theses+"&action=reload&"+unixtime()+"&" ).ready(function() {
		//
	});
}

function init_reload_mapp_cans( theses )
{
	$('#iframe_mapp_cans').attr('src', base_url+"/mapp/mapp_canned.php?ses="+theses+"&action=reload&"+unixtime()+"&" ).ready(function() {
		//
	});
}

function populate_mapp_power( theses )
{
	$('#iframe_mapp_power').attr('src', base_url+"/mapp/mapp_power.php?ses="+theses+"&"+unixtime()+"&" ).ready(function() {
		init_iframe( 'iframe_mapp_power' ) ;
	});
	$('#chat_extra_body_mapp_power').show() ;
}

function populate_mapp_vinfo( theces )
{
	init_iframe( 'iframe_mapp_vinfo' ) ;
	$('#chat_extra_body_mapp_vinfo').show() ;
	setTimeout( function(){
		document.getElementById('iframe_mapp_vinfo').contentWindow.reset_mapp_div_height() ;
		document.getElementById('iframe_mapp_vinfo').contentWindow.populate_vinfo(theces) ;
	}, 100 ) ;
}

function populate_mapp_ops()
{
	document.getElementById('iframe_mapp_vinfo').contentWindow.populate_ops() ;
}

function populate_mapp_trans_vinfo()
{
	document.getElementById('iframe_mapp_vinfo').contentWindow.populate_trans() ;
}

function populate_mapp_cans( theses )
{
	init_iframe( 'iframe_mapp_cans' ) ;
	$('#chat_extra_body_mapp_cans').show() ;
	setTimeout( function(){
		document.getElementById('iframe_mapp_cans').contentWindow.reset_mapp_div_height() ;
	}, 100 ) ;
}

function toggle_mapp_menu_prefs( theforce )
{
	toggle_last_response(1) ;
	if ( $('#div_menu_prefs').is(':visible') || theforce )
	{
		$('#div_menu_prefs').hide() ;
		toggle_mapp_icon( "mapp_prefs", 0 ) ;
	}
	else
	{
		close_extra( extra ) ;
		$('#div_menu_prefs').show() ;
		toggle_mapp_icon( "mapp_prefs", 1 ) ;
	}
}

function toggle_mapp_icon( thediv, theflag )
{
	if ( theflag )
	{
		if ( thediv == "mapp_chats" ) { $('#mapp_icon_chats').css({'border': '1px solid #FFFFFF'}).attr( "src", "../mapp/pics/menu_chats_focus.png?"+cache_v ) ; }
		else if ( thediv == "mapp_cans" ) { $('#mapp_icon_cans').css({'border': '1px solid #FFFFFF'}).attr( "src", "../mapp/pics/menu_cans_focus.png?"+cache_v ) ; }
		else if ( thediv == "mapp_power" ) { $('#mapp_icon_power').css({'border': '1px solid #FFFFFF'}).attr( "src", "../mapp/pics/menu_power_focus.png?"+cache_v ) ; }
		else if ( thediv == "mapp_prefs" ) { $('#mapp_icon_prefs').css({'border': '1px solid #FFFFFF'}).attr( "src", "../mapp/pics/menu_prefs_focus.png?"+cache_v ) ; }
		else if ( thediv == "mapp_traffic" ) { $('#mapp_icon_traffic').css({'border': '1px solid #FFFFFF'}) ; }
	}
	else
	{
		if ( thediv == "mapp_chats" ) { $('#mapp_icon_chats').css({'border': '1px solid #939B9F'}).attr( "src", "../mapp/pics/menu_chats.png?"+cache_v ) ; }
		else if ( thediv == "mapp_cans" ) { $('#mapp_icon_cans').css({'border': '1px solid #939B9F'}).attr( "src", "../mapp/pics/menu_cans.png?"+cache_v ) ; }
		else if ( thediv == "mapp_power" ) { $('#mapp_icon_power').css({'border': '1px solid #939B9F'}).attr( "src", "../mapp/pics/menu_power.png?"+cache_v ) ; }
		else if ( thediv == "mapp_prefs" ) { $('#mapp_icon_prefs').css({'border': '1px solid #939B9F'}).attr( "src", "../mapp/pics/menu_prefs.png?"+cache_v ) ; }
		else if ( thediv == "mapp_traffic" ) { $('#mapp_icon_traffic').css({'border': '1px solid #939B9F'}) ; }
	}
}

function update_mapp_network( theflag )
{
	if ( typeof( document.getElementById('iframe_mapp_power').contentWindow.update_network_img ) != "undefined" )
	{
		document.getElementById('iframe_mapp_power').contentWindow.update_network_img( theflag ) ;
	}
}

function update_mapp_network_log( thecounter, thestring )
{
	if ( typeof( document.getElementById('iframe_mapp_power').contentWindow.update_network_log ) != "undefined" )
	{
		document.getElementById('iframe_mapp_power').contentWindow.update_network_log( thecounter, thestring ) ;
	}
}

function reconnect_mapp()
{
	document.getElementById('iframe_chat_engine').contentWindow.stopped = 0 ;
	$('#reconnect_status').empty().html( "Operator console disconnected.  Reconnecting... <img src=\"../pics/loading_fb.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\">" ) ;
	clear_sound( "new_request" ) ;
	reconnect_counter = 0 ;
	reconnect() ;
}
