<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\SignUp\Bootstrap;

use InvalidArgumentException;
use Phalcon\Cli\Console;
use Phalcon\Di;
use Phalcon\Di\FactoryDefault\Cli;

//JIC It Isn't In The Web Server Config - Only Used In Actual Project So We Don't Care If Path Is Wrong For Local
$_SERVER['DOCUMENT_ROOT'] = ($_SERVER['DOCUMENT_ROOT'] ?? false) ?: realpath(__DIR__ . '/../../../public');

require_once __DIR__ . '/phalcon_loader.php';

//Closure To Prevent Populating Global Namespace
(function () : void {
    if (!isset($_SERVER['argv'][1])) {
        throw new InvalidArgumentException('Missing Task Name');
    }

    Di::setDefault(new Cli());

    $systemDi = require __DIR__ . '/phalcon_bootstrap.php';

    /** @var Console $phalconConsole */
    $phalconConsole = $systemDi->get(
        Console::class,
        [
            $systemDi
        ]
    );

    $systemDi->setShared('console', $phalconConsole);

    $phalconConsole->handle([
        'task'   => $_SERVER['argv'][1],
        'action' => $_SERVER['argv'][2] ?? 'main',
        'params' => $_SERVER['argv'][3] ?? []
    ]);
})();
