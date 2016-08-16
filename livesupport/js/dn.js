/* (c) OSI Codes Inc. */
/* http://www.osicodesinc.com */
/****************************************/
// desktop notification (dn)

var dn_si ;
var dn_his = new Object ;
function dn_pre_request()
{
	dn_request() ;

	if ( typeof( dn_si ) != "undefined" ) ;
		clearInterval( dn_si ) ;

	dn_si = setInterval(function(){ dn_check_si() }, 300) ;
}

function dn_check_browser()
{
	if ( navigator.userAgent.toLowerCase().indexOf('chrome') > -1 )
		return "chrome" ;
	else if ( navigator.userAgent.toLowerCase().indexOf('firefox') > -1 )
		return "firefox" ;
	else
		return "null" ;
}

function dn_check_si()
{
	var dn = dn_check() ;
	if ( parseInt( dn ) == 2 )
	{
		if ( typeof( dn_si ) != "undefined" )
			clearInterval( dn_si ) ;

		$('#dn_request').hide() ;
		$('#dn_disabled').show() ;
	}
	else if ( !parseInt( dn ) && ( parseInt( dn ) != -1 ) )
	{
		if ( typeof( dn_si ) != "undefined" )
			clearInterval( dn_si ) ;

		$('#dn_request').hide() ;
		$('#dn_enabled').show() ;
		if ( dn_enabled )
			$('#dn_enabled_on').show() ;
		else
			$('#dn_enabled_off').show() ;
	}
}

function dn_request()
{
	var dn = dn_check() ;
	if ( parseInt( dn ) == 2 )
	{
		$('#dn_request').hide() ;
		$('#dn_disabled').show() ;
	}
	else if ( !parseInt( dn ) && ( parseInt( dn ) != -1 ) )
		do_alert( 1, "Desktop notification already enabled." ) ;
	else if ( ( "Notification" in window ) )
	{
		Notification.requestPermission(function (permission) {
			if( !( 'permission' in Notification ) ) {
				Notification.permission = permission ;
			}
		});
	}
}

function dn_check()
{
	// -1 - not supported, 0 - allowed, 1 - not allowed, 2 - denied (took action)
	if ( "Notification" in window )
	{
		var permission ;
		if ( typeof( Notification.permission ) != "undefined" )
			permission = Notification.permission ;
		else { permission = "denied" ; }

		if ( permission == "default" ) { return 1 ; }
		else if ( permission === "granted" ) { return 0 ; }
		else if ( permission == "denied" ) { return 2 ; }
		else { alert( "Notification Error: "+permission ) ; } // report unknown error
	}
	else
		return -1 ;
}

function dn_show( theflag, theces, thename, thequestion, theduration )
{
	var dn = dn_check() ;
	if ( !parseInt( dn ) && ( parseInt( dn ) != -1 ) )
	{
		if ( dn_always || ( !dn_always && !focused ) )
		dn_show_doit( theflag, theces, thename, thequestion, theduration ) ;
	}
}

function dn_show_doit( theflag, theces, thename, thequestion, theduration )
{
	var iconurl = "../pics/icons/dn_notify.png" ;
	if ( theflag == "new_response" ) { iconurl = "../pics/icons/dn_notify.png" ; }

	++dn_counter ;
	var dn_counter_temp = dn_counter ;
	if ( typeof( dn_his[theces] ) == "undefined" ) { dn_his[theces] = new Object ; }
	if ( typeof( dn_his[theces][dn_counter_temp] ) == "undefined" )
	{
		dn_his[theces][dn_counter_temp] = new Object ;
		dn_his[theces][dn_counter_temp]["dn"] = new Notification( 
			thename, { 
				icon: iconurl, 
				body: thequestion 
			}
		) ;
		dn_his[theces][dn_counter_temp]["dn"].onclick = function(){
			window.focus() ;
			$('#input_text').focus() ;
			dn_close( theces, dn_counter_temp ) ;
		} ;
	}

	//dn_his[theces][dn_counter_temp].onshow = function() { }
	dn_his[theces][dn_counter_temp]["dn"].onshow = function()
	{ 
		try{
			dn_his[theces][dn_counter_temp]["st"] = setTimeout( function() { dn_close( theces, dn_counter_temp ) ; }, theduration ) ;
		} catch(e){
			//
		}
	}
}

function dn_close( theces, thisdn_counter )
{
	if ( typeof( theces ) == "undefined" )
	{
		for ( var thisces in dn_his )
			dn_close( thisces ) ;
	}
	else if ( typeof( dn_his[theces] ) != "undefined" )
	{
		if ( typeof( dn_his[theces][thisdn_counter] ) != "undefined" )
		{
			dn_his[theces][thisdn_counter]["dn"].close() ;
			clearTimeout( dn_his[theces][thisdn_counter]["st"] ) ;
			dn_his[theces][thisdn_counter] = undeefined ;
		}
		else
		{
			for ( var thisdn_counter in dn_his[theces] )
			{
				if ( ( typeof( dn_his[theces][thisdn_counter] ) != "undefined" ) && ( typeof( dn_his[theces][thisdn_counter]["dn"] ) != "undefined" ) )
				{
					dn_his[theces][thisdn_counter]["dn"].close() ;
					clearTimeout( dn_his[theces][thisdn_counter]["st"] ) ;
				}
			}
			dn_his[theces] = undeefined ;
		}
	}
}
