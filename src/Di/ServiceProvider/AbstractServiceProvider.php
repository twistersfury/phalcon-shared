<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Di\ServiceProvider;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use TwistersFury\Phalcon\Shared\Traits\Register;

/**
 * Class AbstractServiceProvider. Helper Class To Break Up Registrations Into Separate Methods.
 *
 * @package TwistersFury\Merits\Shared\Di\ServiceProvider
 * @method mixed get(string $serviceName, array $params = null)
 * @method set(string $serviceName, \Closure|string $callback)
 * @method setShared(string $serviceName, \Closure|string $path = null)
 * @method bool has(string $serviceName)
 */
abstract class AbstractServiceProvider implements ServiceProviderInterface
{
    use Register;

    /** @var DiInterface */
    private $diInstance;

    public function __call($methodName, $methodArgs)
    {
        if (method_exists($this->diInstance, $methodName)) {
            return call_user_func_array([$this->diInstance, $methodName], $methodArgs);
        }

        throw new \InvalidArgumentException('Invalid Method Name');
    }

    public function register(DiInterface $di)
    {
        $this->diInstance = $di;

        $this->processRegisters();
    }
}
