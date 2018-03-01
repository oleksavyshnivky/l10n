<?php

if (defined('TEST')) error_reporting(E_ALL);

include '../app/functions/functions.php';
include '../app/functions/secure.php';

// Конфігурація
include '../app/config/config.php';
include '../app/config/config-lang.php';

require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
