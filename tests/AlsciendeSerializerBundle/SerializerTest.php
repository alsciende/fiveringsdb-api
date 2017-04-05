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
        $objectManager = new \Alsciende\SerializerBundle\Manager\Entity\ObjectManager($this->em);
        $storingService = new \Alsciende\SerializerBundle\Service\StoringService();
        $encoder = new \Alsciende\SerializerBundle\Service\EncodingService();
        $normalizingService = new \Alsciende\SerializerBundle\Service\NormalizingService($objectManager);
        $serializer = new \Alsciende\SerializerBundle\Serializer\Serializer($storingService, $encoder, $normalizingService, $objectManager, $this->validator);

        $source = new \Alsciende\SerializerBundle\Model\Source(\AppBundle\Entity\Clan::class, __DIR__ . "/DataFixtures");
        $source->addProperty('code', 'string');
        $source->addProperty('name', 'string');
        
        $result = $serializer->importSource($source);
        $this->assertEquals(2, count($result));
    }
    
    public function testImportFragment()
    {
        $objectManager = new \Alsciende\SerializerBundle\Manager\Entity\ObjectManager($this->em);
        $storingService = new \Alsciende\SerializerBundle\Service\StoringService();
        $encoder = new \Alsciende\SerializerBundle\Service\EncodingService();
        $normalizingService = new \Alsciende\SerializerBundle\Service\NormalizingService($objectManager);
        $serializer = new \Alsciende\SerializerBundle\Serializer\Serializer($storingService, $encoder, $normalizingService, $objectManager, $this->validator);

        $source = new \Alsciende\SerializerBundle\Model\Source(\AppBundle\Entity\Clan::class, __DIR__ . "/DataFixtures");
        $source->addProperty('code', 'string');
        $source->addProperty('name', 'string');
        
        $data = ['code' => 'crab', 'name' => 'Crab'];
                
        $block = new \Alsciende\SerializerBundle\Model\Block(json_encode($data));
        $block->setSource($source);
        
        $fragment = new \Alsciende\SerializerBundle\Model\Fragment($data);
        $fragment->setBlock($block);
        
        $result = $serializer->importFragment($fragment);
        $this->assertEquals('Crab', $result['array']['name']);
        $this->assertNull($result['original']['name']);
        $this->assertInstanceOf(\AppBundle\Entity\Clan::class, $result['entity']);
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
