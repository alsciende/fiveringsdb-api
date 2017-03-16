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

    function testNormalizeClan ()
    {
        //setup
        $clan = new Clan();
        $clan->setCode('crab');
        $clan->setName("Crab");
        //work
        $normalizer = new NormalizingService($this->serializer, 'alsciende_serializer');
        $data = $normalizer->normalize($clan);
        //assert
        $this->assertEquals('crab', $data['code']);
        $this->assertEquals("Crab", $data['name']);
    }

    function testNormalizeCard ()
    {
        //setup
        $clan = new Clan();
        $clan->setCode('crab');
        $type = new Type();
        $type->setCode('stronghold');
        $card = new Card();
        $card->setCode('01001');
        $card->setClan($clan);
        $card->setName("The Impregnable Fortress of the Crab");
        $card->setType($type);
        //work
        $normalizer = new NormalizingService($this->serializer, 'alsciende_serializer');
        $data = $normalizer->normalize($card);
        //assert
        $this->assertEquals('01001', $data['code']);
        $this->assertEquals("The Impregnable Fortress of the Crab", $data['name']);
    }

    function testNormalizePack ()
    {
        //setup
        $cycle = new Cycle();
        $cycle->setCode('core');
        $pack = new Pack();
        $pack->setCode('core');
        $pack->setCycle($cycle);
        //work
        $normalizer = new NormalizingService($this->serializer, 'alsciende_serializer');
        $data = $normalizer->normalize($pack);
        //assert
        $this->assertEquals('core', $data['code']);
    }

    function testNormalizePackSlot ()
    {
        //setup
        $pack = new Pack();
        $pack->setCode('core');

        $card = new Card();
        $card->setCode('01001');

        $packslot = new PackSlot();
        $packslot->setCard($card);
        $packslot->setPack($pack);
        $packslot->setQuantity(3);
        //work
        $normalizer = new NormalizingService($this->serializer, 'alsciende_serializer');
        $data = $normalizer->normalize($packslot);
        //assert
        $this->assertEquals(3, $data['quantity']);
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
