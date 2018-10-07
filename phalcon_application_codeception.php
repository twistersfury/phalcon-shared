<?php

/*
 * This is a helper file for Codeception Testing. The Phalcon Module expects the DI to always be fresh/new.
 * The getDefault allowed override causes issues when running codeception.
 */

\Phalcon\Di::setDefault(null);

return __DIR__ . '/phalcon_application.php';