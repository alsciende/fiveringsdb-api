<?php

namespace Tests\AlsciendeSerializerBundle;

use Alsciende\SerializerBundle\Model\Source;
use Alsciende\SerializerBundle\Service\StoringService;
use AppBundle\Entity\Card;
use AppBundle\Entity\Clan;
use AppBundle\Entity\PackSlot;
use AppBundle\Entity\Type;
use PHPUnit_Framework_TestCase;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class StoringServiceTest extends PHPUnit_Framework_TestCase
{

    public function testDecodeFile1 ()
    {
        $path = __DIR__ . "/DataFixtures";

        $storingService = new StoringService();
        $blocks = $storingService->scanFile("$path/Clan.json");

        $this->assertEquals(1, count($blocks));
    }

    public function testDecodeFile2 ()
    {
        $path = __DIR__ . "/DataFixtures";

        $storingService = new StoringService();
        $blocks = $storingService->scanFile("$path/Card/01001.json");

        $this->assertEquals(1, count($blocks));
    }
    
    public function testDecodeDirectory()
    {
        $path = __DIR__ . "/DataFixtures";

        $storingService = new StoringService();
        $blocks = $storingService->scanDirectory("$path/Card");

        $this->assertEquals(2, count($blocks));
    }

    public function testDecodeClan()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new Source(Clan::class, $path);

        $storingService = new StoringService();
        $blocks = $storingService->retrieve($source);

        $this->assertEquals(1, count($blocks));
    }
    
    public function testDecodeType()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new Source(Type::class, $path);

        $storingService = new StoringService();
        $blocks = $storingService->retrieve($source);

        $this->assertEquals(1, count($blocks));
    }
    
    public function testDecodeCard()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new Source(Card::class, $path, "code");

        $storingService = new StoringService();
        $blocks = $storingService->retrieve($source);

        $this->assertEquals(2, count($blocks));
    }
    
    public function testDecodePackSlot()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new Source(PackSlot::class, $path, "pack_code");

        $storingService = new StoringService();
        $blocks = $storingService->retrieve($source);

        $this->assertEquals(1, count($blocks));
    }
}
