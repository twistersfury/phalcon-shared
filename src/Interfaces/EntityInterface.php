<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/28/17
     * Time: 12:38 AM
     */

    namespace TwistersFury\Phalcon\Shared\Interfaces;

    use Phalcon\Mvc\EntityInterface as phEntityInterface;

    interface EntityInterface extends phEntityInterface
    {
        public function getTitle() : string;
        public function getId() : ?int;
    }