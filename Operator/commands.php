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
	
	$this->set_command_info( 'die',           1000,   0, false, '[reason]' );
	$this->set_command_info( 'quote',         1000,   1, false, '<stuff>' );

	$this->set_command_info( 'adduser',        800,   2, false, '<account> <level>' );
	$this->set_command_info( 'moduser',        800,   3, false, '<account> <setting> <param>' );
	$this->set_command_info( 'remuser',        800,   1, false, '<account>' );

	$this->set_command_info( 'broadcast',      700,   1, false, '<message>' );
	$this->set_command_info( 'settime',        700,   0, false );

	$this->set_command_info( 'inviteme',       500,   0, false );
	$this->set_command_info( 'refreshg',       500,   0, false );
	
	$this->set_command_info( 'clearchan',      300,   1, false, '<options> [duration]' );
	$this->set_command_info( 'deopall',        300,   1, false, '<channel>' );
	$this->set_command_info( 'devoiceall',     300,   1, false, '<channel>' );
	$this->set_command_info( 'kickall',        300,   2, false, '<channel> <reason>' );
	$this->set_command_info( 'kickbanall',     300,   2, false, '<channel> <reason>' );
	$this->set_command_info( 'moderate',       300,   1, false, '<channel>' );
	$this->set_command_info( 'opall',          300,   1, false, '<channel>' );
	$this->set_command_info( 'voiceall',       300,   1, false, '<channel>' );
	
	$this->set_command_info( 'addgchan',       200,   3, false, '<channel> <duration> <reason>' );
	$this->set_command_info( 'addgname',       200,   3, false, '<realname> <duration> <reason>' );
	$this->set_command_info( 'ban',            200,   2, false, '<channel> <hostmask> [duration] [level] [reason]' );
	$this->set_command_info( 'clearmodes',     200,   1, false, '<channel>' );
	$this->set_command_info( 'gline',          200,   3, false, '<mask> <duration> <reason>' );
	$this->set_command_info( 'invite',         200,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'kickban',        200,   2, false, '<channel> <nick|hostmask> [reason]' );
	$this->set_command_info( 'mode',           200,   2, false, '<channel> <modes>' );
	$this->set_command_info( 'remgchan',       200,   1, false, '<channel>' );
	$this->set_command_info( 'remgline',       200,   1, false, '<mask>' );
	$this->set_command_info( 'remgname',       200,   1, false, '<realname>' );
	$this->set_command_info( 'unban',          200,   2, false, '<channel> <hostmask>' );
	
	$this->set_command_info( 'deop',           100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'devoice',        100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'kick',           100,   2, false, '<channel> <nick> [reason]' );
	$this->set_command_info( 'op',             100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'topic',          100,   1, false, '<channel> [new topic]' );
	$this->set_command_info( 'voice',          100,   1, false, '<channel> [nick1 [nick2 ...]]' );

	$this->set_command_info( 'access',           0,   1, false, '<mask>' );
	$this->set_command_info( 'banlist',          0,   1, false, '<channel> [mask]' );
	$this->set_command_info( 'chaninfo',         0,   1, false, '<channel>' );
	$this->set_command_info( 'chanlist',         0,   0, false, '[mask]' );
	$this->set_command_info( 'help',             0,   0, false, '[command]' );
	$this->set_command_info( 'opermsg',          0,   1, false, '<message>' );
	$this->set_command_info( 'scan',             0,   1, false, '<mask>' );
	$this->set_command_info( 'showcommands',     0,   0, false );
	$this->set_command_info( 'uptime',           0,   0, false );
	$this->set_command_info( 'whois',            0,   1, false, '<nick>' );
	$this->set_command_info( 'whoison',          0,   1, false, '<channel>' );

?>
