<?php

namespace Tests\AlsciendeSerializerBundle;

use Alsciende\SerializerBundle\Model\Source;
use Alsciende\SerializerBundle\Scanner\Scanner;
use AppBundle\Entity\Card;
use AppBundle\Entity\Clan;
use AppBundle\Entity\PackSlot;
use AppBundle\Entity\Type;
use PHPUnit_Framework_TestCase;

/**
 * Description of ScannerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ScannerTest extends PHPUnit_Framework_TestCase
{

    public function testDecodeFile1 ()
    {
        $path = __DIR__ . "/DataFixtures";

        $scanner = new Scanner();
        $blocks = $scanner->scanFile("$path/Clan.json");

        $this->assertEquals(1, count($blocks));
    }

    public function testDecodeFile2 ()
    {
        $path = __DIR__ . "/DataFixtures";

        $scanner = new Scanner();
        $blocks = $scanner->scanFile("$path/Card/01001.json");

        $this->assertEquals(1, count($blocks));
    }
    
    public function testDecodeDirectory()
    {
        $path = __DIR__ . "/DataFixtures";

        $scanner = new Scanner();
        $blocks = $scanner->scanDirectory("$path/Card");

        $this->assertEquals(2, count($blocks));
    }

    public function testDecodeClan()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new Source(Clan::class, $path);

        $scanner = new Scanner();
        $blocks = $scanner->scan($source);

        $this->assertEquals(1, count($blocks));
    }
    
    public function testDecodeType()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new Source(Type::class, $path);

        $scanner = new Scanner();
        $blocks = $scanner->scan($source);

        $this->assertEquals(1, count($blocks));
    }
    
    public function testDecodeCard()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new Source(Card::class, $path, "code");

        $scanner = new Scanner();
        $blocks = $scanner->scan($source);

        $this->assertEquals(2, count($blocks));
    }
    
    public function testDecodePackSlot()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new Source(PackSlot::class, $path, "pack_code");

        $scanner = new Scanner();
        $blocks = $scanner->scan($source);

        $this->assertEquals(1, count($blocks));
    }
}
