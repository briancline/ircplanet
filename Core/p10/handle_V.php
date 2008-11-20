<?php

	$numeric = $args[0];
	
	$this->sendf( FMT_VERSION_REPLY, SERVER_NUM, $numeric, 
		"core-". CORE_VERSION, 
		SERVER_NAME, 
		SERVICE_VERSION );

?>