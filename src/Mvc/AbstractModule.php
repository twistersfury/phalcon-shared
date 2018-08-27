<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Mvc;

use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\DiInterface;

abstract class AbstractModule implements ModuleDefinitionInterface
{
    public function registerAutoloaders(DiInterface $dependencyInjector = null)
    {}

    public function registerServices(DiInterface $dependencyInjector)
    {
        $dependencyInjector->getShared('dispatcher')
            ->setModuleName($this->getModuleName())
            ->setDefaultNameSpace(
                __NAMESPACE__ . '\\' . $this->getDefaultControllerNamespace()
            );
    }

    protected function getDefaultControllerNamespace(): string
    {
        return 'Controllers';
    }

    protected function getModuleName(): string
    {
        return explode('\\', get_called_class())[2];
    }
}