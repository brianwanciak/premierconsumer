<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;

	$theme = $CONF["THEME"] ;

	$requestinfo = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;
	$operator = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
	$department = Depts_get_DeptInfo( $dbh, $deptid ) ;

	if ( isset( $department["lang"] ) && $department["lang"] )
		$CONF["lang"] = $department["lang"] ;
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;

	$output = UtilChat_ExportChat( "$ces.txt" ) ;
	if ( count( $output ) <= 0 )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;

		$transcript = Chat_ext_get_Transcript( $dbh, $ces ) ;
		$output[] = $transcript["formatted"] ;
		$output[] = $transcript["plain"] ;
	}

	if ( isset( $output[0] ) )
		$output[0] = preg_replace( "/\"/", "&quot;", $output[0] ) ;

	$dept_emo = ( isset( $VALS["EMOS"] ) ) ? unserialize( $VALS["EMOS"] ) : Array() ;
	$addon_emo = 0 ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) )
	{
		if ( isset( $dept_emo[$deptid] ) && $dept_emo[$deptid] ) { $addon_emo = 1 ; }
		else if ( isset( $dept_emo[0] ) && $dept_emo[0] ) { $addon_emo = 1 ; }
	}
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Print Chat Transcript </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>"> 
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/initiate/transcript.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var view = 2 ; // flag used in global_chat.js for minor formatting of divs
	var base_url = ".." ;
	var widget ;

	var addon_emo = <?php echo $addon_emo ?> ;

	$(document).ready(function()
	{
		var transcript = init_timestamps( "<?php echo $output[0] ?>" ) ;
		$('#chat_transcript').html( transcript.emos() ) ;
		window.focus() ;
	});

	function do_print()
	{
		$('#chat_body').focus() ;
		window.print() ;
	}
//-->
</script>
</head>
<body id="chat_body" style="overflow: auto; padding: 0px;">
<div id="chat_options">
	<div style="margin-bottonm: 10px;" class="info_box">
		<div id="options_print" style="cursor: pointer; font-size: 16px; font-weight: bold;" onClick="do_print()"><img src="../themes/initiate/printer.png" width="16" height="16" border="0" alt=""> <?php echo $LANG["CHAT_PRINT"] ?></div>
	</div>
	<div class="cn">
		<?php echo $LANG["CHAT_CHAT_WITH"] ?> <span class="text_operator" style="font-weight: bold;"><?php echo $operator["name"] ?></span> - <span class="text_department" style="font-weight: bold;"><?php echo $department["name"] ?></span>
		<div style="margin-top: 5px;">Chat ID: <?php echo $ces ?></div>
	</div>
</div>
<div id="chat_transcript"></div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>