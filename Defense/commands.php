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
	
	$this->set_command_info('die',           1000,   0, false, '[reason]');
	$this->set_command_info('quote',         1000,   1, false, '<stuff>');

	$this->set_command_info('adduser',        800,   2, false, '<account> <level>');
	$this->set_command_info('moduser',        800,   3, false, '<account> <setting> <param>');
	$this->set_command_info('remuser',        800,   1, false, '<account>');
	
	$this->set_command_info('access',         500,   1, '<mask>');
	$this->set_command_info('inviteme',       500,   0, false);
	$this->set_command_info('addwhite',       500,   1, false, '<hostmask>');
	$this->set_command_info('remwhite',       500,   1, false, '<hostmask>');
	$this->set_command_info('showwhite',      500,   0, false);

	$this->set_command_info('help',             0,   0, false, '[command]');
	$this->set_command_info('showcommands',     0,   0, false);
	$this->set_command_info('uptime',           0,   0, false);


