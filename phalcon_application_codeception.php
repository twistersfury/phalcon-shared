<?php

/*
 * This is a helper file for Codeception Testing. The Phalcon Module expects the DI to always be fresh/new.
 * The getDefault allowed override causes issues when running codeception.
 */

$_ENV['ENV_RUNNING_CODECEPTION'] = time();

return require __DIR__ . '/phalcon_application.php';