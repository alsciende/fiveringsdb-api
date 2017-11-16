<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deck;
use AppBundle\Service\DeckLikeManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Description of DeckLikeController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckLikeController extends AbstractController
{
    /**
     * Create a like from a public deck
     * @Route("/decks/{deckId}/likes")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function postAction (Deck $deck)
    {
        if ($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }

        try {
            $nbLikes = $this->get(DeckLikeManager::class)->addLike($deck, $this->getUser());
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($nbLikes);
    }

    /**
     * Delete a like from a public deck
     * @Route("/decks/{deckId}/likes")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function deleteAction (Deck $deck)
    {
        if ($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }

        try {
            $nbLikes = $this->get(DeckLikeManager::class)->removeLike($deck, $this->getUser());
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($nbLikes);
    }

    /**
     * @Route("/decks/{deckId}/likes/me")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function getAction (Deck $deck)
    {
        if ($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }

        try {
            $like = $this->get(DeckLikeManager::class)->getLike($deck, $this->getUser());
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($like);
    }
}
