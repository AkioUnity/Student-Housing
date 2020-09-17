<?php

ini_set('display_errors', 'On');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include_once 'vendor/autoload.php';

use Simcify\Application;

$app = new Application();
$app->route();

