<?php
	
	db_queryf("update stats_channels set topic = '%s' where channel_name = '%s'",
		$chan->get_topic(),
		$chan->get_name()
	);

?>