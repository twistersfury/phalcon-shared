<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/8/17
     * Time: 2:58 PM
     */

    namespace TwistersFury\Phalcon\Shared\Interfaces;

    interface UserCriteriaInterface {
        public function getUserByEmail($emailAddress);
    }