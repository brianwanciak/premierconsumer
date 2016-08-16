// WinApp Integration
function wp_decline_chat() { chat_decline() ; }

function wp_total_visitors( thecounter )
{
	// put # in taskbar
	// * = logo icon
	if ( ( typeof( window.external ) != "undefined" ) && ( typeof( window.external.wp_total_visitors ) != "undefined" ) )
		window.external.wp_total_visitors( thecounter ) ;
}

function wp_focus_chat() { wp_popup() ; }
function wp_minimize() { window.external.wp_minimize() ; }
function wp_maximize() { window.external.wp_maximize() ; }
function wp_popup() { window.external.window.external.wp_popup() ; }
function wp_hide_tray( theces ) { window.external.wp_hide_tray( theces ) ; }
function wp_new_win( theurl, thetitle, thew, theh)
{
	window.external.wp_new_win( theurl, thetitle, thew, theh ) ;
	// window.open( theurl, thetitle, "scrollbars=yes,menubar=no,resizable=1,location=no,width="+thew+",height="+theh+",status=0" ) ;
}

function wp_pre_go_offline()
{
	// do pre logout, call functions to go offline
	toggle_status(4) ;
}

function wp_go_offline()
{
	// confirm go offline and logout
	wp_pre_go_offline() ;
	if ( ( typeof( window.external ) != "undefined" ) && ( typeof( window.external.wp_go_offline ) != "undefined" ) )
		window.external.wp_go_offline() ;
}
