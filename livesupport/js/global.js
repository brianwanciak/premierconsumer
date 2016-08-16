function nospecials(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789_-\.\(\) ").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function logins(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789_-\.").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function justemails(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789_-\@\.").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function numbersonly(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("0123456789.").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function noquotes(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("\"\'").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function noquotestags(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("\"\'<>").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function notags(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("<>").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function check_email( theemail )
{
	var pattern = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-_]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/ ;
	return pattern.test( theemail ) ;
}

function do_alert( theflag, thetext, theduration )
{
	var message ;
	var delay_vis = ( theflag ) ? 3000 : 4000 ;
	if ( typeof( theduration ) != "undefined" ) { delay_vis = parseInt( theduration ) * 1000 ; }

	var div_exists = $('#login_alert_box').length ;
	if ( div_exists )
		$('#login_alert_box').remove() ;

	if ( theflag )
		message = "<div id=\"login_alert_box\" class=\"info_good\" style=\"display: none; position: absolute; top: 0px; left: 0px; text-align: center; padding: 6px; font-size: 14px; font-weight: bold; z-Index: 200;\">"+thetext+"</div>" ;
	else
		message = "<div id=\"login_alert_box\" class=\"info_error\" style=\"display: none; position: absolute; top: 0px; left: 0px; text-align: center; padding: 6px; font-size: 14px; font-weight: bold; z-Index: 200;\">"+thetext+"</div>" ;

	$('body').append( message ) ;
	$('#login_alert_box').center().show().fadeOut("slow").fadeIn("fast").delay(delay_vis).fadeOut("slow").hide() ;
}

function do_alert_div( thepath, theflag, thetext )
{
	var message ;

	if ( theflag )
		$('#div_alert').removeClass("info_good").removeClass("info_error").addClass("info_good").html( thetext ) ;
	else
		$('#div_alert').removeClass("info_good").removeClass("info_error").addClass("info_error").html( thetext ) ;

	$('#div_alert').fadeIn("fast") ;
}

function do_search( theurl )
{
	var input_search = encodeURIComponent( $('#input_search').val() ) ;
	var s_as = $('#s_as').val() ;

	if ( input_search == "" )
		do_alert( 0, "Please provide a search string." ) ;
	else
		location.href = theurl+'&action=search&s_as='+s_as+'&text='+input_search ;
}

window.unixtime = function() { return parseInt(new Date().getTime().toString().substring(0, 10)) ; }
function microtime( get_as_float )
{
	var now = new Date().getTime() / 1000 ;
	var s = parseInt(now, 10) ;

	return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s ;
}

function pad( number, length )
{
	var str = '' + number ;
	while ( str.length < length )
		str = '0' + str ;
	return str;
}

function autolink_it( message ){
	var thismessage = message ;
	var theregx = /^\/nolink /i ;
	var match = theregx.exec( thismessage ) ;
	if ( match != null )
		message = message.replace( /^\/nolink /i, "" ).replace( /\//g, "&#47;" ).replace( /\./g, "&#46;" ) ;
	return autolinker.link( message ) ;
}

function regmatch(s,r){
	var myString = new String(s) ;
	var myRE = new RegExp(r, "gi") ;
	var results = myString.match(myRE) ;
	return (results[1]) ;
}

function new_win_default( theurl )
{
	var unique = unixtime() ;
	window.open(theurl, unique, 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')
}

function parse_marquee( themessage )
{
	if ( themessage.indexOf("[email") != -1 )
		themessage = themessage.replace(/\[email\](.*?)\[\/email\]/g, "<a href=mailto:$1>$1</a>") ;

	if ( themessage.indexOf("[url=") != -1 )
		themessage = themessage.replace(/\[url=(.*?)\](.*?)\[\/url\]/g, "<a href=$1 target=\"_blank\">$2</a>") ;

	return themessage;
}

function is_mobile()
{
	var userAgent = navigator.userAgent || navigator.vendor || window.opera ;
	if ( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) )
	{
		if ( userAgent.match( /iPad/i ) ) { return 3 ; }
		else { return 1 ; }
	}
	else if( userAgent.match( /Android/i ) ) { return 2 ; }
	else { return 2 ; }
}

function HTML5_audio_support()
{
	var audio_supported = new Object ;
	if ( Modernizr.audio )
	{
		audio_supported["audio"] = 1 ;
		if ( Modernizr.audio.mp3 ){
			audio_supported["mp3"] = 1 ;
		}
		if ( Modernizr.audio.ogg ){
			audio_supported["ogg"] = 1 ;
		}
		if ( Modernizr.audio.m4a ){
			audio_supported["m4a"] = 1 ;
		}
	}
	return audio_supported ;
} var undeefined ;

/***** variable replacing *****/
String.prototype.trimreturn = function(){
	if ( this.substr((this.length-2), this.length) == "\r\n" ) { return this.substr(0, (this.length-2)) ; }
	else if ( this.substr((this.length-1), this.length) == "\n" ) { return this.substr(0, (this.length-1)) ; }
	else { return this ; }
};
String.prototype.noreturns = function(){
	return this.replace( /(\r\n)/g, ' p_br ' ).replace( /(\r)/g, ' p_br ' ).replace( /(\n)/g, ' p_br ' ) ;
};
String.prototype.nl2br = function(){
	// minor thing perhaps add " p_br " spaces for above
	return this.replace( /(p_br)/g, '<br>' ) ;
};

String.prototype.tags = function(){ var string = this.replace(/>/g, "&gt;"); return string.replace(/</g, "&lt;"); };
String.prototype.c615 = function(){ var string = this.replace(/<c615>(.*?)<\/c615>/g, ""); return string; };
String.prototype.vars = function(){
	var string = this ;

	string = string.replace( /((%%user%%)|(%%visitor%%))/g, "<span class='notranslate'>"+chats[ces]["vname"]+"</span>" );
	string = string.replace( /%%operator%%/g, "<span class='notranslate'>"+cname+"</span>" );
	string = string.replace( /%%op_email%%/g, "<a href='mailto:"+cemail+"' class='notranslate'>"+cemail+"</a>" );

	if ( string.indexOf("image:") != -1 )
		string = string.replace( /image:(.*?)($| |<br>)/g, "<img src=$1 > " ) ;

	return string;
};

String.prototype.emos = function(){
	var string = this ;

	if ( ( typeof( addon_emo ) != "undefined" ) && parseInt( addon_emo ) )
	{
		string = string.replace( /(&lt;3)/g, "<img src='"+base_url+"/addons/emoticons/heart.png' width='18' height='18' border='0'>" );
		string = string.replace( /(&gt;:\()/g, "<img src='"+base_url+"/addons/emoticons/angry.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\))/g, "<img src='"+base_url+"/addons/emoticons/smile.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\()/g, "<img src='"+base_url+"/addons/emoticons/sad.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\\)/g, "<img src='"+base_url+"/addons/emoticons/confused.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:'\()/g, "<img src='"+base_url+"/addons/emoticons/cry.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\$)/g, "<img src='"+base_url+"/addons/emoticons/embarrassed.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:D)/g, "<img src='"+base_url+"/addons/emoticons/ecstatic.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:\|)/g, "<img src='"+base_url+"/addons/emoticons/neutral.png' width='18' height='18' border='0'>" );
		string = string.replace( /(\|_)/g, "<img src='"+base_url+"/addons/emoticons/thumbs_up.png' width='18' height='18' border='0'>" );
		string = string.replace( /(;\))/g, "<img src='"+base_url+"/addons/emoticons/wink.png' width='18' height='18' border='0'>" );
		string = string.replace( /(:O)/g, "<img src='"+base_url+"/addons/emoticons/omg.png' width='18' height='18' border='0'>" );
	}

	return string;
};
