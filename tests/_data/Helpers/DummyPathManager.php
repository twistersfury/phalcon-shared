<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/6/17
     * Time: 7:59 PM
     */

    namespace TwistersFury\Shared\Tests\Data\Helpers;

    use TwistersFury\Phalcon\Shared\Helpers\PathManager;

    class DummyPathManager extends PathManager {
        public $wasCalled = false;

        public function setConfiguration( $configData = NULL ) {
            $this->wasCalled = true;
        }
    }