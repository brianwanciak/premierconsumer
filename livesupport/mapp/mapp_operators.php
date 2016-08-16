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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$error = "" ;

	$operators = Ops_get_AllOps( $dbh ) ;
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
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/modernizr.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	"use strict" ;
	var st_op2op ;

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;

		populate_ops_op2op() ;
		st_op2op = setInterval(function(){ populate_ops_op2op() ; }, 25000) ;

		reset_mapp_div_height() ;
		$('#canned_wrapper').show() ;
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
					ops_string += "<table cellspacing=0 cellpadding=2 border=0 width=\"100%\"><tr><td colspan=2><div class=\"chat_info_td_t\" style=\"margin-bottom: 1px;\">"+json_data.departments[c]["name"]+"</div></td></tr>" ;
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
							ops_string += "<tr><td width\"16px;\"><div class=\""+td_div+"\"><span class=\"chat_info_td_traffic\" style=\"\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/"+status_bullet+"\" width=\"12\" height=\"12\" border=\"0\"></td><td width=\"100%\"><span id=\""+id+"\"><b>(You)</b> are "+status+chatting_with+"</span></span></div></td></tr>" ;
						else
							ops_string += "<tr><td width\"16px;\"><div class=\""+td_div+"\"><span class=\"chat_info_td_traffic\" style=\"\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/"+status_bullet+"\" width=\"12\" height=\"12\" border=\"0\"></td><td width=\"100%\">"+button+"<span id=\""+id+"\">"+json_data.departments[c].operators[c2]["name"]+" is "+status+chatting_with+"</span></span></div></td></tr>" ;
					}
					ops_string += "</table>" ;
				}
				$('#canned_container').html( ops_string ) ;
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
			else { do_alert( 0, "Error requesting operator chat.  Please reload the console and try again." ) ; }
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Error requesting operator chat.  Please reload the console and try again." ) ;
		} });
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
