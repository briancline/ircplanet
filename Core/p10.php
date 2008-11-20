<?php
	
	define( 'TOPIC_BURSTING',       true );
	define( 'IDENT_LEN',            10 );
	
	
	define( 'FMT_PASS',             "PASS :%s" );
	// SERVER Operator.Services.Virtuanet.org 1 1130312160 1130312160 J10 Vs]]] +hs :Oper Services
	// AE S Network.Services.Virtuanet.org 2 0 1116989941 P10 M[AB] +s :Network Services
	define( 'FMT_SERVER_SELF',      "SERVER %s 1 [TS] [TS] J10 %s]]] +%s :%s" );
	define( 'FMT_SERVER',           "%s S %s %d 0 %ld P10 %s%s %s :%s" );
	// M[ N Global 2 1116993608 Global Network.Services.Virtuanet.org +oik AAAAAA M[AAA :Global Services
	define( 'FMT_NICK',             "%s N %s %d %ld %s %s +%s %s %s :%s" );
	// AE B #team 1121035201 +stn M[AAD:o
	define( 'FMT_BURST',            "%s B %s %ld %s" );
	define( 'FMT_BURST_BANS',       "%s B %s %ld %s :%%%s" );
	define( 'FMT_BURST_MODES',      "%s B %s %ld +%s %s" );
	define( 'FMT_BURST_MODES_BANS', "%s B %s %ld +%s %s :%%%s" );
	define( 'FMT_ENDOFBURST',       "%s EB" );
	define( 'FMT_ENDOFBURST_ACK',   "%s EA" );
	define( 'FMT_CREATE',           "%s C %s %ld" );
	define( 'FMT_JOIN',             "%s J %s %ld" );
	define( 'FMT_PART',             "%s L %s" );
	define( 'FMT_PART_REASON',      "%s L %s :%s" );
	define( 'FMT_KICK',             "%s K %s %s :%s" );
	define( 'FMT_CLEARMODES',       "%s CM %s %s" );
	define( 'FMT_MODE',             "%s M %s %s %ld" );
	define( 'FMT_MODE_NOTS',        "%s M %s %s" );
	define( 'FMT_OPSELF',           "%s M %s +o %s %ld" );
	define( 'FMT_LEAVE',            "%s L %s" );
	define( 'FMT_PRIVMSG',          "%s P %s :%s" );
	define( 'FMT_NOTICE',           "%s O %s :%s" );
	define( 'FMT_SQ',               "%s SQ %s %ld :%s" );
	define( 'FMT_PONG',             "%s Z %s %s" );
	define( 'FMT_MODE_HACK',        "%s M %s %s %s %s" );
	define( 'FMT_MODE_HACK_NOTS',   "%s M %s %s %s" );
	define( 'FMT_ACCOUNT',          "%s AC %s %s" );
	define( 'FMT_QUIT',             "%s Q :%s" );
	define( 'FMT_INVITE',           "%s I %s :%s" );
	define( 'FMT_GLINE_ADD',        "%s GL * +%s %ld :%s" );
	define( 'FMT_GLINE_REMOVE',     "%s GL * -%s" );
	define( 'FMT_SETTIME',          "%s SE %ld :%s" );
	define( 'FMT_KILL',             "%s D %s :%s (%s)" );
	
	if( TOPIC_BURSTING )
		define( 'FMT_TOPIC',        "%s T %s %ld %ld :%s" );
	else
		define( 'FMT_TOPIC',        "%s T %s :%s" );
	
	define( 'FMT_ADMIN_REPLY_1',    "%s 256 %s :%s" );
	define( 'FMT_ADMIN_REPLY_2',    "%s 257 %s :%s" );
	define( 'FMT_ADMIN_REPLY_3',    "%s 258 %s :%s" );
	define( 'FMT_ADMIN_REPLY_4',    "%s 259 %s :%s" );
	define( 'FMT_VERSION_REPLY',    "%s 351 %s %s %s :%s" );
	define( 'FMT_STATS_U_REPLY',    "%s 242 %s :Server up %s" );
	define( 'FMT_STATS_END',        "%s 219 %s %s :End of /STATS report" );
	
	define( 'FMT_WHOIS_NOTFOUND',   '%s 401 %s %s :No such nick' );
	define( 'FMT_WHOIS_DENIED',     '%s 401 %s %s :You are not an IRC operator' );
	define( 'FMT_WHOIS_USER',       '%s 311 %s %s %s %s * :%s' );
	define( 'FMT_WHOIS_SERVER',     '%s 312 %s %s %s :%s' );
	define( 'FMT_WHOIS_OPER',       '%s 313 %s %s :is an IRC Operator' );
	define( 'FMT_WHOIS_IDLE',       '%s 317 %s %s %ld %ld' );
	define( 'FMT_WHOIS_CHANNELS',   '%s 319 %s %s :%s' );
	define( 'FMT_WHOIS_END',        '%s 318 %s %s :End of /WHOIS list.' );
	
	define( 'MAX_MODES_PER_LINE',   6 );

	define( 'BASE64_USERMAX',       262143 );
	define( 'BASE64_SERVLEN',       2 );
	define( 'BASE64_USERLEN',       3 );
	define( 'BASE64_IPLEN',         6 );
	define( 'BASE64_MAXUSERLEN',    3 );
	
	$BASE64_INT_TO_NUM = array(
		'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P',
		'Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f',
		'g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v',
		'w','x','y','z','0','1','2','3','4','5','6','7','8','9','[',']' );
	$BASE64_NUM_TO_INT = array(
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
        52,53,54,55,56,57,58,59,60,61, 0, 0, 0, 0, 0, 0,
         0, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9,10,11,12,13,14,
        15,16,17,18,19,20,21,22,23,24,25,62, 0,63, 0, 0,
         0,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,
        41,42,43,44,45,46,47,48,49,50,51, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
         0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 );
	

	function int_to_base64( $i, $count )
	{
		global $BASE64_INT_TO_NUM;
		
		$b = str_repeat(' ', $count);
		while( $count > 0 )
		{
			$b[--$count] = $BASE64_INT_TO_NUM[ ($i & 63) ];
			$i = $i >> 6;

			// Force PHP to treat this as unsigned
			if($i < 0)
			{
				debug("Int $i, bit $count ended up with <0 result");
				$i += 67108864;
			}
		}
		
		return $b;
	}
	
	
	function base64_to_int( $b )
	{
		global $BASE64_NUM_TO_INT;
		
		$i = 0;
		$len = strlen( $b );
		
		for( $n = 0; $n < $len; ++$n )
		{
			$i = $i << 6;
			
			// Force PHP to treat this as unsigned
			if( $i < 0 )
			{
				debug("Base64 $b, char $n ended up with <0 result");
				$i += 4294967296;
			}

			$i += $BASE64_NUM_TO_INT[ ord($b[$n]) ];
		}
		
		return $i;
	}
	
	
	function ip_to_base64( $ip )
	{
		$i = ip2long( $ip );
		return int_to_base64( $i, BASE64_IPLEN );
	}
	
	
	function base64_to_ip( $b )
	{
		$i = base64_to_int( $b );
		return long2ip( $i );
	}

	
?>