<?php

namespace Edge\Library\Test\Converter;

use Edge\Library\Converter\Base62;

/**
 * @author Tomáš Kuba <tomas.kuba@edgedesign.cz>
 */
class Base62Test extends \PHPUnit_Framework_TestCase
{
    /**
    * @var Base62
    */
    protected $base62;

    /**
     * @var string
     */
    protected $baseString;

    protected function setUp()
    {
        $this->base62 = new Base62();
        $this->baseString = $this->base62->getBase();
    }
    public function testEncode()
    {
        $this->assertEquals($this->base62->encode(0), $this->baseString[0]);
        $this->assertEquals($this->base62->encode(12), $this->baseString[12]);
        $this->assertEquals($this->base62->encode(62), $this->baseString[1].$this->baseString[0]);
    }

    public function testDecode()
    {
        $this->assertEquals($this->base62->decode($this->baseString[0]), 0);
        $this->assertEquals($this->base62->decode($this->baseString[12]), 12);
        $this->assertEquals($this->base62->decode($this->baseString[1].$this->baseString[0]), 62);
    }

}