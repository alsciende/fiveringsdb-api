<?php

namespace Tests\AlsciendeDoctrineSerializerBundle;

use Alsciende\DoctrineSerializerBundle\AssociationNormalizer;
use AppBundle\Entity\Card;
use AppBundle\Entity\Clan;
use AppBundle\Entity\Cycle;
use AppBundle\Entity\Pack;
use AppBundle\Entity\PackSlot;
use AppBundle\Entity\Type;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of AssociationNormalizerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AssociationNormalizerTest extends KernelTestCase
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
                ->get('alsciende.doctrine_serializer.reference_manager.entity');

        $this->serializer = static::$kernel->getContainer()
                ->get('serializer');

        $this->clearDatabase();
    }

    function testGetSingleIdentifier ()
    {
        $identifier = $this->objectManager->getSingleIdentifier(Card::class);
        $this->assertEquals('code', $identifier);
    }

    function testNormalizeClan ()
    {
        //setup
        $clan = new Clan();
        $clan->setCode('crab');
        $clan->setName("Crab");
        //work
        $normalizer = new AssociationNormalizer($this->objectManager, $this->serializer);
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
        $normalizer = new AssociationNormalizer($this->objectManager, $this->serializer);
        $data = $normalizer->normalize($card);
        //assert
        $this->assertEquals('01001', $data['code']);
        $this->assertEquals("The Impregnable Fortress of the Crab", $data['name']);
        $this->assertEquals('crab', $data['clan_code']);
        $this->assertEquals('stronghold', $data['type_code']);
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
        $normalizer = new AssociationNormalizer($this->objectManager, $this->serializer);
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
        $normalizer = new AssociationNormalizer($this->objectManager, $this->serializer);
        $data = $normalizer->normalize($packslot);
        //assert
        $this->assertEquals('core', $data['pack_code']);
        $this->assertEquals('01001', $data['card_code']);
    }

    function testFindForeignKeyValues ()
    {
        //setup
        $this->createCycleCore();

        $data = [
            "cycle_code" => "core"
        ];
        //work
        $foreignKeyValues = $this->objectManager->findForeignKeyValues(Pack::class, $data);
        //assert
        $this->assertEquals(1, count($foreignKeyValues));
        $foreignKeyValue = $foreignKeyValues[0];
        $this->assertEquals('cycle', $foreignKeyValue['foreignKey']);
        $this->assertArrayHasKey('foreignValue', $foreignKeyValue);
        $this->assertEquals('cycle_code', $foreignKeyValue['joinColumns'][0]);
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
