<?php
	
	/**
	 * Admin-Level Commands (501 and above)
	 */
	$this->set_command_info( 'dumpchan',       999,   1, false, '<channel>' );
	$this->set_command_info( 'dumpuser',       999,   1, false, '<nick>' );
	$this->set_command_info( 'dumpall',        999,   0, false );
	
	$this->set_command_info( 'die',            900,   0, false, '[reason]' );

	$this->set_command_info( 'addadmin',       800,   2, false, '<user> <level>' );
	$this->set_command_info( 'deladmin',       800,   1, false, '<user>' );

	$this->set_command_info( 'adminreg',       700,   2, false, '<channel> <owner> [purpose]' );
	$this->set_command_info( 'delchan',        700,   1, false, '<channel> [reason]' );

	$this->set_command_info( 'adminlist',      501,   0, false, '[search mask]' );
	$this->set_command_info( 'reop',           501,   0, false );
	
	/**
	 * User-Level Commands (500 and below)
	 */
	$this->set_command_info( 'unreg',          500,   1, false, '<channel>' );

	$this->set_command_info( 'set',            450,   1, false, '<channel> <option> [value]' );
	$this->set_command_info( 'kickall',        450,   2, false, '<channel> <reason>' );
	$this->set_command_info( 'kickbanall',     450,   2, false, '<channel> <reason>' );
	
	$this->set_command_info( 'adduser',        400,   2, false, '<channel> <user> [level]' );
	$this->set_command_info( 'moduser',        400,   3, false, '<channel> <user> <option> [value]' );
	$this->set_command_info( 'remuser',        400,   2, false, '<channel> <user>' );
	
	$this->set_command_info( 'clearmodes',     300,   1, false, '<channel>' );
	$this->set_command_info( 'clearbans',      300,   1, false, '<channel>' );
	$this->set_command_info( 'rdefmodes',      300,   1, false, '<channel>' );
	$this->set_command_info( 'rdeftopic',      300,   1, false, '<channel>' );
	
	$this->set_command_info( 'moderate',       200,   1, false, '<channel>' );
	$this->set_command_info( 'opall',          200,   1, false, '<channel>' );
	$this->set_command_info( 'deopall',        200,   1, false, '<channel>' );
	$this->set_command_info( 'voiceall',       200,   1, false, '<channel>' );
	$this->set_command_info( 'devoiceall',     200,   1, false, '<channel>' );
	
	$this->set_command_info( 'mode',           100,   2, false, '<channel> <modes>' );
	$this->set_command_info( 'op',             100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'deop',           100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'voice',          100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'devoice',        100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'invite',         100,   1, false, '<channel> [nick1 [nick2 ...]]' );
	$this->set_command_info( 'say',            100,   1, false, '<channel> <text>' );
	$this->set_command_info( 'do',             100,   1, false, '<channel> <action>' );
	
	$this->set_command_info( 'kick',            75,   2, false, '<channel> <nick> [reason]' );
	$this->set_command_info( 'kickban',         75,   2, false, '<channel> <nick|hostmask> [reason]' );
	$this->set_command_info( 'ban',             75,   2, false, '<channel> <hostmask> [duration] [level] [reason]' );
	$this->set_command_info( 'unban',           75,   2, false, '<channel> <hostmask>' );
	$this->set_command_info( 'banlist',         75,   1, false, '<channel> [mask]' );
	$this->set_command_info( 'topic',           75,   1, false, '<channel> [new topic]' );
	
	$this->set_command_info( 'help',             0,   0, false, '[command]' );
	$this->set_command_info( 'access',           0,   2, false, '<channel> <search mask>' );
	$this->set_command_info( 'chaninfo',         0,   1, false, '<channel>' );
	$this->set_command_info( 'register',         0,   2, false, '<channel> <purpose>' );
	$this->set_command_info( 'showcommands',     0,   0, false );
	$this->set_command_info( 'uptime',           0,   0, false );

?>
