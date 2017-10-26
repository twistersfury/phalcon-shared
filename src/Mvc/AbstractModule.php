<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/28/17
     * Time: 12:36 AM
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
                               ->setDefaultNameSpace(
                                   str_replace(
                                       'Module',
                                       'Mvc\Controllers',
                                       get_called_class()
                                   )
                               );
        }
    }
