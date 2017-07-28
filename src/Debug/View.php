<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/27/17
     * Time: 11:14 PM
     */

    namespace TwistersFury\Phalcon\Shared\Debug;

    use Phalcon\Cache\BackendInterface;
    use Phalcon\Mvc\View as phalconView;

    /**
     * Debug Class View
     *
     * Class exists to use for testing/debugging view paths. Activates Logging.
     *
     * @package TwistersFury\Phalcon\Shared\Debug
     */
    class View extends phalconView
    {
        protected function _engineRender(
            $engines,
            $viewPath,
            $silence,
            $mustClean,
            BackendInterface $cache = null
        ) {
            $engineList = [];
            foreach($engines as $engine) {
                $engineList[] = get_class($engine);
            }


            $this->di->get('logger')->debug(
                'Engine Render',
                [
                    'engines'  => $engineList,
                    'viewPath' => $viewPath,
                    'silence'  => $silence,
                    'mustClean' => $mustClean
                ]
            );

            return parent::_engineRender($engines, $viewPath, $silence, $mustClean,
                                  $cache);
        }
    }