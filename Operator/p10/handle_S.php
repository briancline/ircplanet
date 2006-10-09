<?php

	$uplink = $this->get_server($uplink);
	$server = $this->get_server($numeric);
	$this->report_event('SERVER', $uplink, $server, '('. $server->get_desc() .')');

?>