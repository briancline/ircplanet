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

	$option = strtoupper( $pargs[1] );

	if( $option == 'BAD' )
	{
		$n = 0;

		if( count($this->db_badchans) == 0 )
		{
			$bot->notice( $user, 'There are no badchan entries.' );
			return false;
		}

		$bot->noticef( $user, 'Bad Channel Words List:' );
		foreach( $this->db_badchans as $bad_key => $bad_name )
		{
			$bot->noticef( $user, '  %2d) %s', ++$n, $bad_name );
		}
	}
	elseif( $option == 'CLONES' )
	{
		$clones = array();
		foreach( $this->users as $tmp_numeric => $tmp_user )
		{
			if( !array_key_exists($tmp_user->get_ip(), $clones) )
				$clones[$tmp_user->get_ip()] = 0;

			$clones[$tmp_user->get_ip()]++;
		}

		if( count($clones) == 0 )
		{
			$bot->notice( $user, 'There are currently no clones.' );
			return false;
		}

		$n = 0;
		arsort( $clones );

		$bot->notice( $user, 'Currently connected clones:' );
		foreach( $clones as $ip => $count )
		{
			if( $count == 1 )
				continue;

			$bot->noticef( $user, '  %2d) %d clones from %s', ++$n, $count, $ip );
		}
	}
	elseif( $option == 'GLINES' )
	{
		if( count($this->glines) == 0 )
		{
			$bot->notice( $user, 'There are no g-lines.' );
			return false;
		}

		$n = 0;
		foreach( $this->glines as $gline_key => $gline )
		{
			$exp_date = date( 'D d M H:i:s Y', $gline->get_expire_ts() );

			$bot->noticef( $user, '  %2d) Mask:     %s', ++$n, $gline->get_mask() );
			$bot->noticef( $user, '       Reason:   %s', $gline->get_reason() );
			$bot->noticef( $user, '       Expires:  %s', $exp_date );
		}
	}
	elseif( $option == 'JUPES' )
	{
		if( count($this->jupes) == 0 )
		{
			$bot->notice( $user, 'There are no jupes.' );
			return false;
		}

		$n = 0;
		foreach( $this->jupes as $jupe_key => $jupe )
		{
			$exp_date = date( 'D d M H:i:s Y', $jupe->get_expire_ts() );

			$bot->noticef( $user, '  %2d) Server:   %s', ++$n, $jupe->get_server() );
			$bot->noticef( $user, '       Reason:   %s', $jupe->get_reason() );
			$bot->noticef( $user, '       Expires:  %s', $exp_date );
		}
	}
	elseif( $option == 'OPERS' )
	{
		$n = 0;

		foreach( $this->users as $tmp_numeric => $tmp_user )
		{
			if( !$tmp_user->is_oper() || $tmp_user->is_service() )
				continue;

			$bot->noticef( $user, '  %2d) %s', ++$n, $tmp_user->get_full_mask() );
		}

		$bot->noticef( $user, '%d oper(s) are online.', $n );
	}
	else
	{
		$bot->noticef( $user, '%s is not a valid option. Please use %sHELP SHOW%s to see valid options.',
			$option, BOLD_START, BOLD_END );
	}

?>
