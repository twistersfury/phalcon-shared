<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Mvc\Model\Criteria;

use Phalcon\Di;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\CriteriaInterface;
use TwistersFury\Phalcon\Shared\Interfaces\CriteriaFactory;
use TwistersFury\Phalcon\Shared\Traits\Register;

abstract class Factory extends Di implements CriteriaFactory
{
    use Register;

    public function __construct()
    {
        parent::__construct();

        $this->processRegisters();
    }

    /**
     * @return \Phalcon\Mvc\Model\Criteria
     * @param string $modelName Model Name
     */
    protected function getCriteria(string $modelName): CriteriaInterface
    {
        return $this->get(Criteria::class)->setModelName($modelName);
    }

    /**
     * Fallback To Di::getDefault If Not Registered In Current Di
     *
     * @param string $serviceName
     * @param null   $options
     *
     * @return mixed
     */
    final public function get($serviceName, $options = null)
    {
        if ($this->has($serviceName)) {
            return parent::get($serviceName, $options);
        }

        return Di::getDefault()->get($serviceName, $options);
    }

    /**
     * Fallback To Di::getDefault If Not Registered In Current Di
     * @param string $serviceName
     * @param null   $options
     *
     * @return mixed
     */
    final public function getShared($serviceName, $options = null)
    {
        if ($this->has($serviceName)) {
            return parent::getShared($serviceName, $options);
        }

        return Di::getDefault()->getShared($serviceName, $options);
    }
}
