<?php
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
	
	function right( $str, $len )
	{
		return substr( $str, (-1 * $len) );
	}
	
	
	function is_valid_email( $email )
	{
		$b = eregi( '^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,4}$', $email );
		
		return $b;
	}
	
	
	function is_ip( $s )
	{
		return eregi( '^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$', $s );
	}


	function is_private_ip( $ip )
	{
		$private_ranges = array(
			'0.0.0.0/8',	  // Self-identification
			'1.0.0.0/8',      // IANA Unallocated
			'2.0.0.0/8',      // IANA Unallocated
			'5.0.0.0/8',      // IANA Unallocated
			'10.0.0.0/8',     // Private networks
			'127.0.0.0/8',    // Loopback
			'169.254.0.0/16', // DHCP Self-assignment
			'172.16.0.0/12',  // Private networks
			'192.168.0.0/16'  // Private networks
		);
		
		foreach( $private_ranges as $range )
		{
			list( $range_start, $range_mask ) = explode( '/', $range );
			$tmp_mask = 0xffffffff << ( 32 - $range_mask );
			$tmp_range_mask = ip2long( $range_start ) & $tmp_mask;
			$tmp_ip_mask = ip2long( $ip ) & $tmp_mask;

			if( $tmp_ip_mask == $tmp_range_mask )
				return true;
		}

		return false;
	}


	function fix_host_mask( $mask )
	{
		$ex_pos = strpos( $mask, '!' );
		$at_pos = strpos( $mask, '@' );
		$ident = substr( $mask, $ex_pos + 1, $at_pos - $ex_pos - 1 );
		
		if( strlen($ident) > IDENT_LEN )
		{
			$mask = substr($mask, 0, $ex_pos) .'!*'. right($ident, IDENT_LEN - 1) . substr($mask, $at_pos);
		}
		
		return $mask;
	}
	

	function line_num_args( $s )
	{
		$tokens = 1;
		$s = trim($s);
		$len = strlen( $s );
		
		if($len == 0)
			return 0;
		
		for( $i = 0; $i < strlen($s) - 1; ++$i )
		{
			if( $s[$i] == ' ' && $s[$i + 1] == ':' )
			{
				$tokens++;
				break;
			}
			else if( $s[$i] == ' ' )
			{
				$tokens++;
			}
		}
		
		return $tokens;
	}
	
	
	function line_get_args( $s, $stop_at_colon = true )
	{
		$start = 0;
		$tokens = array();
		$s = trim( $s );
		$len = strlen( $s );
		
		if( $len == 0 )
			return 0;
		
		for( $i = 0; $i < $len; ++$i )
		{
			if( $stop_at_colon && ($s[$i] == ' ' && $i < ($len - 1) && $s[$i + 1] == ':') )
			{
				$tokens[] = substr( $s, $start, $i - $start );
				$tokens[] = substr( $s, $i + 2 );
				break;
			}
			else if( $s[$i] == ' ' )
			{
				$tokens[] = substr( $s, $start, $i - $start );
				$start = $i + 1;
			}
			else if( $i == ($len - 1) )
			{
				$tokens[] = substr( $s, $start );
			}
		}
		
		return $tokens;
	}
	
	
	function get_pretty_size( $bytes )
	{
		$units = array( 'bytes', 'KB', 'MB', 'GB', 'TB', 'PB' );
		$precision = 2;
		
		for( $i = 0; $bytes >= 1024; ++$i )
			$bytes /= 1024;
		
		if( $i > 0 )
			$bytes = sprintf( '%0.'. $precision .'f', $bytes );
		
		return ($bytes .' '. $units[$i]);
	}
	
	
	/**
	 * irc_sprintf provides a cleaner way of sending services-specific data structures
	 * to sprintf without having to repeatedly provide long member function calls as 
	 * sprintf arguments. Since we almost always use the same member functions in
	 * most scenarios, irc_sprintf does a lot of legwork and makes for cleaner code.
	 *
	 * The custom flags that can be used with irc_sprintf follow:
	 *  %A    A space-delimited string representing all of an array's elements.
	 *        Designed for string or numeric arrays only.
	 *  
	 *  %H    Human-readable name of the referenced object.
	 *        For channels:  channel name.
	 *        For servers:   full server name.
	 *        For users:     nick name.
	 *        For bots:      nick name.
	 *        For glines:    the full mask of the gline.
	 *  
	 *  %C    Same as %H. Pneumonically represents channel names; provided as an extra
	 *        flag only for readability in longer format strings.
	 *  
	 *  %N    The ircu numeric of the referenced object.
	 *        For servers:   two-character server numeric (ex., Sc).
	 *        For users:     five-character server+user numeric (ex., ScAAA).
	 *        For bots:      five-character server+user numeric (ex., ScAAA).
	 *  
	 *  %U    The account name of the referenced object.
	 *        For users:     user's logged-in account name, if any.
	 *        For accounts:  account name.
	 *  
	 * Examples:
	 *    sprintf('%s', $user_obj->get_nick());    // Nick name
	 *    irc_sprintf('%H', $user_obj);            // Nick name
	 *    irc_sprintf('[%'#-13H]', $user_obj);     // Nick name, left-aligned in brackets
	 *                                                and padded with hash symbols
	 *  
	 * The following are totally equivalent; the latter saves much space and provides
	 * visual feedback as to what each argument corresponds to (numeric, channel, etc):
	 *    
	 *    sprintf('%s M %s +o %s %ld', $user_obj->get_numeric(), $chan_obj->get_name, 
	 *            $user2_obj->get_numeric(), time());
	 *    
	 *    irc_sprintf('%N M %C +o %N %ld', $user_obj, $chan_obj, $user2_obj, time() );
	 * 
	 */
	function irc_sprintf( $format )
	{
		$std_types = 'bcdeufFosxX';
		$custom_types = 'ACHNU';

		$args = func_get_args(); // Get array of all function arguments for vsprintf
		array_shift( $args );    // Pop the format argument from the top
		
		$len = strlen( $format );
		$arg_index = -1;
		$pct_count = 0;

		for( $i = 0; $i < $len - 1; $i++ )
		{
			$char = $format[$i];
			$next = $format[$i + 1];

			if( $char == '%' )
				$pct_count++;
			else
				$pct_count = 0;

			/**
			 * Skip this character if we don't have the start of a spec yet, or
			 * if we do and the following character is a '%', indicating that
			 * vsprintf will simply substitute a percent sign.
			 */
			if( $pct_count != 1 || $next == '%' )
				continue;

			// Found a spec; hold its place
			$spec_start = $i;
			$spec_end = $i + 1;
			$type = '';

			/**
			 * Loop through the characters immediately following so that we can
			 * attempt to find the type of spec this is. The formatting flags will
			 * be preserved, so we'll ignore them.
			 */
			for( $j = $i + 1; $j < $len - 1; $j++ )
			{
				$tmp_char = $format[$j];
				$is_std_type = ( false !== strpos($std_types, $tmp_char) );
				$is_cust_type = ( false !== strpos($custom_types, $tmp_char) );

				if( $is_std_type || $is_cust_type )
				{
					// Found a valid standard or custom flag, mark its place and stop
					$type = $tmp_char;
					$arg_index++;
					$spec_end = $j;
					break;
				}
			}

			// If we found a custom type in this spec, process it accordingly
			if( $is_cust_type )
			{
				$arg_obj = $args[$arg_index];
				$cust_text = '';

				switch( $type )
				{
					/**
					 * %A: Flat array to string conversion
					 */
					case 'A':
						$cust_text = implode( ' ', $arg_obj );
						break;


					/**
					 * %H: Human-readable name of given object
					 */
					case 'C':
					case 'H':
						if( is_user($arg_obj) )
							$cust_text = $arg_obj->get_nick();
						elseif( is_channel($arg_obj) || is_server($arg_obj) )
							$cust_text = $arg_obj->get_name();
						elseif( is_gline($arg_obj) )
							$cust_text = $arg_obj->get_mask();

						break;


					/**
					 * %N: ircu P10 numeric of given object
					 */
					case 'N':
						if( is_user($arg_obj) || is_server($arg_obj) )
							$cust_text = $arg_obj->get_numeric();

						break;


					/**
					 * %U: Account name of user or account object
					 */
					case 'U':
						if( is_user($arg_obj) )
							$cust_text = $arg_obj->get_account_name();
						elseif( is_account($arg_obj) )
							$cust_text = $arg_obj->get_name();

						break;


					/**
					 * I'm sorry, Dave, I'm afraid I can't do that.
					 * Will pass this unknown spec to vsprintf as-is.
					 */
					default:
						continue;
				}
				
				/**
				 * Change the custom flag to an 's' (string) and replace the argument
				 * with whatever string we determined was most appropriate for it.
				 */
				$format[$spec_end] = 's';
				$args[$arg_index] = $cust_text;

				/**
				 * No need to look at this entire spec anymore, so advance to the next
				 * char after the end of the spec.
				 */
				$i = $spec_end + 1;
			}
		}

		// vsprintf takes care of the standard flags.
		return vsprintf( $format, $args );
	}
	

	function random_kick_reason()
	{
		$ban_reasons = array(
			"Don't let the door hit you on the way out!",
			"Sorry to see you go... actually no, not really.",
			"This is your wake-up call...",
			"Behave yourself!",
			"Ooh, behave...",
			"Not today, child.",
			"All your base are belong to me",
			"Watch yourself!",
			"Better to remain silent and be thought a fool than to speak out and remove all doubt.",
			"kthxbye."
		);
		
		$index = rand(0, count($ban_reasons) - 1);
		return $ban_reasons[$index];
	}

	

?>
