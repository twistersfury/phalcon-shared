<?php
namespace TwistersFury\Phalcon\Shared\Tests\Helpers;

use org\bovigo\vfs\vfsStream;
use Phalcon\Test\UnitTestCase;
use TwistersFury\Phalcon\Shared\Helpers\PathManager;
use TwistersFury\Shared\Tests\Data\Helpers\DummyPathManager;

class PathManagerTest extends UnitTestCase
{
    public function testPathManager()
    {
        $rootFolder = vfsStream::setup('root');
        $rootFolder->addChild(
            vfsStream::newDirectory('caching')
        );

        $rootFolder->addChild(
            vfsStream::newDirectory('application')
        );

        $rootFolder->addChild(
            vfsStream::newDirectory('module')
        );

        $rootFolder->addChild(
            vfsStream::newDirectory('config')
        );

        $rootFolder->addChild(
            vfsStream::newDirectory('views')
        );


        /** @var PathManager $pathManager */
        $pathManager = $this->getMockBuilder(PathManager::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $pathManager->setConfiguration(
            [
                'cache'   => $rootFolder->getChild('caching')->url(),
                'root'    => $rootFolder->getChild('application')->url(),
                'modules' => $rootFolder->getChild('module')->url(),
                'config'  => $rootFolder->getChild('config')->url(),
                'views'   => $rootFolder->getChild('views')->url()
            ]
        );

        $this->assertEquals($rootFolder->getChild('caching')->url(), $pathManager->getCacheDir());
        $this->assertEquals($rootFolder->getChild('application')->url(), $pathManager->getApplicationDir());
        $this->assertEquals($rootFolder->getChild('config')->url(), $pathManager->getConfigDir());
        $this->assertEquals($rootFolder->getChild('module')->url(), $pathManager->getModulesDir());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp #Path (.*) for (.*) does not exist#
     */
    public function testPathManagerThrowsException()
    {
        (new PathManager());
    }

    public function testPastManagerConstruct()
    {
        require_once TF_SHARED_TESTS . '/_data/Helpers/DummyPathManager.php';

        $testSubject = new DummyPathManager();
        $this->assertTrue($testSubject->wasCalled);
    }
}