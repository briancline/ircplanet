<?php
	
	$this->set_command_info( 'die',           1000,   0, false, '[reason]' );
	$this->set_command_info( 'quote',         1000,   1, false, '<stuff>' );

	$this->set_command_info( 'adduser',        800,   2, false, '<account> <level>' );
	$this->set_command_info( 'moduser',        800,   3, false, '<account> <setting> <param>' );
	$this->set_command_info( 'remuser',        800,   1, false, '<account>' );

	$this->set_command_info( 'inviteme',       500,   0, false );

	$this->set_command_info( 'help',             0,   0, false, '[command]' );
	$this->set_command_info( 'showcommands',     0,   0, false );
	$this->set_command_info( 'uptime',           0,   0, false );

?>