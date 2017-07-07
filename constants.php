<?php
    /**
     * @author    Phoenix <phoenix@twistersfury.com>
     * @license   proprietary
     * @copyright 2016 Twister's Fury
     */

    //Using Closer To Trick IDE Into Thinking File Runs Afterwards
    (function() {
        throw new \Exception('This file is only intended as a metadata placeholder for defines.');
    })();

    //Path Constants
    define('TF_APP_ROOT', __DIR__);
    define('TF_SHARED_SOURCE', '');
    define('TF_SHARED_PROJECT', '');
    define('TF_SHARED_TESTS', '');

    //Debug Constants
    define('TF_DEBUG_MODE_DISABLED', 0);
    define('TF_DEBUG_MODE_TESTING' , 1);
    define('TF_DEBUG_MODE'         , TF_DEBUG_MODE_DISABLED);
    define('TF_TESTING_1'          , 10);
    define('TF_TESTING_2'          , 10);