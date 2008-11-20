<?php

	$user = $this->get_user($numeric);
	$this->report_event('PART', $user, join(", ", $channels));

?>