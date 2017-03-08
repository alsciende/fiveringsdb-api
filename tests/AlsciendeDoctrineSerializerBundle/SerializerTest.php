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

        $this->serializer = static::$kernel->getContainer()
                ->get('serializer');

        $this->validator = static::$kernel->getContainer()
                ->get('validator');

        $this->reader = static::$kernel->getContainer()
                ->get('annotation_reader');

        $this->clearDatabase();
    }

    public function testImport ()
    {
        $path = __DIR__ . "/DataFixtures";
        $objectManager = new \Alsciende\DoctrineSerializerBundle\Manager\Entity\ObjectManager($this->em);
        $sourceManager = new \Alsciende\DoctrineSerializerBundle\Manager\SourceManager($objectManager, 'doctrine_serializer', $path);
        $scanner = new \Alsciende\DoctrineSerializerBundle\Scanner\Scanner();
        $encoder = new \Alsciende\DoctrineSerializerBundle\Encoder\Encoder();
        $normalizer = new \Alsciende\DoctrineSerializerBundle\Normalizer\Normalizer($objectManager, $this->serializer);
        $serializer = new \Alsciende\DoctrineSerializerBundle\Serializer\Serializer($scanner, $encoder, $normalizer, $objectManager, $sourceManager, $this->validator, $this->reader);

        $serializer->import();
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
