<?php

foreach ([
    dirname(__DIR__).'/app/Http/Helpers/helpers.php',
    dirname(__DIR__).'/app/helpers.php',
] as $helpersFile) {
    if (is_file($helpersFile)) {
        require_once $helpersFile;
        break;
    }
}
