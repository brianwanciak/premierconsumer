<?php
	if ( defined( 'API_Ops_update_itr' ) ) { return ; }
	define( 'API_Ops_update_itr', true ) ;

	FUNCTION Ops_update_itr_IdleOps( &$dbh )
	{
		global $CONF ; global $VALS ;
		global $VARS_EXPIRED_OPS ;
		$VARS_MOBILE_EXPIRED_OPS = ( isset( $VALS["MOBILE_EXPIRED_OPS"] ) ) ? $VALS["MOBILE_EXPIRED_OPS"] : 10 ;
		$now = time() ; $m = date( "m", $now ) ; $d = date( "j", $now ) ; $y = date( "Y", $now ) ; $hour_now = date( "G", $now ) ;
		$idle = $now - $VARS_EXPIRED_OPS ;
		$idle_mapp = $now - (60*60*$VARS_MOBILE_EXPIRED_OPS) ;
		$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;

		$query = "SELECT SQL_NO_CACHE opID, mapp FROM p_operators WHERE ( ( lastactive < $idle AND mapp = 0 ) OR ( lastactive < $idle_mapp AND mapp = 1 ) ) AND status = 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			if ( database_mysql_nresults( $dbh ) )
			{
				$operators = Array() ;
				while( $data = database_mysql_fetchrow( $dbh ) ) { $operators[] = $data ; }
				$query = "UPDATE p_operators SET status = 0 WHERE ( ( lastactive < $idle AND mapp = 0 ) OR ( lastactive < $idle_mapp AND mapp = 1 ) ) AND status = 1" ;
				database_mysql_query( $dbh, $query ) ;

				for( $c = 0; $c < count( $operators ); ++$c )
				{
					$operator = $operators[$c] ;
					$opid = $operator["opID"] ;

					$query = "INSERT INTO p_opstatus_log VALUES( $now, $opid, 0, 0 )" ;
					database_mysql_query( $dbh, $query ) ;
					$query = "UPDATE p_dept_ops SET status = 0 WHERE opID = $opid" ;
					database_mysql_query( $dbh, $query ) ;

					if ( $operator["mapp"] )
					{
						if ( is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ; }
						$query = "UPDATE p_operators SET ses = 'mapp_idle' WHERE opID = $opid" ;
						database_mysql_query( $dbh, $query ) ;

						if ( isset( $mapp_array[$opid] ) ) { $arn = $mapp_array[$opid]["a"] ; $platform = $mapp_array[$opid]["p"] ; }
						if ( isset( $arn ) && $arn )
						{
							include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;
							Util_MAPP_Publish( $opid, "new_text", $platform, $arn, "You are Offline.  Mobile app has not been accessed in $VARS_MOBILE_EXPIRED_OPS hours." ) ;
						}
					}
				}
			}
		}
		// process mapp auto offline since console not opened
		$dir_files = glob( $CONF["TYPE_IO_DIR"].'/*.mapp', GLOB_NOSORT ) ;
		$total_dir_files = count( $dir_files ) ;
		if ( $total_dir_files )
		{
			$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
			$auto_offline = ( isset( $VALS["AUTO_OFFLINE"] ) && $VALS["AUTO_OFFLINE"] ) ? unserialize( $VALS["AUTO_OFFLINE"] ) : Array() ;
			for ( $c = 0; $c < $total_dir_files; ++$c )
			{
				$opid = str_replace( "$CONF[TYPE_IO_DIR]", "", $dir_files[$c] ) ; $opid = preg_replace( "/[\\/]|(.mapp)/", "", $opid ) ;
				if ( $opid )
				{
					$query = "SELECT * FROM p_dept_ops WHERE opID = '$opid'" ;
					database_mysql_query( $dbh, $query ) ;
					$op_depts_status_hash = Array() ; $online_counter = $auto_offline_counter = 0 ;
					while ( $data = database_mysql_fetchrow( $dbh ) )
					{
						if ( $data["status"] ) { ++$online_counter ; }
						$op_depts_status_hash[$data["deptID"]] = $data["status"] ;
					}

					foreach ( $auto_offline as $deptid => $value )
					{
						if ( isset( $op_depts_status_hash[$deptid] ) && $op_depts_status_hash[$deptid] )
						{
							LIST( $offline_hour, $offline_min, $offline_duration, $offline_rewind ) = explode( ",", $value ) ;
							if ( $hour_now <= $offline_rewind ) { $offline_time_start = mktime( $offline_hour, $offline_min, 0, $m, $d-1, $y ) ; }
							else { $offline_time_start = mktime( $offline_hour, $offline_min, 0, $m, $d, $y ) ; }
							$offline_time_end = $offline_time_start + ( 60*60*$offline_duration ) ;

							if ( ( $now >= $offline_time_start ) && ( $now <= $offline_time_end ) )
							{
								++$auto_offline_counter ;
								$query = "UPDATE p_dept_ops SET status = 0 WHERE opID = '$opid' AND deptID = '$deptid'" ;
								database_mysql_query( $dbh, $query ) ;
							}
						}
					}
					if ( $auto_offline_counter && ( $online_counter == $auto_offline_counter ) )
					{
						include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;

						$query = "INSERT INTO p_opstatus_log VALUES( $now, $opid, 0, 0 )" ;
						database_mysql_query( $dbh, $query ) ;
						$query = "UPDATE p_operators SET status = 0 WHERE opID = '$opid'" ;
						database_mysql_query( $dbh, $query ) ;
						if ( is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ; }
						if ( isset( $mapp_array[$opid] ) ) { $arn = $mapp_array[$opid]["a"] ; $platform = $mapp_array[$opid]["p"] ; }
						if ( isset( $arn ) && $arn ) { Util_MAPP_Publish( $opid, "new_text", $platform, $arn, "You are Offline.  It is past regular chat support hours." ) ; }
					}
				}
			}
		} return true ;
	}
?>