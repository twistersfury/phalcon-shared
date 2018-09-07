<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Di\ServiceProvider;

use Phalcon\Assets\Manager;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Flash\Session;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Session\Adapter\Files;
use Phalcon\Translate\Adapter\NativeArray;
use TwistersFury\Phalcon\Shared\Mvc\View\Debug;

/**
 * Class Mvc
 *
 * @package TwistersFury\Merits\Shared\Di\ServiceProvider
 * @method mixed get(string $serviceName, $params = [])
 */
class Mvc extends AbstractServiceProvider implements ServiceProviderInterface
{

    public function registerOverrides(): self
    {
        if ($this->get('config')->debug) {
            $this->set(View::class, Debug::class);
        }

        return $this;
    }

    public function registerRouter(): self
    {
        $this->set('router', function () {
            /** @var Router $router */
            $router = $this->get(Router::class, [false]);

            $router->removeExtraSlashes(true);
            $router->notFound([
                    'module'     => 'support',
                    'controller' => 'system',
                    'action'     => 'noRoute'
                ]);

            $config = $this->get('config');

            $router->setDefaultModule($this->config->system->defaultRoute->module ?? 'profile')->setDefaultController($this->config->system->defaultRoute->controller ?? 'dashboard')->setDefaultAction($this->config->system->defaultRoute->action ?? 'index');

            foreach ($config->routes as $route) {
                $router->mount($this->get($route));
            }

            return $router;
        });

        return $this;
    }

    protected function registerView(): self
    {
        $this->set('view', function () {
            /** @var View $view */
            $view = $this->get(View::class, [
                $this
            ]);

            $themeName  = $this->get('config')->system->theme;
            $moduleName = $this->get('dispatcher')->getModuleName();

            $viewDirs = [
                '/' . $themeName . '/' . $moduleName,
                '/' . $themeName
            ];

            if ($themeName !== 'default') {
                $viewDirs[] = '/default/' . $moduleName;
                $viewDirs[] = '/default';
            }

            $view->setBasePath(APPLICATION_PATH . '/themes')->setLayoutsDir('layouts/')->setPartialsDir('partials/')->setViewsDir($viewDirs);

            $view->registerEngines([
                '.volt' => 'voltEngine'
            ]);

            return $view;
        });

        return $this;
    }

    protected function registerUrl(): self
    {
        $this->set('url', function () {
            /** @var Url $url */
            $url = $this->get(Url::class);

            $url->setBasePath($this->get('request')->getServer('DOCUMENT_ROOT'))->setBaseUri('//' . $this->get('request')->getServer('HTTP_HOST') . '/')->setStaticBaseUri('//' . $this->get('request')->getServer('HTTP_HOST') . '/');

            return $url;
        });

        return $this;
    }

    protected function registerVoltEngine(): self
    {
        $this->set('voltEngine', function (View $view, DiInterface $di) {
            /** @var Volt $volt */
            $volt = $this->get(Volt::class, [$view, $di]);

            $volt->setOptions([
                'compileAlways'     => $this->get('config')->debug !== false,
                'compiledPath'      => $this->get('config')->application->cacheDir . '/',
                'compiledSeparator' => '.'
            ]);

            $this->get('eventsManager')->fire('voltEngine:initCompiler', $volt->getCompiler());

            return $volt;
        });

        return $this;
    }

    protected function registerFlashSession(): self
    {
        $this->set('flashSession', function () {
            return new Session([
                'error'   => 'alert alert-sm alert-danger',
                'success' => 'alert alert-sm alert-success',
                'notice'  => 'alert alert-sm alert-info',
                'warning' => 'alert alert-sm alert-warning'
            ]);
        });

        return $this;
    }

    protected function registerSession(): self
    {
        $this->setShared('session', function () {
            /** @var Files $session */
            $session = $this->get(Files::class);
            $session->start();

            return $session;
        });

        return $this;
    }

    protected function registerTranslate(): self
    {
        $this->setShared('translate', function () {
            $language = $this->get('request')->getBestLanguage();

            $filePath = APPLICATION_PATH . '/locale/' . $language . '.php';
            if (!file_exists($filePath)) {
                $filePath = APPLICATION_PATH . '/locale/en.php';
            }

            return $this->get(NativeArray::class, [
                    [
                        'content' => include $filePath
                    ]
                ]);
        });

        return $this;
    }

    protected function registerDispatcher(): self
    {
        $this->setShared('dispatcher', function () {
            /** @var Dispatcher $dispatcher */
            $dispatcher = $this->get(Dispatcher::class);

            $dispatcher->setEventsManager($this->get('eventsManager'));

            foreach ($this->get('config')->middleware ?? [] as $middleWare) {
                $dispatcher->getEventsManager()->attach('dispatch', $this->get($middleWare));
            }

            return $dispatcher;
        });

        return $this;
    }

    protected function registerAssets(): self
    {
        $this->setShared(
            'assets',
            function () {
                /** @var Manager $assets */
                $assets = $this->get(Manager::class);

                if (($themeName = $this->get('config')->system->theme)) {
                    $assets->collection($themeName . '-external')
                        ->setLocal(false);

                    $assets->collection($themeName . '-internal')
                        ->setLocal(true);
                }

                return $assets;
            }
        );

        return $this;
    }
}
