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

	$option = strtoupper($pargs[1]);
	
	if ($option == 'ADMINS') {
		$search_mask = '';

		if ($cmd_num_args > 1)
			$search_mask = $pargs[2];
		
		$admins = array();

		$tmp_q = db_query('
				select cs_admins.user_id, accounts.name, cs_admins.level 
				from cs_admins 
				inner join accounts on accounts.account_id = cs_admins.user_id 
				order by level desc
		');
		while ($row = mysql_fetch_assoc($tmp_q)) {
			$tmp_account = $this->get_account($row['name']);
			if (!$tmp_account || (!empty($search_mask) && !fnmatch($search_mask, $tmp_account->get_name())))
				continue;

			$admins[$tmp_account->get_name()] = $row['level'];
		}
		mysql_free_result($tmp_q);


		$bot->noticef($user, '%s  %5s  %-15s  %-30s%s', 
			BOLD_START, 'Level', 'User Name', 'E-mail Address', BOLD_END);
		$bot->noticef($user, str_repeat('-', 56));

		foreach ($admins as $tmp_name => $tmp_level) {
			$tmp_account = $this->get_account($tmp_name);
			$bot->noticef($user, '  %5s  %-15s  %-30s', 
				$tmp_level, $tmp_account->get_name(), 
				$tmp_account->get_email());
		}
	}
	elseif ($option == 'BAD') {
		$bad_count = 0;
		
		$bot->noticef($user, 'Prohibited channel words: ');
		foreach ($this->db_badchans as $badchan) {
			$bot->noticef($user, ' %3d) %s', ++$bad_count, $badchan->get_mask());
		}
		
		$bot->noticef($user, 'Found %d prohibited channel words.', $bad_count);
	}

