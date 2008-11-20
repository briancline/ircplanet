<?php

	$user = $this->get_user($numeric);
	$this->report_event('JOIN', $user, join(", ", $channels));

?>