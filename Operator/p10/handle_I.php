<?php

	$user = $this->get_user($args[0]);
	$chan = $this->get_channel($args[3]);
	$this->report_event('INVITE', $user, $args[2], $chan);

?>