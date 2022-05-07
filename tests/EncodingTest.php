<?php

declare(strict_types=1);

namespace sailenicolas\ForceUTF8\Tests;

use PHPUnit\Framework\TestCase;
use sailenicolas\ForceUTF8\Encoding;

/** @covers Encoding */

final class EncodingTest extends TestCase
{
    private const TEST_FILE = "/data/test1.txt";
    private const TEST_FILE_LATIN = "/data/test1Latin.txt";

    /**
     * @tests
     */
    public function testFileExistsAndAreDifferent()
    {
        EncodingTest::assertFileExists(dirname(__FILE__) . self::TEST_FILE);
        EncodingTest::assertFileExists(dirname(__FILE__) . self::TEST_FILE_LATIN);
        EncodingTest::assertStringNotEqualsFile(
            dirname(__FILE__) . self::TEST_FILE,
            file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN),
            "Source files must not use the same encoding before conversion."
        );
    }
    /**
     * @tests
     */
    public function testToUTF8EncondingWorksAndAreTheSame()
    {
        EncodingTest::assertStringEqualsFile(
            dirname(__FILE__) . self::TEST_FILE,
            Encoding::toUTF8(file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN)),
            "Simple Encoding works."
        );
    }
    /**
     * @tests
     */
    public function testArraysAreDifferent()
    {
        $arr1 = array(
            file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN));
        $arr2 = array(
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE));
        EncodingTest::assertIsArray($arr1, "Source arrays are different.");
        EncodingTest::assertIsArray($arr2, "Source arrays are different.");
        EncodingTest::assertNotEquals($arr1, $arr2, "Arrays should be different");
    }
    /**
     * @tests
     */
    public function testEncodingOfArrays()
    {
        $arr1 = array(
            file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN));
        $arr2 = array(
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE));
        EncodingTest::assertIsArray($arr1, "Source arrays are different.");
        EncodingTest::assertIsArray($arr2, "Source arrays are different.");
        EncodingTest::assertEquals(Encoding::toUTF8Array($arr1), $arr2);
    }
    /**
     * @tests
     */
    public function testEmpty2()
    {
        EncodingTest::assertStringEqualsFile(
            dirname(__FILE__) . self::TEST_FILE,
            Encoding::fixUTF8(file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN)),
            "fixUTF8() maintains UTF-8 string.",
        );
    }
    /**
     * @tests
     */
    public function testEmpty3()
    {
        EncodingTest::assertStringNotEqualsFile(
            dirname(__FILE__) . self::TEST_FILE,
            utf8_encode(file_get_contents(dirname(__FILE__) . self::TEST_FILE)),
            "An UTF-8 double encoded string differs from a correct UTF-8 string.",
        );
    }
    /**
     * @tests
     */
    public function testEmpty4()
    {
        EncodingTest::assertStringEqualsFile(
            dirname(__FILE__) . self::TEST_FILE,
            Encoding::fixUTF8(utf8_encode(file_get_contents(dirname(__FILE__) . self::TEST_FILE))),
            "fixUTF8() reverts to UTF-8 a double encoded string.",
        );
    }
    /**
     * @tests
     */
    public function testDoubleEncodedArraysAreDifferent()
    {
        $arr1 = array(
            utf8_encode(file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN)),
            utf8_encode(file_get_contents(dirname(__FILE__) . self::TEST_FILE)),
            utf8_encode(file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN)));
        $arr2 = array(
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE));
        EncodingTest::assertNotEquals($arr1, $arr2);
    }
    /**
     * @tests
     */
    public function testDoubleEncodedArraysFix()
    {
        $arr1 = array(
            utf8_encode(file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN)),
            utf8_encode(file_get_contents(dirname(__FILE__) . self::TEST_FILE)),
            utf8_encode(file_get_contents(dirname(__FILE__) . self::TEST_FILE_LATIN)));
        $arr2 = array(
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE),
            file_get_contents(dirname(__FILE__) . self::TEST_FILE));
        EncodingTest::assertEquals(Encoding::fixUTF8Array($arr1), $arr2);
    }
    /**
     * @tests
     */
    public function testEmpty77()
    {
        EncodingTest::assertEquals(
            "Fédération Camerounaise de Football\n",
            Encoding::fixUTF8("FÃÂ©dération Camerounaise de Football\n"),
            "fixUTF8() Example 1 still working."
        );
        EncodingTest::assertEquals(
            "Fédération Camerounaise de Football\n",
            Encoding::fixUTF8("FÃ©dÃ©ration Camerounaise de Football\n"),
            "fixUTF8() Example 2 still working.",
        );
        EncodingTest::assertEquals(
            "Fédération Camerounaise de Football\n",
            Encoding::fixUTF8("FÃÂ©dÃÂ©ration Camerounaise de Football\n"),
            "fixUTF8() Example 3 still working.",
        );
        EncodingTest::assertEquals(
            "Fédération Camerounaise de Football\n",
            Encoding::fixUTF8("FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football\n"),
            "fixUTF8() Example 4 still working.",
        );
    }
}
