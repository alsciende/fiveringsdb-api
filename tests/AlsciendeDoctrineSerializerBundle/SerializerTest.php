<?php

namespace Tests\AlsciendeDoctrineSerializerBundle;

/**
 * Description of SerializerTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class SerializerTest extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
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
        
        $this->validator = static::$kernel->getContainer()
                ->get('validator');

        $this->reader = static::$kernel->getContainer()
                ->get('annotation_reader');
        
        $this->clearDatabase();
    }

    public function testImport ()
    {
        $path = __DIR__ . "/DataFixtures";
        $sourceManager = new \Alsciende\DoctrineSerializerBundle\Manager\SourceManager($this->referenceManager, 'doctrine_serializer', $path);
        $serializer = new \Alsciende\DoctrineSerializerBundle\Serializer($this->em, $sourceManager, $this->validator, $this->reader);
        
        $result = $serializer->import();
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
