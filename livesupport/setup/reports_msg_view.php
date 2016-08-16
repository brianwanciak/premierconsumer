<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	$error = "" ;

	include_once( "$CONF[DOCUMENT_ROOT]/API/Messages/get.php" ) ;

	$messageid = Util_Format_Sanatize( Util_Format_GetVar( "messageid" ), "n" ) ;

	$message = Messages_get_MessageByID( $dbh, $messageid ) ;
	if ( isset( $message["messageID"] ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Messages/update.php" ) ;

		$deptinfo = Depts_get_DeptInfo( $dbh, $message["deptID"] ) ;

		$to = "$deptinfo[name] &lt;$deptinfo[email]&gt;" ;
		$subject = Util_Format_ConvertQuotes( $message["subject"] ) ;
		$created = date( "M j, Y (g:i a)", $message["created"] ) ;
		if ( preg_match( "/^http/", $message["onpage"] ) )
		{
			$onpage_snap = ( strlen( $message["onpage"] ) > 80 ) ? substr( $message["onpage"], 0, 40 ) . "..." . substr( $message["onpage"], -40, strlen( $message["onpage"] ) ) : $message["onpage"] ;
			$onpage = "<a href=\"$message[onpage]\" target=_blank alt=\"$message[onpage]\" title=\"$message[onpage]\">$onpage_snap</a>" ;
		}
		else
			$onpage = $message["onpage"] ;
		$message_body = preg_replace( "/(\r\n)|(\n)/", "", nl2br( Util_Format_ConvertQuotes( $message["message"] ) ) ) ;
	}
	else
	{
		if ( isset( $dbh ) && $dbh['con'] ) { database_mysql_close( $dbh ) ; }
		print "Invalid message ID." ;
	}
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> View Offline Message </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	$(document).ready(function()
	{
		$("body").css({'background': '#FAFAFA'}) ;

		var custom_raw = "<?php echo $message["custom"] ?>" ;
		custom_raw = custom_raw.split("-cus-") ;
		var custom_string = "<table cellspacing=0 cellpadding=2 border=0><tr><td nowrap colspan=2 style=\"padding-bottom: 10px;\"><div class=\"info_neutral\"><img src=\"../pics/icons/pin_note.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\"> Custom Variables</div></td></tr>" ;
		for ( var c = 0; c < custom_raw.length; ++c )
		{
			if ( custom_raw[c] != 0 )
			{
				var custom_val = custom_raw[c].split("-_-") ;
				if ( custom_val[1] )
				{
					var custom_value = decodeURIComponent( custom_val[1] ) ;
					if ( custom_value.match( /^http/ ) )
					{
						var custom_value_snap = ( custom_value.length > 60 ) ? custom_value.substring( 0, 30 ) + "..." + custom_value.substring( custom_value.length-30, custom_value.length ) : custom_value ;
						custom_string += "<tr><td nowrap style=\"padding: 2px;\"><b>"+encodeURIComponent( custom_val[0] )+"</b></td><td style=\"padding: 2px; padding-left: 5px;\"><div style=\"padding-top: 0px;\" class=\"chat_info_td\" title=\""+custom_value+"\" alt=\""+custom_value+"\"><a href=\""+custom_value+"\" target=_blank>"+custom_value_snap+"</a></div></td></tr>" ;
					}
					else
						custom_string += "<tr><td nowrap style=\"padding: 2px;\"><b>"+encodeURIComponent( custom_val[0] )+"</b></td><td style=\"padding: 2px; padding-left: 5px;\">"+decodeURIComponent( custom_val[1] )+"</td></tr>" ;
				}
			}
		}
		custom_string += "</table>" ;
		if ( custom_raw.length > 1 ) { $('#custom_variables').html( custom_string ).show() ; }
	});

	function delete_message()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( confirm( "Really delete this message?" ) )
		{
			$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "action=delete_message&ses=<?php echo $ses ?>&messageid=<?php echo $messageid ?>&unique="+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					window.opener.delete_message() ;
					window.close() ;
				}
				else
					do_alert( "Message could not be deleted." ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error deleting message.  Please reload the page and try again." ) ;
			} });
		}
	}
//-->
</script>
</head>
<body style="background: url( ../pics/bg_trans_white.png ) repeat;">
	<div style="padding: 25px; background: #FAFAFA;">
		<div id="div_message_body" class="round">
			<table cellspacing=2 cellpadding=2 border=0 width="100%">
			<tr>
				<td width="100" align="right"><div class="td_dept_td_blank round" style="font-weight: bold; font-size: 14px; background: #EAEAEA; padding: 4px; text-shadow: none;">Subject</div></td>
				<td><div class="td_dept_td_blank" style="padding: 4px; text-shadow: none;"><?php echo $subject ?></div></td>
				<td nowrap align="right" style=""><button type="button" onClick="window.close()">close</button></td>
			</tr>
			<tr>
				<td width="100" align="right"><div class="td_dept_td_blank round" style="font-weight: bold; font-size: 14px; background: #EAEAEA; padding: 4px; text-shadow: none;">From</div></td>
				<td><div class="td_dept_td_blank" style="padding: 4px; text-shadow: none;"><span id="msg_from_name"><?php echo $message["vname"] ?></span> &lt;<span id="msg_from_email"><a href="mailto:<?php echo $message["vemail"] ?>" target="_blank"><?php echo $message["vemail"] ?></a></span>&gt;</div></td>
			</tr>
			<tr>
				<td width="100" align="right"><div class="td_dept_td_blank round" style="font-weight: bold; font-size: 14px; background: #EAEAEA; padding: 4px; text-shadow: none;">To</div></td>
				<td><div class="td_dept_td_blank" id="msg_to" style="padding: 4px; text-shadow: none;"><?php echo $to ?></div></td>
			</tr>
			<tr>
				<td width="100" align="right"><div class="td_dept_td_blank round" style="font-weight: bold; font-size: 14px; background: #EAEAEA; padding: 4px; text-shadow: none;">On Page</div></td>
				<td><div class="td_dept_td_blank" id="msg_to" style="padding: 4px; text-shadow: none;"><?php echo $onpage ?></div></td>
			</tr>
			<tr>
				<td width="100" align="right"><div class="td_dept_td_blank round" style="font-weight: bold; font-size: 14px; background: #EAEAEA; padding: 4px; text-shadow: none;">Sent</div></td>
				<td><div class="td_dept_td_blank" id="msg_created" style="padding: 4px; text-shadow: none;"><?php echo $created ?></div></td>
			</tr>
			<tr>
				<td width="100" align="right" valign="top">
					<div class="td_dept_td_blank round" style="font-weight: bold; font-size: 14px; background: #EAEAEA; padding: 4px; text-shadow: none;">Message</div>
				</td>
				<td valign="top">
					<div class="td_dept_td_blank" style="padding: 4px; text-shadow: none;">
						<div id="msg_message" style="padding: 5px; height: 150px; overflow: auto; border: 1px dashed #A9A9AA;" class="round"><?php echo $message_body ?></div>
					</div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="">
					<div class="info_info" style="display: none; height: 70px; overflow: auto;" id="custom_variables"></div>
					<div style="margin-top: 15px; text-align: right;"><span onClick="delete_message()" style="cursor: pointer;"><img src="../pics/btn_delete.png" width="64" height="23" border="0" alt=""></span></div>
				</td>
			</tr>
			</table>
		</div>
	</div>
</body>
</html>
<?php if ( isset( $dbh ) && $dbh['con'] ) { database_mysql_close( $dbh ) ; } ?>