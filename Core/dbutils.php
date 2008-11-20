<?
	
	require_once( "core_globals.php" );
	
	function db_query( $query, $log = false, $fix_bad_connection = true )
	{
		$result = mysql_query( $query );
		$error = mysql_error();
		$error_no = mysql_errno();
		
		if( $log || !empty($error) )
		{
			$rows = mysql_affected_rows();
			debug( "DB> $query" );
			debug( "DB> $error [$error_no] ($rows affected)" );
		}
		
		if( $error_no == 2006 && $fix_bad_connection )
		{
			/**
			 * If our MySQL connection somehow craps out, attempt a graceful reconnect
			 * and try running the query again.
			 */
			foreach( $GLOBALS['INSTANTIATED_SERVICES'] as $service )
			{
				$service->db_connect();
				return db_query($query, $log, false);
			}
		}
		
		return $result;
	}
	
	function db_queryf($format)
	{
		$args = array();
		$format = addslashes($format);
		for($i = 1; $i < func_num_args(); ++$i)
			$args[] = addslashes(func_get_arg($i));
		
		$arglist = join("', '", $args);
		eval("\$query = sprintf('$format', '$arglist');");
		
		return db_query($query);
	}
	
	function db_date($ts = 0)
	{
		if($ts == 0)
			$ts = time();

		return date('Y-m-d H:i:s', $ts);
	}
	
?>
