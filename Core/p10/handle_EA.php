<?php
	
	$is_uplink = $args[0] == UPLINK_NUM;
	$this->service_postburst( $is_uplink );
	$this->finished_burst = true;
	
?>