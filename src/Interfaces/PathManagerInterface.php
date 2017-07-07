<?php
    /**
     * PHP7 Phalcon Core Library
     *
     * @author Phoenix <phoenix@twistersfury.com>
     * @license http://www.opensource.org/licenses/mit-license.html MIT License
     * @copyright 2016 Twister's Fury
     */

    namespace TwistersFury\Phalcon\Shared\Interfaces;

    interface PathManagerInterface {
        public function getApplicationDir() : string;
        public function getConfigDir()  : string;
        public function getModulesDir() : string;
        public function getCacheDir()   : string;
    }