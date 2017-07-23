<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Entity\Strain;
use AppBundle\Form\Type\DeckType;
use AppBundle\Manager\DeckManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class StrainController extends BaseApiController
{

    /**
     * Create a strain
     * @Route("/strains")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @TODO : check quota
     */
    public function postAction (Request $request)
    {
        $strain = $this->get('app.deck_manager')->createNewStrain($this->getUser());
        $this->getDoctrine()->getManager()->flush();

        return $this->success($strain);
    }

    /**
     * Get all strains
     * @Route("/strains")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction ()
    {
        $strains = $this->getDoctrine()->getRepository(Strain::class)->findBy(['user' => $this->getUser()]);
        return $this->success($strains, [
            'Default',
            'head_group',
            'head' => [
                'Default'
            ]
        ]);
    }

    /**
     * Delete a strain.
     * All its decks are deleted as well.
     * Published (major) decks don't have a strain.
     * @Route("/strains/{id}")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction (Strain $strain)
    {
        if($strain->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $strain->clearHead();
        $this->get('doctrine')->getManager()->flush();
        $this->get('doctrine')->getManager()->remove($strain);
        $this->get('doctrine')->getManager()->flush();

        return $this->success();
    }
}
