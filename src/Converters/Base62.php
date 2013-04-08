<?php

namespace Edge\LibraryBundle\Converter;

/**
 * Encode integer to URL 'sexy' string and decode string back to integer
 * Inspired by: http://stackoverflow.com/questions/4964197/converting-a-number-base-10-to-base-62-a-za-z0-9
 * Using: http://en.wikipedia.org/wiki/Bijection
 *
 * @author Tomáš Kuba <tomas.kuba@edgedesign.cz>
 */
class Base62
{

    /**
     * @var array Character base map
     */
    private $base = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * Return Base62 translating string
     * @return string
     */
    function getBase()
    {
        return $this->base;
    }

    /**
     * Encode number to Base62 string
     * @param int $number
     * @return string
     */
    public function encode($number)
    {
        $b = strlen($this->base);
        $r = $number % $b;
        $res = $this->base[$r];
        $q = floor($number / $b);
        while ($q) {
            $r = $q % $b;
            $q = floor($q / $b);
            $res = $this->base[$r] . $res;
        }
        return $res;
    }

    /**
     * Decode Base62 string to number
     * @param string $string
     * @return int
     */
    public function decode($string)
    {
        $b = strlen($this->base);
        $limit = strlen($string);
        $res = strpos($this->base, $string[0]);
        for ($i = 1; $i < $limit; $i++) {
            $res = $b * $res + strpos($this->base, $string[$i]);
        }
        return $res;
    }

}