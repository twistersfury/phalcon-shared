<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Di\ServiceProvider;

use Phalcon\Config\Adapter\Grouped;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use TwistersFury\Phalcon\Shared\Config\Theme;

class Config extends AbstractServiceProvider implements ServiceProviderInterface
{
    protected $priorityMethods = [
        'registerEventManager',
        'registerEvents'
    ];

    public function registerEventManager() : self
    {
        $this->setShared(
            'eventsManager',
            Manager::class
        );

        return $this;
    }

    public function registerEvents(): self
    {
        $this->get('eventsManager')->attach('config:build', function (Event $event, array $configList) {
            //Base Phalcon Config
            $configList[] = APPLICATION_PATH . '/config/config.php';

            //Any Distribution Config Files
            /** @var \SplFileInfo $configFile */
            foreach (new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    APPLICATION_PATH . '/config/dist/',
                    \RecursiveDirectoryIterator::SKIP_DOTS
                )
            ) as $configFile) {
                if (substr($configFile->getBasename(), 0, 7) === 'config.') {
                    $configList[] = $configFile->getPathname();
                }
            }

            //Local Config
            $filePath = APPLICATION_PATH . '/config/local/config.php';
            if (file_exists($filePath)) {
                $configList[] = $filePath;
            }

            //Environment Config
            $filePath = APPLICATION_PATH . '/config/local/' . APPLICATION_ENV . '.config.php';
            if (file_exists($filePath)) {
                $configList[] = $filePath;
            }

            //Host Config
            $filePath = APPLICATION_PATH . '/config/local/' . gethostname() . '.config.php';
            if (file_exists($filePath)) {
                $configList[] = $filePath;
            }

            return $configList;
        });

        return $this;
    }

    public function registerConfig() : self
    {
        $this->setShared('config', function () {
            $configList = $this->get('eventsManager')->fire('config:build', []);

            return $this->get(Grouped::class, [$configList]);
        });

        return $this;
    }

    protected function registerThemeConfig() : self
    {
        $this->setShared('themeConfig', function () {
            if ($this->has('serviceCache') && ($themeConfig = $this->get('serviceCache')->get('themeConfig'))) {
                return $themeConfig;
            }

            /** @var Theme $config */
            $config = $this->get(
                Theme::class,
                [
                    include APPLICATION_PATH . '/themes/config.php'
                ]
            );

            $filePath = APPLICATION_PATH . '/themes/' . $this->get('config')->system->theme . '/config.php';
            if (file_exists($filePath)) {
                $config->merge(
                    $this->get(
                        Theme::class,
                        [
                            include $filePath
                        ]
                    )
                );
            }

            if ($this->has('serviceCache')) {
                $this->get('serviceCache')->save('themeConfig', $config);
            }

            return $config;
        });

        return $this;
    }
}
