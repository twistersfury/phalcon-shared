<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Di\ServiceProvider\Logging;

use Monolog\Logger;
use Phalcon\DiInterface;
use TwistersFury\Phalcon\Shared\Di\ServiceProvider\AbstractServiceProvider;
use Rollbar\Monolog\Handler\RollbarHandler;
use Rollbar\RollbarLogger;

class Rollbar extends AbstractServiceProvider
{
    public function register(DiInterface $di)
    {
        if (!class_exists('Rollbar\RollbarLogger')) {
            throw new \LogicException('You must install the composer package rollbar/rollbar (^1.5.3) to use this Service Provider');
        }

        parent::register($di);
    }

    protected function registerRollbarLogger(): Rollbar
    {
        $this->set(
            RollbarLogger::class,
            function () {
                $storedConfig = $this->get('config')->services->rollbar ?
                    $this->get('config')->services->rollbar->toArray() : [];

                $rollbarConfig = array_merge(
                    [
                        'access_token'                   => null,
                        'send_message_trace'             => true,
                        'include_error_code_context'     => true,
                        'include_exception_code_context' => true
                    ],
                    $storedConfig
                );

                return new RollbarLogger($rollbarConfig);
            }
        );

        return $this;
    }

    protected function registerRollbarHandler(): Rollbar
    {
        $this->set(
            RollbarHandler::class,
            function (bool $handlerLevel) {
                return new RollbarHandler(
                    $this->get(RollbarLogger::class),
                    $handlerLevel
                );
            }
        );
    }
}