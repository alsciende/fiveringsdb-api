<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Form\Type\DeckType;
use AppBundle\Manager\DeckManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Private decks
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PrivateDeckController extends BaseApiController
{

    /**
     * Create a deck in initial version (0.1)
     * @Route("/private-decks")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request)
    {
      $deck = new Deck();
      $form = $this->createForm(DeckType::class, $deck);
      $form->submit(json_decode($request->getContent(), true), false);

      if($form->isSubmitted() && $form->isValid()) {
        $deck = $this->get('app.deck_manager')->createNewInitialDeck($deck, $this->getUser());
        $this->getDoctrine()->getManager()->flush();

          return $this->success($deck);
      }

      return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * Get all private decks
     * @Route("/private-decks")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction ()
    {
        $decks = $this->getDoctrine()->getRepository(Deck::class)->findBy(['user' => $this->getUser()]);
        return $this->success($decks);
    }

    /**
     * Get a private deck
     * @Route("/private-decks/{id}")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function getAction (Deck $deck)
    {
        if($deck->isPublished()) {
            throw $this->createNotFoundException();
        }
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        return $this->success($deck);
    }

    /**
     * Delete a private deck. Other versions are untouched.
     * @Route("/private-decks/{id}")
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

        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $manager->deleteDeck($deck);
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }
        return $this->success();
    }

}
