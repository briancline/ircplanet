<?php
	
	/**
	 * Check for expired glines
	 */
	
	$expired_glines = array();

	foreach( $this->glines as $gline_key => $gline )
	{
		if( $gline->is_expired() )
		{
			debug( "*** Gline ". $gline->get_mask() ." has expired!" );
			$expired_glines[] = $gline_key;
		}
	}
	
	foreach( $expired_glines as $gline_key )
		$this->remove_gline( $gline_key );
	
?>