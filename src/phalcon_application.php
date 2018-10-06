<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\SignUp\Bootstrap;

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;

//JIC It Isn't In The Web Server Config
$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?: realpath(__DIR__ . '/../public');

require_once __DIR__ . '/phalcon_loader.php';

//Do not run the bootstrap if unit tests are running. This ensures that the unit tests are running in as isolated
// as a state as possible.
if (!($_ENV['ENV_RUNNING_UNIT_TESTS'] ?? false)) {
    require __DIR__ . '/phalcon_bootstrap.php';

    //Add Special Service To Register Modules For Application
    Di::getDefault()->set(
        Application::class,
        function (Di $diInstance) {
            $phApplication = new Application($diInstance);

            //Load Modules From Configuration
            $phApplication->registerModules(
                $phApplication->getDi()->get('config')->modules->toArray()
            );

            return $phApplication;
        }
    );
}

//Closure To Prevent Populating Global Namespace
return (function () : Application {
    //If bootstrapped, we get the application with the modules registered. Otherwise it's just an empty application.
    $systemDi = Di::getDefault() ?? new FactoryDefault();

    return $systemDi->get(
        Application::class,
        [
            $systemDi
        ]
    );
})();
