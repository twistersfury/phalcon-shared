<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Defines;

define('APPLICATION_PATH', realpath(__DIR__ . '/../..'));
define('APPLICATION_ENV', getenv('ENV_APPLICATION_ENV') ?: 'production');
define('DOC_ROOT', realpath(APPLICATION_PATH . '/../public'));

define('TF_SHARED_SOURCE', realpath(__DIR__ . '/../src'));
define('TF_SHARED_PROJECT', realpath(__DIR__ . '/..'));
define('TF_SHARED_TESTS', realpath(TF_SHARED_PROJECT . '/tests'));

(function () {
    $debugMode = TF_DEBUG_DISABLED;

    if (getenv('ENV_DEBUG_MODE')) {
        $debugMode = getenv('ENV_DEBUG_MODE');
    } elseif (APPLICATION_ENV === 'development') {
        $debugMode = TF_DEBUG_ENABLED | TF_DEBUG_PHALCON | TF_DEBUG_SQL;
    }

    define('TF_DEBUG', $debugMode);
})();
