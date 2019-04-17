<?php
/**
 * Copyright (C) 2018 Twister's Fury.
 * Distributed under the MIT License (license terms are at http://opensource.org/licenses/MIT).
 */

namespace TwistersFury\Phalcon\Shared\Di\ServiceProvider;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;
use TwistersFury\Phalcon\Shared\Exceptions\Handler;

class Logging extends AbstractServiceProvider
{
    protected function registerEmailHandler()
    {
        $this->set('emailHandler', function () {
            /** @var \Phalcon\Config $servicesConfig */
            $servicesConfig = $this->get('config')->services;

            /** @var \Swift_Message $exampleMessage */
            $exampleMessage = $this->get(
                \Swift_Message::class,
                [
                    'Critical Server Errors',
                    '',
                    'text/html'
                ]
            );

            $exampleMessage->addTo(
                $servicesConfig->logging->to
            )->addFrom($servicesConfig->mail->from);

            $emailHandler = $this->get(
                SwiftMailerHandler::class,
                [
                    \Swift_Mailer::newInstance(
                        \Swift_SmtpTransport::newInstance(
                            $servicesConfig->mail->host,
                            $servicesConfig->mail->port
                        )
                    ),
                    $exampleMessage,
                    Logger::DEBUG
                ]
            );

            $emailHandler->setFormatter(
                $this->get(HtmlFormatter::class)
            );

            return $emailHandler;
        });
    }

    protected function registerHandler(): self
    {
        $this->setShared(
            Handler::class,
            function (Logger $logger = null) {
                $logger = $logger ?? $this->get('logger');
                $this->get(Handler::class, [$logger]);
            }
        );

        return $this;
    }

    protected function registerLogger() : self
    {
        $this->setShared('logger', function () {
            /** @var \Phalcon\Config $servicesConfig */
            $servicesConfig = $this->get('config')->services;

            $debugLevel = $servicesConfig->logging->env_levels->get(
                $this->get('config')->environment
            ) ?: $servicesConfig->logging->env_levels->get('default');

            /** @var Logger $logger */
            $logger = $this->get(
                Logger::class,
                [
                    'app-logging'
                ]
            )->pushHandler(
                $this->get(
                    RotatingFileHandler::class,
                    [
                        $this->get('config')->application->get(
                            'logFile',
                            $this->get('config')->application->logDir
                        ) . '/logging-app.log',
                        10,
                        $debugLevel
                    ]
                )
            );

            if (TF_PROVIDERS_TYPE === 'cli') {
                $logger->pushHandler(
                    $this->get(
                        StreamHandler::class,
                        [
                            STDOUT,
                            $debugLevel
                        ]
                    )
                );
            }

            if ($servicesConfig->logging->get('email')) {
                $logger->pushHandler(
                    new FingersCrossedHandler(
                        $this->get(
                            'bufferHandler',
                            [
                                $this->get('emailHandler')
                            ]
                        ),
                        $debugLevel
                    )
                );
            }

            //Include Any Primary Handlers (IE: RollBar)
            if (!$this->get('config')->debug && $servicesConfig->logging->get('handlers')) {
                foreach ($servicesConfig->logging->handlers as $handler => $handlerLevel) {
                    if (is_numeric($handler)) {
                        $handler      = $handlerLevel;
                        $handlerLevel = null;
                    }

                    $handlerLevel = $handlerLevel ?? $debugLevel;

                    $logger->pushHandler(
                        $this->get(
                            $handler,
                            [
                                $handlerLevel,
                                $logger,
                                $debugLevel
                            ]
                        )
                    );
                }
            }

            return $logger;
        });

        return $this;
    }

    protected function registerBufferHandler()
    {
        $this->setShared(
            'bufferHandler',
            function (HandlerInterface $handler) {
                /** @var BufferHandler $bufferHandler */
                $bufferHandler = $this->get(BufferHandler::class, [$handler]);
                register_shutdown_function(
                    function () use ($bufferHandler) {
                        $bufferHandler->close();
                    }
                );

                return $bufferHandler;
            }
        );
    }
}
