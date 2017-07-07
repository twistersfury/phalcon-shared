<?php
    /**
     * PHP7 Phalcon Core Library
     *
     * @author Phoenix <phoenix@twistersfury.com>
     * @license http://www.opensource.org/licenses/mit-license.html MIT License
     * @copyright 2016 Twister's Fury
     */

    namespace TwistersFury\Phalcon\Shared;

    use Phalcon\Di;
    use Phalcon\Events\Event;
    use Phalcon\Loader;
    use TwistersFury\Phalcon\Shared\Helpers\Defines;

    //Let Phalcon Handle Auto-Loading
    (new Loader())->registerNamespaces(
        [
            'TwistersFury\Phalcon\Shared' => __DIR__
        ]
    )->register(true);

    //If We are In CLI Mode, Chances Are We Are In Testing Mode
    if (PHP_SAPI !== 'cli') {
        if (!Di::getDefault()) {
            throw new \LogicException("Phalcon Di Not Yet Configured");
        } else if (!Di::getDefault()->has('eventsManager')) {
            throw new \LogicException("Phalcon Di Events Manager Not Yet Configured");
        }
    } else {
        $factoryDefault = new \Phalcon\Di\FactoryDefault();
        $factoryDefault->setShared('eventsManager', \Phalcon\Events\Manager::class);
        $factoryDefault->get('eventsManager')->attach('twistersfury::dynamic-defines', function(\TwistersFury\Phalcon\Shared\Helpers\Defines $defines) {
            $defines->define('TF_DEBUG_MODE', TF_DEBUG_MODE_TESTING);
        });

        \Phalcon\Di::setDefault($factoryDefault);

        unset($factoryDefault);
    }

    /** @var \Phalcon\Events\Manager $eventsManager */
    $eventsManager = Di::getDefault()->get('eventsManager');

    $eventsManager->attach('twistersfury:static-defines', function(Event $event, Defines $definesHelper) {
        $definesHelper->define('TF_DEBUG_MODE_DISABLED', 0)
            ->define('TF_DEBUG_MODE_TESTING' , 1);
    });

    $eventsManager->attach('twistersfury:dynamic-defines', function(Event $event, Defines $definesHelper) {
        $definesHelper->define(
            'TF_SHARED_SOURCE',
            __DIR__
        )->define(
            'TF_SHARED_PROJECT',
            realpath(__DIR__ . '/..')
        )->define(
            'TF_SHARED_TESTS',
            realpath(__DIR__ . '/../tests/unit')
        )->define('TF_APP_ROOT', function() {
            $isComposer = strstr(__DIR__, 'vendor') !== false;

            return $isComposer ? __DIR__ . '/../../../app' : __DIR__ . '/..';
        })->define('TF_DEBUG_MODE', getenv('TF_DEBUG_MODE') ?: TF_DEBUG_MODE_DISABLED);
    }, -1000);

    $eventsManager->fire('twistersfury:static-defines', Di::getDefault()->get(Defines::class));
    $eventsManager->fire('twistersfury:dynamic-defines', Di::getDefault()->get(Defines::class));

    unset($eventsManager);