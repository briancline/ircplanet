<?php

	foreach( $this->db_channels as $key => $dbchan )
	{
		$chan = $this->get_channel($key);
		db_query( "update channels set create_ts = '". $chan->get_ts() ."' where id = '". $dbchan->get_id() ."'" );
	}
	
?>