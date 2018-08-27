<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Mvc\View;

use Phalcon\Cache\BackendInterface;
use Phalcon\Mvc\View;

/**
 * Class Useful For Debugging Views Not Loading. Only Loaded If Debug Mode Enabled.
 *
 * @package TwistersFury\Phalcon\Shared\Mvc\View
 */
class Debug extends View
{
    public function _engineRender($engines, $viewPath, $silence, $mustClean, BackendInterface $cache = null)
    {
        return parent::_engineRender($engines, $viewPath, $silence, $mustClean, $cache);
    }
}
