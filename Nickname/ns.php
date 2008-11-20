<?php

	require( 'globals.php' );
	require( '../Core/service.php' );
	
	
	class NicknameService extends Service
	{
		function service_construct()
		{
		}
		
		
		function service_destruct()
		{
		}
		

		function service_load()
		{
		}
		
		
		function service_preburst()
		{
		}
		
		
		function service_postburst()
		{
		}
		
		
		function service_preread()
		{
		}
		

		function service_close( $reason = 'So long, and thanks for all the fish!' )
		{
			foreach( $this->users as $numeric => $user )
			{
				if( $user->is_bot() )
				{
					$this->sendf( FMT_QUIT, $numeric, $reason );
					$this->remove_user( $numeric );
				}
			}
		}

		
		function service_main()
		{
		}
		
		
		function get_user_level( $user_obj )
		{
			if( !is_object($user_obj) || !get_class($user_obj) == 'User' || !$user_obj->is_logged_in() )
				return 0;
			
			$res = db_query( "select `level` from `ns_admins` where user_id = ". $user_obj->get_account_id() );
			if( $res && mysql_num_rows($res) > 0 )
			{
				$level = mysql_result( $res, 0 );
				mysql_free_result( $res );
				return $level;
			}
			
			return 1;
		}
	}
	
	$cs = new NicknameService();

?>
