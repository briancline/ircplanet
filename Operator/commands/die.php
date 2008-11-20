<?php
	
	if( $cmd_num_args == 0 )
		$reason = 'So long, and thanks for all the fish!';
	else
		$reason = assemble( $pargs, 1 );
	
	$this->close( $reason );

?>