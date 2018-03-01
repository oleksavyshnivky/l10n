<?php

$params = array(
	'dbhost'	=>	'127.0.0.1',
	'dbname'	=>	'devPPDB',
	'dbuser'	=>	'testyurk28',
	'dbpass'	=>	'testyurk28'
);

$db = new db($params);

define('DBNAME', $params['dbname']);
