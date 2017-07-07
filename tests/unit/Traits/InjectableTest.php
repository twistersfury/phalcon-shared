<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/7/16
     * Time: 11:19 AM
     */
    namespace TwistersFury\Phalcon\Shared\Tests\Traits;

    use Phalcon\Test\UnitTestCase;

    class InjectableTest extends UnitTestCase {
        public function testDiNotSet() {
            /** @var \TwistersFury\Phalcon\Shared\Traits\Injectable|\PHPUnit_Framework_MockObject_MockObject $mockInjectable */
            $mockInjectable = $this->getMockBuilder('\TwistersFury\Phalcon\Shared\Traits\Injectable')
                ->getMockForTrait();

            $this->assertEquals($this->di, $mockInjectable->getDI());
        }

        public function testDiSet() {
            /** @var \TwistersFury\Phalcon\Shared\Traits\Injectable|\PHPUnit_Framework_MockObject_MockObject $mockInjectable */
            $mockInjectable = $this->getMockBuilder('\TwistersFury\Phalcon\Shared\Traits\Injectable')
                           ->getMockForTrait();

            /** @var \Phalcon\Di\FactoryDefault|\PHPUnit_Framework_MockObject_MockObject $mockDi */
            $mockDi = $this->getMockBuilder('\Phalcon\Di\FactoryDefault')
                ->disableOriginalConstructor()
                ->getMock();

            $mockInjectable->setDI($mockDi);

            $this->assertSame($mockDi, $mockInjectable->getDI());
        }

        public function testSession() {
            /** @var \TwistersFury\Phalcon\Shared\Traits\Injectable|\PHPUnit_Framework_MockObject_MockObject $mockInjectable */
            $mockInjectable = $this->getMockBuilder('\TwistersFury\Phalcon\Shared\Traits\Injectable')
                                   ->getMockForTrait();

            $mockSession = $this->getMockBuilder('\Phalcon\Session')
                ->disableOriginalConstructor()
                ->getMock();

            $this->di->set('session', $mockSession);

            $this->assertSame($mockSession, $mockInjectable->getSession());
        }
    }
