<?php
	
	$this->set_command_info( 'die',           1000,   0, false, '[reason]' );
	$this->set_command_info( 'quote',         1000,   1, false, '<stuff>' );

	$this->set_command_info( 'broadcast',      700,   1, false, '<message>' );
	$this->set_command_info( 'settime',        700,   0, false );

	$this->set_command_info( 'inviteme',       500,   0, false );
	$this->set_command_info( 'refreshg',       500,   0, false );
	
	$this->set_command_info( 'clearchan',      300,   1, false, '<options> [duration]' );
	$this->set_command_info( 'deopall',        300,   1, false, '<channel>' );
	$this->set_command_info( 'devoiceall',     300,   1, false, '<channel>' );
	$this->set_command_info( 'kickall',        300,   2, false, '<channel> <reason>' );
	$this->set_command_info( 'kickbanall',     300,   2, false, '<channel> <reason>' );
	$this->set_command_info( 'moderate',       300,   1, false, '<channel>' );
	$this->set_command_info( 'opall',          300,   1, false, '<channel>' );
	$this->set_command_info( 'voiceall',       300,   1, false, '<channel>' );
	
	$this->set_command_info( 'ban',            200,   2, false, '<channel> <hostmask> [duration] [level] [reason]' );
	$this->set_command_info( 'clearmodes',     200,   1, false, '<channel>' );
	$this->set_command_info( 'gline',          200,   3, false, '<mask> <duration> <reason>' );
	$this->set_command_info( 'invite',         200,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'kickban',        200,   2, false, '<channel> <nick|hostmask> [reason]' );
	$this->set_command_info( 'mode',           200,   2, false, '<channel> <modes>' );
	$this->set_command_info( 'remgline',       200,   1, false, '<mask>' );
	$this->set_command_info( 'unban',          200,   2, false, '<channel> <hostmask>' );
	
	$this->set_command_info( 'deop',           100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'devoice',        100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'kick',           100,   2, false, '<channel> <nick> [reason]' );
	$this->set_command_info( 'op',             100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'topic',          100,   1, false, '<channel> [new topic]' );
	$this->set_command_info( 'voice',          100,   1, false, '<channel> [nick1 [nick2 ...]]' );

	$this->set_command_info( 'banlist',          0,   1, false, '<channel> [mask]' );
	$this->set_command_info( 'chaninfo',         0,   1, false, '<channel>' );
	$this->set_command_info( 'chanlist',         0,   0, false, '[mask]' );
	$this->set_command_info( 'help',             0,   0, false, '[command]' );
	$this->set_command_info( 'opermsg',          0,   1, false, '<message>' );
	$this->set_command_info( 'scan',             0,   1, false, '<mask>' );
	$this->set_command_info( 'showcommands',     0,   0, false );
	$this->set_command_info( 'uptime',           0,   0, false );
	$this->set_command_info( 'whois',            0,   1, false, '<nick>' );
	$this->set_command_info( 'whoison',          0,   1, false, '<channel>' );

?>