<?php

/**
 * @author   "Sebastián Grignoli" <grignoli@gmail.com>
 * @package  Encoding
 * @version  2.0
 * @link     https://github.com/neitanod/forceutf8
 * @example  https://github.com/neitanod/forceutf8
 * @license  Revised BSD
 */

declare(strict_types=1);

/*
Copyright (c) 2008 Sebastián Grignoli
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. Neither the name of copyright holders nor the names of its
contributors may be used to endorse or promote products derived
from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL COPYRIGHT HOLDERS OR CONTRIBUTORS
BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
 */

namespace sailenicolas\ForceUTF8;

/**
 *
 */
class Encoding
{
    private const ICONV_TRANSLIT = 'TRANSLIT';
    private const ICONV_IGNORE = 'IGNORE';
    private const WITHOUT_ICONV = '';

    protected static array $win1252ToUtf8 = [
        128 => "\xe2\x82\xac",

        130 => "\xe2\x80\x9a",
        131 => "\xc6\x92",
        132 => "\xe2\x80\x9e",
        133 => "\xe2\x80\xa6",
        134 => "\xe2\x80\xa0",
        135 => "\xe2\x80\xa1",
        136 => "\xcb\x86",
        137 => "\xe2\x80\xb0",
        138 => "\xc5\xa0",
        139 => "\xe2\x80\xb9",
        140 => "\xc5\x92",

        142 => "\xc5\xbd",

        145 => "\xe2\x80\x98",
        146 => "\xe2\x80\x99",
        147 => "\xe2\x80\x9c",
        148 => "\xe2\x80\x9d",
        149 => "\xe2\x80\xa2",
        150 => "\xe2\x80\x93",
        151 => "\xe2\x80\x94",
        152 => "\xcb\x9c",
        153 => "\xe2\x84\xa2",
        154 => "\xc5\xa1",
        155 => "\xe2\x80\xba",
        156 => "\xc5\x93",

        158 => "\xc5\xbe",
        159 => "\xc5\xb8",
    ];

    protected static array $brokenUtf8ToUtf8 = [
        "\xc2\x80" => "\xe2\x82\xac",

        "\xc2\x82" => "\xe2\x80\x9a",
        "\xc2\x83" => "\xc6\x92",
        "\xc2\x84" => "\xe2\x80\x9e",
        "\xc2\x85" => "\xe2\x80\xa6",
        "\xc2\x86" => "\xe2\x80\xa0",
        "\xc2\x87" => "\xe2\x80\xa1",
        "\xc2\x88" => "\xcb\x86",
        "\xc2\x89" => "\xe2\x80\xb0",
        "\xc2\x8a" => "\xc5\xa0",
        "\xc2\x8b" => "\xe2\x80\xb9",
        "\xc2\x8c" => "\xc5\x92",

        "\xc2\x8e" => "\xc5\xbd",

        "\xc2\x91" => "\xe2\x80\x98",
        "\xc2\x92" => "\xe2\x80\x99",
        "\xc2\x93" => "\xe2\x80\x9c",
        "\xc2\x94" => "\xe2\x80\x9d",
        "\xc2\x95" => "\xe2\x80\xa2",
        "\xc2\x96" => "\xe2\x80\x93",
        "\xc2\x97" => "\xe2\x80\x94",
        "\xc2\x98" => "\xcb\x9c",
        "\xc2\x99" => "\xe2\x84\xa2",
        "\xc2\x9a" => "\xc5\xa1",
        "\xc2\x9b" => "\xe2\x80\xba",
        "\xc2\x9c" => "\xc5\x93",

        "\xc2\x9e" => "\xc5\xbe",
        "\xc2\x9f" => "\xc5\xb8",
    ];

    protected static array $utf8ToWin1252 = [
        "\xe2\x82\xac" => "\x80",

        "\xe2\x80\x9a" => "\x82",
        "\xc6\x92" => "\x83",
        "\xe2\x80\x9e" => "\x84",
        "\xe2\x80\xa6" => "\x85",
        "\xe2\x80\xa0" => "\x86",
        "\xe2\x80\xa1" => "\x87",
        "\xcb\x86" => "\x88",
        "\xe2\x80\xb0" => "\x89",
        "\xc5\xa0" => "\x8a",
        "\xe2\x80\xb9" => "\x8b",
        "\xc5\x92" => "\x8c",

        "\xc5\xbd" => "\x8e",

        "\xe2\x80\x98" => "\x91",
        "\xe2\x80\x99" => "\x92",
        "\xe2\x80\x9c" => "\x93",
        "\xe2\x80\x9d" => "\x94",
        "\xe2\x80\xa2" => "\x95",
        "\xe2\x80\x93" => "\x96",
        "\xe2\x80\x94" => "\x97",
        "\xcb\x9c" => "\x98",
        "\xe2\x84\xa2" => "\x99",
        "\xc5\xa1" => "\x9a",
        "\xe2\x80\xba" => "\x9b",
        "\xc5\x93" => "\x9c",

        "\xc5\xbe" => "\x9e",
        "\xc5\xb8" => "\x9f",
    ];

    /**
     * @param array $text
     * @param string $option
     * @return array|mixed|string
     */
    public static function fixUTF8Array(array $text, string $option = self::WITHOUT_ICONV): mixed
    {
        foreach ($text as $index => $value) {
            $text[$index] = self::fixUTF8($value, $option);
        }
        return $text;
    }
    /**
     * @param string $text
     * @param string $option
     * @return array|mixed|string
     */
    public static function fixUTF8(string $text, string $option = self::WITHOUT_ICONV): mixed
    {
        $last = "";
        while ($last != $text) {
            $last = $text;
            $text = self::toUTF8(static::utf8Decode($text, $option));
        }
        return self::toUTF8(static::utf8Decode($text, $option));
    }

    /**
     * Function \ForceUTF8\Encoding::toUTF8
     *
     * This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
     *
     * It assumes that the encoding of the original string is either Windows-1252 or ISO 8859-1.
     *
     * It may fail to convert characters to UTF-8 if they fall into one of these scenarios:
     *
     * 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
     *    are followed by any of these:  ("group B")
     *                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
     * For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
     * The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB)
     * is also a valid unicode character, and will be left unchanged.
     *
     * 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
     * 3) when any of these: ðñòó  are followed by THREE chars from group B.
     *
     * @param array $text Any string.
     * @return array  The same string, UTF8 encoded
     */
    public static function toUTF8Array(array $text): array
    {
        foreach ($text as $index => $value) {
            $text[$index] = self::toUTF8($value);
        }
        return $text;
    }

    /**
     * Function \ForceUTF8\Encoding::toUTF8
     *
     * This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
     *
     * It assumes that the encoding of the original string is either Windows-1252 or ISO 8859-1.
     *
     * It may fail to convert characters to UTF-8 if they fall into one of these scenarios:
     *
     * 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
     *    are followed by any of these:  ("group B")
     *                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
     * For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
     * The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB)
     * is also a valid unicode character, and will be left unchanged.
     *
     * 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
     * 3) when any of these: ðñòó  are followed by THREE chars from group B.
     *
     * @param string $text Any string.
     * @return string|array  The same string, UTF8 encoded
     */
    public static function toUTF8(string $text): string|array
    {
        $max = self::strlen($text);
        $generate_cc = function ($c1) {
            $cc1 = (chr(intdiv(ord($c1), 64)) | "\xc0");
            $cc2 = ($c1 & "\x3f") | "\x80";
            return $cc1 . $cc2;
        };
        $buf = "";
        for ($key = 0; $key < $max; $key++) {
            $character = $text[$key];
            if ($character >= "\xc0") {
                //Should be converted to UTF8, if it's not UTF8 already
                $char2 = $key + 1 >= $max ? "\x00" : $text[$key + 1];
                $char3 = $key + 2 >= $max ? "\x00" : $text[$key + 2];
                $char4 = $key + 3 >= $max ? "\x00" : $text[$key + 3];
                if ($character >= "\xc0" & $character <= "\xdf") {
                    //looks like 2 bytes UTF8
                    if ($char2 >= "\x80" && $char2 <= "\xbf") {
                        //yeah, almost sure it's UTF8 already
                        $buf .= $character . $char2;
                        $key++;
                    } else { //not valid UTF8.  Convert it.
                        $buf .= $generate_cc($character);
                    }
                } elseif ($character >= "\xe0" & $character <= "\xef") { //looks like 3 bytes UTF8
                    if ($char2 >= "\x80" && $char2 <= "\xbf" && $char3 >= "\x80" && $char3 <= "\xbf") {
                        //yeah, almost sure it's UTF8 already
                        $buf .= $character . $char2 . $char3;
                        $key = $key + 2;
                    } else { //not valid UTF8.  Convert it.
                        $buf .= $generate_cc($character);
                    }
                } elseif ($character >= "\xf0" & $character <= "\xf7") {
                    //looks like 4 bytes UTF8
                    if (
                        $char2 >= "\x80" && $char2 <= "\xbf" && $char3 >= "\x80"
                        && $char3 <= "\xbf" && $char4 >= "\x80" && $char4 <= "\xbf"
                    ) {
                        //yeah, almost sure it's UTF8 already
                        $buf .= $character . $char2 . $char3 . $char4;
                        $key = $key + 3;
                    } else {
                        //not valid UTF8.  Convert it.
                        $buf .= $generate_cc($character);
                    }
                } else { //doesn't look like UTF8, but should be converted
                    $buf .= $generate_cc($character);
                }
            } elseif (($character & "\xc0") === "\x80") {
                // needs conversion
                $buf .= self::$win1252ToUtf8[ord($character)] ?? $generate_cc($character);
            } else { // it doesn't need conversion
                $buf .= $character;
            }
        }
        return $buf;
    }

    /**
     * @param $text
     * @return int
     */
    protected static function strlen($text): int
    {
        return function_exists('mb_strlen') ?
            mb_strlen($text, '8bit') : strlen($text);
    }

    /**
     * @param string $text
     * @param string $option
     * @return bool|string
     */
    protected static function utf8Decode(string $text, string $option = self::WITHOUT_ICONV): bool|string
    {
        if ($option == self::WITHOUT_ICONV || !function_exists('iconv')) {
            $opt = utf8_decode(
                str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), self::toUTF8($text))
            );
        } else {
            $to = $option === self::ICONV_TRANSLIT ? '//TRANSLIT' : ($option === self::ICONV_IGNORE ? '//IGNORE' : '');
            $opt = iconv("UTF-8", "Windows-1252" . $to, $text);
        }
        return $opt;
    }

    /**
     * @param $text
     * @return array|string
     */
    public static function fixWin1252Chars($text): array|string
    {
        // If you received an UTF-8 string that was converted from Windows-1252 as it was ISO8859-1
        // (ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
        // See: http://en.wikipedia.org/wiki/Windows-1252

        return self::mbStrReplace(array_keys(self::$brokenUtf8ToUtf8), array_values(self::$brokenUtf8ToUtf8), $text);
    }

    /**
     * @param string $str
     * @return string
     */
    public static function removeUTF8BOM(string $str = ""): string
    {
        if (mb_substr($str, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf)) {
            $str = mb_substr($str, 3);
        }
        return $str;
    }

    /**
     * @param $encodingLabel
     * @param $text
     * @return array|bool|mixed|string
     */
    public static function encodeUTF8($encodingLabel, $text): mixed
    {
        $encoding_label = self::normalizeEncoding($encodingLabel);
        if ($encoding_label === 'ISO-8859-1') {
            return self::toLatin1($text);
        }

        return self::toUTF8($text);
    }

    /**
     * @param $encodingLabel
     * @return string
     */
    public static function normalizeEncoding($encodingLabel): string
    {
        $encoding = mb_strtoupper($encodingLabel);
        $encoding = preg_replace('/[^a-zA-Z\d\s]/', '', $encoding);
        $equivalences = array(
            'ISO88591' => 'ISO-8859-1',
            'ISO8859' => 'ISO-8859-1',
            'ISO' => 'ISO-8859-1',
            'LATIN1' => 'ISO-8859-1',
            'LATIN' => 'ISO-8859-1',
            'UTF8' => 'UTF-8',
            'UTF' => 'UTF-8',
            'WIN1252' => 'ISO-8859-1',
            'WINDOWS1252' => 'ISO-8859-1',
        );

        if (empty($equivalences[$encoding])) {
            return 'UTF-8';
        }

        return $equivalences[$encoding];
    }

    /**
     * @param $text
     * @param string $option
     * @return array|bool|mixed|string
     */
    public static function toLatin1($text, string $option = self::WITHOUT_ICONV): mixed
    {
        return self::toWin1252($text, $option);
    }

    /**
     * @param $text
     * @param string $option
     * @return array|bool|mixed|string
     */
    public static function toWin1252($text, string $option = self::WITHOUT_ICONV): mixed
    {
        if (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::toWin1252($value, $option);
            }
            return $text;
        } elseif (is_string($text)) {
            return static::utf8Decode($text, $option);
        } else {
            return $text;
        }
    }

    private static function mbStrReplace($needle, $replacement, $haystack)
    {
        return implode($replacement, mb_split($needle, $haystack));
    }
}
