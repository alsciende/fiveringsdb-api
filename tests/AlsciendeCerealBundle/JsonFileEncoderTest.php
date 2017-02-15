<?php

namespace Tests\AlsciendeCerealBundle;

/**
 * Description of JsonFileEncoderTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class JsonFileEncoderTest extends \PHPUnit_Framework_TestCase
{

    public function testDecodeCombinedFile ()
    {
        $path = __DIR__ . "/DataFixtures/clan.json";

        $encoder = new \Alsciende\CerealBundle\JsonFileEncoder();
        $files = $encoder->decodeCombinedFile($path);

        $this->assertEquals(2, count($files));
    }

    public function testDecodeExplodedFile ()
    {
        $path = __DIR__ . "/DataFixtures/card/01001.json";

        $encoder = new \Alsciende\CerealBundle\JsonFileEncoder();
        $file = $encoder->decodeExplodedFile($path);

        $this->assertNotNull($file);
    }
    
    public function testDecodeDirectory()
    {
        $path = __DIR__ . "/DataFixtures/card";

        $encoder = new \Alsciende\CerealBundle\JsonFileEncoder();
        $files = $encoder->decodeDirectory($path);

        $this->assertEquals(2, count($files));
    }

    public function testDecodeClan()
    {
        $path = __DIR__ . "/DataFixtures";

        $encoder = new \Alsciende\CerealBundle\JsonFileEncoder();
        $files = $encoder->decode($path, \AppBundle\Entity\Clan::class);

        $this->assertEquals(2, count($files));
    }
    
    public function testDecodeType()
    {
        $path = __DIR__ . "/DataFixtures";

        $encoder = new \Alsciende\CerealBundle\JsonFileEncoder();
        $files = $encoder->decode($path, \AppBundle\Entity\Type::class);

        $this->assertEquals(1, count($files));
    }
    
    public function testDecodeCard()
    {
        $path = __DIR__ . "/DataFixtures";

        $encoder = new \Alsciende\CerealBundle\JsonFileEncoder();
        $files = $encoder->decode($path, \AppBundle\Entity\Card::class);

        $this->assertEquals(2, count($files));
    }
}
