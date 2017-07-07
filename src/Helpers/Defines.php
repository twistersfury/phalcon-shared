<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 6/17/17
     * Time: 5:59 PM
     */

    namespace TwistersFury\Phalcon\Shared\Helpers;

    class Defines {

        /**
         * @param string $constantName
         * @param mixed|callable $constantValue
         *
         * @return Defines
         */
        public function define($constantName, $constantValue) : Defines {
            if (!\defined($constantName)) {
                if (is_callable($constantValue)) {
                    $constantValue = call_user_func($constantValue);
                }

                \define($constantName, $constantValue);
            }

            return $this;
        }
    }