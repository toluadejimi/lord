<?php

$bootstrap = dirname(__DIR__).'/bootstrap';

if (is_file($bootstrap.'/helpers_early.php')) {
    require_once $bootstrap.'/helpers_early.php';
}

if (is_file($bootstrap.'/helpers_legacy.php')) {
    require_once $bootstrap.'/helpers_legacy.php';
}
