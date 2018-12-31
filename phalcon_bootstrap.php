<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace Phalcon\Bootstrap;

use Monolog\ErrorHandler;
use Phalcon\Debug;
use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use TwistersFury\Phalcon\Shared\Di\ServiceProvider\Config;
use TwistersFury\Phalcon\Shared\Di\ServiceProvider\Logging;

//Run in closure to not populate Global Namespace
return (function (): Di
{
    $systemDi = null;
    if ($_ENV['ENV_RUNNING_CODECEPTION'] ?? false || !Di::getDefault()) {
        $systemDi = new FactoryDefault();
    }

    $systemDi = $systemDi ?? Di::getDefault();

    //Always Register The Config and Logger Service Providers
    $systemDi->register(
        $systemDi->get(Config::class)
    );

    $systemDi->register(
        $systemDi->get(Logging::class)
    );

    //Load Other Providers From Config
    array_map(
        function ($configName) use ($systemDi) {
            foreach ($systemDi->get('config')->get($configName) ?? [] as $providerClass) {
                $systemDi->register(
                    $systemDi->get($providerClass)
                );
            }
        },
        [
            'providers',
            TF_PROVIDERS_TYPE . '_providers'
        ]
    );

    //Load Services From Config
    array_map(
        function ($configName) use ($systemDi) {
            foreach ($systemDi->get('config')->get($configName) ?? [] as $service => $serviceOptions) {
                if (!$serviceOptions instanceof \Phalcon\Config) {
                    $serviceOptions = [
                        'service' => $serviceOptions
                    ];
                } else {
                    $serviceOptions = $serviceOptions->toArray();
                }

                $serviceOptions = array_merge([
                    'service' => null,
                    'shared'  => false
                ], $serviceOptions);

                $systemDi->set($service, $serviceOptions['service'], $serviceOptions['shared']);
            }
        },
        [
            'di_services',
            TF_PROVIDERS_TYPE . '_di_services'
        ]
    );

    //Register Events From Config
    array_map(
        function ($configName) use ($systemDi) {
            /** @var \Phalcon\Events\Manager $eventsManager */
            $eventsManager = $systemDi->get('eventsManager');
            foreach ($systemDi->get('config')->get($configName) ?? [] as $eventName => $eventCallback) {
                $eventsManager->attach($eventName, $eventCallback);
            }
        },
        [
            'events',
            TF_PROVIDERS_TYPE . '_events'
        ]
    );

    //Register Default Route Details
    if ($systemDi instanceof FactoryDefault) {
        $systemDi->getShared('router')->add('/', [
            'action'     => $systemDi->get('config')->system->defaultRoute->action ?? 'index',
            'controller' => $systemDi->get('config')->system->defaultRoute->controller ?? 'controller',
            'module'     => $systemDi->get('config')->system->defaultRoute->module ?? 'module'
        ]);
    }

    $systemDi->get('logger')->info('Bootstrap Complete');

    //If Debug Mode Enabled, Then Enable Phalcon Debug Listener
    if ($systemDi->get('config')->debug && TF_DEBUG & TF_DEBUG_PHALCON) {
        $systemDi->get('logger')->debug('Phalcon Debug Enabled');

        $systemDi->get(Debug::class)->listen(true, false);

        $errorHandler = $systemDi->get(
            ErrorHandler::class,
            [$systemDi->get('logger')]
        );
    } else {
        //Otherwise Just Enable The Monolog Error Handler
        $errorHandler = ErrorHandler::register($systemDi->get('logger'));
    }

    //Register Instance To Di For Access Later
    $systemDi->set(
        ErrorHandler::class,
        $errorHandler
    );

    return $systemDi;
})();
