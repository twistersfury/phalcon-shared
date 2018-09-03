<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Mvc;

use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\DiInterface;
use Phalcon\Text;

abstract class AbstractModule implements ModuleDefinitionInterface
{
    public function registerAutoloaders(DiInterface $dependencyInjector = null)
    {}

    public function registerServices(DiInterface $dependencyInjector)
    {
        $dispatcher = $dependencyInjector->getShared('dispatcher');

        $className = explode('\\', get_called_class());

        $dispatcher->setModuleName(str_replace('_', '-', Text::uncamelize($className[2])));
        $dispatcher->setDefaultNameSpace(
            str_replace(
                'Module',
                $this->getDefaultControllerNamespace(),
                get_called_class()
            )
        );
    }

    protected function getDefaultControllerNamespace(): string
    {
        return 'Controllers';
    }
}