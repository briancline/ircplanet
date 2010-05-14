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


	/**
	 * At the end of any remote server's burst, check and see if:
	 *  1) We're using the oper service to set g-lines;
	 *  2) We have pending commands that are queued up for the oper service;
	 *  3) The oper service is now present.
	 * 
	 * If all three conditions are true, send all pending GLINE commands to
	 * the operator service. We wait until the end of burst acknowledgement
	 * as PRIVMSGs are not to be sent to any server while it is still in its
	 * burst stage.
	 */
	if( defined('OS_NICK') && defined('OS_GLINE') && OS_GLINE == true
			&& !empty($this->pending_commands) )
	{
		$oper_service = $this->get_user_by_nick( OS_NICK );

		if( !$oper_service )
		{
			// Oper service still not present on the network.
			return;
		}

		foreach( $this->pending_commands as $command )
		{
			$this->default_bot->message( $oper_service, $command );
		}

		$this->pending_commands = array();
	}
	


