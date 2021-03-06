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

	$uid = $pargs[1];
	
	if (!($acct = $this->getAccount($uid))) {
		$bot->noticef($user, '%s is not a valid account name.', $uid);
		return false;
	}
	
	$aid = $acct->getId();
	
	$res = mysql_query("select * from accounts where id = '$aid'");
	$row = @mysql_fetch_assoc($res);
	
	$dbuser = new DB_User($aid);
	$dbuser->loadFromRow($row);
	
	$bot->noticef($user, '%d ?= %d', $dbuser->getId(), $aid);
	
	if ($dbuser->getId() != $aid) {
		$ac_key = strtolower($uid);
		unset($this->accounts[$ac_key]);
		$bot->noticef($user, 'Removed account id %d from memory.', $aid);
	}
	else {
		$ac_key = strtolower($dbuser->getName());
		$this->accounts[$ac_key] = $dbuser;
		$dbuser = $this->getAccount($uid);
		$bot->noticef($user, 'Updated account %s in memory. (%s)', $dbuser->getName(), $dbuser->password);
	}
	

