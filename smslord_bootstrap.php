<?php

/**
 * Optional root-level entry point — delegates to app/helpers.php when present.
 */
$helpers = __DIR__.'/app/helpers.php';
if (is_file($helpers)) {
    require_once $helpers;
}
