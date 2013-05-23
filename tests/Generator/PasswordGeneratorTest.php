<?php

namespace Edge\Utils\Tests\Generator;

use Edge\Utils\Generator\PasswordGenerator;

/**
 * @author VeN <vaclav.novotny@edgedesign.cz>
 */
class PasswordGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerationOfPassword()
    {
        $passwordGenerator = new PasswordGenerator();

        $p1 = $passwordGenerator->generatePassword();
        $p2 = $passwordGenerator->generatePassword();
        $p3 = $passwordGenerator->generatePassword();
        $p4 = $passwordGenerator->generatePassword();
        $p5 = $passwordGenerator->generatePassword();

        $this->assertNotSame($p1, $p2);
        $this->assertNotSame($p2, $p3);
        $this->assertNotSame($p3, $p4);
        $this->assertNotSame($p4, $p5);
    }

}