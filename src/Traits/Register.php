<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Traits;

trait Register
{
    protected $priorityMethods = [];

    public function processRegisters()
    {
        $classMethods = array_unique($this->priorityMethods + get_class_methods($this));

        foreach ($classMethods as $methodName) {
            if ($methodName !== 'register' && substr($methodName, 0, 8) === 'register') {
                $this->{$methodName}();
            }
        }

        return $this;
    }
}
