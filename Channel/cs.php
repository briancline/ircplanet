<?php
/*
 * ircPlanet Services for ircu
 * Copyright (c) 2005 Brian Cline.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:

 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of ircPlanet nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without 
 *    specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

	require_once( 'globals.php' );
	require_once( '../Core/service.php' );
	require_once( SERVICE_DIR .'/db_channel.php' );
	require_once( SERVICE_DIR .'/db_channel_access.php' );
	require_once( SERVICE_DIR .'/db_badchan.php' );
	
	
	class ChannelService extends Service
	{
		var $db_channels = array();
		var $db_badchans = array();
		
		
		function load_channels()
		{
			$res = db_query( 'select * from channels order by lower(name) asc' );
			while( $row = mysql_fetch_assoc($res) )
			{
				$channel_key = strtolower( $row['name'] );
				$channel = new DB_Channel( $row );
				
				if( $channel->auto_limits() && !$channel->has_pending_autolimit() )
				{
					$this->add_timer( false, $channel->get_auto_limit_wait(), 
						'auto_limit.php', $channel->get_name() );
					$channel->set_pending_autolimit( true );
				}
				
				$this->db_channels[$channel_key] = $channel;
			}
			
			debugf( "Loaded %d channel records.", count($this->db_channels) );
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
				if(!$chan)
				{
					debug("*** Loaded channel access pair for channel ID $chan_id, but no such channel exists!");
					continue;
				}

				$access = new DB_Channel_Access( $row );
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
				
				$chan = $this->get_channel_reg_by_id( $chan_id );
				if( !$chan )
				{
					debug("*** Loaded ban for channel ID $chan_id, but no such channel exists!");
					continue;
				}
				
				$ban = new DB_Ban( $chan_id, $user_id, $mask );
				$ban->load_from_row( $row );
				$chan->add_ban( $ban );
				
				$n++;
			}
			
			debug( "Loaded $n channel ban records." );
		}
		
		
		function load_badchans()
		{
			$res = db_query( 'select * from cs_badchans order by badchan_id asc' );
			while( $row = mysql_fetch_assoc($res) )
			{
				$badchan = new DB_BadChan( $row );
				
				$badchan_key = strtolower( $badchan->get_mask() );
				$this->db_badchans[$badchan_key] = $badchan;
			}

			debugf( 'Loaded %d badchans.', count($this->db_badchans) );
		}


		function service_construct()
		{
		}
		
		
		function service_load()
		{
			$this->load_channels();
			$this->load_access();
			$this->load_bans();
			$this->load_badchans();
			
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
					$this->add_channel_user( $chan->get_name(), $botnum, 'o' );
				}
				else
				{
					$ts = $dbchan->get_create_ts();

					if( $ts == 0 || ($ts > $dbchan->get_register_ts() && $dbchan->get_register_ts() > 0) )
					{
						$ts = $dbchan->get_register_ts();
						$dbchan->set_create_ts( $ts );
						$dbchan->save();
					}
					
					$chan = $this->add_channel( $dbchan->get_name(), $ts );
					$chan->add_mode( CMODE_REGISTERED );
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
					$chan->add_modes( $defmodes );
				}
			}
		}
		
		
		function service_postburst( $uplink_burst = false )
		{
			$bot = $this->default_bot;
			$botnum = $bot->get_numeric();
			
			foreach( $this->db_channels as $dbchan_key => $dbchan )
			{
				$chan = $this->get_channel( $dbchan_key );
				if( $chan && !$chan->is_op($botnum) )
				{
					$this->mode( $chan->get_name(), '+Ro '. $botnum );
					$bot->mode( $chan->get_name(), $dbchan->get_default_modes() );
					$dbchan->set_create_ts( $chan->get_ts() );
					$dbchan->save();
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
				if( $chan->get_id() == $chan_id )
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
			if( is_channel_record($chan_name) )
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
				$levels = $chan->get_levels();
				foreach( $levels as $level_uid => $level )
				{
					if( $level_uid == $user_id && $level->get_level() == 500 )
						$chan_count++;
				}
			}
			
			return $chan_count;
		}
		
		
		function is_channel_registered( $chan_name )
		{
			if( is_channel($chan_name) )
				$chan_name = $chan_name->get_name();
			
			return false !== $this->get_channel_reg( $chan_name );
		}
		
		
		function get_admin_level( $user_obj )
		{
			if( !is_object($user_obj) )
				return 0;
			if( !is_account($user_obj) && (!is_user($user_obj) || !$user_obj->is_logged_in()) )
				return 0;

			if( !is_account($user_obj) )
				$account = $this->get_account( $user_obj->get_account_name() );
			else
				$account = $user_obj;
			
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
			
			if( !($chan = $this->get_channel_reg($chan_key)) )
				return false;
			if( !is_object($user_obj) || !is_user($user_obj) || !$user_obj->is_logged_in() )
				return false;
			
			$levels = $chan->get_levels();
			
			if( !array_key_exists($user_obj->get_account_id(), $levels) )
				return false;
			
			return $levels[$user_obj->get_account_id()];
		}
		
		
		function get_channel_access_account( $chan_name, $account_obj )
		{
			$chan_key = strtolower( $chan_name );
			
			if( !($chan = $this->get_channel_reg($chan_key)) )
				return false;
			if( !is_account($account_obj) )
				return false;
			
			$levels = $chan->get_levels();
			if( !array_key_exists($account_obj->get_id(), $levels) )
				return false;
			
			return $levels[$account_obj->get_id()];
		}
		
		
		function get_channel_level( $chan_name, $user_obj )
		{
			$chan_key = strtolower( $chan_name );
			
			if( !array_key_exists($chan_key, $this->db_channels) )
				return 0;
			if( !is_object($user_obj) || !is_user($user_obj) || !$user_obj->is_logged_in() )
				return 0;
			
			$chan = $this->get_channel_reg( $chan_key );
			return $chan->get_level_by_id( $user_obj->get_account_id() );
		}
		
		
		function get_channel_level_by_name( $chan_name, $user_name )
		{
			$chan_key = strtolower( $chan_name );
			
			if( !array_key_exists($chan_key, $this->db_channels) )
				return 0;
			if( !($account = $this->get_account($user_name)) )
				return 0;
			
			$chan = $this->get_channel_reg( $chan_key );
			return $chan->get_level_by_id( $account->get_id() );
		}
		
		
		function get_active_channel_users( $chan_name )
		{
			$chan = $this->get_channel_reg( $chan_name );
			$active_users = array();
			
			if( !$chan )
				return false;
			
			$levels = $chan->get_levels();
			$seek_account_ids = array();
			
			foreach( $levels as $tmp_level )
				$seek_account_ids[] = $tmp_level->get_user_id();
			
			foreach( $this->users as $tmp_numeric => $tmp_user )
			{
				if( !$tmp_user->is_logged_in() )
					continue;
				
				if( in_array($tmp_user->get_account_id(), $seek_account_ids) )
					$active_users[] = $tmp_user;
			}
			
			return $active_users;
		}
		

		function get_badchan( $mask )
		{
			$mask = strtolower( $mask );
			if( array_key_exists($mask, $this->db_badchans) )
				return $this->db_badchans[$mask];

			return false;
		}


		function is_badchan( $chan_name )
		{
			if( is_channel($chan_name) )
				$chan_name = $chan_name->get_name();

			foreach( $this->db_badchans as $b_key => $badchan )
			{
				if( $badchan->matches($chan_name) )
					return true;
			}

			return false;
		}


		function add_badchan( $mask )
		{
			if( $this->get_badchan($mask) != false )
				return false;

			$badchan = new DB_BadChan();
			$badchan->set_mask( $mask );
			$badchan->save();

			$key = strtolower( $mask );
			$this->db_badchans[$key] = $badchan;

			return $this->db_badchans[$key];
		}


		function remove_badchan( $mask )
		{
			$badchan = $this->get_badchan( $mask );
			if( $badchan == false )
				return false;

			$key = strtolower( $mask );
			unset( $this->db_badchans[$key] );
			$badchan->delete();

			return true;
		}


	}
	
	$cs = new ChannelService();


