<?php

define('HTTP_ASSETS', '//' . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__)) . '/public'));
define('HTTP_VENDOR', HTTP_ASSETS . '/vendor');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') define('AJAX', true);

require_once '../app/init.php';

$app = new App;
