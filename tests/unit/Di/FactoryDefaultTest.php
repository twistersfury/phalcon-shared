<?php

namespace TwistersFury\Phalcon\Shared\Tests;

use org\bovigo\vfs\vfsStream;
use Phalcon\Config;
use Phalcon\Config\Adapter\Grouped;
use Phalcon\Crypt;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Adapter\Pdo\Sqlite;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Http\Request;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Test\UnitTestCase;
use TwistersFury\Phalcon\Shared\Di\FactoryDefault;
use TwistersFury\Phalcon\Shared\Helpers\PathManager;

class FactoryDefaultTest extends UnitTestCase
{
    /** @var \TwistersFury\Phalcon\Shared\Di\FactoryDefault|\PHPUnit_Framework_MockObject_MockObject */
    protected $testSubject = null;

    protected function setUp()
    {
        $this->testSubject = $this->getMockBuilder(FactoryDefault::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    protected function prepareMethod($methodName)
    {
        $reflectionMethod = new \ReflectionMethod(FactoryDefault::class, $methodName);
        $reflectionMethod->setAccessible(true);

        $this->assertSame($this->testSubject, $reflectionMethod->invoke($this->testSubject), $methodName . ' Not Returning Self');
    }

    public function testPathManager()
    {
        $mockPathManager = $this->getMockBuilder(PathManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->testSubject->set(PathManager::class, $mockPathManager);

        $this->prepareMethod('registerPathManager');

        $this->assertSame($mockPathManager, $this->testSubject->get('pathManager'), 'Invalid Path Manager');
    }

    public function testUrl()
    {
        $mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getHttpHost'])->getMock();

        $mockRequest->expects($this->once())
            ->method('getHttpHost')
            ->willReturn('www.example.com');

        $this->testSubject->set('request', $mockRequest);

        $mockUrl = $this->getMockBuilder(Url::class)
                                ->disableOriginalConstructor()
                                ->setMethods(['setBaseUri'])
                                ->getMock();

        $mockUrl->expects($this->once())
            ->method('setBaseUri')
            ->with('//www.example.com/')
            ->willReturnSelf();

        $this->testSubject->set(Url::class, $mockUrl);

        $this->prepareMethod('registerUrl');

        $this->assertSame($mockUrl, $this->testSubject->get('url'), 'Invalid Url');
    }

    public function testConfig()
    {
        $mockConfig = $this->getMockBuilder(Grouped::class)
                           ->disableOriginalConstructor()
                           ->setMethods(['setBaseUri'])
                           ->getMock();

        $rootFiles = vfsStream::setup('root');
        $rootFiles->addChild(
            vfsStream::newFile('config.php', '<?php //hello world')
        );

        $mockPathManager = $this->getMockBuilder(PathManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConfigDir'])
            ->getMock();

        $mockPathManager->method('getConfigDir')
            ->willReturn($rootFiles->url());

        $this->testSubject->set('pathManager', $mockPathManager);

        $testInstance = $this;

        $this->testSubject->set(Grouped::class, function($configOptions) use ($mockConfig, $rootFiles, $testInstance) {
            $testInstance->assertEquals(
                [
                    $rootFiles->url() . '/config.dist.php',
                    $rootFiles->url() . '/config.php'
                ],
                $configOptions
            );

            return $mockConfig;
        });

        $this->prepareMethod('registerConfig');

        $this->assertSame($mockConfig, $this->testSubject->get('config'), 'Invalid Config');
    }

    public function testDatabases()
    {
        $dbConfig = new Config(
            [
                'databases' => [
                    'db' => [
                        'user' => 'user',
                        'pass' => 'pass'
                    ],
                    'otherDb' => [
                        'user'    => 'otherUser',
                        'pass'    => 'otherPass',
                        'adapter' => Sqlite::class
                    ]
                ]
            ]
        );

        $testInstance = $this;

        $mockSql = $this->getMockBuilder(Mysql::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->testSubject->set(
            Mysql::class,
            function($configArray) use ($mockSql, $testInstance) {
                $testInstance->assertEquals(
                    [
                        'user' => 'user',
                        'pass' => 'pass'
                    ],
                    $configArray
                );

                return $mockSql;
            }
        );

        $mockSqlite = $this->getMockBuilder(Sqlite::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->testSubject->set(
            Sqlite::class,
            function($configArray) use ($mockSqlite, $testInstance) {
                $testInstance->assertEquals(
                    [
                        'user'    => 'otherUser',
                        'pass'    => 'otherPass',
                        'adapter' => Sqlite::class
                    ],
                    $configArray
                );

                return $mockSqlite;
            }
        );

        $this->testSubject->set('config', $dbConfig);

        $this->prepareMethod('registerDatabases');

        $this->assertSame($mockSql, $this->testSubject->get('db'), 'Invalid Db');
        $this->assertSame($mockSqlite, $this->testSubject->get('otherDb'), 'Invalid Db Other');
    }

    public function testVoltEngine()
    {
        $mockPathManager = $this->getMockBuilder(PathManager::class)
            ->setMethods(['getCacheDir'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockView = $this->getMockBuilder(View::class)
            ->getMock();

        $mockPathManager->method('getCacheDir')
            ->willReturn('cache/path');

        $this->testSubject->set('pathManager', $mockPathManager);

        $voltEngine = $this->getMockBuilder(Volt::class)
            ->disableOriginalConstructor()
            ->setMethods(['setOptions'])
            ->getMock();

        $voltEngine->expects($this->once())
            ->method('setOptions')
            ->with(
                [
                    'includePhpFunctions' => true,
                    'compiledPath'        => 'cache/path/volt',
                    'compileAlways'       => true,
                    'compiledSeparator'   => '-',
                    'compiledExtension'   => '.phtml'
                ]
            );

        $testInstance = $this;
        $testSubject  = $this->testSubject;

        $this->testSubject->set(
            Volt::class,
            function($view, $di) use ($mockView, $testInstance, $testSubject, $voltEngine) {
                $testInstance->assertSame($mockView, $view);
                $testInstance->assertSame($testSubject, $di);

                return $voltEngine;
            }
        );

        $this->prepareMethod('registerVoltEngine');

        $this->assertSame($voltEngine, $this->testSubject->get('voltEngine', [$mockView, $this->testSubject]), 'Invalid Volt Engine');
    }

    public function testFlashSession()
    {
        $mockFlash = $this->getMockBuilder(FlashSession::class)
            ->disableOriginalConstructor()
            ->getMock();

        $testInstance = $this;
        $this->testSubject->set(FlashSession::class, function($configOptions) use ($testInstance, $mockFlash) {
            $testInstance->assertSame(
                [
                    "error"   => "alert alert-danger",
                    "success" => "alert alert-success",
                    "notice"  => "alert alert-info",
                    "warning" => "alert alert-warning",
                ],
                $configOptions
            );

            return $mockFlash;
        });

        $this->prepareMethod('registerFlashSession');

        $this->assertSame($mockFlash, $this->testSubject->get('flashSession'), 'Invalid Flash Session');
    }

    public function testCrypt()
    {
        $rootPath = vfsStream::setup('root');
        $rootPath->addChild((vfsStream::newFile('key.pub'))->withContent('something'));

        $config = new Config(
            [
                'keyFile' => $rootPath->url() . '/key.pub'
            ]
        );

        $this->testSubject->set('config', $config);

        $mockCrypt = $this->getMockBuilder(Crypt::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->testSubject->set(Crypt::class, $mockCrypt);

        $this->prepareMethod('registerCrypt');

        $this->assertSame($mockCrypt, $this->testSubject->get('crypt'), 'Invalid Crypt');
    }

    /**
     * @expectedException \LogicException
     */
    public function testCryptThrowsExceptions() {
        $config = new Config( [] );

        $this->testSubject->set( 'config', $config );

        $mockCrypt = $this->getMockBuilder( Crypt::class )->disableOriginalConstructor()->getMock();

        $this->testSubject->set( Crypt::class, $mockCrypt );

        $this->prepareMethod( 'registerCrypt' );

        $this->testSubject->get( 'crypt' );
    }

    /**
     * @expectedException  \LogicException
     */
    public function testCryptThrowsFileMissingException()
    {
        $config = new Config(['keyFile' => 'missingFile']);
        $this->testSubject->set('config', $config);

        $mockCrypt = $this->getMockBuilder(Crypt::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->testSubject->set(Crypt::class, $mockCrypt);

        $this->prepareMethod('registerCrypt');

        $this->testSubject->get('crypt');
    }

    public function testSession()
    {
        $this->markTestIncomplete('Need To Implement Session Test');
    }
}