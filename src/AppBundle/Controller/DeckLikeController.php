<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deck;
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
class DeckLikeController extends BaseApiController
{
    /**
     * Create a like from a public deck
     * @Route("/decks/{deckId}/like")
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
            $nbLikes = $this->get('app.deck_manager')->addLike($deck, $this->getUser());
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($nbLikes);
    }

    /**
     * Delete a like from a public deck
     * @Route("/decks/{deckId}/like")
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
            $nbLikes = $this->get('app.deck_manager')->removeLike($deck, $this->getUser());
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($nbLikes);
    }
}
