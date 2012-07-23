<?php

/* Set the locale */
setlocale(LC_ALL, array('sv_SE.UTF-8', 'sv_SE'));
setlocale(LC_NUMERIC, array('en_US.UTF-8', 'en_US'));

/* Requirements */
require_once (dirname(__FILE__) . '/../lib/Buzzmix/require.php');
require_once (dirname(__FILE__) . '/../classes/JSONRPC.class.php');

/* Subclass Buzzmix */
class BoxpingAPI extends Buzzmix {
	
	function outputPage($file, $parts, $uri) {
		
        $rpc = JSONRPC::server();
        
        $r = include $file;
        
        return (($r === 1 || $r === true)?200:(($r === false)?500:$r));
	}
	
}

/* Create the Buzzmix */
$site = new Buzzmix(dirname(__FILE__) . '/..');

/* Setup the MySQL Database */
// $site->mysqlSetup("localhost", "username", "password", "database");

/* Display it! */
$status = $site->handleRequest($_SERVER['REQUEST_URI']);
