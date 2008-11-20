<?php

	$nick = $args[3];
	$source_num = $args[0];
	$source = $this->get_user($source_num);
	
	if(!($target = $this->get_user_by_nick($nick)))
	{
		$this->sendf(FMT_WHOIS_NOTFOUND, SERVER_NUM, $source_num, $nick);
		return false;
	}
	
	$t_num = $target->get_numeric();
	$t_host = 'localhost';
	$t_chans = '';
	
	if($target->is_service() || $source->is_oper() || $source == $target)
		$t_host = $target->get_host();
	
	if(!$target->is_service())
	{
		$chans = array();
		foreach($target->get_channel_list() as $chan_key)
		{
			$chan = $this->get_channel($chan_key);
			$prefix = '';
			
			if($chan->is_secret() && !$chan->is_on($source_num) && !$source->is_oper())
				continue;
			
			if($target->is_deaf())
				$prefix .= '-';
			if($chan->is_op($t_num))
				$prefix .= '@';
			else if($chan->is_voice($t_num))
				$prefix .= '+';
			
			$chans[] = $prefix . $chan->get_name(); 
		}
		
		$t_chans = join(' ', $chans);
	}
	
	
	$server = $this->get_server($target->get_server_numeric());
	
	$this->sendf(FMT_WHOIS_USER, SERVER_NUM, $source_num, 
		$target->get_nick(),
		$target->get_ident(),
		$t_host,
		$target->get_name());
	
	if(!empty($t_chans))
	{
		$this->sendf(FMT_WHOIS_CHANNELS, SERVER_NUM, $source_num,
			$target->get_nick(),
			$t_chans);
	}
	
	$this->sendf(FMT_WHOIS_SERVER, SERVER_NUM, $source_num,
		$target->get_nick(),
		$server->get_name(),
		$server->get_desc());
	
	if($target->is_local())
	{
		$this->sendf(FMT_WHOIS_IDLE, SERVER_NUM, $source_num,
			$target->get_nick(),
			$target->get_idle_time(),
			$target->get_signon_ts());
	}
	
	if($target->is_oper())
	{
		$this->sendf(FMT_WHOIS_OPER, SERVER_NUM, $source_num, 
			$target->get_nick());
	}
	
	$this->sendf(FMT_WHOIS_END, SERVER_NUM, $source_num, 
		$target->get_nick());
	
?>