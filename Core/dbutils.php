<?
	
	require_once( "core_globals.php" );
	
	function db_query( $query, $log = false, $fix_bad_connection = true )
	{
		$result = mysql_query( $query );
		$error = mysql_error();
		$error_no = mysql_errno();
		
		if( $log || !empty($error) )
		{
			debug( "DB> $query" );
			debug( "  > $error [$error_no]" );
		}
		
		if( $error_no == 2006 && $fix_bad_connection )
		{
			/**
			 * If our MySQL connection somehow craps out, attempt a graceful reconnect
			 * and try running the query again.
			 */
			foreach( $GLOBALS['INSTANTIATED_SERVICES'] as $service )
			{
				debug("*** Service: ". get_class($service) );
				$service->db_connect();
				return db_query($query, $log, false);
			}
		}
		
		return $result;
	}

?>