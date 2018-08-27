<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 *
 * Shims To Maintain Backwards Compatibility
 */

/**
 * @class TwistersFury\Phalcon\Shared\Di\CriteriaFactory
 * @deprecated
 */
class_alias('\TwistersFury\Phalcon\Shared\Mvc\Model\Criteria\Factory', 'TwistersFury\Phalcon\Shared\Di\CriteriaFactory');

/**
 * @class TwistersFury\Phalcon\Shared\Interfaces\CriteriaFactoryInterface
 * @deprecated
 */
class_alias('\TwistersFury\Phalcon\Shared\Interfaces\CriteriaFactory', '\TwistersFury\Phalcon\Shared\Interfaces\CriteriaFactoryInterface');