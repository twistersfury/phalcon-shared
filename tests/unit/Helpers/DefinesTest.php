<?php
namespace TwistersFury\Phalcon\Shared\Tests\Helpers;

use Phalcon\Test\UnitTestCase;
use TwistersFury\Phalcon\Shared\Helpers\Defines;

class DefinesTest extends UnitTestCase
{
    /** @var \TwistersFury\Phalcon\Shared\Helpers\Defines */
    protected $testSubject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->testSubject = new Defines();
    }

    public function testDefines()
    {
        $this->assertSame($this->testSubject, $this->testSubject->define('TF_TESTING_1', 10));
        //Ensuring Override Doesn't Happen
        $this->assertSame($this->testSubject, $this->testSubject->define('TF_TESTING_1', 20));
        $this->assertEquals(10, TF_TESTING_1);
    }

    public function testDefinesWithCallback()
    {
        $testData = (object) [
            'value' => null
        ];

        $this->assertSame($this->testSubject, $this->testSubject->define('TF_TESTING_2', function() use ($testData) {
            $testData->value = mt_rand(1, 10);

            return $testData->value;
        }));

        $this->assertEquals($testData->value, TF_TESTING_2);
    }
}