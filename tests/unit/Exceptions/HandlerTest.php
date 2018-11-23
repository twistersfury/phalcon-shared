<?php
/**
 * Created by PhpStorm.
 * User: fenikkusu
 * Date: 2018-11-22
 * Time: 22:41
 */

namespace TwistersFury\Phalcon\Shared\Tests\Unit\Exceptions;

use Monolog\Logger;
use TwistersFury\Phalcon\Shared\Exceptions\Handler;

class HandlerTest extends \Codeception\Test\Unit
{
    /** @var Handler */
    private $testSubject;

    /** @var \PHPUnit\Framework\MockObject\MockObject|Logger */
    private $mockLogger;

    /**
     * @throws \Exception
     */
    public function _before()
    {
        $this->mockLogger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->setMethods(['error'])
            ->getMock();

        $this->testSubject = new Handler($this->mockLogger);
    }

    public function testGetter()
    {
        $this->assertSame($this->mockLogger, $this->testSubject->getLogger());
    }

    public function testLogThrowable()
    {
        $mockException = new \Exception('Error');

        $this->mockLogger->expects($this->once())
            ->method('error')
            ->with('Exception Exception: "Error" at /Users/fenikkusu/PhpstormProjects/phalcon-shared/tests/unit/Exceptions/HandlerTest.php line 42');

        $this->assertSame(
            $this->testSubject,
            $this->testSubject->logThrowable($mockException)
        );
    }
}
