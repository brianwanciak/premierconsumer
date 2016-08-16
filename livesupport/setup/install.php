<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	error_reporting(0) ;
	$pv = phpversion() ; if ( $pv >= "5.1.0" ){ date_default_timezone_set( "America/New_York" ) ; }
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_Vals.php" ) ;
	include_once( "../API/Util_Hash.php" ) ;

	$PHPLIVE_SERIES = "4" ; $PHPLIVE_VERSION = "4.4.99.91" ;
	$debug = 0 ; // dev purposes

	$query = $_SERVER["QUERY_STRING"] ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$e = Util_Format_Sanatize( Util_Format_GetVar( "e" ), "ln" ) ;

	if ( !$debug )
	{
		if ( is_file( "../web/config.php" ) )
		{ HEADER( "location: ../index.php?menu=sa" ) ; }
	}

	/***** PRE INSTALL CHECK OF PHP SETTINGS *****/
	// gather ini settings
	$ini_open_basedir = ini_get("open_basedir") ;
	$ini_safe_mode = ini_get("safe_mode") ;
	$safe_mode = preg_match( "/on/i", $ini_safe_mode ) ? 1 : 0 ;

	if ( function_exists( "mysql_get_client_info" ) ) { $mysql_version = mysql_get_client_info() ; }
	else if ( function_exists( "mysqli_get_client_info" ) ) { $mysql_version = mysqli_get_client_info() ; }
	else { $mysql_version = false ; }

	$php_version = PHP_VERSION ;
	/***** PRE INSTALL CHECK OF PHP SETTINGS *****/

	if ( $action == "create_dirs" )
	{
		if ( !is_dir( "../web/chat_initiate" ) )
			mkdir( "../web/chat_initiate", 0777 ) ;
		if ( !is_dir( "../web/chat_sessions" ) )
			mkdir( "../web/chat_sessions", 0777 ) ;
		if ( !is_dir( "../web/patches" ) )
			mkdir( "../web/patches", 0777 ) ;

		if ( ( $safe_mode || ( $ini_safe_mode == 1 ) ) && ( !is_dir( "../web/chat_sessions" ) || !is_writable( "../web/chat_sessions" ) ) )
			$json_data = "json_data = { \"status\": 0, \"error\": \"prep\" };" ;
		else if ( !is_writable( "../web/chat_sessions" ) || !is_dir( "../web/chat_initiate" ) || !is_dir( "../web/patches" ) )
			$json_data = "json_data = { \"status\": 0, \"error\": \"permissions\" };" ;
		else if ( !$mysql_version )
			$json_data = "json_data = { \"status\": 0, \"error\": \"mysql\" };" ;
		else
		{
			if ( !is_file( "../web/vals.php" ) )
			{
				$vals_string = "< php \$VALS = Array() ; \$VALS['CHAT_SPAM_IPS'] = \"\" ; \$VALS['TRAFFIC_EXCLUDE_IPS'] = \"\" ; ?>" ;
				$vals_string = preg_replace( "/< php/", "<?php", $vals_string ) ;

				$fp = fopen ("../web/vals.php", "w") ;
				fwrite( $fp, $vals_string, strlen( $vals_string ) ) ;
				fclose( $fp ) ;
			}
			$json_data = "json_data = { \"status\": 1 };" ;
		}

		print $json_data ;
		exit ;
	}
	else if ( $action == "install" )
	{
		$register = Util_Format_Sanatize( Util_Format_GetVar( "register" ), "n" ) ;
		$email = Util_Format_Sanatize( Util_Format_GetVar( "email" ), "e" ) ;
		$login = Util_Format_Sanatize( Util_Format_GetVar( "login" ), "ln" ) ;
		$password = Util_Format_Sanatize( Util_Format_GetVar( "password" ), "ln" ) ;
		$vpassword = Util_Format_Sanatize( Util_Format_GetVar( "vpassword" ), "ln" ) ;
		$base_url = Util_Format_Sanatize( Util_Format_GetVar( "base_url" ), "base_url" ) ;
		$document_root = Util_Format_Sanatize( Util_Format_GetVar( "document_root" ), "base_url" ) ;
		$db_type = Util_Format_Sanatize( Util_Format_GetVar( "db_type" ), "ln" ) ;
		$db_host = Util_Format_Sanatize( Util_Format_GetVar( "db_host" ), "ln" ) ;
		$db_name = Util_Format_Sanatize( Util_Format_GetVar( "db_name" ), "" ) ;
		$db_login = Util_Format_Sanatize( Util_Format_GetVar( "db_login" ), "" ) ;
		$db_password = Util_Format_Sanatize( preg_replace( "/[\'\"<>`]/", "", Util_Format_GetVar( "db_password" ) ), "" ) ;
		$timezone = Util_Format_Sanatize( Util_Format_GetVar( "timezone" ), "timezone" ) ;

		$str_len = strlen( $base_url ) ;
		$last = ( $str_len ) ? $base_url[$str_len-1] : "" ;
		if ( ( $last == "/" ) || ( $last == "\\" ) )
			$base_url = substr( $base_url, 0, $str_len - 1 ) ;
		$base_url = preg_replace( "/^(http:)/i", "", $base_url ) ;

		$str_len = strlen( $document_root ) ;
		$last = ( $str_len ) ? $document_root[$str_len-1] : "" ;
		if ( ( $last == "/" ) || ( $last == "\\" ) )
			$document_root = substr( $document_root, 0, $str_len - 1 ) ;

		$error = "" ;
		if ( !is_file( "$document_root/phplive.php" ) )
			$error = "Document Root is invalid. $document_root" ;
		else if ( !$db_host || !$db_name || !$db_login || !$db_password )
			$error = "Blank DB value is invalid." ;
		else if ( $vpassword != md5($password) )
			$error = "Setup Password and Verify Password does not match." ;
		else if ( ( $db_type == "mysql" ) && !function_exists('mysql_connect') )
			$error = "PHP MySQL extension is not enabled." ;
		else if ( ( $db_type == "mysqli" ) && !function_exists('mysqli_connect') )
			$error = "PHP MySQLi extension is not enabled." ;
		else if ( ( $db_type == "pdo" ) && !extension_loaded('pdo_mysql') )
			$error = "PHP PDO extension is not enabled." ;

		if ( !$error )
		{
			$CONF = Array() ;
			$CONF["SQLHOST"] = $db_host ;
			$CONF["SQLLOGIN"] = $db_login ;
			$CONF["SQLPASS"] = $db_password ;
			$CONF["DATABASE"] = $db_name ;

			if ( $db_type == "mysql" )
			{
				$sql_type = "SQL.php" ;
				$connection = mysql_connect( $CONF["SQLHOST"], $CONF["SQLLOGIN"], stripslashes( $CONF["SQLPASS"] ) ) ;
				if ( mysql_errno() ) { $error = "MySQL Host or login information is invalid." ; }
				else{
					mysql_select_db( $CONF["DATABASE"] ) ;
					if ( $result = mysql_query( "SHOW TABLES", $connection ) ){
						mysql_close( $connection ) ; unset( $connection ) ;
						include_once( "../API/SQL.php" ) ;
					}
					else{
						mysql_close( $connection ) ;
						$error = "MySQL database ($db_name) not found." ;
					}
				}
			}
			else if ( $db_type == "mysqli" )
			{
				$sql_type = "SQLi.php" ;
				$connection = new mysqli( $CONF["SQLHOST"], $CONF["SQLLOGIN"], stripslashes( $CONF["SQLPASS"] ) ) ;
				if ( $connection->connect_errno && is_numeric( $connection->connect_errno ) ) { $error = "MySQL Host or login information is invalid. [i]" ; }
				else{
					$connection->select_db( $CONF["DATABASE"] ) ;
					if ( $result = $connection->query("SHOW TABLES") ){
						mysqli_close( $connection ) ; unset( $connection ) ;
						include_once( "../API/SQLi.php" ) ;
					}
					else{
						mysqli_close($connection);
						$error = "MySQL database ($db_name) not found. [i]" ;
					}
				}
			}
			else
			{
				$sql_type = "PDO.php" ;
				try {
					$connection = new PDO( "mysql:host=$CONF[SQLHOST];dbname=$CONF[DATABASE];", $CONF["SQLLOGIN"], $CONF["SQLPASS"] ) ;
					$connection = null ; unset( $connection ) ;
					include_once( "../API/PDO.php" ) ;
				} catch ( PDOException $e ) {
					$error = "MySQL host, login, password or database name ($db_name) is invalid." ;
				}
			}

			if ( !$error )
			{
				include_once( "./KEY.php" ) ;

				$query_array = get_db_query() ;
				$errors = "" ;
				for ( $c = 0; $c < count( $query_array ); ++$c )
				{
					if ( $query_array[$c] )
					{
						database_mysql_query( $dbh, $query_array[$c] ) ;
						if ( !$dbh['ok'] )
							$errors .= $dbh['error'] ;
					}
				}

				if ( $errors )
					$error = $errors ;
				else
				{
					$conf_vars = "\$CONF = Array() ;\n" ;
					$conf_vars .= "\$CONF['DOCUMENT_ROOT'] = addslashes( '$document_root' ) ;\n" ;
					$conf_vars .= "\$CONF['BASE_URL'] = '$base_url' ;\n" ;
					$conf_vars .= "\$CONF['SQLTYPE'] = '$sql_type' ;\n" ;
					$conf_vars .= "\$CONF['SQLHOST'] = '$db_host' ;\n" ;
					$conf_vars .= "\$CONF['SQLLOGIN'] = '$db_login' ;\n" ;
					$conf_vars .= "\$CONF['SQLPASS'] = '$db_password' ;\n" ;
					$conf_vars .= "\$CONF['DATABASE'] = '$db_name' ;\n" ;
					$conf_vars .= "\$CONF['THEME'] = 'default' ;\n" ;
					$conf_vars .= "\$CONF['TIMEZONE'] = '$timezone' ;\n" ;
					$conf_vars .= "\$CONF['icon_online'] = '' ;\n" ;
					$conf_vars .= "\$CONF['icon_offline'] = '' ;\n" ;
					$conf_vars .= "\$CONF['lang'] = 'english' ;\n" ;
					$conf_vars .= "\$CONF['logo'] = '' ;\n" ;

					$conf_string = "< php\n	$conf_vars" ;
					$conf_string .= "	if ( phpversion() >= '5.1.0' ){ date_default_timezone_set( \$CONF['TIMEZONE'] ) ; }\n" ;
					$conf_string .= "	include_once( \"\$CONF[DOCUMENT_ROOT]/API/Util_Vars.php\" ) ;\n?>" ;
					$conf_string = preg_replace( "/< php/", "<?php", $conf_string ) ;

					$fp = fopen ("../web/config.php", "w") ;
					fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
					fclose( $fp ) ;

					if ( is_file( "../web/config.php" ) )
					{
						$now = time() ;
						LIST( $login, $password ) = database_mysql_quote( $dbh, $login, $password ) ;

						$query = "INSERT INTO p_admins VALUES(NULL, $now, 0, 0, '', '$login', '$password', '$email')" ;
						database_mysql_query( $dbh, $query ) ;

						$version_string = "< php \$VERSION = \"$PHPLIVE_SERIES\" ; ?>" ;
						$version_string = preg_replace( "/< php/", "<?php", $version_string ) ;
						$fp = fopen ("../web/VERSION.php", "w") ;
						fwrite( $fp, $version_string, strlen( $version_string ) ) ;
						fclose( $fp ) ;

						$base_url = urlencode( preg_replace( "/http/i", "hphp", $base_url ) ) ;
						$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
						$CONF = Array() ; $CONF["DOCUMENT_ROOT"] = $document_root ;
						LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
						if ( $register && function_exists( "allow_url_fopen" ) )
							$tags = get_meta_tags( "http://www.osicodesinc.com/stats/register.php?version=$PHPLIVE_VERSION&key=$KEY&base_url=$base_url&os=$os&browser=$browser&mysql=$mysql_version&php=$php_version&".time() ) ;
					}
					else { $error = "Could not create configuration file." ; }
				}
			}
		}

		if ( $error )
			$json_data = "json_data = { \"status\": 0, \"error\": \"$error\" };" ;
		else
			$json_data = "json_data = { \"status\": 1 };" ;

		print $json_data ;
		exit ;
	}

	$document_root = preg_replace( "/setup(.*?)/i", "", dirname(__FILE__) ) ; include_once( "./KEY.php" ) ; $timezones = Util_Hash_Timezones() ;

	$VERSION = "_install_$PHPLIVE_SERIES" ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support Installation </title>

<meta name="description" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var proto = location.protocol ;
	var execute ;
	var inputs = Array( "email", "login", "password", "vpassword", "base_url", "document_root", "db_host", "db_name", "db_login", "db_password" ) ;
	var inputs_test = Array( "db_host", "db_name", "db_login", "db_password" ) ;

	$(document).ready(function()
	{
		$("body").css({'background': '#FFFFFF'}) ;
		init_menu() ;
		create_dirs() ;

		$('#base_url').val( location.toString().replace( "setup/install.php", "" ) ) ;

		<?php if ( $e ): ?>
		if ( proto.match( /https:/i ) ) { $('#error_cert').show() ; }
		<?php endif ; ?>
	});

	function install()
	{
		var json_data = new Object ;
		var unique = unixtime() ;

		var email = encodeURIComponent( $('#email').val() ) ;
		var login = encodeURIComponent( $('#login').val() ) ;
		var password = phplive_md5( $('#password').val() ) ;
		var vpassword = phplive_md5( password ) ;
		var base_url = encodeURIComponent( $('#base_url').val().replace("http", "hphp") ) ;
		var document_root = encodeURIComponent( $('#document_root').val() ) ;
		var db_type = encodeURIComponent( $('#db_type').val() ) ;
		var db_host = encodeURIComponent( $('#db_host').val() ) ;
		var db_name = encodeURIComponent( $('#db_name').val() ) ;
		var db_login = encodeURIComponent( $('#db_login').val() ) ; $('#db_password').val( $('#db_password').val().replace(/[\'\"<>]/g, '') ) ;
		var db_password = encodeURIComponent( $('#db_password').val() ) ;
		var register = ( $('#register').is(':checked') ) ? 1 : 0 ;
		var timezone = $('#timezone').val() ;

		if ( !check_email( $('#email').val() ) )
			do_alert( 0, "Email format is invalid. (example: you@domain.com)" ) ;
		else if ( !login || ( password == "d41d8cd98f00b204e9800998ecf8427e" ) || !db_host || !db_name || !db_login || !db_password )
			do_alert( 0, "All input values must be provided." ) ;
		else if ( $('#password').val() != $('#vpassword').val() )
			do_alert( 0, "Setup Password and Verify Password does not match." ) ;
		else
		{
			$('#btn_install').html( "Installing..." ) ;
			$('#btn_install').attr('disabled', true) ;
			input_disable() ;

			$.ajax({
			type: "POST",
			url: "install.php",
			data: "action=install&email="+email+"&login="+login+"&password="+password+"&vpassword="+vpassword+"&base_url="+base_url+"&document_root="+document_root+"&db_type="+db_type+"&db_host="+db_host+"&db_name="+db_name+"&db_login="+db_login+"&db_password="+db_password+"&register="+register+"&timezone="+timezone+"&"+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					$('#btn_install').html( "Install Success" ) ;
					do_alert( 1, "Install Success" ) ;
					setTimeout( function(){ location.href = "../patch.php?menu=sa" ; }, 5000 ) ;
				}
				else
				{
					input_enable() ;
					$('#btn_install').html( "Click to Install" ) ;
					$('#btn_install').attr('disabled', false) ;
					do_alert_div( "..", 0, json_data.error ) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				location.href = "install.php?e="+xhr.responseText+"&"+unique ;
			} });
		}
	}

	function create_dirs()
	{
		var json_data = new Object ;
		var unique = unixtime() ;

		$.ajax({
		type: "POST",
		url: "install.php",
		data: "action=create_dirs&"+unique,
		success: function(data){
			eval( data ) ;

			if ( !json_data.status )
			{
				$('#pre_check').hide() ;
				$('#pre_install').show() ;

				if ( json_data.error == "prep" )
					location.href = "../README/PREP.html" ;
				else if ( json_data.error == "mysql" )
					$('#pre_errormysql').show() ;
				else
					$('#pre_errorbox').show() ;
			}
			else
				setTimeout( function(){ $('#pre_check').hide() ; next_step() ; }, 2000 ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			location.href = "install.php?e=2&"+unique ;
		} });
	}

	function next_step()
	{
		$('#pre_install').hide() ;
		$('#form_install').show() ;
	}

	function input_disable()
	{
		$( '*', '#form_install' ).each( function () {
			var div_name = this.id ;
			if ( $(this).is("input") )
				$(this).attr("disabled", true) ;
		}) ;
	}

	function input_enable()
	{
		$( '*', '#form_install' ).each( function () {
			var div_name = this.id ;
			if ( $(this).is("input") )
				$(this).attr("disabled", false) ;
		}) ;
	}

	function http_redirect()
	{
		var base_url = location.toString( ).replace("https:", "http:") ;
			
		location.href = base_url ;
	}

//-->
</script>
</head>
<body>

<div id="body" style="width: 970px; margin: 0 auto; margin-top: 15px;">
	<div id="pre_check" style="margin-top: 15px;">
		Checking directory permissions... <img src="../pics/loading_ci.gif" width="16" height="16" border="0" alt="">
	</div>

	<div id="pre_install" style="display: none;">
		<div id="pre_errorbox" style="display: none;">
			<div class="edit_title info_error">Error: Directory permissions.</div>
			<div style="margin-top: 25px;">Directory permission error.  Please refer to the <a href="http://www.phplivesupport.com/r.php?r=perm" target="new">directory permission documentation</a> to correct the issue.  Once completed, refresh this page to continue.</div>
		</div>
		<div id="pre_errormysql" style="display: none;">
			<div class="edit_title info_error">Error: MySQL support was not detected.</div>
			<div style="margin-top: 25px;">MySQL support was not detected.  Contact your server admin to enable MySQL support for PHP or perhaps check to be sure the MySQL server is running. Once completed, refresh this page to continue.</div>
		</div>
	</div>
	
	<form id="form_install" style="display: none; margin-top: 25px;">
	<input type="hidden" name="base_url" id="base_url" value="">
	<div style="padding: 10px;">
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td style="font-size: 20px;"><font color="3048A1">PHP</font> <font color="#29C029">Live!</font></td>
			<td style="padding-left: 25px;">
				<div style="font-size: 20px;">Installation</div>
				<div style="display: none; margin-top: 5px;" class="info_error" id="error_cert">Possible HTTPS cert error.  Try installing over <a href="JavaScript:void(0)" style="color: #FFFFFF;" onClick="http_redirect()">HTTP</a> protocol.</div>
			</td>
		</tr>
		</table>
	</div>

	<div style="margin-top: 5px;" class="info_info">
		<table cellspacing=0 cellpadding=0 border=0 width="100%">
		<tr>
			<td valign="top" width="45%" style="">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td colspan=2>
						<span class="edit_title">Create Setup Admin Account</span>
						<div style="margin-top: 5px;">The Setup Admin has access to all the setup features such as creating departments, creating operators, upload chat icons, view chat reports, etc.</div>
					</td>
				</tr>
				<?php if ( $pv >= "5.1.0" ): ?>
				<tr>
					<td class="td_dept_td_blank">Timezone</td>
					<td class="td_dept_td_blank">
						<select id="timezone" class="select">
						<?php
							for ( $c = 0; $c < count( $timezones ); ++$c )
							{
								$selected = "" ;
								if ( $timezones[$c] == date_default_timezone_get() )
									$selected = "selected" ;

								print "<option value=\"$timezones[$c]\" $selected>$timezones[$c]</option>" ;
							}
						?>
						</select>
					</td>
				</tr>
				<?php else: ?>
				<tr><td colspan="2"><input type="hidden" id="timezone" value="America/New_York"></td></tr>
				<?php endif ; ?>
				<tr>
					<td class="td_dept_td_blank" nowrap><div style="background: #7CBDCD; padding: 10px; border: 2px solid #77B6C5; color: #FFFFFF; text-shadow: none;" class="round">Setup Admin Login</div></td>
					<td class="td_dept_td_blank"><input type="text" class="input" size="25" maxlength="15" name="login" id="login" onKeyPress="return nospecials(event)" value=""><div style="font-size: 10px;">* letters and numbers only</div></td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap><div style="background: #7CBDCD; padding: 10px; border: 2px solid #77B6C5; color: #FFFFFF; text-shadow: none;" class="round">Setup Password</div></td>
					<td class="td_dept_td_blank"><input type="password" class="input" size="25" maxlength="50" name="password" id="password" value=""></td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap>Verify Setup Password</td>
					<td class="td_dept_td_blank"><input type="password" class="input" size="25" maxlength="50" name="vpassword" id="vpassword" value=""></td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap>Your Email</td>
					<td class="td_dept_td_blank"><input type="text" class="input" size="25" maxlength="50" name="email" id="email" onKeyPress="return justemails(event)" value=""><div style="font-size: 10px;"><span class="info_box">* for password recovery and system notifications</span></div></td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap>Document Root</td>
					<td class="td_dept_td_blank"><input type="text" class="input" size="25" maxlength="255" name="document_root" id="document_root" value="<?php echo $document_root ?>"><div style="font-size: 10px;"><span class="info_box">* do not modify the above value</span></div></td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap>License Key</td>
					<td class="td_dept_td_blank"><input type="text" class="input" size="25" maxlength="25" disabled="disabled" value="<?php echo $KEY ?>"></td>
				</tr>
				</table>
			</td>
			<td width="10%">&nbsp;</td>
			<td valign="top" width="45%" style="">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td colspan=2>
						<span class="edit_title">Database Settings</span>
						<div style="margin-top: 5px;">Contact your website admin to create a database for your PHP Live! system and provide the MySQL database information below.</div>
					</td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap>Connection Type</td>
					<td class="td_dept_td_blank"><select id="db_type" name="db_type" class="select"><option value="mysql">MySQL</option><option value="mysqli" selected>MySQLi</option><option value="pdo">PDO</option></select></td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap>Database Name</td>
					<td class="td_dept_td_blank"><input type="text" class="input" size="25" maxlength="35" name="db_name" id="db_name" value=""></td>
				</tr>
				<tr>
					<td class="td_dept_td_blank">&nbsp;</td>
					<td class="td_dept_td_blank">
						<div><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> The MySQL user (Database Login) should have the following privileges granted to the above Database: SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER</div>
					</td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap>Database Host</td>
					<td class="td_dept_td_blank"><input type="text" class="input" size="25" maxlength="55" name="db_host" id="db_host" value=""></td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap>Database Login</td>
					<td class="td_dept_td_blank"><input type="text" class="input" size="25" maxlength="35" name="db_login" id="db_login" value=""></td>
				</tr>
				<tr>
					<td class="td_dept_td_blank" nowrap>Database Password</td>
					<td class="td_dept_td_blank"><input type="password" class="input" size="25" maxlength="55" name="db_password" id="db_password" onKeyPress="return noquotestags(event)" value=""><div style="font-size: 10px;">* no quotes ('") or tags (&lt;&gt;) or slash (/\) characters</div></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="padding-top: 15px; padding-left: 15px;">
						<div id="div_alert" style="display: none; margin-bottom: 15px;"></div>
						<div style="font-size: 10px; margin-bottom: 15px;"><input type="checkbox" name="register" id="register" checked> Help improve the software by sending the installation information (PHP version, MySQL version, Server OS).</div>
						<div style=""><button type="button" id="btn_install" class="btn" onClick="install()">Click to Install</button></div>

						<div style="margin-top: 25px;">
							For installation assistance, please contact <a href="mailto:tech@osicodesinc.com?subject=Installation:+<?php echo $KEY ?>" target="_blank">tech@osicodesinc.com</a> or visit the <a href="http://www.phplivesupport.com/r.php?r=install" target="_blank">help desk</a> for more information.
						</div>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</div>
	</form>
</div>
<div style="padding: 25px;">
	<div style="padding-top: 25px; width: 970px; margin: 0 auto; font-size: 10px; text-shadow: 1px 1px #FFFFFF;">
		&copy; OSI Codes Inc. - powered by <a href="http://www.phplivesupport.com/?plk=osicodes-5-ykq-m&key=<?php echo $KEY ?>" target="new">PHP Live! Support</a>
	</div>
</div>

</body>
</html>

<?php
	function get_db_query()
	{
		$query = "DROP TABLE IF EXISTS p_admins; CREATE TABLE IF NOT EXISTS p_admins ( adminID int(10) unsigned NOT NULL AUTO_INCREMENT, created int(10) unsigned NOT NULL, lastactive int(10) unsigned NOT NULL, status tinyint(4) NOT NULL, ses varchar(32) NOT NULL, login varchar(15) NOT NULL, password varchar(32) NOT NULL, email varchar(160) NOT NULL, PRIMARY KEY (adminID), KEY ses (ses) ); DROP TABLE IF EXISTS p_canned; CREATE TABLE IF NOT EXISTS p_canned ( canID int(10) unsigned NOT NULL AUTO_INCREMENT, opID int(10) unsigned NOT NULL, deptID int(10) unsigned NOT NULL, title varchar(35) NOT NULL, message mediumtext NOT NULL, PRIMARY KEY (canID), KEY opID (opID), KEY deptID (deptID) ); DROP TABLE IF EXISTS p_departments; CREATE TABLE IF NOT EXISTS p_departments ( deptID int(10) unsigned NOT NULL AUTO_INCREMENT, visible tinyint(4) NOT NULL, queue tinyint(4) NOT NULL, tshare tinyint(4) NOT NULL, texpire int(10) unsigned NOT NULL, rtype tinyint(4) NOT NULL, rtime int(10) unsigned NOT NULL, img_offline varchar(50) NOT NULL, img_online varchar(50) NOT NULL, name varchar(40) NOT NULL, email varchar(160) NOT NULL, msg_greet text NOT NULL, msg_offline text NOT NULL, msg_email text NOT NULL, PRIMARY KEY (deptID) ); DROP TABLE IF EXISTS p_dept_ops; CREATE TABLE IF NOT EXISTS p_dept_ops ( deptID int(10) unsigned NOT NULL, opID int(10) unsigned NOT NULL, display tinyint(4) NOT NULL, visible tinyint(4) NOT NULL, PRIMARY KEY (deptID,opID) ); DROP TABLE IF EXISTS p_external; CREATE TABLE IF NOT EXISTS p_external ( extID int(10) unsigned NOT NULL AUTO_INCREMENT, name varchar(40) NOT NULL, url varchar(255) NOT NULL, PRIMARY KEY (extID) ); DROP TABLE IF EXISTS p_ext_ops; CREATE TABLE IF NOT EXISTS p_ext_ops ( extID int(10) NOT NULL, opID int(10) NOT NULL, UNIQUE KEY extID (extID,opID) ); DROP TABLE IF EXISTS p_footprints; CREATE TABLE IF NOT EXISTS p_footprints ( created int(10) unsigned NOT NULL, ip varchar(25) NOT NULL, os tinyint(1) NOT NULL, browser tinyint(1) NOT NULL, mdfive varchar(32) NOT NULL, onpage varchar(255) NOT NULL, title varchar(150) NOT NULL, KEY ip (ip), KEY created (created) ); DROP TABLE IF EXISTS p_footprints_u; CREATE TABLE IF NOT EXISTS p_footprints_u ( created int(10) unsigned NOT NULL, updated int(10) unsigned NOT NULL, deptID int(10) unsigned NOT NULL, marketID int(10) unsigned NOT NULL, os tinyint(1) NOT NULL, browser tinyint(1) NOT NULL, resolution varchar(15) NOT NULL, ip varchar(25) NOT NULL, hostname varchar(150) NOT NULL, onpage varchar(255) NOT NULL, title varchar(150) NOT NULL, refer varchar(255) NOT NULL, UNIQUE KEY ip (ip), KEY updated (updated) ); DROP TABLE IF EXISTS p_footstats; CREATE TABLE IF NOT EXISTS p_footstats ( sdate int(10) unsigned NOT NULL, total int(10) unsigned NOT NULL, onpage varchar(255) NOT NULL, KEY sdate (sdate) ); DROP TABLE IF EXISTS p_ips; CREATE TABLE IF NOT EXISTS p_ips ( ip varchar(25) NOT NULL, created int(10) unsigned NOT NULL, t_footprints int(10) unsigned NOT NULL, t_requests int(10) unsigned NOT NULL, t_initiate int(11) NOT NULL, PRIMARY KEY (ip) ); DROP TABLE IF EXISTS p_marketing; CREATE TABLE IF NOT EXISTS p_marketing ( marketID int(10) unsigned NOT NULL AUTO_INCREMENT, skey varchar(4) NOT NULL, name varchar(40) NOT NULL, color varchar(6) NOT NULL, PRIMARY KEY (marketID), KEY skey (skey) ); DROP TABLE IF EXISTS p_market_c; CREATE TABLE IF NOT EXISTS p_market_c ( sdate int(10) unsigned NOT NULL, marketID int(10) unsigned NOT NULL, clicks mediumint(8) unsigned NOT NULL, PRIMARY KEY (sdate,marketID) ); DROP TABLE IF EXISTS p_marquees; CREATE TABLE IF NOT EXISTS p_marquees ( marqID int(10) unsigned NOT NULL AUTO_INCREMENT, display tinyint(4) NOT NULL, deptID int(10) unsigned NOT NULL, snapshot varchar(35) NOT NULL, message varchar(255) NOT NULL, PRIMARY KEY (marqID) ); DROP TABLE IF EXISTS p_operators; CREATE TABLE IF NOT EXISTS p_operators ( opID int(10) unsigned NOT NULL AUTO_INCREMENT, lastactive int(10) unsigned NOT NULL, lastrequest int(11) unsigned NOT NULL, status tinyint(4) NOT NULL, signall tinyint(4) NOT NULL, rate tinyint(4) NOT NULL, op2op tinyint(4) NOT NULL, traffic tinyint(4) NOT NULL, ses varchar(32) NOT NULL, login varchar(15) NOT NULL, password varchar(32) NOT NULL, name varchar(40) NOT NULL, email varchar(160) NOT NULL, pic varchar(50) NOT NULL, theme varchar(15) NOT NULL, PRIMARY KEY (opID), KEY ses (ses), KEY lastactive (lastactive,status) ); DROP TABLE IF EXISTS p_opstatus_log; CREATE TABLE IF NOT EXISTS p_opstatus_log ( created int(11) NOT NULL, opID int(11) NOT NULL, status tinyint(4) NOT NULL, KEY created (created) ); DROP TABLE IF EXISTS p_refer; CREATE TABLE IF NOT EXISTS p_refer ( ip varchar(25) NOT NULL, created int(10) unsigned NOT NULL, marketID int(10) unsigned NOT NULL, mdfive varchar(32) NOT NULL, refer varchar(255) NOT NULL, KEY mdfive (mdfive), KEY ip (ip) ); DROP TABLE IF EXISTS p_referstats; CREATE TABLE IF NOT EXISTS p_referstats ( sdate int(10) unsigned NOT NULL, total int(10) unsigned NOT NULL, refer varchar(255) NOT NULL, KEY sdate (sdate) ); DROP TABLE IF EXISTS p_reqstats; CREATE TABLE IF NOT EXISTS p_reqstats ( sdate int(10) unsigned NOT NULL, deptID int(10) unsigned NOT NULL, opID int(10) unsigned NOT NULL, requests int(10) NOT NULL, taken smallint(5) unsigned NOT NULL, declined smallint(5) unsigned NOT NULL, message smallint(5) unsigned NOT NULL, initiated smallint(5) unsigned NOT NULL, PRIMARY KEY (sdate,deptID,opID) ); DROP TABLE IF EXISTS p_requests; CREATE TABLE IF NOT EXISTS p_requests ( requestID int(10) unsigned NOT NULL AUTO_INCREMENT, created int(10) unsigned NOT NULL, updated int(10) unsigned NOT NULL, vupdated int(10) unsigned NOT NULL, status tinyint(1) NOT NULL, deptID int(11) unsigned NOT NULL, opID int(11) unsigned NOT NULL, op2op int(10) unsigned NOT NULL, marketID int(10) NOT NULL, os tinyint(1) NOT NULL, browser tinyint(1) NOT NULL, requests int(10) unsigned NOT NULL, ces varchar(32) NOT NULL, resolution varchar(15) NOT NULL, vname varchar(40) NOT NULL, vemail varchar(160) NOT NULL, ip varchar(25) NOT NULL, hostname varchar(150) NOT NULL, agent varchar(200) NOT NULL, onpage varchar(255) NOT NULL, title varchar(150) NOT NULL, rstring varchar(255) NOT NULL, refer varchar(255) NOT NULL, question text NOT NULL, PRIMARY KEY (requestID), UNIQUE KEY ces (ces), KEY opID (opID), KEY op2op (op2op), KEY updated (updated), KEY status (status) ); DROP TABLE IF EXISTS p_req_log; CREATE TABLE IF NOT EXISTS p_req_log ( ces varchar(32) NOT NULL, created int(10) unsigned NOT NULL, ended int(10) unsigned NOT NULL, status tinyint(1) NOT NULL, deptID int(11) unsigned NOT NULL, opID int(11) unsigned NOT NULL, op2op int(11) NOT NULL, marketID int(10) NOT NULL, os tinyint(1) NOT NULL, browser tinyint(1) NOT NULL, resolution varchar(15) NOT NULL, vname varchar(40) NOT NULL, vemail varchar(160) NOT NULL, ip varchar(25) NOT NULL, hostname varchar(150) NOT NULL, agent varchar(200) NOT NULL, onpage varchar(255) NOT NULL, title varchar(150) NOT NULL, question text NOT NULL, PRIMARY KEY (ces), KEY opID (opID), KEY ip (ip) ); DROP TABLE IF EXISTS p_transcripts; CREATE TABLE IF NOT EXISTS p_transcripts ( ces varchar(32) NOT NULL, created int(11) unsigned NOT NULL, ended int(10) unsigned NOT NULL, deptID int(11) unsigned NOT NULL, opID int(11) unsigned NOT NULL, op2op tinyint(4) NOT NULL, rating tinyint(1) NOT NULL, fsize mediumint(9) NOT NULL, vname varchar(40) NOT NULL, vemail varchar(160) NOT NULL, ip varchar(25) NOT NULL, question text NOT NULL, formatted text NOT NULL, plain text NOT NULL, PRIMARY KEY (ces), KEY ip (ip), KEY created (created), KEY rating (rating), KEY opID (opID) ); DROP TABLE IF EXISTS p_vars; CREATE TABLE IF NOT EXISTS p_vars ( code varchar(10) NOT NULL ); DROP TABLE IF EXISTS p_rstats_depts; DROP TABLE IF EXISTS p_rstats_ops; DROP TABLE IF EXISTS p_dept_vars; DROP TABLE IF EXISTS p_op_vars;" ;

		$query_array = explode( ";", $query ) ;
		return $query_array ;
	}
?>
