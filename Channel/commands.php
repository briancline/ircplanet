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
	 * Admin-Level Commands (501 and above)
	 */
	$this->setCommandInfo('dumpchan',       999,   1, false, '<channel>');
	$this->setCommandInfo('dumpuser',       999,   1, false, '<nick>');
	$this->setCommandInfo('dumpall',        999,   0, false);
	
	$this->setCommandInfo('die',            900,   0, false, '[reason]');

	$this->setCommandInfo('addadmin',       800,   2, false, '<user> <level>');
	$this->setCommandInfo('deladmin',       800,   1, false, '<user>');

	$this->setCommandInfo('addbad',         700,   1, false, '<channel mask>');
	$this->setCommandInfo('adminreg',       700,   2, false, '<channel> <owner> [purpose]');
	$this->setCommandInfo('delchan',        700,   1, false, '<channel> [reason]');
	$this->setCommandInfo('rembad',         700,   1, false, '<channel mask>');

	$this->setCommandInfo('show',           501,   1, false, '<option>');
	$this->setCommandInfo('reop',           501,   0, false);
	
	/**
	 * User-Level Commands (500 and below)
	 */
	$this->setCommandInfo('unreg',          500,   1, false, '<channel>');

	$this->setCommandInfo('set',            450,   1, false, '<channel> <option> [value]');
	$this->setCommandInfo('join',           450,   1, false, '<channel>');
	$this->setCommandInfo('kickall',        450,   2, false, '<channel> <reason>');
	$this->setCommandInfo('kickbanall',     450,   2, false, '<channel> <reason>');
	$this->setCommandInfo('part',           450,   1, false, '<channel>');
	
	$this->setCommandInfo('adduser',        400,   2, false, '<channel> <user> [level]');
	$this->setCommandInfo('moduser',        400,   3, false, '<channel> <user> <option> [value]');
	$this->setCommandInfo('remuser',        400,   2, false, '<channel> <user>');
	
	$this->setCommandInfo('clearmodes',     300,   1, false, '<channel>');
	$this->setCommandInfo('clearbans',      300,   1, false, '<channel>');
	$this->setCommandInfo('rdefmodes',      300,   1, false, '<channel>');
	$this->setCommandInfo('rdeftopic',      300,   1, false, '<channel>');
	
	$this->setCommandInfo('moderate',       200,   1, false, '<channel>');
	$this->setCommandInfo('opall',          200,   1, false, '<channel>');
	$this->setCommandInfo('deopall',        200,   1, false, '<channel>');
	$this->setCommandInfo('voiceall',       200,   1, false, '<channel>');
	$this->setCommandInfo('devoiceall',     200,   1, false, '<channel>');
	
	$this->setCommandInfo('mode',           100,   2, false, '<channel> <modes>');
	$this->setCommandInfo('op',             100,   1, false, '<channel> [nick1 [nick2 ...]]');
	$this->setCommandInfo('deop',           100,   1, false, '<channel> [nick1 [nick2 ...]]');
	$this->setCommandInfo('voice',          100,   1, false, '<channel> [nick1 [nick2 ...]]');
	$this->setCommandInfo('devoice',        100,   1, false, '<channel> [nick1 [nick2 ...]]');
	$this->setCommandInfo('invite',         100,   1, false, '<channel> [nick1 [nick2 ...]]');
	$this->setCommandInfo('say',            100,   1, false, '<channel> <text>');
	$this->setCommandInfo('do',             100,   1, false, '<channel> <action>');
	
	$this->setCommandInfo('kick',            75,   2, false, '<channel> <nick> [reason]');
	$this->setCommandInfo('kickban',         75,   2, false, '<channel> <nick|hostmask> [reason]');
	$this->setCommandInfo('ban',             75,   2, false, '<channel> <hostmask> [duration] [level] [reason]');
	$this->setCommandInfo('unban',           75,   2, false, '<channel> <hostmask>');
	$this->setCommandInfo('banlist',         75,   1, false, '<channel> [mask]');
	$this->setCommandInfo('topic',           75,   1, false, '<channel> [new topic]');
	
	$this->setCommandInfo('help',             0,   0, false, '[command]');
	$this->setCommandInfo('access',           0,   2, false, '<channel> <search mask>');
	$this->setCommandInfo('chaninfo',         0,   1, false, '<channel>');
	$this->setCommandInfo('register',         0,   2, false, '<channel> <purpose>');
	$this->setCommandInfo('showcommands',     0,   0, false);
	$this->setCommandInfo('uptime',           0,   0, false);
	$this->setCommandInfo('verify',           0,   1, false, '<nick>');


