<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 6/3/17
     * Time: 12:09 PM
     */

    namespace TwistersFury\Phalcon\Shared\Forms\Element;

    use Phalcon\Config;
    use Phalcon\Forms\Element;
    use TwistersFury\Phalcon\Shared\Traits\Injectable;
    use TwistersFury\Phalcon\Shared\Validation\Validator\Captcha as CaptchaValidator;

    class Captcha extends Element {
        use Injectable;

        protected $apiKey = null;

        public function __construct( $name, $attributes = NULL ) {
            parent::__construct( $name, $attributes );

            /** @var Config $captchaConfig */
            $captchaConfig = $this->getDi()->get('config')->get('captcha');
            if (!$captchaConfig) {
                throw new \LogicException('Captcha API Key Config Value Not Set');
            }

            $this->apiKey = $captchaConfig->get('public');
            if (!$this->apiKey) {
                throw new \LogicException('Captcha API Public Key Not Set');
            }

            $this->addValidator(
                $this->getDi()->get(CaptchaValidator::class, [])
            );
        }

        public function render( $attributes = NULL ) {
            return <<<HTML
                <script src="https://www.google.com/recaptcha/api.js?hl=en"></script>
                <div class="g-recaptcha" data-sitekey="{$this->apiKey}"></div>
HTML;
        }
    }