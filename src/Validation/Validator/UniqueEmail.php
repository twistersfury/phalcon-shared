<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 6/2/17
     * Time: 10:18 PM
     */

    namespace TwistersFury\Inventory\Shared\Validation\Validator;

    use Phalcon\Validation;
    use Phalcon\Validation\Validator;
    use TwistersFury\Phalcon\Shared\Interfaces\UserCriteriaInterface;
    use TwistersFury\Phalcon\Shared\Traits\Injectable;

    class UniqueEmail extends Validator {
        use Injectable;

        public function validate( Validation $validation, $attribute ) {
            $userModel = $this->buildCriteria($validation, $attribute)->execute()->getFirst();

            if ($userModel) {
                $userMessage = $this->getOption('message', 'E-Mail already exists.');

                $validation->appendMessage(
                    $this->getDI()->get(
                        '\Phalcon\Validation\Message',
                        [
                            $userMessage,
                            $attribute,
                            "E-Mail"
                        ]
                    )
                );

                return false;
            }

            return true;
        }

        protected function buildCriteria(Validation $validation, $attribute)
        {
            $criteriaFactory = $this->getDI()->get('criteriaFactory');
            if (!($criteriaFactory instanceof UserCriteriaInterface)) {
                throw new \LogicException('Criteria Factory Must Implement UserCriteriaInterface');
            }

            return $criteriaFactory->getUserByEmail($validation->getValue($attribute));
        }
    }