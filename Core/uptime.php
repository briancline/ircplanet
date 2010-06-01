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

	function get_uptime_info()
	{
		$info = array();
		$secs = time() - START_TIME;
		$pretty_uptime = '';
		$mins = 0;
		$hours = 0;
		$days = 0;
		
		while ($secs >= 86400) {
			$secs -= 86400;
			$days++;
		}
		while ($secs >= 3600) {
			$secs -= 3600;
			$hours++;
		}
		while ($secs >= 60) {
			$secs -= 60;
			$mins++;
		}
		

		if ($days > 0) {
			$pretty_uptime .= $days .' day';
			if ($days > 1)
				$pretty_uptime .= 's';
		}
		if ($hours > 0) {
			if (!empty($pretty_uptime))
				$pretty_uptime .= ', ';
			
			$pretty_uptime .= $hours .' hour';
			if ($hours > 1)
				$pretty_uptime .= 's';
		}
		if ($mins > 0) {
			if (!empty($pretty_uptime))
				$pretty_uptime .= ', ';
			
			$pretty_uptime .= $mins .' min';
			if ($mins > 1)
				$pretty_uptime .= 's';
		}
		if ($secs > 0) {
			if (!empty($pretty_uptime))
				$pretty_uptime .= ', ';
			
			$pretty_uptime .= $secs .' sec';
			if ($secs > 1)
				$pretty_uptime .= 's';
		}
		
		$short_uptime = sprintf("%d days, %d:%02d:%02d", $days, $hours, $mins, $secs);
		
		$info = array(
			'days' => $days,
			'hours' => $hours,
			'mins' => $mins,
			'secs' => $secs,
			'pretty' => $pretty_uptime,
			'stats' => $short_uptime);
		
		return $info;
	}


