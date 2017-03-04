<?php

namespace Tests\AlsciendeDoctrineSerializerBundle;

/**
 * Description of DeserializationJobTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeserializationJobTest extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{

    use DomainFixtures;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var \Symfony\Component\Validator\Validator\RecursiveValidator */
    private $validator;

    /**
     * {@inheritDoc}
     */
    protected function setUp ()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager();

        $this->validator = static::$kernel->getContainer()
                ->get('validator');

        $this->clearDatabase();
    }

    function testRunClan ()
    {
        //setup
        $filepath = __DIR__ . "/DataFixtures/Clan.json";
        $data = json_decode(file_get_contents($filepath), true);
        $incoming = $data[0];
        $classname = \AppBundle\Entity\Clan::class;
        //work
        $job = new \Alsciende\DoctrineSerializerBundle\DeserializationJob($filepath, $incoming, $classname);
        $job->run($this->em, $this->validator);
        //assert
        /* @var $entity \AppBundle\Entity\Clan */
        $entity = $job->getEntity();
        $changes = $job->getChanges();
        $original = $job->getOriginal();
        $this->assertNotNull($entity);
        $this->assertEquals('crab', $entity->getCode());
        $this->assertEquals("Crab", $entity->getName());
        $this->assertEquals(1, count($changes));
        $this->assertEquals("Crab", $changes['name']);
        $this->assertEquals(2, count($original));
        $this->assertEquals('crab', $original['code']);
        $this->assertNull($original['name']);
    }

    function testRunCard ()
    {
        //setup
        $this->createStronghold();
        $this->createCrab();

        $filepath = __DIR__ . "/DataFixtures/Card/01001.json";
        $incoming = json_decode(file_get_contents($filepath), true);
        $classname = \AppBundle\Entity\Card::class;
        $job = new \Alsciende\DoctrineSerializerBundle\DeserializationJob($filepath, $incoming, $classname);

        //work
        $job->run($this->em, $this->validator);

        //assert
        /* @var $entity \AppBundle\Entity\Card */
        $entity = $job->getEntity();
        $changes = $job->getChanges();
        $original = $job->getOriginal();
        $this->assertNotNull($entity);
        $this->assertEquals('01001', $entity->getCode());
        $this->assertEquals("The Impregnable Fortress of the Crab", $entity->getName());
        $this->assertEquals("The Impregnable Fortress of the Crab", $changes['name']);
        $this->assertEquals('crab', $changes['clan_code']);
        $this->assertEquals('stronghold', $changes['type_code']);
        $this->assertEquals('01001', $original['code']);
        $this->assertNull($original['name']);
    }

    function testRunPackSlot ()
    {
        //setup
        $this->createCrabFortress();
        $this->createPackCore();

        $filepath = __DIR__ . "/DataFixtures/PackSlot/core.json";
        $data = json_decode(file_get_contents($filepath), true);
        $incoming = $data[0];
        $classname = \AppBundle\Entity\PackSlot::class;
        $job = new \Alsciende\DoctrineSerializerBundle\DeserializationJob($filepath, $incoming, $classname);

        //work
        $job->run($this->em, $this->validator);

        //assert
        /* @var $entity \AppBundle\Entity\Card */
        $entity = $job->getEntity();
        $changes = $job->getChanges();
        $original = $job->getOriginal();
        $this->assertNotNull($entity);
        $this->assertEquals('01001', $entity->getCard()->getCode());
        $this->assertEquals('core', $entity->getPack()->getCode());
        $this->assertEquals(3, $entity->getQuantity());
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
