<?php

namespace Tests\AlsciendeSerializerBundle;

use AppBundle\Entity\Card;
use AppBundle\Entity\Clan;
use AppBundle\Entity\Cycle;
use AppBundle\Entity\Pack;
use AppBundle\Entity\PackSlot;
use AppBundle\Entity\Type;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class ReferencingServiceTest extends KernelTestCase
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

        $this->objectManager = static::$kernel->getContainer()
                ->get('alsciende_serializer.object_manager.entity');

        $this->serializer = static::$kernel->getContainer()
                ->get('serializer');

        $this->clearDatabase();
    }

    function testReferenceClan ()
    {
        //setup
        $clan = new Clan();
        $clan->setCode('crab');
        $clan->setName("Crab");
        //work
        $referencer = new \Alsciende\SerializerBundle\Service\ReferencingService($this->objectManager);
        $data = $referencer->reference($clan);
        //assert
        $this->assertEmpty($data);
    }

    function testDereferenceClan()
    {
        //setup
        $data = [];
        //work
        $referencer = new \Alsciende\SerializerBundle\Service\ReferencingService($this->objectManager);
        $object = $referencer->dereference($data, Clan::class);
        //assert
        $this->assertEquals(Clan::class, get_class($object));
        $this->assertEmpty($object->getCode());
    }

    function testReferenceCard ()
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
        $referencer = new \Alsciende\SerializerBundle\Service\ReferencingService($this->objectManager);
        $data = $referencer->reference($card);
        //assert
        $this->assertEquals('crab', $data['clan_code']);
        $this->assertEquals('stronghold', $data['type_code']);
    }

    function testDereferenceCard ()
    {
        //setup
        $this->createCrab();
        $this->createStronghold();
        $data = ['clan_code' => 'crab', 'type_code' => 'stronghold'];
        //work
        $referencer = new \Alsciende\SerializerBundle\Service\ReferencingService($this->objectManager);
        $card = $referencer->dereference($data, Card::class);
        //assert
        $this->assertEquals(Card::class, get_class($card));
        $this->assertEquals('crab', $card->getClan()->getCode());
        $this->assertEquals('stronghold', $card->getType()->getCode());
    }

    function testReferencePack ()
    {
        //setup
        $cycle = new Cycle();
        $cycle->setCode('core');
        $pack = new Pack();
        $pack->setCode('core');
        $pack->setCycle($cycle);
        //work
        $referencer = new \Alsciende\SerializerBundle\Service\ReferencingService($this->objectManager);
        $data = $referencer->reference($pack);
        //assert
        $this->assertEquals('core', $data['cycle_code']);
    }

    function testReferencePackSlot ()
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
        $referencer = new \Alsciende\SerializerBundle\Service\ReferencingService($this->objectManager);
        $data = $referencer->reference($packslot);
        //assert
        $this->assertEquals('core', $data['pack_code']);
        $this->assertEquals('01001', $data['card_code']);
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
