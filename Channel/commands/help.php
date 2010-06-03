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
	
	$help_topic = 'help';
	$help_level = 0;
	
	if ($cmd_num_args > 0) {
		$help_topic = assemble($pargs, 1);
		$help_topic_first = $pargs[1];
		$help_level = $this->getCommandLevel($help_topic_first);
	}
	
	$res = db_query("select text from help where service = 'CS' and topic = '$help_topic' and minlevel <= $user_level");
	if ($res && mysql_num_rows($res) > 0) {
		$row = mysql_fetch_assoc($res);
		$lines = explode("\n", $row['text']);
		$spacing = str_repeat(' ', 30 - strlen($help_topic));
		$help_syntax = $this->getCommandSyntax($help_topic);
		
		$bot->noticef($user, "%sHELP on %s %s %10s%s",
			BOLD_START, $help_topic, $spacing, 'Level '. $help_level, BOLD_END);
		$bot->noticef($user, "");
		
		if (!preg_match('/syntax:/i', $row['text'])) {
			$bot->noticef($user, "%sSyntax:%s %s %s", BOLD_START, BOLD_END, $help_topic, $help_syntax);
			$bot->noticef($user, "");
		}

		foreach ($lines as $line) {
			$line = str_replace("%N", $bot->getNick(), $line);
			$line = str_replace("%S", SERVER_NAME, $line);
			$line = str_replace("%B", BOLD_START, $line);

			while (preg_match('/(\%[A-Z_]+\%)/', $line, $regs)) {
				eval('$sub_val = '. str_replace('%', '', $regs[1]) .';');
				$line = str_replace($regs[1], $sub_val, $line);
			}

			$bot->notice($user, $line);
		}
	}
	else {
		$bot->noticef($user, "No help is available for %s%s%s.",
			BOLD_START, $help_topic, BOLD_END);
	}
	

