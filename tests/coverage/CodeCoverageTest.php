<?php
    /**
     * Created by PhpStorm.
     * User: fenikkusu
     * Date: 7/3/16
     * Time: 9:19 PM
     */

    namespace TwistersFury\Phalcon\Shared\Tests;

    use \SplFileInfo;

    class CodeCoverageTest extends \PHPUnit_Framework_TestCase {

        /**
         * @dataProvider _dpLoadFiles
         * @param string $fileName File To Test
         */
        public function testUnitTestExists(SplFileInfo $fileName) {
            $filePath = str_replace(TF_SHARED_SOURCE, TF_SHARED_TESTS, $fileName->getRealPath());
            $filePath = str_replace('.php', 'Test.php', $filePath);

            $this->assertFileExists($filePath);
        }

        public function _dpLoadFiles() {
            $filesList = [];

            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(TF_SHARED_SOURCE, \RecursiveDirectoryIterator::SKIP_DOTS)) as $filePath) {
                //Ignore Config Items
                if (strstr($filePath->getRealPath(), TF_SHARED_SOURCE . '/etc') !== FALSE || $filePath->getBasename() === 'phalcon_bootstrap.php' || strstr($filePath->getRealPath(), '/Interfaces/') !== FALSE) {
                    continue;
                }

                $filesList[] = [$filePath];
            }

            return $filesList;
        }
    }