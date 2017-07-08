<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 6/3/17
     * Time: 11:53 AM
     */

    namespace TwistersFury\Inventory\Shared\Validation\Validator;

    use Phalcon\Validation;
    use Phalcon\Validation\Validator;
    use TwistersFury\Phalcon\Shared\Traits\Injectable;

    class Captcha extends Validator {
        const API_URI = 'https://www.google.com/recaptcha/api/siteverify';

        use Injectable;

        private $apiKey = null;

        public function __construct( array $options = NULL ) {
            parent::__construct( $options );

            /** @var \Phalcon\Config $captchaConfig */
            $captchaConfig = $this->getDi()->get('config')->get('captcha');
            if (!$captchaConfig) {
                throw new \InvalidArgumentException('Captcha Config Not Set');
            } else if (!$captchaConfig->get('private')) {
                throw new \InvalidArgumentException('Captcha API Key Config Value Not Set');
            }

            $this->apiKey = $captchaConfig->get('private');
        }

        public function validate( Validation $validation, $attribute ) {
            if (!$this->verifyCaptcha($validation->getValue('g-recaptcha-response'), $this->getDi()->get('request')->getClientAddress())) {
                $validation->appendMessage(
                    $this->getDi()->get(
                        '\Phalcon\Validation\Message',
                        [
                            $this->getOption('message', 'Invalid Captcha'),
                            $attribute,
                            'Captcha'
                        ]
                    )
                );

                return false;
            }

            return true;
        }

        protected function verifyCaptcha($responseValue, $remoteIp) {
            $requestParams = [
                'secret'   => $this->apiKey,
                'response' => $responseValue,
                'remoteip' => $remoteIp
            ];

            $jsonResponse = json_decode(file_get_contents(static::API_URI . '?' . http_build_query($requestParams)));
            return (bool) $jsonResponse->success;
        }
    }