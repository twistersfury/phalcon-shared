<?php
/**
 * Created by PhpStorm.
 * User: fenikkusu
 * Date: 9/4/18
 * Time: 10:19 PM
 *//**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Di\ServiceProvider;

use Phalcon\Db\Adapter\Factory;
use Phalcon\Db\AdapterInterface;

class Database extends AbstractServiceProvider
{

    /**
     * Register Database(s) From Config
     *
     * @return \TwistersFury\Phalcon\Shared\Di\ServiceProvider\Database
     */
    protected function registerDatabases(): self
    {
        /** @var \Phalcon\Config $config */
        $config = $this->get('config');

        //Register Primary Config
        if ($config->get('database')) {
            $this->registerDatabase(
                'db',
                $config->database
            );
        };

        foreach ($config->databases ?? [] as $serviceName => $serviceConfig) {
            $this->registerDatabase(
                $serviceName,
                $serviceConfig
            );
        }

        return $this;
    }

    /**
     * Registers Specific Database.
     *
     * Note: Private So Normal Register Loop Does Not Hit
     *
     * @param string          $serviceName
     * @param \Phalcon\Config $config
     *
     * @return \TwistersFury\Phalcon\Shared\Di\ServiceProvider\Database
     */
    private function registerDatabase(string $serviceName, \Phalcon\Config $config): self
    {
        $this->setShared(
            $serviceName,
            function() use ($config): AdapterInterface {
                return Factory::load($config->toArray());
            }
        );

        return $this;
    }
}