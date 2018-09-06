<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Di\ServiceProvider;

use Phalcon\Cache\Backend\Factory;
use Phalcon\Cache\Frontend\None as NoneFrontend;
use TwistersFury\Phalcon\Shared\Cache\Backend\None;

class Cache extends AbstractServiceProvider
{
    protected function registerCaches() : self
    {
        foreach ($this->get('config')->services->cache as $cacheName => $cacheConfig) {
            $this->configureCache(
                $cacheName,
                $cacheConfig
            );
        }

        return $this;
    }

    private function configureCache(string $cacheName, \Phalcon\Config $config)
    {
        $this->setShared(
            $cacheName,
            function () use ($config) {
                if ($config->adapter === 'none') {
                    return $this->get(
                        None::class,
                        [
                            [
                                'frontend' => $this->get(NoneFrontend::class)
                            ]
                        ]
                    );
                }

                return Factory::load($config);
            }
        );
    }
}
