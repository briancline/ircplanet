<?php
	
	/**
	 * Save channels and accounts
	 */
	foreach( $this->db_channels as $reg_key => $reg )
		$reg->save();
	
?>
