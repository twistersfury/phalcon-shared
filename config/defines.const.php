<?php
/**
 * @author    Phoenix <phoenix@twistersfury.com>
 * @license   proprietary
 * @copyright 2016 Twister's Fury
 */

namespace TwistersFury\Defines;

define('TF_COMPOSER_DEVELOPMENT', false);

define(
    'TF_LOADER_NAMESPACES',
    []
);

define(
    'TF_LOADER_DIRECTORIES',
    []
);

define(
    'TF_LOADER_CLASSES',
    []
);

/*
 * Debug Stuff
 */
define('TF_DEBUG_DISABLED', 0);
define('TF_DEBUG_ENABLED', 1);
define('TF_DEBUG_PHALCON', 2);
define('TF_DEBUG_SQL', 4);

/*
 * Path Stuff
 */
define('DS', DIRECTORY_SEPARATOR);


define('TF_PROVIDERS_TYPE', 'mvc');