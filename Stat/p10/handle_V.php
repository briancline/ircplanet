<?php

	$user = $this->get_user($args[0]);
	$server = $this->get_server($args[2]);
	$this->report_event('VERSION', $user, $server);

?>