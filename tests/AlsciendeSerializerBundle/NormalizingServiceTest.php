<?php

namespace Tests\AlsciendeSerializerBundle;

use Alsciende\SerializerBundle\Service\NormalizingService;
use AppBundle\Entity\Card;
use AppBundle\Entity\Clan;
use AppBundle\Entity\Cycle;
use AppBundle\Entity\Pack;
use AppBundle\Entity\PackSlot;
use AppBundle\Entity\Type;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of NormalizerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class NormalizingServiceTest extends KernelTestCase
{

    use DomainFixtures;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp ()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager();

        $this->serializer = static::$kernel->getContainer()
                ->get('serializer');

        $this->clearDatabase();
    }

    /**
     * 
     * @return NormalizingService
     */
    private function getNormalizer ()
    {
        $objectManager = new \Alsciende\SerializerBundle\Manager\Entity\ObjectManager($this->em);
        $normalizer = new NormalizingService($objectManager);
        return $normalizer;
    }

    function testNormalizeClan ()
    {
        //setup
        $data = [
            'code' => 'crab',
            'name' => "Crab",
        ];
        $map = [
            "code" => "string",
            "name" => "string",
        ];
        //work
        $data = $this->getNormalizer()->normalize($data, Clan::class, $map);
        //assert
        $this->assertEquals('crab', $data['code']);
        $this->assertEquals("Crab", $data['name']);
    }

    function testNormalizePack ()
    {
        //setup
        $data = [
            'code' => 'core',
            'name' => "Core Set",
            'position' => 1,
            'size' => 1,
            'ffgId' => 1,
            'releasedAt' => \DateTime::createFromFormat('Y-m-d', '2017-09-01'),
            'cycle' => $this->createCycleCore(),
        ];
        $map = [
            "code" => "string",
            "name" => "string",
            "position" => "integer",
            "size" => "integer",
            "ffgId" => "integer",
            "releasedAt" => "date",
            "cycle" => "association",
        ];
        //work
        $data = $this->getNormalizer()->normalize($data, Pack::class, $map);
        //assert
        $this->assertEquals(['code' => 'core', 'name' => "Core Set", 'position' => 1, 'size' => 1, 'ffg_id' => 1, 'released_at' => '2017-09-01', 'cycle_code' => 'core'], $data);
    }

    function testNormalizeCard ()
    {
        //setup
        $data = [
            'code' => '01001',
            'name' => "The Impregnable Fortress of the Crab",
            'clan' => $this->createCrab(),
            'type' => $this->createStronghold(),
        ];
        $map = [
            "code" => "string",
            "name" => "string",
            "clan" => "association",
            "type" => "association",
        ];
        //work
        $data = $this->getNormalizer()->normalize($data, Card::class, $map);
        //assert
        $this->assertEquals(['code' => '01001', 'name' => "The Impregnable Fortress of the Crab", 'clan_code' => 'crab', 'type_code' => 'stronghold'], $data);
    }

    function testNormalizePackSlot ()
    {
        //setup
        $data = [
            'quantity' => 3,
            'pack' => $this->createPackCore(),
            'card' => $this->createCrabFortress(),
        ];
        $map = [
            "quantity" => "integer",
            "pack" => "association",
            "card" => "association",
        ];
        //work
        $data = $this->getNormalizer()->normalize($data, PackSlot::class, $map);
        //assert
        $this->assertEquals(['quantity'=>3, 'pack_code' => 'core', 'card_code' => "01001"], $data);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown ()
    {
        parent::tearDown();

        $this->clearDatabase();
        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

}
