<?php
	
	$this->set_command_info( 'die',           1000,   0, false, '[reason]' );

	$this->set_command_info( 'newpass',          1,   1, false, '<password>' );
	$this->set_command_info( 'set',              1,   1, false, '<option> [value]' );

	$this->set_command_info( 'help',             0,   0, false, '[command]' );
	$this->set_command_info( 'login',            0,   1, false, '[account] <password>' );
	$this->set_command_info( 'register',         0,   2, false, '<password> <email>' );
	$this->set_command_info( 'showcommands',     0,   0, false );
	$this->set_command_info( 'uptime',           0,   0, false );

?>