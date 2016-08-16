<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }

	$query = Util_Format_Sanatize( Util_Format_GetVar( "q" ), "" ) ;
	if ( !$query ) { $query = Util_Format_Sanatize( Util_Format_GetVar( "v" ), "" ) ; }
	if ( !isset( $CONF["IE_cs"] ) ) { $CONF["IE_cs"] = 0 ; }
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;

	$params = Array( ) ; $params = explode( "|", $query ) ;
	$deptid = ( isset( $params[0] ) && $params[0] ) ? Util_Format_Sanatize( $params[0], "n" ) : 0 ;
	$btn = ( isset( $params[1] ) && $params[1] ) ? Util_Format_Sanatize( $params[1], "n" ) : 0 ;
	$placeholder = ( isset( $params[2] ) && $params[2] ) ? Util_Format_Sanatize( rawurldecode( $params[2] ), "n" ) : 0 ;
	$text = ( isset( $params[3] ) && $params[3] ) ? Util_Format_Sanatize( rawurldecode( $params[3] ), "ln" ) : "" ;
	$theme = ( isset( $params[4] ) && $params[4] ) ? Util_Format_Sanatize( rawurldecode( $params[4] ), "ln" ) : "" ;
	$base_url = $CONF["BASE_URL"] ;
	if ( !isset( $CONF['foot_log'] ) ) { $CONF['foot_log'] = "on" ; }
	if ( !isset( $CONF['icon_check'] ) ) { $CONF['icon_check'] = "on" ; }
	$popout_div = ( !isset( $VALS["POPOUT"] ) || ( $VALS["POPOUT"] != "off" ) ) ? "<div id='phplive_embed_menu_popout' style='float: left; width: 20px; height: 20px; margin: 0px; margin-left: 12px; cursor: pointer;' onClick='phplive_widget_embed_popout( )'><img src='$base_url/pics/space_big.png' width='20' height='20' border='0' alt='' style='width: 20px !important; height: 20px !important;'></div>" : "" ;
	if ( !isset( $VARS_ADA_TXT ) ) { $VARS_ADA_TXT = "" ; }

	$initiate = ( isset( $VALS["auto_initiate"] ) && $VALS["auto_initiate"] ) ? unserialize( html_entity_decode( $VALS["auto_initiate"] ) ) : Array( ) ;
	$widget_max = 4 ;
	$widget_slider = ( isset( $initiate["pos"] ) ) ? $initiate["pos"] : 1 ;
	if ( $widget_slider == 1 ) { $widget_animate_show = "left: '50px'" ; $widget_animate_hide = "left: -800" ; $widget_top_left = "top: 190px; left: 50px;" ; $widget_cover_top_left = "top: 190px; left: -800px;" ; }
	else if ( $widget_slider == 2 ) { $widget_animate_show = "right: '50px'" ; $widget_animate_hide = "right: -800" ; $widget_top_left = "top: 190px; right: 50px;" ; $widget_cover_top_left = "top: 190px; right: -800px;" ; }
	else if ( $widget_slider == 3 ) { $widget_animate_show = "bottom: '50px'" ; $widget_animate_hide = "bottom: -800" ; $widget_top_left = "bottom: 50px; left: 50px;" ; $widget_cover_top_left = "bottom: -800px; left: 50px;" ; }
	else if ( $widget_slider == 4 ) { $widget_animate_show = "bottom: '50px'" ; $widget_animate_hide = "bottom: -800" ; $widget_top_left = "bottom: 50px; right: 50px;" ; $widget_cover_top_left = "bottom: -800px; right: 50px;" ; }
	else { $widget_animate_show = $widget_animate_hide = $widget_top_left = $widget_cover_top_left = "" ; }

	$online = ( isset( $VALS['ONLINE'] ) && $VALS['ONLINE'] ) ? unserialize( $VALS['ONLINE'] ) : Array( ) ;
	if ( !isset( $online[0] ) ) { $online[0] = "embed" ; }
	if ( !isset( $online[$deptid] ) ) { $online[$deptid] = $online[0] ; }
	$offline = ( isset( $VALS['OFFLINE'] ) && $VALS['OFFLINE'] ) ? unserialize( $VALS['OFFLINE'] ) : Array( ) ;
	if ( !isset( $offline[0] ) ) { $offline[0] = "embed" ; }
	if ( !isset( $offline[$deptid] ) ) { $offline[$deptid] = $offline[0] ; }

	$redirect_url = ( isset( $offline[$deptid] ) && !preg_match( "/^(icon|hide|embed)$/", $offline[$deptid] ) ) ? $offline[$deptid] : "" ;
	$icon_hide = ( isset( $offline[$deptid] ) && preg_match( "/^(hide)$/", $offline[$deptid] ) ) ? 1 : 0 ;
	$embed_online = ( isset( $online[$deptid] ) && preg_match( "/^(embed)$/", $online[$deptid] ) ) ? 1 : 0 ;
	$embed_offline = ( isset( $offline[$deptid] ) && preg_match( "/^(embed)$/", $offline[$deptid] ) ) ? 1 : 0 ;

	if ( !isset( $VALS["EXCLUDE"] ) ) { $VALS["EXCLUDE"] = "" ; }
	$exclude_array = explode( ",", $VALS["EXCLUDE"] ) ; $exclude_process = 0 ; $exclude_string = "" ;
	for ( $c = 0; $c < count( $exclude_array ); ++$c ) { if ( $exclude_array[$c] ) { $exclude_string .= "($exclude_array[$c])|" ; } }
	if ( $exclude_string ) { $exclude_process = 1 ; $exclude_string = substr_replace( $exclude_string, "", -1 ) ; }
	else { $exclude_string = "place-holder_text" ; }

	if ( !isset( $CONF["vsize"] ) ) { $width = $VARS_CHAT_WIDTH ; $height = $VARS_CHAT_HEIGHT ; }
	else { LIST( $width, $height ) = explode( "x", $CONF["vsize"] ) ; }
	$widget_shadow_image = ( $VARS_CHAT_WIDGET_SHADOW ) ? "$base_url/themes/initiate/bg_trans.png" : "$base_url/pics/space.gif" ;
	Header( "Content-Type: text/javascript" ) ;
?>
if ( typeof( phplive_utf8_encode ) == "undefined" ){ function phplive_utf8_encode(r){if(null===r||"undefined"==typeof r)return"";var e,n,t=r+"",a="",o=0;e=n=0,o=t.length;for(var f=0;o>f;f++){var i=t.charCodeAt(f),l=null;if(128>i)n++;else if(i>127&&2048>i)l=String.fromCharCode(i>>6|192,63&i|128);else if(55296!=(63488&i))l=String.fromCharCode(i>>12|224,i>>6&63|128,63&i|128);else{if(55296!=(64512&i))throw new RangeError("Unmatched trail surrogate at "+f);var d=t.charCodeAt(++f);if(56320!=(64512&d))throw new RangeError("Unmatched lead surrogate at "+(f-1));i=((1023&i)<<10)+(1023&d)+65536,l=String.fromCharCode(i>>18|240,i>>12&63|128,i>>6&63|128,63&i|128)}null!==l&&(n>e&&(a+=t.slice(e,n)),a+=l,e=n=f+1)}return n>e&&(a+=t.slice(e,o)),a} function phplive_md5(n){var r,t,u,e,o,f,c,i,a,h,v=function(n,r){return n<<r|n>>>32-r},g=function(n,r){var t,u,e,o,f;return e=2147483648&n,o=2147483648&r,t=1073741824&n,u=1073741824&r,f=(1073741823&n)+(1073741823&r),t&u?2147483648^f^e^o:t|u?1073741824&f?3221225472^f^e^o:1073741824^f^e^o:f^e^o},s=function(n,r,t){return n&r|~n&t},d=function(n,r,t){return n&t|r&~t},l=function(n,r,t){return n^r^t},w=function(n,r,t){return r^(n|~t)},A=function(n,r,t,u,e,o,f){return n=g(n,g(g(s(r,t,u),e),f)),g(v(n,o),r)},C=function(n,r,t,u,e,o,f){return n=g(n,g(g(d(r,t,u),e),f)),g(v(n,o),r)},b=function(n,r,t,u,e,o,f){return n=g(n,g(g(l(r,t,u),e),f)),g(v(n,o),r)},m=function(n,r,t,u,e,o,f){return n=g(n,g(g(w(r,t,u),e),f)),g(v(n,o),r)},y=function(n){for(var r,t=n.length,u=t+8,e=(u-u%64)/64,o=16*(e+1),f=new Array(o-1),c=0,i=0;t>i;)r=(i-i%4)/4,c=i%4*8,f[r]=f[r]|n.charCodeAt(i)<<c,i++;return r=(i-i%4)/4,c=i%4*8,f[r]=f[r]|128<<c,f[o-2]=t<<3,f[o-1]=t>>>29,f},L=function(n){var r,t,u="",e="";for(t=0;3>=t;t++)r=n>>>8*t&255,e="0"+r.toString(16),u+=e.substr(e.length-2,2);return u},S=[],_=7,j=12,k=17,p=22,q=5,x=9,z=14,B=20,D=4,E=11,F=16,G=23,H=6,I=10,J=15,K=21;for(n=this.phplive_utf8_encode(n),S=y(n),c=1732584193,i=4023233417,a=2562383102,h=271733878,r=S.length,t=0;r>t;t+=16)u=c,e=i,o=a,f=h,c=A(c,i,a,h,S[t+0],_,3614090360),h=A(h,c,i,a,S[t+1],j,3905402710),a=A(a,h,c,i,S[t+2],k,606105819),i=A(i,a,h,c,S[t+3],p,3250441966),c=A(c,i,a,h,S[t+4],_,4118548399),h=A(h,c,i,a,S[t+5],j,1200080426),a=A(a,h,c,i,S[t+6],k,2821735955),i=A(i,a,h,c,S[t+7],p,4249261313),c=A(c,i,a,h,S[t+8],_,1770035416),h=A(h,c,i,a,S[t+9],j,2336552879),a=A(a,h,c,i,S[t+10],k,4294925233),i=A(i,a,h,c,S[t+11],p,2304563134),c=A(c,i,a,h,S[t+12],_,1804603682),h=A(h,c,i,a,S[t+13],j,4254626195),a=A(a,h,c,i,S[t+14],k,2792965006),i=A(i,a,h,c,S[t+15],p,1236535329),c=C(c,i,a,h,S[t+1],q,4129170786),h=C(h,c,i,a,S[t+6],x,3225465664),a=C(a,h,c,i,S[t+11],z,643717713),i=C(i,a,h,c,S[t+0],B,3921069994),c=C(c,i,a,h,S[t+5],q,3593408605),h=C(h,c,i,a,S[t+10],x,38016083),a=C(a,h,c,i,S[t+15],z,3634488961),i=C(i,a,h,c,S[t+4],B,3889429448),c=C(c,i,a,h,S[t+9],q,568446438),h=C(h,c,i,a,S[t+14],x,3275163606),a=C(a,h,c,i,S[t+3],z,4107603335),i=C(i,a,h,c,S[t+8],B,1163531501),c=C(c,i,a,h,S[t+13],q,2850285829),h=C(h,c,i,a,S[t+2],x,4243563512),a=C(a,h,c,i,S[t+7],z,1735328473),i=C(i,a,h,c,S[t+12],B,2368359562),c=b(c,i,a,h,S[t+5],D,4294588738),h=b(h,c,i,a,S[t+8],E,2272392833),a=b(a,h,c,i,S[t+11],F,1839030562),i=b(i,a,h,c,S[t+14],G,4259657740),c=b(c,i,a,h,S[t+1],D,2763975236),h=b(h,c,i,a,S[t+4],E,1272893353),a=b(a,h,c,i,S[t+7],F,4139469664),i=b(i,a,h,c,S[t+10],G,3200236656),c=b(c,i,a,h,S[t+13],D,681279174),h=b(h,c,i,a,S[t+0],E,3936430074),a=b(a,h,c,i,S[t+3],F,3572445317),i=b(i,a,h,c,S[t+6],G,76029189),c=b(c,i,a,h,S[t+9],D,3654602809),h=b(h,c,i,a,S[t+12],E,3873151461),a=b(a,h,c,i,S[t+15],F,530742520),i=b(i,a,h,c,S[t+2],G,3299628645),c=m(c,i,a,h,S[t+0],H,4096336452),h=m(h,c,i,a,S[t+7],I,1126891415),a=m(a,h,c,i,S[t+14],J,2878612391),i=m(i,a,h,c,S[t+5],K,4237533241),c=m(c,i,a,h,S[t+12],H,1700485571),h=m(h,c,i,a,S[t+3],I,2399980690),a=m(a,h,c,i,S[t+10],J,4293915773),i=m(i,a,h,c,S[t+1],K,2240044497),c=m(c,i,a,h,S[t+8],H,1873313359),h=m(h,c,i,a,S[t+15],I,4264355552),a=m(a,h,c,i,S[t+6],J,2734768916),i=m(i,a,h,c,S[t+13],K,1309151649),c=m(c,i,a,h,S[t+4],H,4149444226),h=m(h,c,i,a,S[t+11],I,3174756917),a=m(a,h,c,i,S[t+2],J,718787259),i=m(i,a,h,c,S[t+9],K,3951481745),c=g(c,u),i=g(i,e),a=g(a,o),h=g(h,f);var M=L(c)+L(i)+L(a)+L(h);return M.toLowerCase()} }
if ( typeof( phplive_init_jquery ) == "undefined" )
{
	var phplive_jquery ;
	var phplive_stat_refer = encodeURIComponent( document.referrer.replace("http", "hphp") ) ;
	var phplive_stat_onpage = encodeURIComponent( location.toString( ).replace("http", "hphp") ) ;
	var phplive_stat_title = encodeURIComponent( document.title ) ;
	var phplive_stat_title_temp = phplive_stat_title.replace( / /g,'' ) ; if ( !phplive_stat_title_temp )  { phplive_stat_title = "- no title -" ; }
	var phplive_win_width = screen.width ;
	var phplive_win_height = screen.height ;
	var phplive_resolution = encodeURI( phplive_win_width + " x " + phplive_win_height ) ;
	var phplive_query_extra = "&r="+phplive_stat_refer+"&title="+phplive_stat_title+"&resolution="+phplive_resolution ;
	var proto = location.protocol ; // to avoid JS proto error, use page proto for areas needing to access the JS objects
	var phplive_browser = navigator.appVersion ; var phplive_mime_types = "" ;
	var phplive_display_width = screen.availWidth ; var phplive_display_height = screen.availHeight ; var phplive_display_color = screen.colorDepth ; var phplive_timezone = new Date().getTimezoneOffset() ;
	if ( navigator.mimeTypes.length > 0 ) { for (var x=0; x < navigator.mimeTypes.length; x++) { phplive_mime_types += navigator.mimeTypes[x].description ; } }
	var phplive_browser_token = phplive_md5( phplive_display_width+phplive_display_height+phplive_display_color+phplive_timezone+phplive_browser+phplive_mime_types ) ;
	var phplive_session_support = ( typeof( Storage ) !== "undefined" ) ? 1 : 0 ;
	if ( phplive_session_support ) { try { sessionStorage.setItem( "minmax", 1 ) ; } catch (error) {} }
	var phplive_js_center = function(a){var b=phplive_jquery(window),c=b.scrollTop( );return this.each(function( ){var f=phplive_jquery(this),e=phplive_jquery.extend({against:"window",top:false,topPercentage:0.5},a),d=function( ){var h,g,i;if(e.against==="window"){h=b;}else{if(e.against==="parent"){h=f.parent( );c=0;}else{h=f.parents(against);c=0;}}g=((h.width( ))-(f.outerWidth( )))*0.5;i=((h.height( ))-(f.outerHeight( )))*e.topPercentage+c;if(e.top){i=e.top+c;}f.css({left:g,top:i});};d( );b.resize(d);});} ;
	var phplive_jquery_loading = 0 ; var undeefined ;

	var phplive_quirks = 0 ;
	var phplive_IE ;
	//@cc_on phplive_IE = navigator.appVersion ;

	var phplive_mobile = 0 ;
	var phplive_userAgent = navigator.userAgent || navigator.vendor || window.opera ;
	if ( phplive_userAgent.match( /iPad/i ) || phplive_userAgent.match( /iPhone/i ) || phplive_userAgent.match( /iPod/i ) )
	{
		if ( phplive_userAgent.match( /iPad/i ) ) { phplive_mobile = 0 ; }
		else { phplive_mobile = 1 ; }
	}
	else if ( phplive_userAgent.match( /Android/i ) ) { phplive_mobile = 2 ; }

	var phplive_IE_cs = ( phplive_IE && !<?php echo $mobile ?> ) ? <?php echo $CONF["IE_cs"] ?> : 0 ;
	var mode = document.compatMode,m ;
	if ( ( mode == 'BackCompat' ) && phplive_IE ) { phplive_quirks = 1 ; }

	window.phplive_init_jquery = function( )
	{
		if ( typeof( phplive_jquery ) == "undefined" )
		{
			if ( typeof( window.jQuery ) == "undefined" )
			{
				if ( !phplive_jquery_loading )
				{
					phplive_jquery_loading = 1 ;
					var script_jquery = document.createElement('script') ;
					script_jquery.type = "text/javascript" ; script_jquery.async = true ;
					script_jquery.onload = script_jquery.onreadystatechange = function ( ) {
						if ( ( typeof( this.readyState ) == "undefined" ) || ( this.readyState == "loaded" || this.readyState == "complete" ) )
						{
							phplive_jquery_loading = 0 ;
							phplive_jquery = window.jQuery.noConflict( true ) ;
							phplive_jquery.fn.center = phplive_js_center ;
						}
					} ;
					script_jquery.src = "<?php echo $base_url ?>/js/framework.js?<?php echo $VERSION ?>" ;
					var script_jquery_s = document.getElementsByTagName('script')[0] ;
					script_jquery_s.parentNode.insertBefore(script_jquery, script_jquery_s) ;
				}
			}
			else
			{
				phplive_jquery_loading = 0 ;
				phplive_jquery = window.jQuery ;
				phplive_jquery.fn.center = phplive_js_center ;
			}
		} else { phplive_jquery_loading = 0 ; }
	}
	window.phplive_unique = function( ) { var date = new Date( ) ; return date.getTime( ) ; }
	phplive_init_jquery( ) ;
}

if ( typeof( phplive_widget_embed ) == "undefined" )
{
	var phplive_interval_jquery_check ;

	// one default invite per page to avoid multiple invites
	var phplive_widget_embed = 0 ;
	var this_position = ( phplive_quirks ) ? "absolute" : "fixed" ;

	var phplive_embed_div_loaded = 0 ;

	var phplive_widget_div_js_loaded = 0 ;
	var phplive_widget_div_loaded = 0 ;

	var phplive_mobile_v_right = 25 ;
	var phplive_mobile_v_width = <?php echo $VARS_CHAT_WIDTH_WIDGET ?> ;
	var phplive_mobile_v_height = <?php echo $VARS_CHAT_HEIGHT_WIDGET ?> ;
	var phplive_mobile_v_popout = "<?php echo $popout_div ?>" ;
	if ( phplive_mobile )
	{
		phplive_mobile_v_right = 0 ;
		phplive_mobile_v_width = screen.width ;
		
		var phplive_mobile_v_height_adjust = ( phplive_mobile == 2 ) ? 135 : 125 ;
		phplive_mobile_v_height = screen.height - phplive_mobile_v_height_adjust ;
		phplive_mobile_v_popout = "" ;
	}

	// seems the quirks fixed so keep it same for now (was 270x180)
	var phplive_widget_width = ( phplive_quirks ) ? 250 : 250 ;
	var phplive_widget_height = ( phplive_quirks ) ? 160 : 160 ;
	var phplive_widget_image = '<?php echo Util_Upload_GetInitiate( 0 ) ; ?>' ;
	var phplive_widget_image_op = '<?php echo $base_url ?>/themes/initiate/initiate_op_cover.png' ;

	var phplive_widget = "<map name='initiate_chat_cover'><area shape='rect' coords='222,2,247,26' href='JavaScript:void(0)' onClick='phplive_widget_decline( )' style='outline: none;'><area shape='rect' coords='0,26,250,160' href='JavaScript:void(0)' onClick='phplive_widget_launch( )' style='outline: none;'></map><div id='phplive_widget' name='phplive_widget' style='display: none; position: "+this_position+"; <?php echo $widget_top_left ?> background: url( <?php echo $widget_shadow_image ?> ) repeat; padding: 10px; width: "+phplive_widget_width+"px; height: "+phplive_widget_height+"px; -moz-border-radius: 5px; border-radius: 5px; z-Index: 1000001;'></div><div id='phplive_widget_image' style='display: none; position: "+this_position+"; <?php echo $widget_cover_top_left ?> padding: 10px; z-Index: 1000002;'><img src='"+phplive_widget_image+"' id='phplive_widget_image_img' width='250' height='160' border=0 usemap='#initiate_chat_cover' style='width: 250px !important; height: 160px !important; -moz-border-radius: 5px; border-radius: 5px; outline: none;'></div>" ;
	var phplive_widget_embed_div = "<div id='phplive_widget_embed_iframe_loading' style='display: none; position: fixed; width: 31px; height: 31px; padding: 2px; right: "+phplive_mobile_v_right+"px; bottom: 5px; background: #FFFFFF; border: 1px solid #F1F5FB; -moz-border-radius: 5px; border-radius: 5px; z-Index: 1000005;'><img src='<?php echo $base_url ?>/themes/initiate/loading.gif' width='31' height='31' border='0' alt='' style='width: 31px !important; height: 31px !important; -moz-border-radius: 5px; border-radius: 5px;'></div> \
	<div id='phplive_widget_embed_iframe' style='position: fixed; width: "+phplive_mobile_v_width+"px; height: "+phplive_mobile_v_height+"px; right: "+phplive_mobile_v_right+"px; text-align: left; bottom: 50000px; z-Index: 1000003;'> \
		<div id='phplive_widget_embed_iframe_wrapper' style='width: 100%; height: 100%; -moz-border-radius: 5px; border-radius: 5px;'></div> \
		<div id='phplive_widget_embed_actions' style='position: absolute; top: 0px; left: 0px; width: "+phplive_mobile_v_width+"px; height: 44px;'> \
			<div style='padding: 7px; margin: 0px; top: 0px;'> \
				<div id='phplive_embed_menu_maximize' style='display: none; float: left; width: 20px; height: 20px; margin: 0px; cursor: pointer;' onClick='phplive_widget_embed_maximize( )'><img src='<?php echo $base_url ?>/pics/space_big.png' width='20' height='20' border='0' alt='' style='width: 20px !important; height: 20px !important;'></div> \
				<div id='phplive_embed_menu_minimize' style='float: left; width: 20px; height: 20px; margin: 0px; cursor: pointer;' onClick='phplive_widget_embed_minimize( )'><img src='<?php echo $base_url ?>/pics/space_big.png' width='20' height='20' border='0' alt='' style='width: 20px !important; height: 20px !important;'></div>"+phplive_mobile_v_popout+"<div style='float: right; width: 20px; height: 20px; margin: 0px; margin-left: 12px; cursor: pointer;' onClick='phplive_widget_embed_close( )'><img src='<?php echo $base_url ?>/pics/space_big.png' width='20' height='20' border='0' alt='' style='width: 20px !important; height: 20px !important;'></div> \
				<div style='clear: both;'></div> \
			</div> \
		</div> \
	</div> \
	<div id='phplive_widget_embed_iframe_shadow' style='display: none; position: fixed; width: "+parseInt(phplive_mobile_v_width+23)+"px; height: <?php echo ( $VARS_CHAT_HEIGHT_WIDGET + 28 ) ; ?>px; right: 19px; bottom: 0px; z-Index: 1000001;'><img src='<?php echo $base_url ?>/themes/initiate/widget_shadow.png' width='"+parseInt(phplive_mobile_v_width+23)+"' height='<?php echo ( $VARS_CHAT_HEIGHT_WIDGET + 28 ) ; ?>' border='0' alt='' style='width: "+parseInt(phplive_mobile_v_width+23)+"px !important; height: <?php echo ( $VARS_CHAT_HEIGHT_WIDGET + 28 ) ; ?>px !important;'></div><div id='phplive_widget_embed_iframe_shadow_minimzed' style='display: none; position: fixed; width: 265px; height: 55px; right: 20px; bottom: 0px; z-Index: 1000001;'><img src='<?php echo $base_url ?>/themes/initiate/widget_shadow_minimized.png' width='265' height='55' border='0' alt='' style='width: 265px !important; height: 55px !important;'></div>" ;

	window.phplive_display_invite_widget = function( thecoverimg, theurl )
	{
		if ( !phplive_widget_div_js_loaded )
		{
			phplive_widget_div_js_loaded = 1 ;
			phplive_jquery( "body" ).append( phplive_widget ) ;
		}

		if ( !phplive_widget_div_loaded )
		{
			phplive_widget_div_loaded = 1 ;
			phplive_create_iframe( 'phplive_widget', 'iframe_widget', theurl ) ;
			phplive_jquery( '#phplive_widget_image_img' ).attr( 'src', thecoverimg ) ;

			if ( <?php echo $widget_slider ?> > <?php echo $widget_max ?> )
			{
				phplive_jquery( '#phplive_widget_image' ).center( ).show( ) ;
				phplive_jquery( '#phplive_widget' ).center( ).fadeIn('fast') ;
			}
			else
			{
				if ( thecoverimg.indexOf( "op_cover" ) != -1 )
				{
					phplive_jquery( '#phplive_widget' ).fadeIn( "fast", function( ) {
						phplive_jquery( '#phplive_widget_image' ).animate({ <?php echo $widget_animate_show ?> }, 200, function( ) {
							phplive_jquery( '#phplive_widget_image' ).show( ) ;
						}) ;
					}) ;
				}
				else
				{
					phplive_jquery( '#phplive_widget_image' ).show( ).animate({ <?php echo $widget_animate_show ?> }, 2000, function( ) {
						phplive_jquery( '#phplive_widget' ).fadeIn('fast') ;
					}) ;
				}
			}
		}
	}
	window.phplive_widget_init = function ( )
	{
		if ( typeof( phplive_interval_jquery_check ) != "undefined" ) { clearInterval( phplive_interval_jquery_check ) ; }
		phplive_interval_jquery_check = setInterval(function( ){
			if ( typeof( phplive_jquery ) != "undefined" )
			{
				clearInterval( phplive_interval_jquery_check ) ;
				phplive_display_invite_widget( phplive_widget_image_op, "<?php echo $base_url ?>/widget.php?token="+phplive_browser_token+"&height="+phplive_widget_height +"&"+phplive_unique( ) ) ;
			}
		}, 200) ;
	}
	window.phplive_widget_launch = function( )
	{
		phplive_widget_div_loaded = 0 ;
		phplive_widget_close( ) ;
		phplive_launch_chat_<?php echo $deptid ?>(0, <?php echo $deptid ?>, 1, "<?php echo $theme ?>", 0, 0, 1) ;
	}
	window.phplive_widget_close = function( )
	{
		phplive_jquery( '#phplive_widget_image' ).stop().fadeOut('fast') ;
		phplive_jquery( '#phplive_widget' ).fadeOut('fast') ;
		if ( <?php echo ( $widget_slider <= $widget_max ) ? 1 : 0 ; ?> ) { phplive_jquery( '#phplive_widget_image' ).animate({ <?php echo $widget_animate_hide ?> }, 2000) ; }
		phplive_create_iframe( 'phplive_widget', 'iframe_widget', 'about:blank' ) ;
	}
	window.phplive_widget_decline = function( )
	{
		if ( phplive_widget_div_loaded )
		{
			var phplive_pullimg_widget = new Image ;
			phplive_pullimg_widget.onload = function( ) {
				//
			};
			phplive_pullimg_widget.src = "<?php echo $base_url ?>/ajax/chat_actions_disconnect.php?action=disconnect&token="+phplive_browser_token+"&isop=0&widget=1&"+phplive_unique( ) ;
			phplive_widget_close( ) ;
			phplive_widget_div_loaded = 0 ;
		}
	}
	window.phplive_widget_embed_launch = function( theurl, theminmax, theauto )
	{
		if ( !phplive_embed_div_loaded )
		{
			phplive_embed_div_loaded = 1 ;
			var load_counter = 0 ;
			phplive_jquery( "body" ).append( phplive_widget_embed_div ) ;
			if ( phplive_session_support ) { try { sessionStorage.setItem( "minmax", theminmax ) ; } catch (error) {} }

			if ( !theminmax )
			{
				phplive_jquery('#phplive_widget_embed_iframe').css({'bottom': 50000}).show( ) ;
				setTimeout( function( ){ phplive_widget_embed_minimize( ) ; phplive_jquery('#phplive_widget_embed_iframe').css({'bottom': 5}) ; }, 1500 ) ;
			}
			else
			{
				phplive_jquery('#phplive_widget_embed_iframe_loading').show( ) ;
				phplive_jquery('#phplive_widget_embed_iframe').hide( ) ;
				phplive_jquery('#phplive_widget_embed_iframe').css({'bottom': -550}).show( ) ;
				phplive_jquery('#phplive_widget_embed_iframe').animate({
					bottom: 5
				}, 500, function( ) {
					if ( !phplive_mobile ) { phplive_jquery('#phplive_widget_embed_iframe_shadow').fadeIn('fast') ; }
				}) ;
			}
			phplive_create_iframe( 'phplive_widget_embed_iframe_wrapper', 'iframe_widget_embed', theurl+"&embed=1&"+phplive_unique( ) ) ;
			phplive_jquery( '#iframe_widget_embed' ).load(function ( ){
				++load_counter ;
				// some browsers triggers load() multiple times... only check on 1st instance
				if ( load_counter == 1 )
					phplive_jquery('#phplive_widget_embed_iframe_loading').hide( ) ;
			}) ;
		}
		else
		{
			phplive_widget_embed_window_reset( ) ;
			if ( phplive_session_support ) { try { sessionStorage.setItem( "minmax", 1 ) ; } catch (error) {} }
			phplive_jquery('#phplive_widget_embed_iframe').fadeOut('fast', function( ) {
				phplive_jquery('#phplive_widget_embed_iframe').fadeIn('fast', function ( ) { if ( !phplive_mobile ) { phplive_jquery( '#phplive_widget_embed_iframe_shadow' ).fadeIn('fast') ; } } ) ;
			}) ;
		}
	}
	window.phplive_widget_embed_minimize = function( )
	{
		phplive_jquery('#phplive_widget_embed_iframe').css({'height': 45}) ;
		phplive_jquery('#phplive_widget_embed_iframe').css({'width': 250}) ;
		phplive_jquery('#phplive_embed_menu_minimize').hide( ) ;
		phplive_jquery('#phplive_embed_menu_popout').hide( ) ;
		phplive_jquery('#phplive_embed_menu_maximize').show( ) ;
		if ( !phplive_mobile )
		{
			phplive_jquery('#phplive_widget_embed_iframe_shadow').hide( ) ;
			phplive_jquery('#phplive_widget_embed_iframe_shadow_minimzed').fadeIn("fast") ;
		}
		if ( phplive_session_support ) { try { sessionStorage.setItem( "minmax", 0 ) ; } catch (error) {} }
	}
	window.phplive_widget_embed_maximize = function( )
	{
		phplive_widget_embed_window_reset( ) ;
		if ( !phplive_mobile )
		{
			phplive_jquery('#phplive_widget_embed_iframe_shadow').fadeIn("fast") ;
			phplive_jquery('#phplive_widget_embed_iframe_shadow_minimzed').hide( ) ;
		}
		if ( phplive_session_support ) { try { sessionStorage.setItem( "minmax", 1 ) ; } catch (error) {} }
	}
	window.phplive_widget_embed_popout = function( )
	{
		phplive_launch_chat_<?php echo $deptid ?>(1, <?php echo $deptid ?>, 1, "<?php echo $theme ?>", 0, 0, 0) ;
		phplive_jquery('#phplive_widget_embed_iframe').css({'bottom': 50000}) ;
		if ( !phplive_mobile )
		{
			phplive_jquery('#phplive_widget_embed_iframe_shadow').hide( ) ;
			phplive_jquery('#phplive_widget_embed_iframe_shadow_minimzed').hide( ) ;
		}
		phplive_widget_embed_window_reset( ) ;
		phplive_create_iframe( 'phplive_widget_embed_iframe_wrapper', 'iframe_widget_embed', 'about:blank' ) ;
		phplive_embed_div_loaded = 0 ;
	}
	window.phplive_widget_embed_close = function( )
	{
		phplive_jquery('#phplive_widget_embed_iframe').css({'bottom': 50000}) ;
		if ( !phplive_mobile )
		{
			phplive_jquery('#phplive_widget_embed_iframe_shadow').hide( ) ;
			phplive_jquery('#phplive_widget_embed_iframe_shadow_minimzed').hide( ) ;
		}
		phplive_widget_embed_window_reset( ) ;
		phplive_create_iframe( 'phplive_widget_embed_iframe_wrapper', 'iframe_widget_embed', 'about:blank' ) ;
		phplive_embed_div_loaded = 0 ;
	}
	window.phplive_widget_embed_mimax = function( )
	{
		if ( !phplive_jquery('#phplive_embed_menu_minimize').is(':visible') ) { phplive_widget_embed_maximize( ) ; }
		else { phplive_widget_embed_minimize( ) ; }
	}
	window.phplive_widget_embed_window_reset = function( )
	{
		phplive_jquery('#phplive_widget_embed_iframe').css({'height': phplive_mobile_v_height}) ;
		phplive_jquery('#phplive_widget_embed_iframe').css({'width': phplive_mobile_v_width}) ;
		phplive_jquery('#phplive_embed_menu_maximize').hide( ) ;
		phplive_jquery('#phplive_embed_menu_popout').show( ) ;
		phplive_jquery('#phplive_embed_menu_minimize').show( ) ;
		if ( !phplive_mobile ) { phplive_jquery('#phplive_widget_embed_iframe_shadow_minimzed').hide( ) ; }
	}
	window.phplive_create_iframe = function( thediv, thename, theurl )
	{
		if ( document.getElementById(thename) ) { phplive_jquery('#'+thename).empty().remove() ; }
		var phplive_dynamic_iframe = document.createElement("iframe") ;
		phplive_dynamic_iframe.src = theurl ;
		phplive_dynamic_iframe.id = thename ; phplive_dynamic_iframe.name = thename ;
		phplive_dynamic_iframe.style.width = "100%" ;
		phplive_dynamic_iframe.style.height = "100%" ;
		phplive_dynamic_iframe.style.border = 0 ;
		phplive_dynamic_iframe.scrolling = "no" ;
		phplive_dynamic_iframe.frameBorder = 0 ;
		phplive_dynamic_iframe.style.MozBorderRadius = "5px" ;
		phplive_dynamic_iframe.style.borderRadius = "5px" ;
		phplive_jquery('#'+thediv).empty( ).html( phplive_dynamic_iframe ) ;
	}
	var phplive_interval_jquery_init = setInterval(function( ){ if ( typeof( phplive_jquery ) != "undefined" ) {
			clearInterval( phplive_interval_jquery_init ) ;
	} }, 100) ;
}

if ( typeof( phplive_thec_<?php echo $deptid ?> ) == "undefined" )
{
	var phplive_thec_<?php echo $deptid ?> = 0 ;
	var phplive_fetch_status_image_<?php echo $deptid ?> ;
	var phplive_fetch_footprint_image_<?php echo $deptid ?> ;

	var phplive_interval_fetch_status_<?php echo $deptid ?> ;
	var phplive_interval_footprint_<?php echo $deptid ?> ;
	var phplive_request_url_query_<?php echo $deptid ?> = "d=<?php echo $deptid ?>&token="+phplive_browser_token+"&onpage="+phplive_stat_onpage+"&title="+phplive_stat_title+"&" ;
	var phplive_fetch_status_url_<?php echo $deptid ?> = "<?php echo $base_url ?>/ajax/status.php?action=js&token="+phplive_browser_token+"&deptid=<?php echo $deptid ?>&jkey=<?php echo md5( $CONF["API_KEY"] ) ?>" ;
	var phplive_request_url_<?php echo $deptid ?> = "<?php echo $base_url ?>/phplive.php?"+phplive_request_url_query_<?php echo $deptid ?> ;
	var phplive_request_url_<?php echo $deptid ?>_embed = "<?php echo $base_url ?>/phplive_embed.php?"+phplive_request_url_query_<?php echo $deptid ?> ;
	var phplive_offline_redirect_<?php echo $deptid ?> = 0 ;
	var phplive_online_offline_<?php echo $deptid ?> ;

	var phplive_image_online_<?php echo $deptid ?> = "<?php echo Util_Upload_GetChatIcon( "icon_online", $deptid ) ?>" ;
	var phplive_image_offline_<?php echo $deptid ?> = "<?php echo Util_Upload_GetChatIcon( "icon_offline", $deptid ) ?>" ;

	if ( <?php echo $exclude_process ?> && phplive_stat_onpage.match( /<?php echo $exclude_string ?>/ ) )
	{ phplive_image_online_<?php echo $deptid ?> = "<?php echo $base_url ?>/pics/space.gif" ; phplive_image_offline_<?php echo $deptid ?> = "<?php echo $base_url ?>/pics/space.gif" ; }

	window.phplive_get_thec_<?php echo $deptid ?> = function( ) { return phplive_thec_<?php echo $deptid ?> ; }
	window.phplive_fetch_status_<?php echo $deptid ?> = function( )
	{
		phplive_fetch_status_image_<?php echo $deptid ?> = new Image ;
		phplive_fetch_status_image_<?php echo $deptid ?>.onload = phplive_fetch_status_actions_<?php echo $deptid ?> ;
		phplive_fetch_status_image_<?php echo $deptid ?>.src = phplive_fetch_status_url_<?php echo $deptid ?>+"&"+phplive_unique( ) ;
	}
	window.phplive_fetch_status_actions_<?php echo $deptid ?> = function( )
	{
		var thisflag = phplive_fetch_status_image_<?php echo $deptid ?>.width ;

		if ( ( thisflag == 1 ) || ( thisflag == 4 ) || ( thisflag == 6 ) || ( thisflag == 8 ) || ( thisflag == 10 ) )
		{
			phplive_online_offline_<?php echo $deptid ?> = 1 ;
			if ( ( thisflag == 4 ) && !phplive_widget_div_loaded && !phplive_embed_div_loaded ) { phplive_widget_init( ) ; }
			else if ( ( thisflag == 10 ) && !phplive_widget_div_loaded && !phplive_embed_div_loaded ) { }
			else if ( ( thisflag == 8 ) && !phplive_embed_div_loaded )
			{
				// setTimeout is for brief delay to avoid too fast loading causing 1px height issue
				if ( phplive_session_support )
				{
					if ( sessionStorage.getItem("minmax") == 1 ) { setTimeout( function( ){ phplive_launch_chat_<?php echo $deptid ?>(0, <?php echo $deptid ?>, 1, "<?php echo $theme ?>", 1, 1, 0) ; }, 100 ) ; }
					else { setTimeout( function( ){ phplive_launch_chat_<?php echo $deptid ?>(0, <?php echo $deptid ?>, 0, "<?php echo $theme ?>", 1, 1, 0) ; }, 100 ) ; }
				}
				else { setTimeout( function( ){ phplive_launch_chat_<?php echo $deptid ?>(0, <?php echo $deptid ?>, 0, "<?php echo $theme ?>", 0, 1, 0) ; }, 100 ) ; }
			}
			else if ( thisflag == 6 ) { phplive_display_invite_widget( phplive_widget_image, "about:blank" ) ; } // auto invite
		}
		else
		{
			phplive_online_offline_<?php echo $deptid ?> = 0 ;
			if ( ( thisflag == 5 ) && !phplive_widget_div_loaded && !phplive_embed_div_loaded ) { phplive_widget_init( ) ; }
			else if ( ( thisflag == 11 ) && !phplive_widget_div_loaded && !phplive_embed_div_loaded ) { }
			if ( ( thisflag == 9 ) && !phplive_embed_div_loaded )
			{
				if ( phplive_session_support )
				{
					if ( sessionStorage.getItem("minmax") == 1 ) { setTimeout( function( ){ phplive_launch_chat_<?php echo $deptid ?>(0, <?php echo $deptid ?>, 1, "<?php echo $theme ?>", 1, 1, 0) ; }, 100 ) ; }
					else { setTimeout( function( ){ phplive_launch_chat_<?php echo $deptid ?>(0, <?php echo $deptid ?>, 0, "<?php echo $theme ?>", 1, 1, 0) ; }, 100 ) ; }
				}
				else { setTimeout( function( ){ phplive_launch_chat_<?php echo $deptid ?>(0, <?php echo $deptid ?>, 0, "<?php echo $theme ?>", 0, 1, 0) ; }, 100 ) ; }
			}
			else if ( thisflag == 7 ) { phplive_display_invite_widget( phplive_widget_image, "about:blank" ) ; } // auto invite
		}
		if ( typeof( phplive_interval_fetch_status_<?php echo $deptid ?> ) != "undefined" )
			clearInterval( phplive_interval_fetch_status_<?php echo $deptid ?> ) ;

		<?php if ( $CONF['icon_check'] == "on" ): ?>
		phplive_interval_fetch_status_<?php echo $deptid ?> = setInterval(function( ){ phplive_fetch_status_<?php echo $deptid ?>( ) ; }, <?php echo $VARS_JS_INVITE_CHECK ?> * 1000) ;
		<?php endif ; ?>

	}
	window.phplive_footprint_track_<?php echo $deptid ?> = function( )
	{
		var c = phplive_get_thec_<?php echo $deptid ?>( ) ; ++phplive_thec_<?php echo $deptid ?> ;
		var fetch_url = "<?php echo $base_url ?>/ajax/footprints.php?deptid=<?php echo $deptid ?>&token="+phplive_browser_token+"&onpage="+phplive_stat_onpage+"&c="+c+"&"+phplive_unique( ) ;

		if ( !c ) { fetch_url += phplive_query_extra ; }
		phplive_fetch_footprint_image_<?php echo $deptid ?> = new Image ;
		phplive_fetch_footprint_image_<?php echo $deptid ?>.onload = phplive_fetch_footprint_actions_<?php echo $deptid ?> ;
		phplive_fetch_footprint_image_<?php echo $deptid ?>.src = fetch_url ;
	}
	window.phplive_fetch_footprint_actions_<?php echo $deptid ?> = function( )
	{
		var thisflag = phplive_fetch_footprint_image_<?php echo $deptid ?>.width ;

		if ( thisflag == 1 )
		{
			if ( typeof( phplive_interval_footprint_<?php echo $deptid ?> ) != "undefined" )
				clearInterval( phplive_interval_footprint_<?php echo $deptid ?> ) ;

			<?php if ( $CONF['icon_check'] == "on" ): ?>
			phplive_interval_footprint_<?php echo $deptid ?> = setTimeout(function( ){ phplive_footprint_track_<?php echo $deptid ?>( ) }, <?php echo $VARS_JS_FOOTPRINT_CHECK ?> * 1000) ;
			<?php endif ; ?>

		}
		else if ( thisflag == 4 )
		{
			// if browser idle too long, clear all interval processes to save on resources
			if ( typeof( phplive_interval_footprint_<?php echo $deptid ?> ) != "undefined" ) { clearInterval( phplive_interval_footprint_<?php echo $deptid ?> ) ; }
			if ( typeof( phplive_interval_fetch_status_<?php echo $deptid ?> ) != "undefined" ) { clearInterval( phplive_interval_fetch_status_<?php echo $deptid ?> ) ; }
			if ( ( typeof( phplive_interval_status_check_<?php echo $btn ?> ) != "undefined" ) && ( typeof( phplive_btn_loaded_complete_<?php echo $btn ?> ) != "undefined" ) )
			{
				clearInterval( phplive_interval_status_check_<?php echo $btn ?> ) ;
			}
		}
	}
	window.phplive_launch_chat_<?php echo $deptid ?> = function( theflag, thedeptid, theminmax, thetheme, theforce, theauto, thewidget )
	{
		// theflag - indication of md5 reset for chat window popout
		// theforce - force embed chat
		var winname = ( theflag ) ? "popup_win" : "win_"+phplive_unique( ) ;
		var name = "", email = "" ;
		var custom_vars = "&custom=" ;
		if ( typeof( thedeptid ) == "undefined" ){ thedeptid = <?php echo $deptid ?> ; }
		if ( typeof( theminmax ) == "undefined" ){ theminmax = 1 ; }
		if ( typeof( thetheme ) == "undefined" ){ thetheme = "<?php echo $theme ?>" ; }

		if ( typeof( phplive_v ) != "undefined" )
		{
			for ( var key in phplive_v )
			{
				if ( key == "name" ) { name = encodeURIComponent( phplive_v["name"] ) ; }
				else if ( key == "email" ) { email = encodeURIComponent( phplive_v["email"] ) ; }
				else { custom_vars += encodeURIComponent( key )+"-_-"+encodeURIComponent( phplive_v[key] )+"-cus-" ; }
			}
		}

		phplive_widget_close( ) ; // incase widget is opened, close it since chat window opened
		if ( ( "<?php echo $redirect_url ?>" != "" ) && !phplive_online_offline_<?php echo $deptid ?> && !theauto )
		{
			location.href = "<?php echo $redirect_url ?>" ;
			//window.open( "<?php echo $redirect_url ?>", "_blank" ) ;
		}
		else
		{
			var launch_embed = ( theforce ) ? 1 : 0 ;

			if ( phplive_online_offline_<?php echo $deptid ?> ) { if ( <?php echo $embed_online ?>  && !theflag ) { launch_embed = 1 ; } }
			else { if ( <?php echo $embed_offline ?>  && !theflag ) { launch_embed = 1 ; } }

			// override deptid if provided
			if ( thedeptid != <?php echo $deptid ?> )
			{
				phplive_request_url_<?php echo $deptid ?>_embed = phplive_request_url_<?php echo $deptid ?>_embed.replace( /d=<?php echo $deptid ?>&/g, "d="+thedeptid+"&" ) ;
				phplive_request_url_<?php echo $deptid ?> = phplive_request_url_<?php echo $deptid ?>.replace( /d=<?php echo $deptid ?>&/g, "d="+thedeptid+"&" ) ;
			}

			if ( launch_embed && !phplive_IE_cs )
				phplive_widget_embed_launch( phplive_request_url_<?php echo $deptid ?>_embed+"&theme="+thetheme+"&js_name="+name+"&js_email="+email+custom_vars+"&", theminmax, theauto ) ;
			else if ( !theauto )
			{
				if ( thewidget ) { theflag = 1 ; }
				window.open( phplive_request_url_<?php echo $deptid ?>+"&popout="+theflag+"&theme="+thetheme+"&js_name="+name+"&js_email="+email+custom_vars+"&", winname, 'scrollbars=no,resizable=yes,menubar=no,location=no,screenX=50,screenY=100,width=<?php echo $width ?>,height=<?php echo $height ?>' ) ;
			}
		}
	}

	phplive_fetch_status_<?php echo $deptid ?>( ) ;
	phplive_footprint_track_<?php echo $deptid ?>( ) ;
}
if ( typeof( phplive_btn_loaded_<?php echo $btn ?> ) == "undefined" )
{
	var phplive_btn_loaded_<?php echo $btn ?> = 1 ;
	var phplive_btn_loaded_complete_<?php echo $btn ?> ;
	var phplive_interval_status_check_<?php echo $btn ?> ;
	var phplive_interval_jquery_check_<?php echo $btn ?> ;
	var phplive_online_offline_prev_<?php echo $btn ?> ;

	window.phplive_image_refresh_<?php echo $btn ?> = function( )
	{
		if ( typeof( phplive_interval_status_check_<?php echo $btn ?> ) != "undefined" ) { clearInterval( phplive_interval_status_check_<?php echo $btn ?> ) ; }

		var image_or_text ;
		var premierImage = (window.location.hostname == "www.librededeudas.com") ? "//www.librededeudas.com/assets/images/es/chat-v2.png" : "//www.premierconsumer.org/assets/images/en/chat-v2.png";
		if ( phplive_online_offline_<?php echo $deptid ?> )
			image_or_text = ( <?php echo ( $text ) ? 1 : 0 ; ?> ) ? "<?php echo $text ?>" : "<img src=\""+premierImage+"\" border=0 alt=\"<?php echo $VARS_ADA_TXT ?>\" title=\"<?php echo $VARS_ADA_TXT ?>\">" ;
		else
		{
			if ( <?php echo $icon_hide ?> ) { image_or_text = "" ; }
			else { image_or_text = ( <?php echo ( $text ) ? 1 : 0 ; ?> ) ? "<?php echo $text ?>" : "<img src=\""+phplive_image_offline_<?php echo $deptid ?>+"\" border=0 alt=\"<?php echo $VARS_ADA_TXT ?>\" title=\"<?php echo $VARS_ADA_TXT ?>\">" ; }
		}
		if ( phplive_online_offline_prev_<?php echo $btn ?> != image_or_text )
		{
			document.getElementById("phplive_btn_<?php echo $btn ?>").innerHTML = image_or_text ;
			phplive_online_offline_prev_<?php echo $btn ?> = image_or_text ;
		}
		phplive_btn_loaded_complete_<?php echo $btn ?> = 1 ;
		phplive_interval_status_check_<?php echo $btn ?> = setInterval(function( ){ phplive_image_refresh_<?php echo $btn ?>( ) ; }, 5000) ;
	}
	window.phplive_output_image_or_text_<?php echo $btn ?> = function( )
	{
		if ( typeof( phplive_online_offline_<?php echo $deptid ?> ) == "undefined" )
		{
			phplive_interval_status_check_<?php echo $btn ?> = setInterval(function( ){
				if ( typeof( phplive_online_offline_<?php echo $deptid ?> ) != "undefined" )
					phplive_image_refresh_<?php echo $btn ?>( ) ;
			}, 200) ;
		}
		else { phplive_image_refresh_<?php echo $btn ?>( ) ; }
	}
	window.phplive_process_<?php echo $btn ?> = function( )
	{
		if ( phplive_quirks )
		{
			var phplive_btn = document.getElementById('phplive_btn_<?php echo $btn ?>') ;
			if ( ( typeof( phplive_btn.style.position ) != "undefined" ) && phplive_btn.style.position )
				phplive_btn.style.position = "absolute" ;
		}

		phplive_output_image_or_text_<?php echo $btn ?>( ) ;
	}

	var phplive_interval_jquery_check_<?php echo $btn ?> = setInterval(function( ){
        if ( typeof( phplive_jquery ) != "undefined" ){
			clearInterval( phplive_interval_jquery_check_<?php echo $btn ?> ) ; phplive_interval_jquery_check_<?php echo $btn ?> = undeefined ;
			phplive_process_<?php echo $btn ?>( ) ;
		}
		else if ( !phplive_jquery_loading ) { phplive_init_jquery( ) ; }
	}, 200) ;
}
