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
	
	$user_name = $pargs[1];

	if( !($account = $this->get_account($user_name)) )
	{
		$bot->noticef( $user, "%s is not a registered nick.", $user_name );
		return false;
	}

	
	$instances = array();
	foreach($this->users as $numeric => $tmp_user)
	{
		if($tmp_user->get_account_id() == $account->get_id())
		{
			$instances[] = $tmp_user->get_full_mask();
		}
	}

	$is_admin = ($this->get_user_level($user) > 1);
	$privileged = $is_admin || $user->is_oper() || ($account->get_id() == $user->get_account_id());
	$logged_in = !empty( $instances );

	$bot->noticef( $user, 'Account information for %s%s%s', BOLD_START, $account->get_name(), BOLD_END );
	$bot->noticef( $user, str_repeat('-', 70) );

	if( $privileged && $logged_in )
	{
		$bot->noticef( $user, 'Logged In:    %s - %s', $logged_in ? 'Yes' : 'No ', $instances[0] );
		unset( $instances[0] );

		foreach($instances as $mask)
			$bot->noticef( $user, '                    %s', $mask );
	}
	else
	{
		$bot->noticef( $user, 'Logged In:    %s', $logged_in ? 'Yes' : 'No ' );
	}

	if( $privileged )
	{
		$bot->noticef( $user, 'E-mail Addr:  %s', $account->get_email() );
		$bot->noticef( $user, 'Enforcement:  %s       Auto Op: %s      Auto Voice: %s',
				$account->enforces_nick() ? 'Yes' : 'No',
				$account->auto_ops()      ? 'Yes' : 'No',
				$account->auto_voices()   ? 'Yes' : 'No'
		);
		$bot->noticef( $user, 'Suspended:    %s       Permanent: %s',
				$account->is_suspended()  ? 'Yes' : 'No',
				$account->is_permanent()  ? 'Yes' : 'No'
		);
	}
	
	if( $account->has_info_line() )
		$bot->noticef( $user, 'Info Line:    %s', $account->get_info_line() );
	
	$bot->noticef( $user, 'Registered:   %s', date('l j F Y h:i:s A T (\G\M\TO)', $account->get_register_ts()) );
	$bot->noticef( $user, 'Last Seen:    %s', date('l j F Y h:i:s A T (\G\M\TO)', $account->get_lastseen_ts()) );

?>
