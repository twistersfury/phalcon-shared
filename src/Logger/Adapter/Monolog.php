<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/27/17
     * Time: 11:27 PM
     */

    namespace TwistersFury\Phalcon\Shared\Logger\Adapter;

    use Monolog\Logger;
    use Phalcon\Logger as phLogger;
    use Phalcon\Logger\Adapter;
    use Phalcon\Logger\FormatterInterface;
    use Phalcon\Logger\AdapterInterface;
    use TwistersFury\Phalcon\Shared\Traits\Injectable;

    /**
     * Class Monolog
     *
     * Class intended as a Phalcon Valid Adapter for Logging that just proxies all calls to the Monolog Package
     *
     * @package TwistersFury\Phalcon\Shared\Logger\Adapter
     */
    class Monolog extends Adapter implements AdapterInterface
    {
        use Injectable;

        /** @var Logger */
        protected $monoLogger = null;

        public function __construct(Logger $monoLog = null) {
            if (!$monoLog) {
                $monoLog = $this->getDi()->get(Logger::class, ['global']);
            }

            $this->monoLogger = $monoLog;
        }

        public function getLogger()
        {
            return $this->monoLogger;
        }

        public function getFormatter()
        {
            throw new \LogicException('Phalcon Formatter Not Implemented. Use Monolog::getLogger() Instead.');
        }

        public function setFormatter(FormatterInterface $formatter)
        {
            throw new \LogicException('Phalcon Formatter Not Implemented. Use Monolog::getLogger() Instead.');
        }

        public function close()
        {
            $this->getLogger()->close();
        }

        protected function logInternal(string $message, int $type, int $time, array $context)
        {
            switch($type) {
                case phLogger::DEBUG:
                    $type = Logger::DEBUG;
                    break;
                case phLogger::WARNING:
                    $type = Logger::WARNING;
                    break;
                case phLogger::NOTICE:
                    $type = Logger::NOTICE;
                    break;
                case phLogger::ALERT:
                    $type = Logger::ALERT;
                    break;
                case phLogger::INFO:
                    $type = Logger::INFO;
                    break;
                case phLogger::ERROR:
                    $type = Logger::ERROR;
                    break;
                case phLogger::EMERGENCY:
                case phLogger::EMERGENCE:
                    $type = Logger::EMERGENCY;
                    break;
            }

            if ($context === null) {
                $context = [];
            }

            if (is_array($context)) {
                $context['log-time'] = $time;
            }

            $this->getLogger()->log($type, $message, $context);
        }
    }