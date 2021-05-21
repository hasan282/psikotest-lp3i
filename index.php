<?php

function config($name)
{
    include 'all_config.php';
    return $Configuration[$name];
}

ini_set('max_execution_time', config('execution_time'));
if (function_exists('date_default_timezone_set')) date_default_timezone_set(config('timezone'));

if (!session_id()) session_start();

require_once 'app/init.php';

$app = new App();
