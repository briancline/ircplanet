<?php
	
	$chan_name = $pargs[1];
	$purpose = assemble( $pargs, 2 );
	
	if( !$user->is_logged_in() )
	{
		$bot->notice( $user, 'You must register a user account before you can register a channel.' );
		return false;
	}
	
	if( $chan_name[0] != '#' )
	{
		$bot->notice( $user, 'Channel names must begin with the # character.' );
		return false;
	}
	
	if( $this->get_channel_reg_count($user->get_account_id()) >= MAX_CHAN_REGS )
	{
		$bot->noticef( $user, 'You cannot register more than %d channels.', 
			MAX_CHAN_REGS );
		return false;
	}
	
	if( !($reg = $this->get_channel_reg($chan_name)) )
	{
		$chan = $this->get_channel( $chan_name );
		$create_ts = time();

		if($chan != NULL)
			$create_ts = $chan->get_ts();

		$reg = new DB_Channel( $chan_name, $user->get_account_id() );
		$reg->set_purpose( $purpose );
		$reg->set_create_ts( $create_ts );
		$reg->set_register_date( db_date() );
		$reg->save();
		$reg = $this->add_channel_reg( $reg );
		
		if( $chan = $this->get_channel($chan_name) )
		{
			$this->sendf( FMT_JOIN, $bot->get_numeric(), $chan_name, time() );
			$chan->add_user( $bot->get_numeric(), 'o' );
			$this->op( $chan_name, $bot->get_numeric() );
		}
		else
		{
			$this->sendf( FMT_CREATE, $bot->get_numeric(), $chan_name, time() );
			$this->add_channel( $chan_name, time() );
			$this->add_channel_user( $chan_name, $bot->get_numeric(), 'o' );
		}
	}
	else
	{
		$bot->noticef( $user, 'Sorry, %s is already registered.',
			$reg->get_name() );
		return false;
	}

?>
