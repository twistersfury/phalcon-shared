<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/6/17
     * Time: 7:50 PM
     */

    namespace TwistersFury\Shared\Tests\Data\Di;

    use TwistersFury\Phalcon\Shared\Di\AbstractFactory;

    class DummyFactory extends AbstractFactory {
        protected $priorityServices = [];

        public function registerSomeTest()
        {
            $this->set('someTest', 'someTestClass');
        }
    }