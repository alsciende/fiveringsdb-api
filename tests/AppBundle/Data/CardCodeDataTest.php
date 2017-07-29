<?php

declare(strict_types=1);

namespace Tests\AppBundle\Data;

/**
 * Description of CardIdDataTest
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardIdDataTest extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }
    
    public function testIds()
    {
        /* @var $doctrine \Doctrine\Bundle\DoctrineBundle\Registry */
        $doctrine = $this->container->get('doctrine');
        
        /* @var $slugify \Cocur\Slugify\Slugify */
        $slugify = $this->container->get('cocur_slugify');        
        
        /* @var $cards \AppBundle\Entity\Card[] */
        $cards = $doctrine->getManager()->getRepository(\AppBundle\Entity\Card::class)->findAll();
        foreach($cards as $card) {
            $this->assertEquals($card->getId(), $slugify->slugify($card->getName()));
        }
            
    }
}
