<?php

namespace Tests\AlsciendeDoctrineSerializerBundle;

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

    public function testDecodeFile1 ()
    {
        $path = __DIR__ . "/DataFixtures";
        
        $source = new \Alsciende\DoctrineSerializerBundle\Model\Source([
            "break" => null,
            "path" => $path,
            "className" => Clan::class
        ]);

        $encoder = new JsonFileEncoder();
        $files = $encoder->decodeFile($source, "$path/Clan.json");

        $this->assertEquals(2, count($files));
    }

    public function testDecodeFile2 ()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new \Alsciende\DoctrineSerializerBundle\Model\Source([
            "break" => "code",
            "path" => $path,
            "className" => Card::class
        ]);

        $encoder = new JsonFileEncoder();
        $files = $encoder->decodeFile($source, "$path/Card/01001.json");

        $this->assertEquals(1, count($files));
    }
    
    public function testDecodeDirectory()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new \Alsciende\DoctrineSerializerBundle\Model\Source([
            "break" => "code",
            "path" => $path,
            "className" => Card::class
        ]);

        $encoder = new JsonFileEncoder();
        $files = $encoder->decodeDirectory($source, "$path/Card");

        $this->assertEquals(2, count($files));
    }

    public function testDecodeClan()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new \Alsciende\DoctrineSerializerBundle\Model\Source([
            "break" => null,
            "path" => $path,
            "className" => Clan::class
        ]);

        $encoder = new JsonFileEncoder();
        $files = $encoder->decode($source);

        $this->assertEquals(2, count($files));
    }
    
    public function testDecodeType()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new \Alsciende\DoctrineSerializerBundle\Model\Source([
            "break" => null,
            "path" => $path,
            "className" => Type::class
        ]);

        $encoder = new JsonFileEncoder();
        $files = $encoder->decode($source);

        $this->assertEquals(1, count($files));
    }
    
    public function testDecodeCard()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new \Alsciende\DoctrineSerializerBundle\Model\Source([
            "break" => "code",
            "path" => $path,
            "className" => Card::class
        ]);

        $encoder = new JsonFileEncoder();
        $files = $encoder->decode($source);

        $this->assertEquals(2, count($files));
    }
    
    public function testDecodePackSlot()
    {
        $path = __DIR__ . "/DataFixtures";

        $source = new \Alsciende\DoctrineSerializerBundle\Model\Source([
            "break" => "pack_code",
            "path" => $path,
            "className" => PackSlot::class
        ]);

        $encoder = new JsonFileEncoder();
        $files = $encoder->decode($source);

        $this->assertEquals(1, count($files));
    }
}
