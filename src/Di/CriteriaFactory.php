<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/8/17
     * Time: 12:29 AM
     */

    namespace TwistersFury\Phalcon\Shared\Di;

    use \Phalcon\Mvc\Model\CriteriaInterface;

    /**
     * Class CriteriaFactory
     *
     * @package TwistersFury\Phalcon\Shared\Di
     * @method CriteriaInterface getCriteria
     */
    class CriteriaFactory extends AbstractFactory
    {
        protected function registerCriteria()
        {
            $this->set(
                'criteria',
                function($modelName = null) {
                    $criteria = $this->get('\Phalcon\Mvc\Model\Criteria');
                    if ($modelName !== null) {
                        $criteria->setModelName($modelName);
                    }

                    return $criteria;
                }
            );

            return $this;
        }

        public function get($serviceName, $options = null) {
            if ($this->has($serviceName)) {
                return parent::get($serviceName, $options);
            } else if (static::$_default !== $this) {

                return static::$_default->get($serviceName, $options);
            }

            throw new \RuntimeException('Service Not Defined: ' . $serviceName);
        }

        public function getShared($serviceName, $options = null) {
            if ($this->has($serviceName)) {
                return parent::getShared($serviceName, $options);
            } else if (static::$_default !== $this) {
                return static::$_default->getShared($serviceName, $options);
            }

            throw new \RuntimeException('Service Not Defined: ' . $serviceName);
        }
    }