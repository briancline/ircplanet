<?php
	
	$chan_name = $pargs[1];
	$owner_nick = $pargs[2];
	$purpose = '';
	
	if( $cmd_num_args > 2 )
		$purpose = assemble( $pargs, 3 );
	
	if( $chan_name[0] != '#' )
	{
		$bot->notice( $user, 'Channel names must begin with the # character.' );
		return false;
	}
	
	if( !($owner = $this->get_account($owner_nick)) )
	{
		$bot->noticef( $user, '%s is not a known account name!', $owner_nick );
		return false;
	}
	
	if( !($reg = $this->get_channel_reg($chan_name)) )
	{
		$reg = new DB_Channel( $chan_name, $owner->get_id() );
		$reg->set_purpose( $purpose );
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