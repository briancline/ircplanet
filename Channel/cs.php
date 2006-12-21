<?php

	require_once( 'globals.php' );
	require_once( '../Core/service.php' );
	require_once( SERVICE_DIR .'/db_channel.php' );
	require_once( SERVICE_DIR .'/db_channel_access.php' );
	
	
	class ChannelService extends Service
	{
		var $accounts = array();
		var $db_channels = array();
		
		
		function load_channels()
		{
			$n = 0;
			$res = db_query( 'select * from channels order by lower(name) asc' );
			while( $row = mysql_fetch_assoc($res) )
			{
				$channel_key = strtolower( $row['name'] );
				$channel = new DB_Channel( $channel_key );
				$channel->load_from_row( $row );
				
				if( $channel->auto_limits() && !$channel->has_pending_autolimit() )
				{
					$this->add_timer( false, $channel->get_auto_limit_wait(), 
						'auto_limit.php', $channel->get_name() );
					$channel->set_pending_autolimit( true );
				}
				
				$this->db_channels[$channel_key] = $channel;
				$n++;
			}
			
			debug( "Loaded $n channel records." );
		}
		
		
		function load_access()
		{
			$n = 0;
			$res = db_query( 'select * from channel_access' );
			while( $row = mysql_fetch_assoc($res) )
			{
				$chan_id = $row['chan_id'];
				$user_id = $row['user_id'];
				
				$chan = $this->get_channel_reg_by_id( $chan_id );
				$access = new DB_Channel_Access( $chan_id, $user_id );
				$access->load_from_row( $row );
				$chan->add_access( $access );
				
				$n++;
			}
			
			debug( "Loaded $n channel access records." );
		}
		
		
		function load_bans()
		{
			$n = 0;
			$res = db_query( 'select * from channel_bans' );
			while( $row = mysql_fetch_assoc($res) )
			{
				$chan_id = $row['chan_id'];
				$user_id = $row['user_id'];
				$mask = $row['mask'];
				$duration_secs = time() - $row['duration'];
				
				
				$chan = $this->get_channel_reg_by_id( $chan_id );
				$ban = new DB_Ban( $chan_id, $user_id, $mask );
				$ban->load_from_row( $row );
				$chan->add_ban( $ban );
				
				$n++;
			}
			
			debug( "Loaded $n channel ban records." );
		}
		
		
		function service_construct()
		{
		}
		
		
		function service_load()
		{
			$this->load_channels();
			$this->load_access();
			$this->load_bans();
			
			$this->add_timer( true, 5, 'refresh_data.php' );
			$this->add_timer( true, 300, 'save_data.php' );
			$this->add_timer( true, 30, 'expire_channels.php' );
		}
		
		
		function service_preburst()
		{
			$bot = $this->default_bot;
			$botnum = $bot->get_numeric();

			foreach( $this->db_channels as $dbchan_key => $dbchan )
			{
				if( $chan = $this->get_channel($dbchan_key) )
				{
					//$this->sendf( FMT_JOIN, $botnum, $chan->get_name(), time() );
					$this->add_channel_user( $chan->get_name(), $botnum, 'o' );
					//$this->op( $chan->get_name(), $botnum );
				}
				else
				{
					//$this->sendf( FMT_CREATE, $botnum, $dbchan->get_name(), time() );
					$ts = $dbchan->get_create_ts();
					if( $ts < $dbchan->get_register_ts() )
					{
						$ts = $dbchan->get_register_ts();
						$dbchan->set_create_ts( $ts );
						$dbchan->save();
					}
					
					$chan = $this->add_channel( $dbchan->get_name(), $ts );
					$this->add_channel_user( $dbchan->get_name(), $botnum, 'o' );
				}
				
				if( $dbchan->has_default_topic() )
				{
					$deftopic = $dbchan->get_default_topic();
					$chan->set_topic( $deftopic );
				}
				
				if( $dbchan->has_default_modes() )
				{
					$defmodes = $dbchan->get_default_modes();
//						if( !$chan->has_exact_modes($defmodes) )
//						{
//							$chan->clear_modes();
//							$bot->clear_modes( $chan->get_name() );
						$chan->add_modes( $defmodes );
						//$bot->mode( $chan->get_name(), $defmodes );
//						}
				}
			}
		}
		
		
		function service_postburst( $uplink_burst = false )
		{
			$bot = $this->default_bot;
			$botnum = $bot->get_numeric();
			
			if( $uplink_burst )
			{
			}
			else
			{
				foreach( $this->db_channels as $dbchan_key => $dbchan )
				{
					$chan = $this->get_channel( $dbchan_key );
					if( $chan && !$chan->is_op($botnum) )
						$this->op( $chan->get_name(), $botnum );
				}
			}
		}
		
		
		function service_destruct()
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
		
		
		function service_preread()
		{
		}


		function get_channel_reg_by_id( $chan_id )
		{
			foreach( $this->db_channels as $chan_key => $chan )
			{
				if( $chan->id == $chan_id )
					return $chan;
			}
			
			return false;
		}
		
		
		function add_channel_reg( $reg )
		{
			$chan_key = strtolower( $reg->get_name() );
			
			$this->db_channels[$chan_key] = $reg;
			
			return $this->db_channels[$chan_key];
		}
		
		
		function remove_channel_reg( $chan_name )
		{
			if( is_object($chan_name) && get_class($chan_name) == 'DB_Channel' )
				$chan_name = $chan_name->get_name();
			
			$chan_reg = 0;
			$chan_key = strtolower( $chan_name );
			
			if( array_key_exists($chan_key, $this->db_channels) )
			{
				$chan_reg = $this->db_channels[$chan_key];
				unset( $this->db_channels[$chan_key] );
			}
			
			return $chan_reg;
		}
		
		
		function get_channel_reg( $chan_name )
		{
			$chan_key = strtolower( $chan_name );
			
			if( array_key_exists($chan_key, $this->db_channels) )
				return $this->db_channels[$chan_key];
			
			return false;
		}
		
		
		function get_channel_reg_count( $user_id )
		{
			$chan_count = 0;
			
			foreach( $this->db_channels as $chan_key => $chan )
			{
				foreach( $chan->levels as $level_uid => $level )
				{
					if( $level_uid == $user_id && $level->get_level() == 500 )
						$chan_count++;
				}
			}
			
			return $chan_count;
		}
		
		
		function get_admin_level( $user_obj )
		{
			if( !is_object($user_obj) || !get_class($user_obj) == 'User' || !$user_obj->is_logged_in() )
				return 0;
			
			$account = $this->get_account( $user_obj->get_account_name() );
			
			$res = db_query( "select `level` from `cs_admins` where user_id = ". $account->get_id() );
			if( $res && mysql_num_rows($res) > 0 )
			{
				$level = mysql_result( $res, 0 );
				mysql_free_result( $res );
				return $level;
			}
			
			return 0;
		}
		
		
		function get_channel_access( $chan_name, $user_obj )
		{
			$chan_key = strtolower( $chan_name );
			
			if( !($chan = $this->get_channel_reg($chan_name)) )
			{
				debug('gca> chan not regged');
				return false;
			}
			if( !is_object($user_obj) || !get_class($user_obj) == 'User' || !$user_obj->is_logged_in() )
			{
				debug('gca> user crap failed');
				return false;
			}
			
			if( !array_key_exists($user_obj->get_account_id(), $chan->levels) )
			{
				debug('gca> account id does not exist in chan levels');
				return false;
			}
			
			return $chan->levels[$user_obj->get_account_id()];
		}
		
		
		function get_channel_access_account( $chan_name, $account_obj )
		{
			$chan_key = strtolower( $chan_name );
			
			if( !($chan = $this->get_channel_reg($chan_name)) )
				return false;
			if( !is_object($account_obj) || !get_class($account_obj) == 'DB_User' )
				return false;
			
			if( !array_key_exists($account_obj->get_id(), $chan->levels) )
				return false;
			
			return $chan->levels[$account_obj->get_id()];
		}
		
		
		function get_channel_level( $chan_name, $user_obj )
		{
			$chan_key = strtolower( $chan_name );
			
			if( !array_key_exists($chan_name, $this->db_channels) )
				return 0;
			if( !is_object($user_obj) || !get_class($user_obj) == 'User' || !$user_obj->is_logged_in() )
				return 0;
			
			$chan = $this->get_channel_reg( $chan_name );
			return $chan->get_level_by_id( $user_obj->get_account_id() );
		}
		
		
		function get_channel_level_by_name( $chan_name, $user_name )
		{
			$chan_key = strtolower( $chan_name );
			
			if( !array_key_exists($chan_name, $this->db_channels) )
				return 0;
			if( !($account = $this->get_account($user_name)) )
				return 0;
			
			$chan = $this->get_channel_reg( $chan_name );
			return $chan->get_level_by_id( $account->get_id() );
		}
		
	}
	
	$cs = new ChannelService();

?>