<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Entity\Strain;
use AppBundle\Form\Type\StrainType;
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
     * If 'origin' is set in the request body,
     * find the deck whose id 'origin' is
     * and create a first deck in the strain as a copy of that deck
     * @Route("/strains")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @TODO check quota
     */
    public function postAction (Request $request)
    {
        $strain = new Strain($this->getUser());
        $form = $this->createForm(StrainType::class, $strain);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($strain);
            if ($strain->getOrigin() !== null) {
                $origin = $this->getDoctrine()->getManager()->getRepository(Deck::class)->find($strain->getOrigin());
                if ($origin instanceof Deck
                    && $this->isGranted('READ_DECK', $origin)) {
                    $copy = new Deck();
                    $copy->setUser($this->getUser())->setStrain($strain);
                    $this->get('app.deck_manager')->copy($copy, $origin)->persist($copy);
                }
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->success($strain, [
                'Default',
                'head_group',
                'head' => [
                    'Default'
                ]
            ]);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
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
        if ($strain->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $strain->clearHead();
        $this->get('doctrine')->getManager()->flush();
        $this->get('doctrine')->getManager()->remove($strain);
        $this->get('doctrine')->getManager()->flush();

        return $this->success();
    }
}
