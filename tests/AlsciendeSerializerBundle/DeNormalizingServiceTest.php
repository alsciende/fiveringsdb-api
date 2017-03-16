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
class DeNormalizingServiceTest extends KernelTestCase
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

        $this->clearDatabase();
    }

    function testDenormalizeClan ()
    {
        //setup
        $data = array('code' => 'crab', 'name' => "Crab");
        //work
        $objectManager = new \Alsciende\SerializerBundle\Manager\Entity\ObjectManager($this->em);
        $normalizer = new NormalizingService($objectManager);
        $data = $normalizer->denormalize($data, Clan::class, ["code" => "string", "name" => "string"]);
        //assert
        $this->assertSame('crab', $data['code']);
        $this->assertSame("Crab", $data['name']);
    }

    function testDenormalizePack ()
    {
        //setup
        $this->createCycleCore();
        $data = [
            'code' => 'core',
            'name' => "Core Set",
            'position' => 1,
            'size' => 1,
            'ffg_id' => 1,
            'released_at' => '2017-09-01',
            'cycle_code' => 'core',
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
        $objectManager = new \Alsciende\SerializerBundle\Manager\Entity\ObjectManager($this->em);
        $normalizer = new NormalizingService($objectManager);
        $data = $normalizer->denormalize($data, Pack::class, $map);
        //assert
        $this->assertSame('core', $data['code']);
        $this->assertSame("Core Set", $data['name']);
        $this->assertSame(1, $data['position']);
        $this->assertSame(1, $data['size']);
        $this->assertSame(1, $data['ffgId']);
        $this->assertInstanceOf(\DateTime::class, $data['releasedAt']);
        $this->assertInstanceOf(Cycle::class, $data['cycle']);
    }

    function testDenormalizeCard ()
    {
        //setup
        $this->createCrab();
        $this->createStronghold();
        $data = [
            'code' => '01001',
            'name' => "The Impregnable Fortress of the Crab",
            'clan_code' => 'crab',
            'type_code' => 'stronghold'
        ];
        $map = [
            "code" => "string",
            "name" => "string",
            "clan" => "association",
            "type" => "association"
        ];
        //work
        $objectManager = new \Alsciende\SerializerBundle\Manager\Entity\ObjectManager($this->em);
        $normalizer = new NormalizingService($objectManager);
        $data = $normalizer->denormalize($data, Card::class, $map);
        //assert
        $this->assertSame('01001', $data['code']);
        $this->assertSame("The Impregnable Fortress of the Crab", $data['name']);
        $this->assertInstanceOf(Clan::class, $data['clan']);
        $this->assertInstanceOf(Type::class, $data['type']);
    }

    function testDenormalizePackSlot ()
    {
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
