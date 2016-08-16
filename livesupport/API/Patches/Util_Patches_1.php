<?php
	$charset_string = ( database_mysql_old( $dbh ) ) ? "" : "CHARACTER SET utf8 COLLATE utf8_general_ci" ;

	/* auto patch of versions and needed modifications */
	if ( !is_file( "$CONF[CONF_ROOT]/patches/1" ) )
	{ $patched = 1 ;
		Util_Vals_WriteVersion( "4.1.1" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/2" ) )
	{ $patched = 2 ;
		$query = "ALTER TABLE p_requests ADD etrans TINYINT( 1 ) NOT NULL AFTER status" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "ALTER TABLE p_req_log ADD etrans TINYINT( 1 ) NOT NULL AFTER status" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.1.2" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/3" ) )
	{ $patched = 3 ;
		$query = "ALTER TABLE p_requests ADD tupdated INT NOT NULL AFTER created" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.1.3" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/4" ) )
	{ $patched = 4 ;
		$query = "ALTER TABLE p_vars ADD sm_fb TEXT NOT NULL, ADD sm_tw TEXT NOT NULL, ADD sm_yt TEXT NOT NULL, ADD sm_li TEXT NOT NULL " ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.1.4" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/5" ) )
	{ $patched = 5 ;
		$query = "ALTER TABLE p_requests ADD initiated TINYINT( 1 ) NOT NULL AFTER status" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "ALTER TABLE p_req_log ADD initiated TINYINT( 1 ) NOT NULL AFTER status" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "ALTER TABLE p_footprints_u ADD chatting TINYINT( 1 ) NOT NULL AFTER marketID" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "ALTER TABLE p_reqstats ADD initiated_taken SMALLINT UNSIGNED NOT NULL AFTER initiated" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "ALTER TABLE p_transcripts ADD initiated TINYINT( 1 ) NOT NULL AFTER opID" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "ALTER TABLE p_footprints_u ADD agent VARCHAR( 200 ) NOT NULL AFTER hostname" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.1.54" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/6" ) )
	{ $patched = 6 ;
		// attempt to drop table to reset
		$query = "DROP TABLE IF EXISTS p_sm" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "CREATE TABLE IF NOT EXISTS p_sm ( deptID INT( 10 ) UNSIGNED NOT NULL , sm LONGTEXT NOT NULL , PRIMARY KEY ( deptID ) )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "SELECT * FROM p_vars LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;
		$data = database_mysql_fetchrow( $dbh ) ;

		if ( isset( $data["sm_fb"] ) || isset( $data["sm_tw"] ) || isset( $data["sm_yt"] ) || isset( $data["sm_li"] ) )
		{
			$sm_fb = ( isset( $data["sm_fb"] ) ) ? $data["sm_fb"] : "" ;
			$sm_tw = ( isset( $data["sm_tw"] ) ) ? $data["sm_tw"] : "" ;
			$sm_yt = ( isset( $data["sm_yt"] ) ) ? $data["sm_yt"] : "" ;
			$sm_li = ( isset( $data["sm_li"] ) ) ? $data["sm_li"] : "" ;

			$sm_string = "$sm_fb-sm-$sm_tw-sm-$sm_yt-sm-$sm_li" ;
			$query = "INSERT INTO p_sm VALUES( 0, '$sm_string' )" ;
			database_mysql_query( $dbh, $query ) ;
		}

		$query = "ALTER TABLE p_vars DROP sm_fb, DROP sm_tw, DROP sm_yt, DROP sm_li" ;
		database_mysql_query( $dbh, $query ) ;

		// patching from very beginning to now needs to include lang var
		if ( !isset( $CONF["lang"] ) )
		{
			$CONF["lang"] = "english" ;
			Util_Vals_WriteToConfFile( "lang",  "english" ) ;
		}

		Util_Vals_WriteVersion( "4.1.55" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/7" ) )
	{ $patched = 7 ; Util_Vals_WriteVersion( "4.1.56" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/8" ) )
	{ $patched = 8 ; Util_Vals_WriteVersion( "4.1.57" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/9" ) )
	{ $patched = 9 ;
		$query = "ALTER TABLE p_departments ADD lang VARCHAR( 15 ) NOT NULL AFTER rtime" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "UPDATE p_departments SET lang = '$CONF[lang]'" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.1.58" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/10" ) )
	{ $patched = 10 ; Util_Vals_WriteVersion( "4.1.59" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/11" ) )
	{ $patched = 11 ; Util_Vals_WriteVersion( "4.1.60" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/12" ) )
	{ $patched = 12 ; Util_Vals_WriteVersion( "4.1.61" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/13" ) )
	{ $patched = 13 ; Util_Vals_WriteVersion( "4.1.62" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/14" ) )
	{ $patched = 14 ;
		$query = "ALTER TABLE p_departments ADD temail TINYINT NOT NULL AFTER texpire" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_reqstats ADD rateit SMALLINT UNSIGNED NOT NULL , ADD ratings SMALLINT UNSIGNED NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators ADD ces VARCHAR( 32 ) NOT NULL AFTER ses, ADD rating TINYINT NOT NULL AFTER ces" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh["error"] == "None" )
		{
			$query = "UPDATE p_departments SET temail = 1" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "UPDATE p_departments SET msg_email = 'Hi %%visitor%%,\r\n\r\nThank you for taking the time to chat with us.  Here is the complete chat transcript for your reference:\r\n\r\n%%transcript%%\r\n\r\nThank you,\r\n%%operator%%\r\n%%op_email%%\r\n'" ;
			database_mysql_query( $dbh, $query ) ;

			$dates = Array() ;
			$query = "SELECT * FROM p_reqstats ORDER BY sdate ASC" ;
			database_mysql_query( $dbh, $query ) ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$dates[] = $data ;

			for( $c = 0; $c < count( $dates ); ++$c )
			{
				$date = $dates[$c] ;
				$deptid = $date["deptID"] ;
				$opid = $date["opID"] ;

				$stat_start = mktime( 0, 0, 1, date( "m", $date["sdate"] ), date( "j", $date["sdate"] ), date( "Y", $date["sdate"] ) ) ;
				$stat_end = mktime( 0, 0, 1, date( "m", $date["sdate"] ), date( "j", $date["sdate"] )+1, date( "Y", $date["sdate"] ) ) ; ;

				$ratings = Array() ;
				$query = "SELECT count(*) AS rateit, SUM(rating) AS ratings FROM p_transcripts WHERE deptID = $deptid AND opID = $opid AND opID <> 0 AND created >= $stat_start AND created < $stat_end AND rating <> 0" ;
				database_mysql_query( $dbh, $query ) ;
				while ( $data = database_mysql_fetchrow( $dbh ) )
					$ratings[] = $data ;

				for ( $c2 = 0; $c2 < count( $ratings ); ++$c2 )
				{
					$rating = $ratings[$c2] ;
					$query = "UPDATE p_reqstats SET rateit = $rating[rateit], ratings = $rating[ratings] WHERE sdate = $date[sdate] AND deptID = $deptid AND opID = $opid" ;
					database_mysql_query( $dbh, $query ) ;
				}
			}
		}

		Util_Vals_WriteVersion( "4.1.7" ) ;
		if ( is_file( "$CONF[CHAT_IO_DIR]/TIMESTAMP" ) )
			unlink( "$CONF[CHAT_IO_DIR]/TIMESTAMP" ) ;

		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/15" ) )
	{ $patched = 15 ; Util_Vals_WriteVersion( "4.1.72" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/16" ) )
	{ $patched = 16 ;
		$query = "ALTER TABLE p_footprints_u DROP INDEX created" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footprints_u ADD INDEX ( created , deptID )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footprints DROP INDEX mdfive" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footprints ADD INDEX ( mdfive )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_departments ADD temaild TINYINT NOT NULL AFTER temail" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_ips DROP INDEX created" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_ips ADD INDEX ( created )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_ips ADD i_footprints INT UNSIGNED NOT NULL AFTER t_initiate , ADD i_timestamp INT UNSIGNED NOT NULL AFTER i_footprints , ADD i_initiate INT UNSIGNED NOT NULL AFTER i_timestamp" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_dept_ops DROP INDEX display" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_dept_ops ADD INDEX ( display , visible , status )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_dept_ops ADD status TINYINT NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "TRUNCATE TABLE p_footstats" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footstats DROP INDEX sdate" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footstats ADD mdfive VARCHAR( 32 ) NOT NULL AFTER sdate" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footstats ADD PRIMARY KEY ( sdate , mdfive )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_refer DROP INDEX created" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_refer ADD INDEX ( created )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "TRUNCATE TABLE p_referstats" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_referstats DROP INDEX sdate" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_referstats ADD mdfive VARCHAR( 32 ) NOT NULL AFTER sdate" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_referstats ADD PRIMARY KEY ( sdate , mdfive )" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.1.8" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/17" ) )
	{ $patched = 17 ; Util_Vals_WriteVersion( "4.1.81" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/18" ) )
	{ $patched = 18 ; Util_Vals_WriteVersion( "4.1.82" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/19" ) )
	{ $patched = 19 ; Util_Vals_WriteVersion( "4.1.83" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/20" ) )
	{ $patched = 20 ; Util_Vals_WriteVersion( "4.1.84" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/21" ) )
	{ $patched = 21 ;
		$query = "ALTER TABLE p_requests CHANGE agent agent VARCHAR( 255 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "DROP TABLE IF EXISTS p_geo_bloc" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "CREATE TABLE IF NOT EXISTS p_geo_bloc ( startIpNum int(10) unsigned NOT NULL, endIpNum int(10) unsigned NOT NULL, locId int(10) unsigned NOT NULL, network mediumint(6) unsigned NOT NULL, PRIMARY KEY (endIpNum), KEY network (network) )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "DROP TABLE IF EXISTS p_geo_loc" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "CREATE TABLE IF NOT EXISTS p_geo_loc ( locId int(10) unsigned NOT NULL, country char(2) $charset_string NOT NULL, region char(42) $charset_string NOT NULL, city varchar(50) $charset_string DEFAULT NULL, latitude float DEFAULT NULL, longitude float DEFAULT NULL, PRIMARY KEY (locId) )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( !isset( $CONF["geo"] ) ) { Util_Vals_WriteToConfFile( "geo",  "" ) ; }

		$query = "ALTER TABLE p_footprints_u ADD country CHAR( 2 ) NOT NULL, ADD region CHAR( 42 ) NOT NULL, ADD city CHAR( 50 ) NOT NULL, ADD latitude FLOAT NOT NULL, ADD longitude FLOAT NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.2" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/22" ) )
	{ $patched = 22 ; Util_Vals_WriteVersion( "4.2.1" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/23" ) )
	{ $patched = 23 ; Util_Vals_WriteVersion( "4.2.2" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/24" ) )
	{ $patched = 24 ; Util_Vals_WriteVersion( "4.2.3" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/25" ) )
	{ $patched = 25 ; Util_Vals_WriteVersion( "4.2.4" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/26" ) )
	{ $patched = 26 ; Util_Vals_WriteVersion( "4.2.5" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/27" ) )
	{ $patched = 27 ; Util_Vals_WriteVersion( "4.2.6" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/28" ) )
	{ $patched = 28 ; Util_Vals_WriteVersion( "4.2.7" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/29" ) )
	{ $patched = 29 ; Util_Vals_WriteVersion( "4.2.8" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/30" ) )
	{ $patched = 30 ; Util_Vals_WriteVersion( "4.2.9" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/31" ) )
	{ $patched = 31 ; Util_Vals_WriteVersion( "4.2.11" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/32" ) )
	{ $patched = 32 ; Util_Vals_WriteVersion( "4.2.12" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/33" ) )
	{ $patched = 33 ; Util_Vals_WriteVersion( "4.2.13" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/34" ) )
	{ $patched = 34 ; Util_Vals_WriteVersion( "4.2.14" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/35" ) )
	{ $patched = 35 ;
		$query = "ALTER TABLE p_vars ADD position TINYINT( 1 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.2.15" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/36" ) )
	{ $patched = 36 ; Util_Vals_WriteVersion( "4.2.16" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/37" ) )
	{ $patched = 37 ; Util_Vals_WriteVersion( "4.2.17" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/38" ) )
	{ $patched = 38 ; Util_Vals_WriteVersion( "4.2.18" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/39" ) )
	{ $patched = 39 ; Util_Vals_WriteVersion( "4.2.19" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/40" ) )
	{ $patched = 40 ; Util_Vals_WriteVersion( "4.2.91" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/41" ) )
	{ $patched = 41 ; Util_Vals_WriteVersion( "4.2.92" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/42" ) )
	{ $patched = 42 ; Util_Vals_WriteVersion( "4.2.93" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/43" ) )
	{ $patched = 43 ; Util_Vals_WriteVersion( "4.2.94" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/44" ) )
	{ $patched = 44 ; Util_Vals_WriteVersion( "4.2.95" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/45" ) )
	{ $patched = 45 ; Util_Vals_WriteVersion( "4.2.96" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/46" ) )
	{ $patched = 46 ; Util_Vals_WriteVersion( "4.2.97" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/47" ) )
	{ $patched = 47 ;
		$query = "SELECT * FROM p_sm" ;
		database_mysql_query( $dbh, $query ) ;
		if ( isset( $dbh["result"] ) && $dbh["result"] )
		{
			$socials = Array() ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$socials[] = $data ;

			$query = "CREATE TABLE IF NOT EXISTS p_socials ( deptID int(10) unsigned NOT NULL, status tinyint(1) NOT NULL, social varchar(15) $charset_string NOT NULL, tooltip varchar(55) $charset_string NOT NULL, url varchar(255) $charset_string NOT NULL, UNIQUE KEY deptID (deptID,social) )" ;
			database_mysql_query( $dbh, $query ) ;

			for ( $c = 0; $c < count( $socials ); ++$c )
			{
				$sm = $socials[$c] ;

				$deptid = $sm["deptID"] ;
				$sm_fb_array = $sm_tw_array = $sm_yt_array = $sm_li_array = Array() ;
				LIST( $sm_fb, $sm_tw, $sm_yt, $sm_li ) = explode( "-sm-", $sm["sm"] ) ;
				$sm_fb_array = unserialize( $sm_fb ) ;
				$sm_tw_array = unserialize( $sm_tw ) ;
				$sm_yt_array = unserialize( $sm_yt ) ;
				$sm_li_array = unserialize( $sm_li ) ;

				LIST( $tooltip, $url ) = database_mysql_quote( $dbh, $sm_fb_array["tooltip"], $sm_fb_array["url"] ) ;
				if ( $url )
				{
					$query = "INSERT INTO p_socials VALUES( $deptid, $sm_fb_array[status], 'facebook', '$tooltip', '$url')" ;
					database_mysql_query( $dbh, $query ) ;
				}

				LIST( $tooltip, $url ) = database_mysql_quote( $dbh, $sm_tw_array["tooltip"], $sm_tw_array["url"] ) ;
				if ( $url )
				{
					$query = "INSERT INTO p_socials VALUES( $deptid, $sm_tw_array[status], 'twitter', '$tooltip', '$url')" ;
					database_mysql_query( $dbh, $query ) ;
				}

				LIST( $tooltip, $url ) = database_mysql_quote( $dbh, $sm_yt_array["tooltip"], $sm_yt_array["url"] ) ;
				if ( $url )
				{
					$query = "INSERT INTO p_socials VALUES( $deptid, $sm_yt_array[status], 'youtube', '$tooltip', '$url')" ;
					database_mysql_query( $dbh, $query ) ;
				}

				LIST( $tooltip, $url ) = database_mysql_quote( $dbh, $sm_li_array["tooltip"], $sm_li_array["url"] ) ;
				if ( $url )
				{
					$query = "INSERT INTO p_socials VALUES( $deptid, $sm_li_array[status], 'linkedin', '$tooltip', '$url')" ;
					database_mysql_query( $dbh, $query ) ;
				}
			}

			$query = "DROP TABLE p_sm" ;
			database_mysql_query( $dbh, $query ) ;
		}

		Util_Vals_WriteVersion( "4.2.98" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/48" ) )
	{ $patched = 48 ; Util_Vals_WriteVersion( "4.2.99" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/49" ) )
	{ $patched = 49 ; Util_Vals_WriteVersion( "4.2.99-1" ) ; touch( "$CONF[CONF_ROOT]/patches/$patched" ) ; }
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/50" ) )
	{ $patched = 50 ;
		$query = "ALTER TABLE p_operators ADD sound VARCHAR( 15 ) NOT NULL AFTER theme" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh["error"] == "None" )
		{
			$query = "UPDATE p_operators SET sound = 'default'" ;
			database_mysql_query( $dbh, $query ) ;
		}

		Util_Vals_WriteVersion( "4.2.99-2" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	}
	else if ( !is_file( "$CONF[CONF_ROOT]/patches/51" ) )
	{ $patched = 51 ;
		$query = "ALTER TABLE p_req_log CHANGE agent agent VARCHAR( 255 ) NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_canned CHANGE title title VARCHAR( 80 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_canned CHANGE message message MEDIUMTEXT $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_departments CHANGE name name VARCHAR( 80 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_departments CHANGE msg_greet msg_greet TEXT $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_departments CHANGE msg_offline msg_offline TEXT $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_departments CHANGE msg_email msg_email TEXT $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_external CHANGE name name VARCHAR( 40 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footprints CHANGE title title VARCHAR( 150 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_footprints_u CHANGE title title VARCHAR( 150 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_marketing CHANGE name name VARCHAR( 80 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_marquees CHANGE snapshot snapshot VARCHAR( 80 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_marquees CHANGE message message VARCHAR( 255 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_operators CHANGE name name VARCHAR( 80 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_requests CHANGE vname vname VARCHAR( 80 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_requests CHANGE title title VARCHAR( 150 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;
		
		$query = "ALTER TABLE p_requests CHANGE question question TEXT $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_req_log CHANGE vname vname VARCHAR( 40 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_req_log CHANGE title title VARCHAR( 150 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_req_log CHANGE question question TEXT $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_socials CHANGE tooltip tooltip VARCHAR( 80 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_transcripts CHANGE vname vname VARCHAR( 80 ) $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_transcripts CHANGE question question TEXT $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_transcripts CHANGE formatted formatted TEXT $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "ALTER TABLE p_transcripts CHANGE plain plain TEXT $charset_string NOT NULL" ;
		database_mysql_query( $dbh, $query ) ;

		Util_Vals_WriteVersion( "4.2.99-3" ) ;
		touch( "$CONF[CONF_ROOT]/patches/$patched" ) ;
	} else { $patched = 51 ; }
	/* end auto patch area */
?>