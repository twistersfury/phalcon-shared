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
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;

class Logging extends AbstractServiceProvider
{
    protected function registerLogger() : self
    {
        $this->setShared('logger', function () {
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

            $debugLevel = $servicesConfig->logging->env_levels->get(
                $this->get('config')->environment
            ) ?: $servicesConfig->logging->env_levels->get('default');

            $emailHandler->setFormatter(
                $this->get(HtmlFormatter::class)
            );

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
            )->pushHandler(
                new FingersCrossedHandler(
                    $this->get(
                        'bufferHandler',
                        [
                            $emailHandler
                        ]
                    ),
                    $debugLevel
                )
            );

            //Include Any Primary Handlers (IE: RollBar)
            if (!$this->get('config')->debug && $servicesConfig->logging->handlers) {
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
