<?php

namespace TwistersFury\Phalcon\Shared\Tests\Di;

use Phalcon\Test\UnitTestCase;
use TwistersFury\Phalcon\Shared\Di\AbstractFactory;
use TwistersFury\Shared\Tests\Data\Di\DummyFactory;

class AbstractFactoryTest extends UnitTestCase
{
    /**
     * @var \Tester
     */
    protected $tester;

    /** @var \TwistersFury\Phalcon\Shared\Di\FactoryDefault|\PHPUnit_Framework_MockObject_MockObject */
    protected $testSubject = null;

    protected function setUp()
    {
        $this->testSubject = $this->getMockBuilder(AbstractFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['registerConfig', 'registerDatabases'])
            ->getMockForAbstractClass();
    }

    public function testProcessServices()
    {
        $this->testSubject->expects( $this->at( 0 ) )->method( 'registerDatabases' )->willReturnSelf();

        $this->testSubject->expects( $this->at( 1 ) )->method( 'registerConfig' )->willReturnSelf();

        $reflectionProperty = new \ReflectionProperty( AbstractFactory::class, 'priorityServices' );
        $reflectionProperty->setAccessible( TRUE );

        $reflectionProperty->setValue( $this->testSubject, [ 'registerDatabases' ] );

        $reflectionMethod = new \ReflectionMethod( AbstractFactory::class, 'processServices' );
        $reflectionMethod->setAccessible( TRUE );

        $this->assertEquals( $this->testSubject, $reflectionMethod->invoke( $this->testSubject ) );
    }

    public function testConstruct()
    {
        require_once TF_SHARED_TESTS . '../../_data/Di/DummyFactory.php';

        $this->testSubject = new DummyFactory();
        $this->assertTrue($this->testSubject->has('someTest'));
    }
}