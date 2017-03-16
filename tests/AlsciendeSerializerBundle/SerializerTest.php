<?php

namespace Tests\AlsciendeSerializerBundle;

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
        $objectManager = new \Alsciende\SerializerBundle\Manager\Entity\ObjectManager($this->em);
        $sourceManager = new \Alsciende\SerializerBundle\Manager\SourceManager($objectManager, 'serializer', $path);
        $storingService = new \Alsciende\SerializerBundle\Service\StoringService();
        $encoder = new \Alsciende\SerializerBundle\Service\EncodingService();
        $normalizer = new \Alsciende\SerializerBundle\Normalizer\Normalizer($objectManager, $this->serializer);
        $serializer = new \Alsciende\SerializerBundle\Serializer\Serializer($storingService, $encoder, $normalizer, $objectManager, $sourceManager, $this->validator, $this->reader);

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
