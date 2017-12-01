<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Label;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 */
class LabelController extends AbstractApiController
{
    /**
     * Get all Labels
     * @Route("/labels", name="labels_list")
     * @Method("GET")
     */
    public function listAction (Request $request, EntityManagerInterface $entityManager)
    {
        $this->setPublic($request);

        return $this->success(
            $entityManager
                ->getRepository(Label::class)
                ->findBy(['lang' => $request->getLocale()]),
            [
                'Default',
            ]
        );
    }
}
