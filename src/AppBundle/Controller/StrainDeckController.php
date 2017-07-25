<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Entity\Strain;
use AppBundle\Form\Type\DeckType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class StrainDeckController extends BaseApiController
{
    /**
     * Create a minor version
     * @Route("/strains/{id}/decks")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request, Strain $strain)
    {
      if($strain->getUser() !== $this->getUser()) {
          throw $this->createAccessDeniedException();
      }

      $deck = new Deck();
      $form = $this->createForm(DeckType::class, $deck);
      $form->submit(json_decode($request->getContent(), true), false);

      if($form->isSubmitted() && $form->isValid()) {
        $deck->setUser($this->getUser())->setStrain($strain);
        $this->get('app.deck_manager')->persist($deck);
        $this->getDoctrine()->getManager()->flush();

        return $this->success($deck);
      }

      return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * Get all private versions of a deck
     * @Route("/strains/{id}/decks")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction (Strain $strain)
    {
        if($strain->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $decks = $this
          ->getDoctrine()
          ->getRepository(Deck::class)
          ->findBy(['strain' => $strain], ['createdAt' => 'ASC']);

        return $this->success($decks);
    }

    /**
     * Get a private deck
     * @Route("/strains/{strainId}/decks/{id}")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function getAction (Deck $deck)
    {
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->success($deck);
    }

    /**
     * Delete a private deck. Other versions are untouched.
     * @Route("/strains/{strainId}/decks/{id}")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction (Deck $deck)
    {
        if($deck->isPublished()) {
            throw $this->createNotFoundException();
        }
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        try {
            $this->get('app.deck_manager')->deleteDeck($deck);
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }
        return $this->success();
    }
}
