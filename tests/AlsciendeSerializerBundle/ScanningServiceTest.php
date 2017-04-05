<?php

namespace Tests\AlsciendeSerializerBundle;

/**
 * Description of ScanningServiceTest
 *
 * @author Cédric Bertolini <cedric.bertolini@proximedia.fr>
 */
class ScanningServiceTest extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    
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

    }
    
    public function testFindSources()
    {
        $path = __DIR__ . "/DataFixtures";
        $objectManager = new \Alsciende\SerializerBundle\Manager\Entity\ObjectManager($this->em);
        $sourceOrderingService = new \Alsciende\SerializerBundle\Service\SourceOrderingService($objectManager);
        $scanningService = new \Alsciende\SerializerBundle\Service\ScanningService($objectManager, $sourceOrderingService, $this->reader, $path);
        $sources = $scanningService->findSources();
        $this->assertEquals(6, count($sources));
    }

}
