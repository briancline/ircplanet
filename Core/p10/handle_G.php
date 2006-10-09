<?php
	
	$ping_string = '';
	for( $i = 2; $i < count($args); ++$i )
		$ping_string .= ' '. $args[$i];
	
	$ping_string = trim( $ping_string );
	
	$this->sendf( FMT_PONG, SERVER_NUM, SERVER_NUM, $ping_string );

?>