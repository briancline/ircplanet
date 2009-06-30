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
	
	if(!$nick_change)
	{
		$gline_mask = '*@'. $user->get_ip();
		$gline_set = false;

		if( defined('BLACK_GLINE') && BLACK_GLINE == true && !$gline_set 
				&& $this->is_blacklisted_file($user->get_ip()) )
		{
			$this->perform_gline( $gline_mask, BLACK_DURATION, BLACK_REASON );
			$gline_set = true;
		}
		
		if( defined('TOR_GLINE') && TOR_GLINE == true && !$gline_set 
				&& $this->is_tor_host($user->get_ip()) )
		{
			$this->perform_gline( $gline_mask, TOR_DURATION, TOR_REASON );
			$gline_set = true;
		}
		
		if( defined('COMP_GLINE') && COMP_GLINE == true && !$gline_set
				&& $this->is_compromised_host($user->get_ip()) )
		{
			$this->perform_gline( $gline_mask, COMP_DURATION, COMP_REASON );
			$gline_set = true;
		}
	}

?>
