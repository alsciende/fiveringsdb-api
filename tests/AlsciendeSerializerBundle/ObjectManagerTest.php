<?php

namespace Tests\AlsciendeSerializerBundle;

use AppBundle\Entity\Card;
use AppBundle\Entity\Pack;

/**
 * Description of ObjectManagerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ObjectManagerTest extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
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

    function testGetSingleIdentifier ()
    {
        $identifier = $this->objectManager->getSingleIdentifier(Card::class);
        $this->assertEquals('code', $identifier);
    }

    function testFindAssociations ()
    {
        //setup
        $this->createCycleCore();

        $data = [
            "cycle_code" => "core"
        ];
        //work
        $associations = $this->objectManager->findAssociations(Pack::class, $data);
        //assert
        $this->assertEquals(1, count($associations));
        $association = $associations[0];
        $this->assertEquals('cycle', $association['associationKey']);
        $this->assertArrayHasKey('associationValue', $association);
        $this->assertEquals('cycle_code', $association['referenceKeys'][0]);
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
