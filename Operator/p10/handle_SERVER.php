<?php

	$server = $this->get_server($numeric);
	$self = $this->get_server(SERVER_NUM);
	$this->report_event('SERVER', $self, $server, '('. $server->get_desc() .')');

?>