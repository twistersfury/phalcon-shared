<?php
/**
 * Created by PhpStorm.
 * User: fenikkusu
 * Date: 2018-12-02
 * Time: 15:00
 */

namespace TwistersFury\Phalcon\Shared\Tests\Unit\Mvc;

use Codeception\Stub;
use Phalcon\Dispatcher;
use TwistersFury\Phalcon\Shared\Mvc\AbstractModule;
use TwistersFury\Phalcon\Shared\Tests\Mocks\Mvc\MockModule;

class AbstractModuleTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    public function testRegisterServices()
    {
        $mockDispatcher = $this->getMockBuilder(Dispatcher::class)
            ->disableOriginalConstructor()
            ->setMethods(['setModuleName', 'setDefaultNameSpace'])
            ->getMock();

        $mockDispatcher->expects($this->once())
            ->method('setModuleName')
            ->with('mocks');

        $mockDispatcher->expects($this->once())
            ->method('setDefaultNamespace')
            ->with('TwistersFury\Phalcon\Shared\Tests\Mocks\Mvc\MockController');

        $this->tester->haveServiceInDi(
            'dispatcher',
            $mockDispatcher
        );

        (new MockModule())->registerServices($this->tester->getPhalcon()->di);
    }

    public function testGetModuleThrowsException()
    {
        $this->tester->expectException(
            new \LogicException('Module Name Could Not Be Determined'),
            function () {
                $this->tester->haveServiceInDi('dispatcher', Stub::makeEmpty(Dispatcher::class));

                $mockModule = $this->getMockBuilder(AbstractModule::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass();

                $mockModule->registerServices($this->tester->getPhalcon()->di);
            }
        );
    }
}
