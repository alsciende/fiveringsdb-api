<?php

namespace Tests\AlsciendeDoctrineSerializerBundle;

use Alsciende\DoctrineSerializerBundle\AlsciendeDoctrineSerializerBundle;
use Alsciende\DoctrineSerializerBundle\JsonFileEncoder;
use AppBundle\Entity\Card;
use AppBundle\Entity\Clan;
use AppBundle\Entity\PackSlot;
use AppBundle\Entity\Type;
use PHPUnit_Framework_TestCase;

/**
 * Description of JsonFileEncoderTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class JsonFileEncoderTest extends PHPUnit_Framework_TestCase
{

    public function testDecodeCombinedFile ()
    {
        $path = __DIR__ . "/DataFixtures/Clan.json";

        $encoder = new JsonFileEncoder();
        $files = $encoder->decodeCombinedFile($path);

        $this->assertEquals(2, count($files));
    }

    public function testDecodeExplodedFile ()
    {
        $path = __DIR__ . "/DataFixtures/Card/01001.json";

        $encoder = new JsonFileEncoder();
        $file = $encoder->decodeExplodedFile($path);

        $this->assertNotNull($file);
    }
    
    public function testDecodeDirectory()
    {
        $path = __DIR__ . "/DataFixtures/Card";

        $encoder = new JsonFileEncoder();
        $files = $encoder->decodeDirectory($path, true);

        $this->assertEquals(2, count($files));
    }

    public function testDecodeClan()
    {
        $path = __DIR__ . "/DataFixtures";

        $encoder = new JsonFileEncoder();
        $files = $encoder->decode($path, Clan::class, false, true);

        $this->assertEquals(2, count($files));
    }
    
    public function testDecodeType()
    {
        $path = __DIR__ . "/DataFixtures";

        $encoder = new JsonFileEncoder();
        $files = $encoder->decode($path, Type::class, false, true);

        $this->assertEquals(1, count($files));
    }
    
    public function testDecodeCard()
    {
        $path = __DIR__ . "/DataFixtures";

        $encoder = new JsonFileEncoder();
        $files = $encoder->decode($path, Card::class, true, false);

        $this->assertEquals(2, count($files));
    }
    
    public function testDecodePackSlot()
    {
        $path = __DIR__ . "/DataFixtures";

        $encoder = new JsonFileEncoder();
        $files = $encoder->decode($path, PackSlot::class, true, true);

        $this->assertEquals(1, count($files));
    }
}
