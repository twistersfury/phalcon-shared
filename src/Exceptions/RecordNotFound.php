<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Exceptions;

use InvalidArgumentException;

class RecordNotFound extends InvalidArgumentException
{
    protected $message = 'Record could not be found.';
}