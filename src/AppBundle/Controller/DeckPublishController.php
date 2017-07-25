<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Strain;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Description of DeckPublishController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckPublishController extends BaseApiController
{
    /**
     * Publish the head of the strain
     * @Route("/strains/{id}/publish")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Strain $strain)
    {
        if($strain->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        if($strain->getHead()->isPublished()) {
          throw $this->createNotFoundException();
        }

        $this->get('app.deck_manager')->publish($strain->getHead());
        $this->getDoctrine()->getManager()->flush();

        return $this->success($strain->getHead());
    }
}
