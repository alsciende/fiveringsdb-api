<?php

namespace AppBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Description of BaseManage
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class BaseManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Serializer */
    protected $serializer;

    public function __construct (EntityManagerInterface $entityManager, Serializer $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }
}
