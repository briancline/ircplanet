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
	$value = '';
	
	if( $cmd_num_args > 1 )
		$value = assemble( $pargs, 2 );
	
	$account = $this->get_account( $user->get_account_name() );
	
	if( $option == 'EMAIL' )
	{
		if( strlen($value) >= MAXLEN_USEREMAIL )
		{
			$bot->notice( $user, 'Your email address is too long. Please try something shorter.' );
			return false;
		}
		
		$account->set_email( $value );
		$bot->notice( $user, 'Your e-mail address has been updated.' );
	}
	else if( $option == 'INFO' )
	{
		if( strlen($value) >= MAXLEN_INFOLINE )
		{
			$bot->notice( $user, 'Your infoline is too long. Please try something shorter.' );
			return false;
		}
		
		$account->set_info_line( $value );
		$bot->noticef( $user, 'Your info line has been %s.',
			empty($value) ? 'cleared' : 'updated' );
	}
	else if( $option == 'AUTOOP' )
	{
		if( empty($value) )
		{
			$value = !$account->auto_ops();
		}
		else
		{
			$value = strtoupper($value);
			if ( $value == 'ON' ) $value = true;
			else if( $value == 'OFF' ) $value = false;
			else {
				$bot->notice( $user, 'Value must either be ON or OFF.' );
				return false;
			}
		}
		
		$account->set_auto_op( $value );
		$bot->noticef( $user, 'Switched global auto op to %s.',
			$value ? 'ON' : 'OFF' );
	}
	else if( $option == 'AUTOVOICE' )
	{
		if( empty($value) )
		{
			$value = !$account->auto_voices();
		}
		else
		{
			$value = strtoupper($value);
			if ( $value == 'ON' ) $value = true;
			else if( $value == 'OFF' ) $value = false;
			else {
				$bot->notice( $user, 'Value must either be ON or OFF.' );
				return false;
			}
		}
		
		$account->set_auto_voice( $value );
		$bot->noticef( $user, 'Switched global auto voice to %s.',
			$value ? 'ON' : 'OFF' );
	}
	else if( $option == 'ENFORCE' )
	{
		if( empty($value) )
		{
			$value = !$account->enforces_nick();
		}
		else
		{
			$value = strtoupper($value);
			if ( $value == 'ON' ) $value = true;
			else if( $value == 'OFF' ) $value = false;
			else {
				$bot->notice( $user, 'Value must either be ON or OFF.' );
				return false;
			}
		}
		
		$account->set_enforce_nick( $value );
		$bot->noticef( $user, 'Toggled %s nickname enforcement.',
			$value ? 'ON' : 'OFF' );
	}
	else if( $option == 'PASSWORD' )
	{
		$bot->noticef( $user, 'Please use the %sNEWPASS%s command to change your password.',
			BOLD_START, BOLD_END );
		return false;
	}
	else
	{
		$bot->noticef( $user, '%s%s%s is not a valid option!',
			BOLD_START, $option, BOLD_END );
		return false;
	}
	
	$account->save();
	

