<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/8/17
     * Time: 12:29 AM
     */

    namespace TwistersFury\Phalcon\Shared\Di;

    use Phalcon\Mvc\Model\CriteriaInterface;
    use TwistersFury\Phalcon\Shared\Interfaces\CriteriaFactoryInterface;

    /**
     * Class CriteriaFactory
     *
     * @package TwistersFury\Phalcon\Shared\Di
     * @method CriteriaInterface getCriteria
     */
    class CriteriaFactory extends AbstractFactory implements CriteriaFactoryInterface
    {
        public function __construct() {
            //Disabling Default Constructor
            //TODO: This Shouldn't Have To Happen - Only Here Because AbstractFactory::processServices
            //TODO: Not In \Phalcon\Di
        }

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
    }