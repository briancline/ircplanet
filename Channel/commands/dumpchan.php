<?php
	
	$ckey = $pargs[1];
	
	if( array_key_exists($ckey, $this->channels) )
		print_array($this->channels[$ckey]);
	else
		$bot->notice( $user->numeric, "No such channel, fucker" );

	if( array_key_exists($ckey, $this->db_channels) )
		print_array($this->db_channels[$ckey]);

?>