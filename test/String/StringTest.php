<?php

namespace Edge\LibraryBundle\Tests\String;

use Edge\LibraryBundle\String\String;

/**
 * @author Tomáš Kuba <tomas.kuba@edgedesign.cz>
 */
class StringTest extends \Edge\LibraryBundle\Test\UnitTestCase
{

    public function testCoversionToString()
    {
        $s = new String('Loď čeří kýlem tůň obzvlášť.');
        $this->assertSame('Loď čeří kýlem tůň obzvlášť.', $s->__toString());
        $this->assertEquals('Loď čeří kýlem tůň obzvlášť.', $s);
    }

    public function testCheckEncoding()
    {
        $s = new String('Loď čeří kýlem tůň obzvlášť.');
        $this->assertTrue($s->hasEncoding('UTF-8'));
        $this->assertFalse($s->hasEncoding('UTF-16'));
    }

    public function testFixEncoding()
    {
        $s = new String("Loď če\xD8ří kýle\xD8m tůň obzvlášť\xD9.");
        $this->assertEquals('Loď čeří kýlem tůň obzvlášť.', $s->fixEncoding());
        $this->assertEquals('Loď čeří kýlem tůň obzvlášť.', $s->fixEncoding('ISO-8859-5'));
    }

    public function testCreateStringFromChrValue()
    {
        $s = new String();
        $this->assertEquals($s->chr(381), 'Ž');
        $this->assertNotEquals($s->chr(382), 'Á');
    }

    public function testIsStartingWith()
    {
        $s = new String('Žluťočký kůň');
        $this->assertTrue($s->isStartingWith('Ž'));
        $this->assertTrue($s->isStartingWith('Žluť'));
        $this->assertFalse($s->isStartingWith('ň'));
    }

    public function testIsEndingWith()
    {
        $s = new String('Žluťočký kůň');
        $this->assertTrue($s->isEndingWith('ň'));
        $this->assertTrue($s->isEndingWith(' kůň'));
        $this->assertFalse($s->isEndingWith('ť'));
    }

    public function testIsContainingString()
    {
        $s = new String('Žluťočký kůň');
        $this->assertTrue($s->isContaining('ů'));
        $this->assertTrue($s->isContaining('čký '));
        $this->assertFalse($s->isContaining('bž'));
    }

    public function testSubstring()
    {
        $s = new String('Loď čeří kýlem tůň obzvlášť.');
        $this->assertEquals('čeří kýlem', $s->substring(4, 10));
    }

    public function testNormalize()
    {
        $s = new String("Loď čeří kýlem\x01 tůň\r\n obzvlášť\x0B v Grónské úžině.\n");
        $this->assertEquals("Loď čeří kýlem tůň\n obzvlášť v Grónské úžině.", $s->normalize());
    }

    public function testToAscii()
    {
        $s = new String('Loď čeří kýlem tůň obzvlášť v Grónské úžině.');
        $this->assertEquals('Lod ceri kylem tun obzvlast v Gronske uzine.', $s->toAscii());
    }

    public function testWebalize()
    {
        $s = new String('Loď čeří kýlem tůň obzvlášť v Grónské úžině.');
        $this->assertEquals('lod-ceri-kylem-tun-obzvlast-v-gronske-uzine', $s->webalize());
    }

    public function testTruncate()
    {
        $s = new String('Loď čeří kýlem tůň obzvlášť v Grónské úžině.');
        $this->assertEquals('Loď čeří kýlem tůň…', $s->truncate(19));
    }

    public function testIndent()
    {
        $s = new String('Loď čeří kýlem tůň obzvlášť v Grónské úžině.');
        $this->assertEquals('---Loď čeří kýlem tůň obzvlášť v Grónské úžině.', $s->indent(3, '-'));
    }

    public function testLower()
    {
        $s = new String('Loď čeří kýlem TŮŇ');
        $this->assertEquals('loď čeří kýlem tůň', $s->lower());
    }

    public function testUpper()
    {
        $s = new String('Loď čeří kýlem tůň');
        $this->assertEquals('LOĎ ČEŘÍ KÝLEM TŮŇ', $s->upper());
    }

    public function testFirstUpper()
    {
        $s = new String('loď čeří kýlem tůň');
        $this->assertEquals('Loď čeří kýlem tůň', $s->firstUpper());
    }

    public function testCapitalize()
    {
         $s = new String('Loď čeří kýlem tůň');
        $this->assertEquals('Loď Čeří Kýlem Tůň', $s->capitalize());
    }

    public function testIsSameAs()
    {
        $s = new String('Loď čeří kýlem tůň');
        $this->assertTrue($s->isSameAs('Loď Čeří Kýlem Tůň'));
        $this->assertFalse($s->isSameAs('Čeří Loď Kýlem Tůň'));
    }

    public function testLength()
    {
        $s = new String('Loď čeří kýlem tůň');
        $this->assertEquals(18, $s->length());
    }

    public function testTrim()
    {
        $s = new String("\n\r Loď čeří kýlem tůň\t \x0B\xC2\xA0");
        $this->assertEquals('Loď čeří kýlem tůň', $s->trim());
    }

    public function testPadLeft()
    {
        $s = new String('142');
        $this->assertEquals('0000000142', $s->padLeft(10, '0'));
    }

    public function testPadRight()
    {
        $s = new String('142');
        $this->assertEquals('1420000000', $s->padRight(10, '0'));
    }

    public function testReverse()
    {
        $s = new String('Obzvlášť v Grónské úžině.');
        $this->assertEquals('.ěnižú éksnórG v ťšálvzbO', $s->reverse());
    }

    public function testMultipleTransformations()
    {
        $s = new String('Obzvlášť v Grónské úžině.');
        $this->assertEquals('-----ĚNIŽÚ ÉKSNÓRG V ŤŠÁLVZBO', $s->reverse()->upper()->trim('.')->indent(5, '-'));
    }

}