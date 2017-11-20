<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Label;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 */
class LabelController extends AbstractController
{
    /**
     * Get all Labels
     * @Route("/labels")
     * @Method("GET")
     */
    public function listAction (Request $request)
    {
        $this->setPublic($request);

        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(Label::class)
                ->findBy(['lang' => $request->getLocale()]),
            [
                'Default',
            ]
        );
    }
}
