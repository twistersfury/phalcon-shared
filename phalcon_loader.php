<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use Phalcon\Loader;

//Ensuring Only Loaded Once
if (defined('TF_LOADER_INITIALIZED')) {
    return;
}
define('TF_LOADER_INITIALIZED', true);

//Closure To Prevent Populating Global Namespace
(function () {
    $isComposer    = !defined('TF_COMPOSER_DEVELOPMENT');
    $projectFolder = !$isComposer ? __DIR__ : ($_SERVER['DOCUMENT_ROOT'] ?? false ? dirname($_SERVER['DOCUMENT_ROOT']) : __DIR__ . '/../../../..') . '/app';
    $filesList     = [
        realpath($projectFolder . '/../vendor/autoload.php')
    ];

    // Load All Non-Standard Configuration Files (IE: INI Changes, Defines, Etc).
    array_map(
        function ($fileName) use (&$filesList, $isComposer, $projectFolder) {
            if ($isComposer) {
                //Local Copy
                $filePath = $projectFolder . '/config/local/' . $fileName . '.php';
                if (file_exists($filePath)) {
                    $filesList[] = $filePath;
                }

                //Dist Copy
                $filePath = $projectFolder . '/config/dist/' . $fileName . '.php';
                if (file_exists($filePath)) {
                    $filesList[] = $filePath;
                }
            }

            //Shared Copy
            $filePath = __DIR__ . '/config/' . $fileName . '.php';
            if (file_exists($filePath)) {
                $filesList[] = $filePath;
            }
        },
        [
            'defines.functions', //Helper Function
            'defines.const',     //Non-Dynamic Defines
            'defines',           //Dynamic Defines
            'defines.shim',      //Shim Defines
            'ini_set'            //Changes To INI Settings
        ]
    );

    $phalconLoader = new Loader();

    $phalconLoader->registerFiles($filesList);

    if (getenv('ENV_PHALCON_LOADER')) {
        $phalconLoader->setEventsManager(new Manager());
        $phalconLoader->getEventsManager()->attach(
            'loader',
            function (Event $event, Loader $phalconLoader) {
                if ($event->getType() === 'beforeCheckPath') {
                    echo $phalconLoader->getCheckedPath() . '<hr />';
                }
            }
        );
    }

    //Register First To Load All Procedural Files
    $phalconLoader->register(true);

    $loaderNamespaces = array_merge(
        [
            'TwistersFury\Phalcon\Shared' => __DIR__ . '/src'
        ],
        TF_LOADER_NAMESPACES
    );

    $phalconLoader->registerNamespaces(
        $loaderNamespaces
    )->registerDirs(TF_LOADER_DIRECTORIES)
                  ->registerClasses(TF_LOADER_CLASSES);

    if (file_exists(__DIR__ . '/config/shim.php')) {
        require_once __DIR__ . '/config/shim.php';
    }

    if ($isComposer && file_exists($projectFolder . '/config/dist/shim.php')) {
        require_once $projectFolder . '/config/dist/shim.php';
    }
})();
