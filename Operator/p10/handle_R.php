<?php
	
	$user = $this->get_user( $numeric );
	$server = $this->get_server( $args[3] );
	
	$this->report_event('STATS', $user, $flag, 'to', $server);

?>