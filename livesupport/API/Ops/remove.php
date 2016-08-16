<?php
	if ( defined( 'API_Ops_remove' ) ) { return ; }
	define( 'API_Ops_remove', true ) ;

	FUNCTION Ops_remove_Op( &$dbh,
						$opid )
	{
		if ( $opid == "" )
			return false ;

		global $CONF ; global $VALS ;
		if ( !defined( 'API_Util_Vals' ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ; }

		LIST( $opid ) = database_mysql_quote( $dbh, $opid ) ;

		$query = "DELETE FROM p_canned WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_dept_ops WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_ext_ops WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_transcripts WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_notes WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_operators WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_opstatus_log WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_rstats_ops WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_rstats_log WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_req_log WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_op_vars WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;

		$op_sounds = isset( $VALS["op_sounds"] ) ? unserialize( $VALS["op_sounds"] ) : Array() ;
		if ( isset( $op_sounds[$opid] ) ) { unset( $op_sounds[$opid] ) ; Util_Vals_WriteToFile( "op_sounds", serialize( $op_sounds ) ) ; }

		$mapp_array = isset( $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
		if ( isset( $mapp_array[$opid] ) ) { unset( $mapp_array[$opid] ) ; Util_Vals_WriteToFile( "MAPP", serialize( $mapp_array ) ) ; }
		return true ;
	}

	FUNCTION Ops_remove_CleanStats( &$dbh )
	{
		$expired = time() - ( (60*60*24*365)*5 ) ;
		$query = "DELETE FROM p_opstatus_log WHERE created < $expired" ;
		database_mysql_query( $dbh, $query ) ;
	}

	FUNCTION Ops_remove_OpDept( &$dbh,
						$opid,
						$deptid )
	{
		if ( ( $opid == "" ) || ( $deptid == "" ) )
			return false ;

		LIST( $opid, $deptid ) = database_mysql_quote( $dbh, $opid, $deptid ) ;

		$query = "SELECT * FROM p_dept_ops WHERE deptID = '$deptid' AND opID = '$opid'" ;
		database_mysql_query( $dbh, $query ) ;
		$data = database_mysql_fetchrow( $dbh ) ;

		$query = "DELETE FROM p_dept_ops WHERE deptID = '$deptid' AND opID = '$opid'" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "UPDATE p_dept_ops SET display = display-1 WHERE deptID = '$deptid' AND display >= $data[display]" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "DELETE FROM p_canned WHERE deptID = '$deptid' AND opID = '$opid'" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>