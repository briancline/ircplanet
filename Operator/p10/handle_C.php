<?php

	$user = $this->get_user($numeric);
	$this->report_event('CREATE', $user, join(", ", $channels));

?>