<?php

	if(!$target || !$target->is_bot())
		return false;
	
	$chan = $this->get_channel($chan_name);
	$reg = $this->get_channel_reg($chan_name);
	$adm_level = $this->get_admin_level($user);
	
	if($adm_level < 100 || !$reg || !$chan)
		return false;
	
	if($chan->is_on($target->get_numeric()))
	{
		$target->noticef($user, "I'm already on %s!", $chan->get_name());
		return false;
	}
	
	$target->join($chan->get_name());
	$this->op($chan->get_name(), $target->get_numeric());
	
?>