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
    use TwistersFury\Phalcon\Shared\Traits\Injectable;

    class UniqueEmail extends Validator {
        use Injectable;

        public function validate( Validation $validation, $attribute ) {
            $userModel = $this->getDI()->get('criteriaFactory')
                                       ->getUserByEmail($validation->getValue($attribute))
                                       ->execute()->getFirst();

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
    }