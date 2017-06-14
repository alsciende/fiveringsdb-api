<?php

namespace AppBundle\Manager;

/**
 * Description of BaseManage
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class BaseManager
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     *
     * @var \Symfony\Component\Serializer\Serializer
     */
    protected $serializer;

    public function __construct (\Doctrine\ORM\EntityManager $entityManager, \Symfony\Component\Serializer\Serializer $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

}
