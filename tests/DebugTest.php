<?php

namespace Renatoaraujo\Tests;

use Renatoaraujo\Debug;

/**
 * Unit Test Class to test debug object
 *
 * @category Tests
 *
 * @package Renatoaraujo\Tests
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @version Release: 1.0
 *
 * @link https://github.com/renatoaraujo/debug
 */
class DebugTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string directoryLog
     */
    protected $directoryLog = __DIR__ . '/../logs';

    /**
     * Method setUp
     *
     * @see \PHPUnit_Framework_TestCase::setUp()
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $logFile = $this->directoryLog . '/debug.log';

        !file_exists($logFile) ?: unlink($logFile);

        $this->assertFileNotExists($logFile);
    }

    /**
     * Method to test Debug::dump() functionality
     *
     * @dataProvider dumpDataProvider
     *
     * @param $param1
     * @param $param2
     *
     * @return void
     */
    public function testDebugDump($param1 = null, $param2 = null)
    {
        $strDebug = Debug::dump($param1, $param2);
        $this->assertNotEmpty($strDebug);
        $this->assertNotNull($strDebug);
        $this->assertInternalType('string', $strDebug);
    }

    /**
     * Method to test Debug::printr() functionality
     *
     * @dataProvider dumpDataProvider
     *
     * @param $param1
     * @param $param2
     *
     * @return void
     */
    public function testDebugPrintr($param1 = null, $param2 = null)
    {
        $strDebug = Debug::printr($param1, $param2);
        $this->assertNotEmpty($strDebug);
        $this->assertNotNull($strDebug);
        $this->assertInternalType('string', $strDebug);
    }

    /**
     * Method to test Debug::json() functionality
     *
     * @dataProvider dumpDataProvider
     *
     * @param $param1
     * @param $param2
     *
     * @return void
     */
    public function testDebugJson($param1 = null, $param2 = null)
    {
        $jsonDebug = Debug::json($param1, $param2);
        $this->assertJson($jsonDebug);
        $this->assertNotEmpty($jsonDebug);
        $this->assertNotNull($jsonDebug);
        $this->assertInternalType('string', $jsonDebug);
    }

    /**
     * Method to test Debug::log() functionality
     *
     * @dataProvider dumpDataProvider
     *
     * @param $param1
     * @param $param2
     *
     * @return void
     */
    public function testDebugLog($param1 = null, $param2 = null)
    {
        $boolDebug = Debug::log($param1, $param2);
        $this->assertNotEmpty($boolDebug);
        $this->assertNotNull($boolDebug);
        $this->assertTrue($boolDebug);
        $this->assertInternalType('boolean', $boolDebug);
        $this->assertFileExists($this->directoryLog . '/debug.log');
    }

    /**
     * Method to test Debug::console() functionality
     *
     * @dataProvider dumpDataProvider
     *
     * @param $param1
     * @param $param2
     *
     * @return void
     */
    public function testDebugConsole($param1 = null, $param2 = null)
    {
        $boolDebug = Debug::console($param1, $param2);
        $this->assertNotEmpty($boolDebug);
        $this->assertNotNull($boolDebug);
        $this->assertTrue($boolDebug);
        $this->assertInternalType('boolean', $boolDebug);
    }

    /**
     * Dump data provider
     *
     * @return array
     */
    public function dumpDataProvider()
    {
        return array(
            array(0, 0),
            array(0, null),
            array(1),
            array(1, 2),
            array('foo', 'bar'),
            array(\stdClass::class, 'string'),
            array(1, $this),
            array(),
            array(Debug::class),
            array(
                array(
                    'foo',
                    array(
                        'bar'
                    ),
                    array(
                        array(
                            array(
                                'foobar'
                            ),
                            array(
                                $this
                            ),
                            array(
                                \stdClass::class,
                                $this,
                                Debug::class,
                            )
                        )
                    )
                ),
                'Simple string'
            )
        );
    }
}
