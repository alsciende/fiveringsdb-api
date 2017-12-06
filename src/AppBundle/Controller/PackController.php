<?php

namespace AppBundle\Controller;

use AppBundle\Behavior\Service\GetRepositoryTrait;
use AppBundle\Entity\Pack;
use AppBundle\Repository\PackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of PacksController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackController extends AbstractApiController
{
    use GetRepositoryTrait;

    /**
     * Get all Packs
     * @Route("/packs", name="packs_list")
     * @Method("GET")
     */
    public function listAction (Request $request, EntityManagerInterface $entityManager)
    {
        $this->setPublic($request);

        /** @var PackRepository $repository */
        $repository = $this->getRepository($entityManager, Pack::class);

        return $this->success(
            $repository->findAllSorted(),
            [
                'Default',
                'Cycle',
                'cycle' => [
                    'Id',
                ],
            ]
        );
    }
}
