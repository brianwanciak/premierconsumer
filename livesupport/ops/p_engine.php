<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;

	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
	$charset = Util_Format_Sanatize( Util_Format_GetVar( "charset" ), "ln" ) ;
	if ( !$charset ) { $charset = "UTF-8" ; }
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> [ chat engine ] </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $charset ?>"> 
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/default/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	"use strict" ;
	var on = 1 ; var dev_run = 0 ;
	var isop = parent.isop ; var isop_ ; var isop__ ;
	var ces = "<?php echo $ces ?>" ;
	var stopped = 0 ;
	var reconnect = 0 ;
	var rloop = parent.rloop ;
	var loop = 1 ;
	var chatting_err_915, chatting_err_815 ;

	var c_routing = 0, c_chatting = 0, c_requesting = 0 ;
	var st_routing, st_chatting, st_requesting, st_init_chatting, st_network, st_connect, st_reconnect ;

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;

		if ( on )
		{
			if ( isop )
			{
				if ( typeof( st_requesting ) != "undefined" ) { clearTimeout( st_requesting ) ; }
				st_requesting = setTimeout( "requesting()", 2000 ) ; // slight delay to let parent load
			}
			else if ( !parent.widget )
				st_routing = setTimeout( "routing()" , 1000 ) ; // take out timeout on live
		
			init_chatting() ;
		}
	});

	function init_chatting()
	{
		if ( !parent.loaded )
			st_init_chatting = setTimeout(function(){ init_chatting() }, 300) ;
		else
		{
			if ( typeof( st_init_chatting ) != "undefined" )
			{
				clearTimeout( st_init_chatting ) ; st_init_chatting = undeefined ;
			}

			// only start chatting() if not operator... operators are started with requesting()
			if ( !isop )
				chatting() ;
		}
	}

	function routing()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "GET",
		url: parent.base_url_full+"/ajax/chat_routing.php",
		data: "&a=routing&c="+ces+"&d="+parent.deptid+"&r="+parent.rtype+"&rt="+parent.rtime+"&cr="+c_routing+"&rl="+rloop+"&l="+loop+"&"+unique,
		success: function(data){
			try {
				eval(data) ;
			} catch(err) {
				// suppress for now until error reporting
				// should never reach here unless a script error
			}

			if ( typeof( st_routing ) != "undefined" )
			{
				clearTimeout( st_routing ) ;
				st_routing = undeefined ;
			}

			if ( json_data.status == 1 )
				parent.init_connect( json_data ) ;
			else if ( json_data.status == 2 )
			{
				// routed to new operator
				if ( typeof( json_data.reset ) != "undefined" )
					++loop ;

				++c_routing ;
				st_routing = setTimeout( "routing()" , <?php echo $VARS_JS_ROUTING ?> * 1000 ) ;
			}
			else if ( json_data.status == 10 )
			{
				stopit(0) ;
				parent.leave_a_mesg() ;
			}
			else if ( json_data.status == 0 )
			{
				++c_routing ;
				st_routing = setTimeout( "routing()" , <?php echo $VARS_JS_ROUTING ?> * 1000 ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			if ( typeof( st_routing ) != "undefined" )
			{
				clearTimeout( st_routing ) ;
				st_routing = undeefined ;
			}
			st_routing = setTimeout( "routing()" , <?php echo $VARS_JS_ROUTING ?> * 1000 ) ;
		} });
	}

	function requesting()
	{
		var start = microtime( true ) ;
		var unique = unixtime() ;
		var json_data = new Object ; c_chatting = c_requesting ;
		var chatting_query = get_chatting_query() ; if ( chatting_query ) { chatting_query = "&"+chatting_query ; }
		var q_ces = "" ;

		if ( typeof( st_network ) != "undefined" ) { clearTimeout( st_network ) ; st_network = undeefined ; }
		if ( typeof( st_requesting ) != "undefined" ) { clearTimeout( st_requesting ) ; st_requesting = undeefined ; }

		for ( var ces in parent.chats )
		{
			q_ces += "qc[]="+ces+"&" ;
		}

		if ( !reconnect )
			st_network = setTimeout( function(){ stopit(0) ; parent.check_network( 715, undeefined, undeefined ) }, parseInt( <?php echo $VARS_JS_OP_CONSOLE_TIMEOUT ?> ) * 1000 ) ;
		else
			st_network = setTimeout( function(){ stopit(0) ; parent.check_network( 717, undeefined, undeefined ) }, parseInt( <?php echo $VARS_JS_REQUESTING ?> ) * 1000 ) ;

		$.ajax({
		type: "GET",
		url: parent.base_url_full+"/ajax/chat_op_requesting.php",
		data: "m="+parent.mapp+"&a=rq&ps="+parent.prev_status+"&t="+parent.traffic+"&cr="+c_requesting+"&"+q_ces+chatting_query+"&"+unique+"&",
		success: function(data){
			if ( typeof( st_network ) != "undefined" ) { clearTimeout( st_network ) ; st_network = undeefined ; }
			try {
				eval(data) ;
			} catch(err) {
				// most likely internet disconnect or server response error will cause console to disconnect automatically
				// suppress error and let the console reconnect
				if ( !reconnect )
				{
					parent.check_network( 716, undeefined, undeefined ) ;
				} return false ;
			}

			chatting_err_815 = undeefined ;
			if ( !stopped || ( stopped && reconnect ) )
			{
				stopped = 0 ; // reset it for disconnect situation
				reconnect = 0 ;
				parent.reconnect_success() ;

				// reset it here for network status
				unique = unixtime() ;

				if ( json_data.status == -1 )
				{
					parent.toggle_status( 3 ) ;
				}
				else if ( json_data.status )
				{
					var json_length = json_data.requests.length ;
					for ( var c = 0; c < json_length; ++c )
					{
						var thisces = json_data.requests[c]["ces"] ;
						var thisdeptid = json_data.requests[c]["did"] ;
						var rupdated = ( typeof( parent.depts_rtime_hash[thisdeptid] ) != "undefined" ) ? parseInt( json_data.requests[c]["vup"] ) + parseInt( parent.depts_rtime_hash[thisdeptid] ) : unique ;
						// ( unique <= rupdated ) - need to plan further

						if ( json_data.requests[c]["op2op"] || ( typeof( parent.op_depts_hash[thisdeptid] ) != "undefined" ) )
						{
							parent.new_chat( json_data.requests[c], unique ) ;
						}
					}

					parent.init_chat_list( unique ) ;
					parent.update_traffic_counter( pad( json_data.traffics, 2 ) ) ;

					if ( typeof( st_requesting ) == "undefined" )
						st_requesting = setTimeout( "requesting()" , <?php echo $VARS_JS_REQUESTING ?> * 1000 ) ;

					var end = microtime( true ) ;
					var diff = end - start ;

					parent.check_network( diff, unique, json_data.pd ) ;
				}

				++c_requesting ;

				// process chats (same as in chatting() function)
				if ( chatting_query )
				{
					var thisces = ( typeof( parent.ces ) != "undefined" ) ? parent.ces : "" ;
					var json_length = json_data.chats.length ;
					for ( var c = 0; c < json_length; ++c )
						parent.update_ces( json_data.chats[c] ) ;

					parent.init_chats() ;

					if ( typeof( parent.chats[thisces] ) != "undefined" )
						parent.chats[thisces]["istyping"] = json_data.istyping ;
				}
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			if ( typeof( chatting_err_815 ) == "undefined" )
			{
				chatting_err_815 = 1 ;
				parent.update_network_log( "<tr id='div_network_his_"+parent.network_counter+"' style='display: none'><td class='chat_info_td' colspan='3'>xhr: 815:"+xhr.status+"</td></tr>" ) ;
				setTimeout(function(){ requesting() ; }, 3000) ;
			}
			else
			{
				// for Mobile Apps, some devices pauses network at pause/resume.  add some buffer so the disconnect message is only
				// displayed on actual network disconnect
				if ( parent.mapp && chatting_err_815 && ( chatting_err_815 < 3 ) ) { ++chatting_err_815 ; }
				else
				{
					stopit(0) ;
					st_reconnect = setTimeout(function(){ parent.check_network( 815+":"+xhr.status, undeefined, undeefined ) ; }, 3000) ;
				}
			}
		} });

		if ( dev_run && ( c_requesting > 1 ) )
			stopit(0) ;
	}

	function chatting()
	{
		var json_data = new Object ;
		var chatting_query = get_chatting_query() ;

		if ( typeof( st_chatting ) != "undefined" )
		{
			clearTimeout( st_chatting ) ; st_chatting = undeefined ;
		}

		if ( chatting_query )
		{
			var unique = unixtime() ;

			$.ajax({
			type: "GET",
			url: parent.base_url_full+"/ajax/chat_op_requesting.php",
			data: chatting_query+"&"+unique+"&",
			success: function(data){
				try {
					eval(data) ;
				} catch(err) {
					// if operator, the console will attempt to reconnect
					// if visitor, keep trying to send the data
					if ( !isop ) { visitor_reconnect() ; }
				}

				chatting_err_915 = undeefined ;
				if ( !stopped || ( stopped && reconnect ) )
				{
					stopped = 0 ; // reset it for disconnect situation
					reconnect = 0 ;

					if ( json_data.status )
					{
						// process chats
						var thisces = ( typeof( parent.ces ) != "undefined" ) ? parent.ces : "" ;
						var json_length = json_data.chats.length ;
						for ( var c = 0; c < json_length; ++c )
							parent.update_ces( json_data.chats[c] ) ;

						parent.init_chats() ;

						if ( typeof( parent.chats[thisces] ) != "undefined" )
							parent.chats[thisces]["istyping"] = json_data.istyping ;

						// only apply to visitor... for operator requesting() calls it for disconnection detection
						if ( !isop )
						{
							if ( typeof( st_chatting ) == "undefined" )
								st_chatting = setTimeout( "chatting()" , <?php echo $VARS_JS_REQUESTING ?> * 1000 ) ;
						}
					}
				}
				else
				{
					clearTimeout( st_chatting ) ; st_chatting = undeefined ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				if ( isop )
				{
					if ( typeof( chatting_err_915 ) == "undefined" )
					{
						chatting_err_915 = 1 ;
						parent.update_network_log( "<tr id='div_network_his_"+parent.network_counter+"' style='display: none'><td class='chat_info_td' colspan='3'>xhr: 915:"+xhr.status+"</td></tr>" ) ;
						setTimeout(function(){ chatting() ; }, 3000) ;
					}
					else
					{
						stopit(0) ;
						st_reconnect = setTimeout(function(){ parent.check_network( 915+":"+xhr.status, undeefined, undeefined ) ; }, 1000) ;
					}
				}
				else { visitor_reconnect() ; }
			} });
			++c_chatting ;
		}
		else
		{
			if ( !isop ) { st_chatting = setTimeout( "chatting()" , <?php echo $VARS_JS_REQUESTING ?> * 1000 ) ; }
		}
	}

	function get_chatting_query()
	{
		var query ;
		var start = 0 ;
		var q_ces = "" ;
		var q_chattings = "" ;
		var q_isop_ = "" ;
		var q_isop__ = "" ;

		for ( var ces in parent.chats )
		{
			// only check chats that are in session...
			if ( ( ( parent.chats[ces]["status"] == 1 ) || parent.chats[ces]["op2op"] || parent.chats[ces]["initiated"] ) && !parent.chats[ces]["disconnected"] && !parent.chats[ces]["tooslow"] )
			{
				q_ces += "qcc[]="+ces+"&" ;
				q_chattings += "qch[]="+parent.chats[ces]["chatting"]+"&" ;
				q_isop_ += "qo_[]="+parent.chats[ces]["op2op"]+"&" ;
				q_isop__ += "qo__[]="+parent.chats[ces]["opid"]+"&" ;
				start = 1 ;
			}
		}
		if ( start )
		{
			isop_ = parent.isop_ ; isop__ = parent.isop__ ;
			var thisces = ( typeof( parent.ces ) != "undefined" ) ? parent.ces : "" ;
			var requestid = ( thisces && typeof( parent.chats[thisces]["requestid"] ) != "undefined" ) ? parent.chats[thisces]["requestid"] : 0 ;
			var t_vses = ( thisces ) ? parent.chats[thisces]["t_ses"] : 0 ;
			var mobile = ( typeof( parent.mobile ) != "undefined" ) ? parent.mobile : 0 ;
			var mapp = ( !parent.isop ) ? parent.chats[thisces]["mapp"] : parent.mapp ;

			query = "rq="+requestid+"&t="+t_vses+"&o="+isop+"&o_="+isop_+"&o__="+isop__+"&c="+thisces+"&ch="+c_chatting+"&"+q_ces+q_chattings+q_isop_+q_isop__+"&mo="+mobile+"&mp="+mapp ;
		}
		return query ;
	}

	function restart_requesting()
	{
		requesting() ;
	}

	function visitor_reconnect()
	{
		// keep trying to reconnect the chat engine
		// todo: maximum number of attempts before final disconnect
		if ( typeof( st_chatting ) != "undefined" )
		{
			clearTimeout( st_chatting ) ;
			st_chatting = undeefined ;
		}
		st_chatting = setTimeout( "chatting()" , <?php echo $VARS_JS_REQUESTING ?> * 1000 ) ;
	}

	function stopit( thereconnect )
	{
		reconnect = thereconnect ;
		clear_timeouts() ;
		if ( !isop ) { parent.disconnect_complete() ; }
	}

	function clear_timeouts()
	{
		if ( typeof( st_routing ) != "undefined" ) { clearTimeout( st_routing ) ; st_routing = undeefined ; }
		if ( typeof( st_chatting ) != "undefined" ) { clearTimeout( st_chatting ) ; st_chatting = undeefined ; }
		if ( typeof( st_requesting ) != "undefined" ) { clearTimeout( st_requesting ) ; st_requesting = undeefined ; }
		if ( typeof( st_network ) != "undefined" ) { clearTimeout( st_network ) ; st_network = undeefined ; }
		if ( typeof( st_reconnect ) != "undefined" ) { clearTimeout( st_reconnect ) ; st_reconnect = undeefined ; }
		stopped = 1 ;
	}
//-->
</script>
</head>
<body>
</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>