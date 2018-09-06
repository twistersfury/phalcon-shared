<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Cache\Backend;

use Phalcon\Cache\Backend;
use Phalcon\Cache\BackendInterface;

class None extends Backend implements BackendInterface
{
    public function get($keyName, $lifetime = null)
    {
        return null;
    }

    public function exists($keyName = null, $lifetime = null)
    {
        return false;
    }

    public function delete($keyName)
    {
        return true;
    }

    public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
    {
        return true;
    }

    public function queryKeys($prefix = null)
    {
        return [];
    }
}
