<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Form\Type\DeckType;
use AppBundle\Manager\DeckManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of DeckLineageController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckLineageController extends BaseApiController
{

    /**
     * Create a minor version
     * @Route("/private-decks/{deckId}/lineage")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("parent", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function postAction (Request $request, Deck $parent)
    {
      if($parent->getUser() !== $this->getUser()) {
          throw $this->createAccessDeniedException();
      }

      $deck = new Deck();
      $form = $this->createForm(DeckType::class, $deck);
      $form->submit(json_decode($request->getContent(), true), false);

      if($form->isSubmitted() && $form->isValid()) {
        $deck = $this->get('app.deck_manager')->createNewMinorVersion($deck, $parent);
        $this->getDoctrine()->getManager()->flush();

          return $this->success($deck);
      }

      return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * Get all private versions of a deck
     * @Route("/private-decks/{deckId}/lineage")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function listAction (Deck $deck)
    {
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        /* @var $repository \AppBundle\Repository\DeckRepository */
        $repository = $this->getDoctrine()->getRepository(Deck::class);
        $decks = $repository->findByLineage($deck->getLineage(), $deck->getUser());
        return $this->success($decks);
    }

    /**
     * Delete a lineage: all private versions of a deck
     * @Route("/private-decks/{deckId}/lineage")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function deleteAction (Deck $deck)
    {
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $manager->deleteLineage($deck);
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success();
    }
}
