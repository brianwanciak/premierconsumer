<?php
	$charset_string = ( database_mysql_old( $dbh ) ) ? "" : "CHARACTER SET utf8 COLLATE utf8_general_ci" ;

	/* auto patch of versions and needed modifications */
	if ( !is_file( "$CONF[CONF_ROOT]/patches/52" ) )
	{ $patched = 52 ; Util_Vals_WriteVersion( "4.2.99-4" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/53" ) )
	{ $patched = 53 ;
		$query = "ALTER TABLE p_requests ADD auto_pop TINYINT NOT NULL AFTER status" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footprints CHANGE ip ip VARCHAR( 45 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footprints_u CHANGE ip ip VARCHAR( 45 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_ips CHANGE ip ip VARCHAR( 45 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_refer CHANGE ip ip VARCHAR( 45 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_requests CHANGE ip ip VARCHAR( 45 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_req_log CHANGE ip ip VARCHAR( 45 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_transcripts CHANGE ip ip VARCHAR( 45 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footprints_u CHANGE agent agent VARCHAR( 255 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators ADD viewip TINYINT NOT NULL AFTER traffic" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh['error'] == "None" )
		{
			$query = "UPDATE p_operators SET viewip = 1" ;
			database_mysql_query( $dbh, $query ) ;
		}

		$query = "ALTER TABLE p_departments ADD remail TINYINT NOT NULL AFTER texpire" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh['error'] == "None" )
		{
			$query = "UPDATE p_departments SET remail = 1" ;
			database_mysql_query( $dbh, $query ) ;
		}

		Util_Vals_WriteVersion( "4.2.99-5" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/54" ) )
	{ $patched = 54 ; Util_Vals_WriteVersion( "4.2.99-6" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/55" ) )
	{ $patched = 55 ; Util_Vals_WriteVersion( "4.2.99-7" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/56" ) )
	{ $patched = 56 ;
		$query = "ALTER TABLE p_operators ADD sms INT UNSIGNED NOT NULL AFTER rating, ADD smsnum VARCHAR( 65 ) NOT NULL AFTER sms" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.2.99-8" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/57" ) )
	{ $patched = 57 ; Util_Vals_WriteVersion( "4.2.99-9" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/58" ) )
	{ $patched = 58 ; Util_Vals_WriteVersion( "4.2.100" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/59" ) )
	{ $patched = 59 ;
		if ( !isset( $CONF["SALT"] ) ) { Util_Vals_WriteToConfFile( "SALT", Util_Format_RandomString( 10 ) ) ; }

		$query = "ALTER TABLE p_operators DROP INDEX lastactive" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators DROP INDEX status" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators ADD INDEX ( status )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_requests DROP INDEX updated" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators ADD dn TINYINT( 1 ) UNSIGNED NOT NULL AFTER viewip" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh['error'] == "None" )
		{
			$query = "UPDATE p_operators SET dn = 1" ;
			database_mysql_query( $dbh, $query ) ;
		}

		$query = "ALTER TABLE p_operators CHANGE viewip viewip TINYINT( 1 ) UNSIGNED NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators CHANGE traffic traffic TINYINT( 1 ) UNSIGNED NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators CHANGE op2op op2op TINYINT( 1 ) UNSIGNED NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators CHANGE rate rate TINYINT( 1 ) UNSIGNED NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.2.101" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/60" ) )
	{ $patched = 60 ;
		$query = "ALTER TABLE p_req_log DROP INDEX created" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_req_log DROP INDEX deptID" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_req_log ADD INDEX ( created, deptID )" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.2.102" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/61" ) )
	{ $patched = 61 ; Util_Vals_WriteVersion( "4.2.103" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/62" ) )
	{ $patched = 62 ; Util_Vals_WriteVersion( "4.2.104" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/63" ) )
	{ $patched = 63 ;
		$query = "ALTER TABLE p_operators ADD sound2 VARCHAR( 15 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh["error"] == "None" )
		{
			$query = "UPDATE p_operators SET sound2 = 'default'" ;
			database_mysql_query( $dbh, $query ) ;
		}

		$query = "ALTER TABLE p_operators CHANGE sound sound1 VARCHAR( 15 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		// double check and drop sound to fix issue if system was repatched
		$query = "ALTER TABLE p_operators DROP sound" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "TRUNCATE TABLE p_reqstats" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_reqstats ADD requests_ INT( 10 ) UNSIGNED NOT NULL AFTER requests" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.2.105" ) ;

		if ( is_file( "$CONF[CONF_ROOT]/patches/VERSION.php" ) )
			unlink( "$CONF[CONF_ROOT]/patches/VERSION.php" ) ;

		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/64" ) )
	{ $patched = 64 ; Util_Vals_WriteVersion( "4.2.106" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/65" ) )
	{ $patched = 65 ; Util_Vals_WriteVersion( "4.2.107" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/66" ) )
	{ $patched = 66 ; Util_Vals_WriteVersion( "4.2.108" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/67" ) )
	{ $patched = 67 ;
		if ( database_mysql_old( $dbh ) )
		{
			$query = "ALTER TABLE p_requests ADD custom VARCHAR( 255 ) NOT NULL AFTER refer" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "ALTER TABLE p_req_log ADD custom VARCHAR( 255 ) NOT NULL AFTER title" ;
			database_mysql_query( $dbh, $query ) ;
		}
		else
		{
			$query = "ALTER TABLE p_requests ADD custom VARCHAR( 255 ) $charset_string NOT NULL AFTER refer" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "ALTER TABLE p_req_log ADD custom VARCHAR( 255 ) $charset_string NOT NULL AFTER title" ;
			database_mysql_query( $dbh, $query ) ;
		}

		$query = "ALTER TABLE p_departments DROP img_offline, DROP img_online" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.2.109" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/68" ) )
	{ $patched = 68 ; Util_Vals_WriteVersion( "4.2.110" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/69" ) )
	{ $patched = 69 ; Util_Vals_WriteVersion( "4.2.111" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/70" ) )
	{ $patched = 70 ; Util_Vals_WriteVersion( "4.2.112" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/71" ) )
	{ $patched = 71 ;
		$query = "SELECT * FROM p_vars LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;
		$data = database_mysql_fetchrow( $dbh ) ;

		if ( !isset( $data["code"] ) )
		{
			$query = "INSERT INTO p_vars VALUES( 0, 1 )" ;
			database_mysql_query( $dbh, $query ) ;
		}

		$query = "ALTER TABLE p_vars ADD ts_clean INT UNSIGNED NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.2.113" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/72" ) )
	{ $patched = 72 ; Util_Vals_WriteVersion( "4.2.114" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/73" ) )
	{ $patched = 73 ; Util_Vals_WriteVersion( "4.2.115" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/74" ) )
	{ $patched = 74 ; Util_Vals_WriteVersion( "4.2.116" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/75" ) )
	{ $patched = 75 ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

		if ( !isset( $VALS['OFFLINE'] ) ) { $VALS['OFFLINE'] = "" ; }
		Util_Vals_WriteToFile( "TRAFFIC_EXCLUDE_IPS", "" ) ;
		$VALS['TRAFFIC_EXCLUDE_IPS'] = "" ;

		$query = "ALTER TABLE p_requests ADD sim_ops VARCHAR( 155 ) NOT NULL AFTER hostname, ADD sim_ops_ VARCHAR( 155 ) NOT NULL AFTER sim_ops" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_req_log ADD sim_ops VARCHAR( 155 ) NOT NULL AFTER hostname" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_departments ADD rloop TINYINT UNSIGNED NOT NULL AFTER rtime, ADD savem TINYINT UNSIGNED NOT NULL AFTER rloop" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh["error"] == "None" )
		{
			$query = "UPDATE p_departments SET rloop = 1" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "UPDATE p_departments SET savem = 1" ;
			database_mysql_query( $dbh, $query ) ;
		}

		$query = "ALTER TABLE p_departments ADD custom VARCHAR( 255 ) $charset_string NOT NULL AFTER savem" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_vars ADD char_set VARCHAR( 155 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_admins DROP INDEX login" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "ALTER TABLE p_admins ADD UNIQUE (login)" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "DROP TABLE IF EXISTS p_messages" ;
		database_mysql_query( $dbh, $query ) ;
		
		$query = "CREATE TABLE IF NOT EXISTS p_messages ( messageID int(10) unsigned NOT NULL AUTO_INCREMENT, created int(10) unsigned NOT NULL, status tinyint(4) NOT NULL, chat tinyint(3) unsigned NOT NULL, locked int(11) NOT NULL, deptID int(10) unsigned NOT NULL, footprints int(10) unsigned NOT NULL, ip varchar(45) NOT NULL, vname varchar(80) NOT NULL, vemail varchar(160) NOT NULL, subject varchar(155) NOT NULL, agent varchar(255) NOT NULL, onpage varchar(255) NOT NULL, refer varchar(255) NOT NULL, message text $charset_string NOT NULL, PRIMARY KEY (messageID) )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_departments ADD smtp VARCHAR( 255 ) NOT NULL AFTER custom" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteToFile( "CHAT_SPAM_IPS", "" ) ;
		Util_Vals_WriteVersion( "4.3" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/76" ) )
	{ $patched = 76 ;
		$query = "UPDATE p_departments SET smtp = ''" ;
		database_mysql_query( $dbh, $query ) ;

		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/77" ) )
	{ $patched = 77 ;
		$query = "TRUNCATE TABLE p_marquees" ;
		database_mysql_query( $dbh, $query ) ;

		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/78" ) )
	{ $patched = 78 ;
		$query = "DROP TABLE IF EXISTS p_reqstats" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "CREATE TABLE IF NOT EXISTS p_rstats_depts ( sdate int(10) unsigned NOT NULL, deptID int(10) unsigned NOT NULL, requests int(10) NOT NULL, taken smallint(5) unsigned NOT NULL, declined smallint(5) unsigned NOT NULL, message smallint(5) unsigned NOT NULL, initiated smallint(5) unsigned NOT NULL, initiated_ smallint(5) unsigned NOT NULL, rateit smallint(5) unsigned NOT NULL, ratings smallint(5) unsigned NOT NULL, PRIMARY KEY (sdate,deptID) )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "CREATE TABLE IF NOT EXISTS p_rstats_ops ( sdate int(10) unsigned NOT NULL, opID int(10) unsigned NOT NULL, requests int(10) NOT NULL, taken smallint(5) unsigned NOT NULL, declined smallint(5) unsigned NOT NULL, message smallint(5) unsigned NOT NULL, initiated smallint(5) unsigned NOT NULL, initiated_ smallint(5) unsigned NOT NULL, rateit smallint(5) unsigned NOT NULL, ratings smallint(5) unsigned NOT NULL, PRIMARY KEY (sdate,opID) )" ;
		database_mysql_query( $dbh, $query ) ;

		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/79" ) )
	{ $patched = 79 ;
		$query = "ALTER TABLE p_req_log DROP etrans" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_requests DROP etrans" ;
		database_mysql_query( $dbh, $query ) ;

		// remove expired to limit query time on drop index
		$expired = time() - (60*60*24*$VARS_IP_LOG_EXPIRE) ;
		$query = "DELETE FROM p_ips WHERE created < $expired" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_ips DROP INDEX created" ;
		database_mysql_query( $dbh, $query ) ;

		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/80" ) )
	{ $patched = 80 ;
		if ( !isset( $CONF["API_KEY"] ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
			Util_Vals_WriteToConfFile( "API_KEY", Util_Format_RandomString( 10 ) ) ;
		}

		$query = "ALTER TABLE p_departments CHANGE smtp smtp TEXT NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.3.1" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/81" ) )
	{ $patched = 81 ;
		$query = "ALTER TABLE p_footprints_u ADD footprints INT UNSIGNED NOT NULL AFTER browser" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.3.2" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/82" ) )
	{ $patched = 82 ; Util_Vals_WriteVersion( "4.3.3" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	if ( !is_file( "$CONF[CONF_ROOT]/patches/83" ) )
	{ $patched = 83 ;
		$query = "ALTER TABLE p_departments ADD msg_busy TEXT $charset_string NOT NULL AFTER msg_offline" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.3.4" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/84" ) )
	{ $patched = 84 ;
		$query = "ALTER TABLE p_footprints_u ADD requests INT UNSIGNED NOT NULL AFTER footprints, ADD initiates INT UNSIGNED NOT NULL AFTER requests" ;
		database_mysql_query( $dbh, $query ) ;

		// reset the Ips table for optimization
		$query = "DROP TABLE IF EXISTS p_ips" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "CREATE TABLE IF NOT EXISTS p_ips ( ip varchar(32) NOT NULL, created int(10) unsigned NOT NULL, t_footprints int(10) unsigned NOT NULL, t_requests int(10) unsigned NOT NULL, t_initiate int(11) NOT NULL, i_footprints int(10) unsigned NOT NULL, i_timestamp int(10) unsigned NOT NULL, i_initiate int(10) unsigned NOT NULL, PRIMARY KEY (ip), KEY created (created) )" ;
		database_mysql_query( $dbh, $query ) ;
		// end reset

		Util_Vals_WriteVersion( "4.3.5" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	if ( !is_file( "$CONF[CONF_ROOT]/patches/85" ) )
	{ $patched = 85 ;
		$query = "ALTER TABLE p_requests ADD t_vses TINYINT( 2 ) UNSIGNED NOT NULL AFTER op2op" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_requests ADD agent_md5 VARCHAR( 32 ) NOT NULL AFTER agent, ADD INDEX ( agent_md5 )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "CREATE TABLE IF NOT EXISTS p_canned_auto ( opID int(10) unsigned NOT NULL, canID int(10) unsigned NOT NULL, PRIMARY KEY (opID) )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_messages ADD custom VARCHAR( 255 ) $charset_string NOT NULL AFTER refer" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators ADD curc TINYINT( 1 ) UNSIGNED NOT NULL AFTER dn , ADD maxc TINYINT( 1 ) NOT NULL AFTER curc" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh["error"] == "None" )
		{
			$query = "UPDATE p_operators SET maxc = -1" ;
			database_mysql_query( $dbh, $query ) ;
		}

		if ( isset( $CONF["SQLTYPE"] ) && !preg_match( "/(SQLi)|(PDO)/", $CONF["SQLTYPE"] ) ) { Util_Vals_WriteToConfFile( "SQLTYPE", "SQL.php" ) ; }
		if ( !isset( $CONF["SQLTYPE"] ) ) { Util_Vals_WriteToConfFile( "SQLTYPE", "SQL.php" ) ; }
		Util_Vals_WriteVersion( "4.3.6" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	} else { $patched = 85 ; }
	/* end auto patch area */
?>