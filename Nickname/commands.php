<?php
	
	/**
	 * Admin-Level Commands (501 and above)
	 */
	$this->set_command_info( 'die',           1000,   0, false, '[reason]' );

	$this->set_command_info( 'addadmin',       800,   2, false, '<user> <level>' );
	$this->set_command_info( 'deladmin',       800,   1, false, '<user>' );

	$this->set_command_info( 'drop',           501,   1, false, '<user>' );
	$this->set_command_info( 'adminlist',      501,   0, false, '[search mask]' );

	/**
	 * User-Level Commands (500 and below)
	 */
	$this->set_command_info( 'newpass',          1,   1, false, '<password>' );
	$this->set_command_info( 'set',              1,   1, false, '<option> [value]' );

	$this->set_command_info( 'ghost',            0,   0, false, '[nickname] [password]' );
	$this->set_command_info( 'help',             0,   0, false, '[command]' );
	$this->set_command_info( 'info',             0,   1, false, '<nickname>' );
	$this->set_command_info( 'login',            0,   1, false, '[account] <password>' );
	$this->set_command_info( 'register',         0,   2, false, '<password> <email>' );
	$this->set_command_info( 'showcommands',     0,   0, false );
	$this->set_command_info( 'uptime',           0,   0, false );

?>
