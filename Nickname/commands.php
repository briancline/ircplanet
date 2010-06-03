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
	$this->setCommandInfo('die',           1000,   0, false, '[reason]');

	$this->setCommandInfo('addadmin',       800,   2, false, '<user> <level>');
	$this->setCommandInfo('deladmin',       800,   1, false, '<user>');

	$this->setCommandInfo('addbad',         600,   1, false, '<account>');
	$this->setCommandInfo('drop',           600,   1, false, '<user>');
	$this->setCommandInfo('rembad',         600,   1, false, '<account>');

	$this->setCommandInfo('adminlist',      501,   0, false, '[search mask]');

	/**
	 * User-Level Commands (500 and below)
	 */
	$this->setCommandInfo('newpass',          1,   1, false, '<password>');
	$this->setCommandInfo('set',              1,   1, false, '<option> [value]');

	$this->setCommandInfo('ghost',            0,   0, false, '[nickname] [password]');
	$this->setCommandInfo('help',             0,   0, false, '[command]');
	$this->setCommandInfo('info',             0,   1, false, '<nickname>');
	$this->setCommandInfo('login',            0,   1, false, '[account] <password>');
	$this->setCommandInfo('register',         0,   2, false, '<password> <email>');
	$this->setCommandInfo('showcommands',     0,   0, false);
	$this->setCommandInfo('uptime',           0,   0, false);


