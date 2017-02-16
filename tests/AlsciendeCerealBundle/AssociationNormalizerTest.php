<?php

namespace Tests\AlsciendeCerealBundle;

/**
 * Description of AssociationNormalizerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AssociationNormalizerTest extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{

    use DomainFixtures;

    /**
     * @var \Doctrine\ORM\EntityManager
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

    function testGetSingleIdentifier ()
    {
        $normalizer = new \Alsciende\CerealBundle\AssociationNormalizer($this->em);
        $identifier = $normalizer->getSingleIdentifier($this->em->getClassMetadata(\AppBundle\Entity\Card::class));
        $this->assertEquals('code', $identifier);
    }

    function testNormalizeClan ()
    {
        //setup
        $clan = new \AppBundle\Entity\Clan();
        $clan->setCode('crab');
        $clan->setName("Crab");
        //work
        $normalizer = new \Alsciende\CerealBundle\AssociationNormalizer($this->em);
        $data = $normalizer->normalize($clan);
        //assert
        $this->assertTrue(is_array($data));
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertEquals('crab', $data['code']);
        $this->assertEquals("Crab", $data['name']);
    }

    function testNormalizeCard ()
    {
        //setup
        $clan = new \AppBundle\Entity\Clan();
        $clan->setCode('crab');
        $type = new \AppBundle\Entity\Type();
        $type->setCode('stronghold');
        $card = new \AppBundle\Entity\Card();
        $card->setCode('01001');
        $card->setClan($clan);
        $card->setName("The Impregnable Fortress of the Crab");
        $card->setType($type);
        //work
        $normalizer = new \Alsciende\CerealBundle\AssociationNormalizer($this->em);
        $data = $normalizer->normalize($card);
        //assert
        $this->assertTrue(is_array($data));
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('clan_code', $data);
        $this->assertArrayHasKey('type_code', $data);
        $this->assertEquals('01001', $data['code']);
        $this->assertEquals("The Impregnable Fortress of the Crab", $data['name']);
        $this->assertEquals('crab', $data['clan_code']);
        $this->assertEquals('stronghold', $data['type_code']);
    }

    function testFindReferenceMetadata ()
    {
        //setup
        $data = [
            'clan_code' => 'crab',
            'type_code' => 'stronghold'
        ];
        $associationMapping = $this->em->getClassMetadata(\AppBundle\Entity\Card::class)->getAssociationMapping('clan');
        //work
        $normalizer = new \Alsciende\CerealBundle\AssociationNormalizer($this->em);
        $reference = $normalizer->findReferenceMetadata($data, $associationMapping);
        $this->assertArrayHasKey('joinColumns', $reference);
        $this->assertArrayHasKey('className', $reference);
        $this->assertArrayHasKey('clan_code', $reference['joinColumns']);
        $this->assertArrayHasKey('referencedColumnName', $reference['joinColumns']['clan_code']);
        $this->assertArrayHasKey('referencedValue', $reference['joinColumns']['clan_code']);
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
            'className' => \AppBundle\Entity\Clan::class
        ];
        $this->createCrab();
        //work
        $normalizer = new \Alsciende\CerealBundle\AssociationNormalizer($this->em);
        $entity = $normalizer->findReferencedEntity('clan', $reference, $this->em);
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
        $normalizer = new \Alsciende\CerealBundle\AssociationNormalizer($this->em);
        $associations = $normalizer->findReferences($data, \AppBundle\Entity\Card::class);
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
