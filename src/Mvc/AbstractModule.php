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



        $dispatcher->setModuleName($this->getModuleName());
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
        return 'Controller';
    }

    /**
     * Retrieves Module Name. Assumes layer above Mvc in namespace.
     *
     * @return string
     * @throws \LogicException
     */
    protected function getModuleName(): string
    {
        $className = explode('\\', get_called_class());

        $totalItems = count($className) - 1;

        for ($currentPos = 2; $currentPos < $totalItems; $currentPos++) {
            $moduleName = $className[$currentPos];
            if ($className[$currentPos + 1] === 'Mvc') {
                break;
            }
        }

        if (!isset($moduleName)) {
            throw new \LogicException('Module Name Could Not Be Determined');
        }


        return str_replace('_', '-', Text::uncamelize($className[2]));
    }
}