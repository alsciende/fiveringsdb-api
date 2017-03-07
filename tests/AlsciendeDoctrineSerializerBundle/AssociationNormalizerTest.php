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

        $this->referenceManager = static::$kernel->getContainer()
                ->get('alsciende.doctrine_serializer.reference_manager.entity');
        
        $this->clearDatabase();
    }

    function testGetSingleIdentifier ()
    {
        $normalizer = new AssociationNormalizer($this->referenceManager, $this->em);
        $identifier = $normalizer->getSingleIdentifier($this->em->getClassMetadata(Card::class));
        $this->assertEquals('code', $identifier);
    }

    function testNormalizeClan ()
    {
        //setup
        $clan = new Clan();
        $clan->setCode('crab');
        $clan->setName("Crab");
        //work
        $normalizer = new AssociationNormalizer($this->referenceManager, $this->em);
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
        $normalizer = new AssociationNormalizer($this->referenceManager, $this->em);
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
        $normalizer = new AssociationNormalizer($this->referenceManager, $this->em);
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
        $normalizer = new AssociationNormalizer($this->referenceManager, $this->em);
        $data = $normalizer->normalize($packslot);
        //assert
        $this->assertEquals('core', $data['pack_code']);
        $this->assertEquals('01001', $data['card_code']);
    }

    function testFindReferencedEntity ()
    {
        //setup
        $reference = [
            'joinColumns' => [
                'clan_code' => [
                    'referencedColumnName' => 'code',
                    'referencedValue' => 'crab'
                ]
            ],
            'className' => Clan::class
        ];
        $this->createCrab();
        //work
        $entity = $this->referenceManager->findReferencedEntity('clan', $reference);
        //assert
        $this->assertNotNull($entity);
        $this->assertEquals('crab', $entity->getCode());
    }

    function testFindReferences ()
    {
        //setup
        $this->createStronghold();
        $this->createCrab();

        $data = [
            'clan_code' => 'crab',
            'type_code' => 'stronghold'
        ];
        //work
        $associations = $this->referenceManager->findReferences(Card::class, $data);
        //assert
        $this->assertEquals(2, count($associations));
        $this->assertArrayHasKey('clan', $associations);
        $this->assertArrayHasKey('type', $associations);
        $this->assertArrayHasKey('joinColumns', $associations['clan']);
        $this->assertArrayHasKey('className', $associations['clan']);
        $this->assertArrayHasKey('clan_code', $associations['clan']['joinColumns']);
        $this->assertArrayHasKey('referencedColumnName', $associations['clan']['joinColumns']['clan_code']);
        $this->assertArrayHasKey('referencedValue', $associations['clan']['joinColumns']['clan_code']);
    }
    
    function testFindForeignKeyValues()
    {
        //setup
        $this->createCycleCore();

        $data = [
            "cycle_code" => "core"
        ];
        //work
        $normalizer = new AssociationNormalizer($this->referenceManager, $this->em);
        $references = $this->referenceManager->findReferences(Pack::class, $data);
        $foreignKeyValues = $normalizer->findForeignKeyValues($references);
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
