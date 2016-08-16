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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/External/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

	$https = $error = "" ;
	if ( isset( $_SERVER["HTTP_CF_VISITOR"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_CF_VISITOR"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTP_X_FORWARDED_PROTO"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_X_FORWARDED_PROTO"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTPS"] ) && preg_match( "/(on)/i", $_SERVER["HTTPS"] ) ) { $https = "s" ; }

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$jump = ( Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) : "apis" ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;

	if ( $action == "submit" )
	{
		if ( $jump == "apis" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

			$error = ( Util_Vals_WriteToConfFile( "API_KEY", Util_Format_RandomString( 10 ) ) ) ? "" : "Could not write to config file." ;
		}
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/External/put.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/External/remove.php" ) ;

			$extid = Util_Format_Sanatize( Util_Format_GetVar( "extid" ), "n" ) ;
			$name = Util_Format_Sanatize( Util_Format_GetVar( "name" ), "ln" ) ;
			$url = Util_Format_Sanatize( Util_Format_GetVar( "url" ), "url" ) ;
			$opids = Util_Format_Sanatize( Util_Format_GetVar( "opids" ), "a" ) ;

			if ( $id = External_put_External( $dbh, $extid, $name, $url ) )
			{
				External_remove_AllExtOps( $dbh, $id ) ;
				for ( $c = 0; $c < count( $opids ); ++$c )
				{
					$opid = Util_Format_Sanatize( $opids[$c], "n" ) ;
					External_put_ExtOp( $dbh, $id, $opid ) ;
				}
			}
			else	
				$error = "Name ($name) is already in use." ;
		}
	}
	else if ( $action == "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/External/remove.php" ) ;

		$extid = Util_Format_Sanatize( Util_Format_GetVar( "extid" ), "n" ) ;
		External_remove_External( $dbh, $extid ) ;
		$jump = "external" ;
	}

	$departments = Depts_get_AllDepts( $dbh ) ;
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;

	$operators = Ops_get_AllOps( $dbh ) ;
	$externals = External_get_AllExternal( $dbh ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

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
	var ops = new Array() ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "extras" ) ;

		show_div( "<?php echo $jump ?>" ) ;
		switch_dept_api( 0 ) ;
		switch_geoip_api( "csv" ) ;

		<?php if ( ( $action == "submit" ) && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
	});

	function do_options( theoption, theextid, thename, theurl, theops )
	{
		if ( theoption == "edit" )
		{
			ops = theops.split( "," ) ;
			$( "input#extid" ).val( theextid ) ;
			$( "input#name" ).val( thename ) ;
			$( "input#url" ).val( theurl ) ;

			check_all( "undefined" ) ;
			for ( var c = 0; c < ops.length; ++c )
				$('#ck_op_'+ops[c]).attr( 'checked', true ) ;

			location.href = "#a_edit" ;
		}
		else if ( theoption == "delete" )
		{
			if ( confirm( "Delete this External URL?" ) )
				location.href = "extras.php?ses=<?php echo $ses ?>&action=delete&extid="+theextid ;
		}
	}

	function do_submit()
	{
		var name = $( "input#name" ).val() ;
		var url = $( "input#url" ).val() ;
		var flag = 0 ;

		$( '*', 'body' ).find('div').each( function () {
			var div_name = this.id ;
			if ( div_name.indexOf( "ck_op_" ) == 0 )
			{
				if ( $(this).attr( 'checked' ) )
					flag = 1 ;
			}
		}) ;

		if ( name == "" )
			do_alert( 0, "Please provide the external url name." ) ;
		else if ( url == "" )
			do_alert( 0, "Please provide the external url." ) ;
		else if ( !flag )
		{
			// let it pass for now to reset if only 1 operator assigned previously
			//do_alert( 0, "At least one operator should be checked." ) ;
			$('#theform').submit() ;
		}
		else
			$('#theform').submit() ;
	}

	function launch_external( theextid, theurl )
	{
		var unique = unixtime() ;
		window.open(theurl, unique, 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1') ;
	}

	function check_all( theobject )
	{
		if ( ( typeof( theobject ) != "undefined" ) && ( theobject.checked ) )
		{
			$( '#theform' ).find('*').each( function () {
				var div_name = this.id ;
				if ( div_name.indexOf( "ck_op_" ) == 0 )
					this.checked = true ;
			}) ;
		}
		else
		{
			$( '#theform' ).find('*').each( function () {
				var div_name = this.id ;
				if ( div_name.indexOf( "ck_op_" ) == 0 )
					this.checked = false ;
			}) ;
		}
	}

	function reset_check_all()
	{
		$('#ck_op_all').attr( 'checked', false ) ;
	}

	function switch_dept_api( thedeptid )
	{
		$('#api_status').val( "<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>/ajax/status.php?akey=<?php echo $CONF["API_KEY"] ?>&deptid="+thedeptid ) ;
	}

	function switch_geoip_api( theformat )
	{
		$('#api_geoip').val( "<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>/wapis/geoip.php?akey=<?php echo $CONF["API_KEY"] ?>&f="+theformat+"&ip=124.108.31.255" ) ;
	}

	function gen_new_key()
	{
		var unique = unixtime() ;

		if ( confirm( "You'll want to update your API URLs once a new key is generated.  Continue?" ) )
		{
			location.href = "extras.php?ses=<?php echo $ses ?>&action=submit&jump=apis&"+unique ;
		}
	}

//-->
</script>
<?php include_once( "./inc_header.php" ) ?>

		<?php include_once( "./inc_menu.php" ) ; ?>

		<div style="display: none; margin-top: 25px;" id="extras_external">
			<div>
				External URLs allow operators to easily access a webpage within the operator console.  URLs could be to "Client Search", "Trial Accounts", "Company Directory", "Order Search" or other helpful URLs.  The links will appear on the operator console footer menu.
			</div>

			<?php if ( count( $operators ) ): ?>
				<form>
				<table cellspacing=0 cellpadding=0 border=0 width="100%" style="margin-top: 25px;">
				<tr>
					<td width="40"><div class="td_dept_header">&nbsp;</div></td>
					<td><div class="td_dept_header">Name</div></td>
					<td width="100%"><div class="td_dept_header">URL</div></td>
				</tr>
				<?php
					for ( $c = 0; $c < count( $externals ); ++$c )
					{
						$external = $externals[$c] ;
						$ops = External_get_ExtOps( $dbh, $external["extID"] ) ;

						$ops_string = $ops_js_string = "" ;
						for ( $c2 = 0; $c2 < count( $ops ); ++$c2 )
						{
							$op = $ops[$c2] ;
							$ops_string .= " <div class=\"li_op round\">$op[name]</div>" ;
							$ops_js_string .= "$op[opID]," ;
						}
						$ops_js_string = substr_replace( $ops_js_string, "", -1 ) ;

						$edit_delete = "<div onClick=\"do_options( 'edit', $external[extID], '$external[name]', '$external[url]', '$ops_js_string' )\" style=\"cursor: pointer;\"><img src=\"../pics/btn_edit.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div><div onClick=\"do_options( 'delete', $external[extID], '$external[name]', '$external[url]', '$ops_js_string' )\" style=\"margin-top: 10px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

						$td1 = "td_dept_td" ;

						print "
							<tr>
								<td class=\"$td1\" nowrap>$edit_delete</td>
								<td class=\"$td1\" nowrap>$external[name]</td>
								<td class=\"$td1\">
									<div style=\"margin-bottom: 5px;\"><a href=\"JavaScript:void(0)\" onClick=\"launch_external( '$external[extID]', '$external[url]' )\">$external[url]</a></div>
									$ops_string
								</td>
							</tr>
						" ;
					}
					if ( $c == 0 )
						print "<tr><td colspan=7 class=\"td_dept_td\">Blank results.</td></tr>" ;
				?>
				</table>
				</form>

				<div style="padding: 5px; margin-top: 55px;">
					<a name="a_edit"></a><div class="edit_title">Create/Edit External URL <span class="txt_red"><?php echo $error ?></span></div>
					<div style="margin-top: 10px;">
						<form method="POST" action="extras.php?submit" id="theform">
						<input type="hidden" name="action" value="submit">
						<input type="hidden" name="jump" value="external">
						<input type="hidden" name="ses" value="<?php echo $ses ?>">
						<input type="hidden" name="extid" id="extid" value="0">
						<table cellspacing=0 cellpadding=5 border=0>
						<tr>
							<td>Name<br><input type="text" name="name" id="name" size="50" maxlength="15" value=""></td>
						</tr>
						<tr>
							<td>Target URL<br><input type="text" name="url" id="url" size="120" maxlength="255" value=""></td>
						</tr>
						<tr>
							<td>
								<div><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> It is recommended that each operator has max THREE external URLs to maintain proper operator console formatting.</div>
								<div style="margin-top: 15px;">Operator(s) who can access this external URL:</div>
								<div id="li_ops" style="margin-top: 5px;">
								<div class="li_op_focus round"><input type="checkbox" id="ck_op_all" name="opids[]" value="all" onClick="check_all(this)"> Check All</div>
								<?php
									for ( $c = 0; $c < count( $operators ); ++$c )
									{
										$operator = $operators[$c] ;

										if ( $operator["name"] != "Archive" )
											print "<div class=\"li_op round\"><input type=\"checkbox\" id=\"ck_op_$operator[opID]\" name=\"opids[]\" value=\"$operator[opID]\" onClick=\"reset_check_all()\"> $operator[name]</div>" ;
									}
								?>
								<div style="clear: both;"></div>
								</div>
							</td>
						</tr>
						<tr>
							<td> <div style="padding-top: 25px;"><input type="button" value="Submit" onClick="do_submit()" class="btn"> &nbsp; &nbsp; <input type="reset" value="Reset" onClick="$( 'input#extid' ).val(0)" class="btn"></div></td>
						</tr>
						</table>
						</form>
					</div>
				</div>
			<?php else: ?>
			<div style="margin-top: 15px;"><span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> An <a href="ops.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Operator</a> must be created to continue.</span></div>
			<?php endif ;?>
		</div>

		<div style="display: none; margin-top: 25px;" id="extras_apis">
			<div>
				The following HTTP APIs are for developers to aid with website or application integration.  The APIs will retrieve a specific response from the chat system.  Some coding knowledge is needed, such as <a href="http://php.net/manual/en/function.fopen.php" target="new">PHP fopen()</a> or <a href="http://php.net/manual/en/book.curl.php" target="new">PHP curl</a>.  Other languages, such as ASP or Java can utilize the HTTP APIs by simply calling the query URL and obtaining the output value.
			</div>

			<div style="margin-top: 25px;">
				<div class="info_info">
					<table cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td><div><span class="info_box" style="padding: 15px;">API Key: <input type="text" id="text_api_key" size="10" value="<?php echo $CONF["API_KEY"] ?>" onMouseDown="setTimeout(function(){ $('#text_api_key').select(); }, 200);" readonly></span></div></td>
						<td style="padding-left: 15px;"><div style="margin-top: 15px;"><input type="button" value="Generate New API Key" onClick="gen_new_key()"> HTTP APIs require an API Key (akey) for authentication.</div></td>
					</tr>
					</table>
				</div>

				<div style="margin-top: 25px;">
					<div>The following HTTP API URL returns a 1 (online) or 0 (offline) depending on the department chat availability status.</div>
					<div style="margin-top: 5px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td class="td_dept_td">
								<select name="deptid" id="deptid" style="width: 200px; font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept_api( this.value )">
								<option value="0">All Departments</option>
								<?php
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;
										if ( $department["name"] != "Archive" )
										{
											$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
											print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
										}
									}
								?>
								</select>
							</td>
							<td class="td_dept_td"><input type="text" class="input" style="background: #CEE0F4;" id="api_status" readonly size="90" value="" onMouseDown="setTimeout(function(){ $('#api_status').select(); }, 200);"></td>
						</tr>
						</table>
					</div>

					<div style="margin-top: 25px;">The following HTTP API URL will return the number of visitors currently on your website. (tracks pages that has the <a href="code.php?ses=<?php echo $ses ?>">Standard HTML Code</a>)</div>
					<div style="margin-top: 5px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td class="td_dept_td">
								<select style="width: 200px; font-size: 16px; background: #D4FFD4; color: #009000;">
								<option value="0">All Departments</option>
								</select>
							</td>
							<td class="td_dept_td"><input type="text" class="input" style="background: #CEE0F4;" id="api_traffic" readonly size="90" value="<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>/wapis/traffic.php?akey=<?php echo $CONF["API_KEY"] ?>" onMouseDown="setTimeout(function(){ $('#api_traffic').select(); }, 200);"></td>
						</tr>
						</table>
					</div>

					<div style="margin-top: 25px;">The following HTTP API URL will return the provided IP's GeoIP location.  Change the <i>ip=</i> to an IP of your choice.  The output will be in the format of:<br> <code>country abbreviation,country name,region,city,latitude,longitude</code></div>
					<div style="margin-top: 5px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<?php if ( $geoip ): ?>
						<tr>
							<td class="td_dept_td">
								<select name="f" id="f" style="width: 200px; font-size: 16px; background: #A3B0D0; color: #FFFFFF; border: 1px solid #6D7892;" OnChange="switch_geoip_api( this.value )">
								<option value="csv">Comma Seperated</option>
								<option value="json">Json Format</option>
								</select>
							</td>
							<td class="td_dept_td"><input type="text" class="input" style="background: #CEE0F4;" id="api_geoip" readonly size="90" value="" onMouseDown="setTimeout(function(){ $('#api_geoip').select(); }, 200);"></td>
						</tr>
						<?php else: ?>
						<tr><td class="td_dept_td" style="text-shadow: none;"><span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> <a href="extras_geo.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Enable GeoIP Addon</a> for the GeoIP API.</span></td></tr>
						<?php endif ; ?>
						</table>
					</div>

				</div>
			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ?>

