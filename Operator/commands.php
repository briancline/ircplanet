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
	
	$this->setCommandInfo('die',           1000,   0, false, '[reason]');
	$this->setCommandInfo('quote',         1000,   1, false, '<stuff>');

	$this->setCommandInfo('adduser',        800,   2, false, '<account> <level>');
	$this->setCommandInfo('moduser',        800,   3, false, '<account> <setting> <param>');
	$this->setCommandInfo('remuser',        800,   1, false, '<account>');

	$this->setCommandInfo('broadcast',      700,   1, false, '<message>');
	$this->setCommandInfo('fakehost',       700,   2, false, '<nick> <hostname>');
	$this->setCommandInfo('jupe',           700,   3, false, '<server> <duration> <reason>');
	$this->setCommandInfo('settime',        700,   0, false);
	$this->setCommandInfo('unjupe',         700,   1, false, '<server>');

	$this->setCommandInfo('addbad',         500,   1, false, '<word>');
	$this->setCommandInfo('inviteme',       500,   0, false);
	$this->setCommandInfo('refreshg',       500,   0, false);
	$this->setCommandInfo('rembad',         500,   1, false, '<word>');

	$this->setCommandInfo('clearchan',      300,   1, false, '<options> [duration]');
	$this->setCommandInfo('deopall',        300,   1, false, '<channel>');
	$this->setCommandInfo('devoiceall',     300,   1, false, '<channel>');
	$this->setCommandInfo('kickall',        300,   2, false, '<channel> <reason>');
	$this->setCommandInfo('kickbanall',     300,   2, false, '<channel> <reason>');
	$this->setCommandInfo('moderate',       300,   1, false, '<channel>');
	$this->setCommandInfo('opall',          300,   1, false, '<channel>');
	$this->setCommandInfo('voiceall',       300,   1, false, '<channel>');
	
	$this->setCommandInfo('addgchan',       200,   3, false, '<channel> <duration> <reason>');
	$this->setCommandInfo('addgname',       200,   3, false, '<realname> <duration> <reason>');
	$this->setCommandInfo('ban',            200,   2, false, '<channel> <hostmask> [duration] [level] [reason]');
	$this->setCommandInfo('clearmodes',     200,   1, false, '<channel>');
	$this->setCommandInfo('gline',          200,   3, false, '<mask> <duration> <reason>');
	$this->setCommandInfo('invite',         200,   1, false, '<channel> [nick1 [nick2 ...]]');
	$this->setCommandInfo('kickban',        200,   2, false, '<channel> <nick|hostmask> [reason]');
	$this->setCommandInfo('mode',           200,   2, false, '<channel> <modes>');
	$this->setCommandInfo('remgchan',       200,   1, false, '<channel>');
	$this->setCommandInfo('remgline',       200,   1, false, '<mask>');
	$this->setCommandInfo('remgname',       200,   1, false, '<realname>');
	$this->setCommandInfo('unban',          200,   2, false, '<channel> <hostmask>');
	
	$this->setCommandInfo('deop',           100,   1, false, '<channel> [nick1 [nick2 ...]]');
	$this->setCommandInfo('devoice',        100,   1, false, '<channel> [nick1 [nick2 ...]]');
	$this->setCommandInfo('kick',           100,   2, false, '<channel> <nick> [reason]');
	$this->setCommandInfo('op',             100,   1, false, '<channel> [nick1 [nick2 ...]]');
	$this->setCommandInfo('topic',          100,   1, false, '<channel> [new topic]');
	$this->setCommandInfo('voice',          100,   1, false, '<channel> [nick1 [nick2 ...]]');

	$this->setCommandInfo('access',           0,   1, false, '<mask>');
	$this->setCommandInfo('banlist',          0,   1, false, '<channel> [mask]');
	$this->setCommandInfo('chaninfo',         0,   1, false, '<channel>');
	$this->setCommandInfo('chanlist',         0,   0, false, '[mask]');
	$this->setCommandInfo('help',             0,   0, false, '[command]');
	$this->setCommandInfo('opermsg',          0,   1, false, '<message>');
	$this->setCommandInfo('scan',             0,   1, false, '<mask>');
	$this->setCommandInfo('showcommands',     0,   0, false);
	$this->setCommandInfo('show',             0,   1, false, '<option>');
	$this->setCommandInfo('uptime',           0,   0, false);
	$this->setCommandInfo('whois',            0,   1, false, '<nick>');
	$this->setCommandInfo('whoison',          0,   1, false, '<channel>');


