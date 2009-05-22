<?
/*
 * ircPlanet Services for ircu
 * Copyright (c) 2005 Brian Cline.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:

 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of ircPlanet nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without 
 *    specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */
	
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
		$args = func_get_args();
		$format = array_shift( $args );
		$query = vsprintf( $format, $args );
		
		return db_query($query);
	}
	

	function db_date($ts = 0)
	{
		if($ts == 0)
			$ts = time();

		return date('Y-m-d H:i:s', $ts);
	}
	

?>
