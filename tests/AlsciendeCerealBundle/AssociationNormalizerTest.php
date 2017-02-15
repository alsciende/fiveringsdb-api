<?php

namespace Tests\AlsciendeCerealBundle;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of AssociationNormalizerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AssociationNormalizerTest extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{

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
    }
    
    function testGetSingleIdentifier()
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

    /**
     * {@inheritDoc}
     */
    protected function tearDown ()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

}
