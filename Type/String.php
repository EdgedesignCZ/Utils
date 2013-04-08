<?php

namespace Edge\Utils\Type;

/**
 * This Class is based on part of the Nette Framework (http://nette.org)
 *
 * @author Tomáš Kuba <tomas.kuba@edgedesign.cz>
 */
class String
{

    /**
     * Internal string variable
     * @var string
     */
    private $string;

    /**
     * @param string $string
     */
    public function __construct($string = '')
    {
        $this->string = $string;
    }

    /**
     * Magic method implementation
     * @return string
     */
    public function __toString()
    {
        return (string) $this->string;
    }

    /**
     * Checks if the string is valid for the specified encoding.
     * @param  string  expected encoding
     * @return bool
     */
    public function hasEncoding($encoding = 'UTF-8')
    {
        $s = clone $this;
        return $this->string === (string)$s->fixEncoding($encoding);
    }

    /**
     * Returns correctly encoded string.
     * @param  string  encoding
     * @return \Edge\LibraryBundle\String\String
     */
    public function fixEncoding($encoding = 'UTF-8')
    {
        // removes xD800-xDFFF, xFEFF, xFFFF, x110000 and higher
        $this->string = @iconv('UTF-16', $encoding . '//IGNORE', iconv($encoding, 'UTF-16//IGNORE', $this->string)); // intentionally @
        $this->string = str_replace("\xEF\xBB\xBF", '', $this->string); // remove UTF-8 BOM

        return $this;
    }

    /**
     * Returns a specific character.
     * @param  int     codepoint
     * @param  string  encoding
     * @return \Edge\LibraryBundle\String\String
     */
    public function chr($code, $encoding = 'UTF-8')
    {
        $this->string = iconv('UTF-32BE', $encoding . '//IGNORE', pack('N', $code));

        return $this;
    }

    /**
     * Is the string starting with the prefix $needle?
     * @param  string   needle
     * @return bool
     */
    public function isStartingWith($needle)
    {
        return strncmp($this->string, $needle, strlen($needle)) === 0;
    }

    /**
     * Is the string ending with the suffix $needle?
     * @param  string   needle
     * @return bool
     */
    public function isEndingWith($needle)
    {
        return strlen($needle) === 0 || substr($this->string, -strlen($needle)) === $needle;
    }

    /**
     * Is string containing $needle?
     * @param  string   needle
     * @return bool
     */
    public function isContaining($needle)
    {
        return strpos($this->string, $needle) !== FALSE;
    }

    /**
     * Returns a part of UTF-8 string.
     * @param  int
     * @param  int
     * @return \Edge\LibraryBundle\String\String
     */
    public function substring($start, $length = NULL)
    {
        if ($length === NULL) {
            $length = $this->length();
        }

        if (function_exists('mb_substr')) {
            $this->string = mb_substr($this->string, $start, $length, 'UTF-8'); // MB is much faster
        } else {
            $this->string = iconv_substr($this->string, $start, $length, 'UTF-8');
        }

        return $this;
    }

    /**
     * Removes special controls characters and normalizes line endings and spaces.
     * @return \Edge\LibraryBundle\String\String
     */
    public function normalize()
    {
        // standardize line endings to unix-like
        $this->string = str_replace("\r\n", "\n", $this->string); // DOS
        $this->string = strtr($this->string, "\r", "\n"); // Mac
        // remove control characters; leave \t + \n
        $this->string = preg_replace('#[\x00-\x08\x0B-\x1F\x7F]+#', '', $this->string);
        // right trim
        $this->string = preg_replace("#[\t ]+$#m", '', $this->string);
        // leading and trailing blank lines
        $this->string = trim($this->string, "\n");

        return $this;
    }

    /**
     * Converts to ASCII.
     * @return \Edge\LibraryBundle\String\String
     */
    public function toAscii()
    {
        $this->string = preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u', '', $this->string);
        $this->string = strtr($this->string, '`\'"^~', "\x01\x02\x03\x04\x05");
        if (ICONV_IMPL === 'glibc') {
            $this->string = @iconv('UTF-8', 'WINDOWS-1250//TRANSLIT', $this->string); // intentionally @
            $this->string = strtr($this->string, "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"
                . "\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"
                . "\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"
                . "\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe",
                "ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt");
        } else {
            $this->string = @iconv('UTF-8', 'ASCII//TRANSLIT', $this->string); // intentionally @
        }
        $this->string = str_replace(array('`', "'", '"', '^', '~'), '', $this->string);
        $this->string = strtr($this->string, "\x01\x02\x03\x04\x05", '`\'"^~');

        return $this;
    }

    /**
     * Converts to web safe characters [a-z0-9-] text.
     * @param  string  allowed characters
     * @param  bool
     * @return \Edge\LibraryBundle\String\String
     */
    public function webalize($charlist = NULL, $lower = TRUE)
    {
        $this->string = (string) $this->toAscii();
        if ($lower) {
            $this->string = strtolower($this->string);
        }
        $this->string = preg_replace('#[^a-z0-9' . preg_quote($charlist, '#') . ']+#i', '-', $this->string);
        $this->string = trim($this->string, '-');

        return $this;
    }

    /**
     * Truncates string to maximal length.
     * @param  int
     * @param  string  UTF-8 encoding
     * @return \Edge\LibraryBundle\String\String
     */
    public function truncate($maxLen, $append = "\xE2\x80\xA6")
    {
        if (iconv_strlen($this->string, 'UTF-8') > $maxLen) {
            $maxLen = $maxLen - iconv_strlen($append, 'UTF-8');
            if ($maxLen < 1) {
                $this->string = $append;
            } elseif (preg_match('#^.{1,' . $maxLen . '}(?=[\s\x00-/:-@\[-`{-~])#us', $this->string, $matches)) {
                $this->string = $matches[0] . $append;
            } else {
                $this->string = iconv_substr($this->string, 0, $maxLen, 'UTF-8') . $append;
            }
        }

        return $this;
    }

    /**
     * Indents the content from the left.
     * @param  int
     * @param  string
     * @return \Edge\LibraryBundle\String\String
     */
    public function indent($level = 1, $chars = "\t")
    {
        if ($level > 0) {
            $this->string = preg_replace('#(?:^|[\r\n]+)(?=[^\r\n])#', '$0' . str_repeat($chars, $level), $this->string);
        }

        return $this;
    }

    /**
     * Convert to lower case.
     * @return \Edge\LibraryBundle\String\String
     */
    public function lower()
    {
        $this->string = mb_strtolower($this->string, 'UTF-8');

        return $this;
    }

    /**
     * Convert to upper case.
     * @return \Edge\LibraryBundle\String\String
     */
    public function upper()
    {
        $this->string = mb_strtoupper($this->string, 'UTF-8');

        return $this;
    }

    /**
     * Convert first character to upper case.
     * @return \Edge\LibraryBundle\String\String
     */
    public function firstUpper()
    {
        $firstLetter = clone $this;

        $this->string = (string) $firstLetter->substring(0, 1)->upper() . $this->substring(1);

        return $this;
    }

    /**
     * Capitalize string.
     * @return \Edge\LibraryBundle\String\String
     */
    public function capitalize()
    {
        $this->string = mb_convert_case($this->string, MB_CASE_TITLE, 'UTF-8');

        return $this;
    }

    /**
     * Case-insensitive compares UTF-8 strings.
     * @param  string Compare to
     * @param  int
     * @return bool
     */
    public function isSameAs($string, $len = NULL)
    {
        $left = clone $this;
        $right = new String($string);

        if ($len < 0) {
            $left = $left->substring($len, -$len);
            $right = $right->substring($len, -$len);
        } elseif ($len !== NULL) {
            $left = $left->substring(0, $len);
            $right = $right->substring(0, $len);
        }

        return (string) $left->lower() === (string) $right->lower();
    }

    /**
     * Returns UTF-8 string length.
     * @return int
     */
    public function length()
    {
        return strlen(utf8_decode($this->string)); // fastest way
    }

    /**
     * Strips whitespace.
     * @param  string Characters to be stripped
     * @return \Edge\LibraryBundle\String\String
     */
    public function trim($charlist = " \t\n\r\0\x0B\xC2\xA0")
    {
        $charlist = preg_quote($charlist, '#');
        $this->string = preg_replace('#^[' . $charlist . ']+|[' . $charlist . ']+$#u', '', $this->string);

        return $this;
    }

    /**
     * Pad a string to a certain length with another string.
     * @param  int
     * @param  string
     * @return \Edge\LibraryBundle\String\String
     */
    public function padLeft($length, $pad = ' ')
    {
        $length = max(0, $length - iconv_strlen($this->string, 'UTF-8'));
        $padLen = iconv_strlen($pad, 'UTF-8');
        $this->string = str_repeat($pad, $length / $padLen) . iconv_substr($pad, 0, $length % $padLen, 'UTF-8') . $this->string;

        return $this;
    }

    /**
     * Pad a string to a certain length with another string.
     * @param  int
     * @param  string
     * @return \Edge\LibraryBundle\String\String
     */
    public function padRight($length, $pad = ' ')
    {
        $length = max(0, $length - iconv_strlen($this->string, 'UTF-8'));
        $padLen = iconv_strlen($pad, 'UTF-8');
        $this->string = $this->string . str_repeat($pad, $length / $padLen) . iconv_substr($pad, 0, $length % $padLen, 'UTF-8');

        return $this;
    }

    /**
     * Reverse string.
     * @return \Edge\LibraryBundle\String\String
     */
    public function reverse()
    {
        $this->string = @iconv('UTF-32LE', 'UTF-8', strrev(@iconv('UTF-8', 'UTF-32BE', $this->string)));

        return $this;
    }

    /**
     * Generate random string.
     * @param  int
     * @param  string
     * @return \Edge\LibraryBundle\String\String
     */
    public function random($length = 10, $charlist = '0-9a-z')
    {
        $charlist = str_shuffle(preg_replace_callback('#.-.#', create_function('$m', '
			return implode(\'\', range($m[0][0], $m[0][2]));
		'), $charlist));
        $chLen = strlen($charlist);

        $this->string = '';
        for ($i = 0; $i < $length; $i++) {
            if ($i % 5 === 0) {
                $rand = lcg_value();
                $rand2 = microtime(TRUE);
            }
            $rand *= $chLen;
            $this->string .= $charlist[($rand + $rand2) % $chLen];
            $rand -= (int) $rand;
        }

        return $this;
    }

}