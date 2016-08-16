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

	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	$error = "" ;

	$operators = Ops_get_AllOps( $dbh ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> operators </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/<?php echo $opinfo["theme"] ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var st_op2op ;

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;

		populate_ops_op2op() ;
		st_op2op = setInterval(function(){
			location.href = "op_op2op.php?ses=<?php echo $ses ?>&action=reload" ;
		}, 45000) ;

		var div_height = parent.extra_wrapper_height - 55 ;
		$('#canned_body').css({'height': div_height}) ;
		$('#div_operators').css({'height': div_height}) ;
		$('#canned_wrapper').fadeIn() ;

		//$(document).dblclick(function() {
		//	parent.close_extra( "op2op" ) ;
		//});

		parent.init_extra_loaded() ;

		<?php if ( $action == "reload" ): ?>parent.do_alert( 1, "Refresh Success" ) ;<?php endif ; ?>
	});

	function populate_ops_op2op()
	{
		var json_data = new Object ;
		var unique = unixtime() ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_deptops.php",
		data: "action=deptops&"+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				var ops_string = "" ;
				for ( var c = 0; c < json_data.departments.length; ++c )
				{
					ops_string += "<div class=\"chat_info_td_t\" style=\"margin-bottom: 1px;\">"+json_data.departments[c]["name"]+"</div>" ;
					for ( var c2 = 0; c2 < json_data.departments[c].operators.length; ++c2 )
					{
						var id, btn_id ;
						var status = "offline" ;
						var status_js = "JavaScript:void(0)" ;
						var status_bullet = "online_grey.png" ;
						var td_div = "chat_info_td_blank" ;
						var button = "" ;
						var chatting_with = ( parent.nchats ) ? " chatting with "+json_data.departments[c].operators[c2]["requests"]+" visitors" : "" ;

						if ( json_data.departments[c].operators[c2]["status"] )
						{
							id = "op2op_"+json_data.departments[c].operators[c2]["opid"] ;
							btn_id = "btn_"+json_data.departments[c]["deptid"]+"_"+json_data.departments[c].operators[c2]["opid"] ;
							status = "online" ;
							status_js = "request_op2op("+json_data.departments[c]["deptid"]+","+json_data.departments[c].operators[c2]["opid"]+")" ;
							button = "<button type=\"button\" id=\""+btn_id+"\" class=\"input_button\" onClick=\""+status_js+"\">request chat</button> " ;

							status_bullet= "online_green.png" ;
						}

						if ( json_data.departments[c].operators[c2]["opid"] == parent.isop )
							ops_string += "<div class=\""+td_div+"\"><span class=\"chat_info_td_traffic\" style=\"padding-left: 15px;\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/"+status_bullet+"\" width=\"12\" height=\"12\" border=\"0\"> <span id=\""+id+"\"><b>(You)</b> are "+status+chatting_with+"</span></span></div>" ;
						else
							ops_string += "<div class=\""+td_div+"\"><span class=\"chat_info_td_traffic\" style=\"padding-left: 15px;\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/"+status_bullet+"\" width=\"12\" height=\"12\" border=\"0\"> "+button+"<span id=\""+id+"\">"+json_data.departments[c].operators[c2]["name"]+" is "+status+chatting_with+"</span></span></div>" ;
					}
				}
				$('#canned_body').html( ops_string ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error retrieving operator list.  Please reload the console and try again." ) ;
		} });
	}

	function request_op2op( thedeptid, theopid )
	{
		$('#btn_'+thedeptid+"_"+theopid).html( "requesting..." ).attr("disabled", "true") ;
		request_op2op_doit( thedeptid, theopid ) ;
	}

	function request_op2op_doit( thedeptid, theopid )
	{
		var win_width = screen.width ;
		var win_height = screen.height ;
		var win_dim = win_width + " x " + win_height ;
		var json_data = new Object ;
		var unique = unixtime() ;

		$.ajax({
		type: "POST",
		url: "../ajax/chat_actions_op_op2op.php",
		data: "action=op2op&deptid="+thedeptid+"&opid="+theopid+"&resolution="+win_dim+"&unique="+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status ) { setTimeout( function(){ parent.input_focus() ; parent.close_extra( parent.extra ) ; }, 5000 ) ; }
			else { do_alert( 0, "Error requesting operator chat.  Please refresh the console and try again." ) ; }
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error requesting operator chat.  Please refresh the console and try again." ) ;
		} });
	}

//-->
</script>
</head>
<body>

<div id="canned_wrapper" style="display: none; height: 100%; overflow: auto;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td width="60%" valign="top">
					<div style="margin-bottom: 10px; font-size: 14px; font-weight: bold;"><img src="../themes/leaves/info_chats.gif" width="10" height="10" border="0" alt=""> Departments</div>
					<div id="canned_body" style="height: 300px; overflow-y: auto;"></div>
				</td>
				<td width="40%" valign="top" style="padding-left: 15px;">
					<div style="margin-bottom: 10px; font-size: 14px; font-weight: bold;"><img src="../themes/leaves/info_initiate.gif" width="10" height="10" border="0" alt=""> Operators</div>
					<div id="div_operators" style="height: 300px; overflow-y: auto;">
						<?php
							for ( $c = 0; $c < count( $operators ); ++$c )
							{
								$operator = $operators[$c] ;
								
								$you_indication = ( $opinfo["opID"] == $operator["opID"] ) ? " (You)" : "" ;
								$online_indication = ( $operator["status"] ) ? " <img src='../themes/$opinfo[theme]/online_green.png' width=12 height=12 border=0 alt='online' title='online'> " : " <img src='../themes/$opinfo[theme]/online_grey.png' width=12 height=12 border=0 alt='offline' title='offline'> " ;

								$profile_pic_url = Util_Upload_GetLogo( "profile", $operator["opID"] ) ;
								$operator_info = "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=\"$profile_pic_url\" width=\"55\" height=\"55\" border=0 class=\"profile_pic_img\"></td><td style=\"padding-left: 5px;\">&nbsp; $online_indication &nbsp; $operator[name]$you_indication</td></tr></table>" ;

								print "<div style=\"margin-bottom: 10px;\" class=\"info_neutral\">$operator_info</div>" ;
							}
						?>
					</div>
				</td>
			</tr>
			</table>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>
