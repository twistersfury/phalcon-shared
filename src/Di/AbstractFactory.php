<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 6/17/17
     * Time: 10:32 PM
     */

    namespace TwistersFury\Phalcon\Shared\Di;

    use Phalcon\Di\FactoryDefault;

    /**
     * Class AbstractFactory
     *
     * Basic Services For Factory Default
     *
     * @package TwistersFury\Phalcon\Shared\Di
     */
    abstract class AbstractFactory extends FactoryDefault
    {
        /**
         * @var array - Pre Defined Priority List
         */
        protected $priorityServices = [];

        /**
         * Register All Services Prefixed With 'register'
         * @return AbstractFactory
         */
        final public function processServices() : AbstractFactory {
            $classMethods = array_unique(
                array_merge(
                    $this->priorityServices,
                    get_class_methods($this)
                )
            );

            foreach($classMethods as $methodName) {
                if ($methodName !== 'register' && substr($methodName, 0, 8) === 'register') {
                    $this->{$methodName}();
                }
            }

            return $this;
        }

        public function get($serviceName, $options = null) {
            if ($this->has($serviceName)) {
                return parent::get($serviceName, $options);
            } else if (static::getDefault() !== $this) {

                return static::getDefault()->get($serviceName, $options);
            }

            throw new \RuntimeException('Service Not Defined: ' . $serviceName);
        }

        public function getShared($serviceName, $options = null) {
            if ($this->has($serviceName)) {
                return parent::getShared($serviceName, $options);
            } else if (static::getDefault() !== $this) {
                return static::getDefault()->getShared($serviceName, $options);
            }

            throw new \RuntimeException('Service Not Defined: ' . $serviceName);
        }
    }