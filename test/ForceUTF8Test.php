<?php
declare (strict_types = 1);
namespace TestSpace;

use \ForceUTF8\Encoding;
use \PHPUnit\Framework\TestCase;

final class ForceUTF8Test extends TestCase
{
    const TEST_FILE = "/data/test1.txt";
    const TEST_FILE_LATIN = "/data/test1Latin.txt";
// ForceUTF8 tests.
    public function testFileExistsAndAreDifferent()
    {
        $this->assertFileExists(dirname(__FILE__) .self::TEST_FILE);
        $this->assertFileExists(dirname(__FILE__) .self::TEST_FILE_LATIN);
        $this->assertStringNotEqualsFile(
            dirname(__FILE__) .self::TEST_FILE,
            file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN),
            "Source files must not use the same encoding before conversion.");
    }
    public function testToUTF8EncondingWorksAndAreTheSame()
    {
        $this->assertStringEqualsFile(dirname(__FILE__) .self::TEST_FILE,
            Encoding::toUTF8(file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN)),
            "Simple Encoding works."
        );
    }
    public function test_arrays_are_different()
        {
            $arr1 = array(
                file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN));
            $arr2 = array(
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE));
            $this->assertIsArray($arr1, "Source arrays are different.");
            $this->assertIsArray($arr2, "Source arrays are different.");
            $this->assertNotEquals($arr1, $arr2, "Arrays should be different");
    }

    public function test_encoding_of_arrays()
        {
            $arr1 = array(
                file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN));
            $arr2 = array(
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE));
            $this->assertIsArray($arr1, "Source arrays are different.");
            $this->assertIsArray($arr2, "Source arrays are different.");
            $this->assertEquals(Encoding::toUTF8($arr1), $arr2);
    }

    public function testEmpty2()
    {
        $this->assertStringEqualsFile(
            dirname(__FILE__) .self::TEST_FILE,
            Encoding::fixUTF8(file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN)),
            "fixUTF8() maintains UTF-8 string.",
        );
    }
    public function testEmpty3()
    {
        $this->assertStringNotEqualsFile(
            dirname(__FILE__) .self::TEST_FILE,
            utf8_encode(file_get_contents(dirname(__FILE__) .self::TEST_FILE)),
            "An UTF-8 double encoded string differs from a correct UTF-8 string.",
        );
    }
    public function testEmpty4()
    {
        $this->assertStringEqualsFile(
            dirname(__FILE__) . self::TEST_FILE,
            Encoding::fixUTF8(utf8_encode(file_get_contents(dirname(__FILE__) .self::TEST_FILE))),
            "fixUTF8() reverts to UTF-8 a double encoded string.",
        );
    }
    public function test_double_encoded_arrays_are_different()
        {
            $arr1 = array(
                utf8_encode(file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN)),
                utf8_encode(file_get_contents(dirname(__FILE__) .self::TEST_FILE)),
                utf8_encode(file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN)));
            $arr2 = array(
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE));
            $this->assertNotEquals($arr1, $arr2);
    }
    public function  test_double_encoded_arrays_fix()
        {
            $arr1 = array(
                utf8_encode(file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN)),
                utf8_encode(file_get_contents(dirname(__FILE__) .self::TEST_FILE)),
                utf8_encode(file_get_contents(dirname(__FILE__) .self::TEST_FILE_LATIN)));
            $arr2 = array(
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE),
                file_get_contents(dirname(__FILE__) .self::TEST_FILE));
            $this->assertEquals(Encoding::fixUTF8($arr1), $arr2);
   }

    public function testEmpty77()
    {
        $this->assertEquals(
            "Fédération Camerounaise de Football\n",
            Encoding::fixUTF8("FÃÂ©dération Camerounaise de Football\n"),
            "fixUTF8() Example 1 still working."
        );
        $this->assertEquals(
            "Fédération Camerounaise de Football\n",
            Encoding::fixUTF8("FÃ©dÃ©ration Camerounaise de Football\n"),
                       "fixUTF8() Example 2 still working.",

        );
        $this->assertEquals(
            "Fédération Camerounaise de Football\n",
            Encoding::fixUTF8("FÃÂ©dÃÂ©ration Camerounaise de Football\n"),
            "fixUTF8() Example 3 still working.",
        );
        $this->assertEquals(
            "Fédération Camerounaise de Football\n",
            Encoding::fixUTF8("FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football\n"),
            "fixUTF8() Example 4 still working.",
        );
    }
}
