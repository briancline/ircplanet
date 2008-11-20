<?php

	class Timer
	{
		var $recurring;
		var $ts;
		var $include_file;
		var $ts_last_run;
		
		var $data = array();
		
		function __construct( $recurring, $ts, $include_file, $data = 0 )
		{
			$this->recurring = $recurring;
			$this->ts = $ts;
			$this->ts_last_run = time();
			$this->include_file = $include_file;
			
			if( !$recurring )
				$this->ts += time();
			
			if( $data > 0 )
				$this->data = $data;
		}
		
		function is_recurring()        { return $this->recurring; }
		function get_interval()        { return $this->ts; }
		function get_include()         { return $this->include_file; }
	
		function get_data_elements()
		{
			if(!is_array($this->data))
				return array($this->data);
			
			return $this->data;
		}


		function get_next_run()
		{
			if( $this->is_recurring() )
				return $this->ts_last_run + $this->ts;
			else
				return $this->ts;
		}

		
		function update()              { $this->ts_last_run = time(); }
		function set_data_elements($v)
		{
			if(!is_array($v))
				$v = array($v);
			
			$this->data = $v; 
		}
	}

?>