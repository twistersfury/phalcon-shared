<?php
/**
 * Created by PhpStorm.
 * User: fenikkusu
 * Date: 2018-11-22
 * Time: 22:24
 */

namespace TwistersFury\Phalcon\Shared\Exceptions;

use Monolog\Logger;
use Throwable;

class Handler
{

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->setLogger($logger);
    }

    public function setLogger(Logger $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public function logThrowable(Throwable $throwable): self
    {
        $this->logger->error(
            sprintf('Exception %s: "%s" at %s line %s', get_class($throwable), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()),
            ['exception' => $throwable]
        );

        return $this;
    }
}