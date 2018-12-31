<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\SignUp\Bootstrap;

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;

//JIC It Isn't In The Web Server Config - Only Used In Actual Project So We Don't Care If Path Is Wrong For Local
$_SERVER['DOCUMENT_ROOT'] = ($_SERVER['DOCUMENT_ROOT'] ?? false) ?: realpath(__DIR__ . '/../../../public');

require_once __DIR__ . '/phalcon_loader.php';

//Closure To Prevent Populating Global Namespace
return (function () : Application {
    $systemDi = new FactoryDefault();
    //Do not run the bootstrap if unit tests are running. This ensures that the unit tests are running in as isolated
    // as a state as possible.
    if (!($_ENV['ENV_RUNNING_UNIT_TESTS'] ?? false)) {
        $systemDi = require __DIR__ . '/phalcon_bootstrap.php';

        //Add Special Service To Register Modules For Application
        $systemDi->attempt(Application::class, function (Di $diInstance) {
            $phApplication = new Application($diInstance);

            //Load Modules From Configuration
            $phApplication->registerModules($phApplication->getDi()->get('config')->modules->toArray());

            return $phApplication;
        });
    }

    return $systemDi->get(
        Application::class,
        [
            $systemDi
        ]
    );
})();
