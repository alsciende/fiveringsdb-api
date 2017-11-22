<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deck;
use AppBundle\Service\DeckLikeManager;
use Doctrine\ORM\EntityManagerInterface;
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
    public function postAction (Deck $deck, DeckLikeManager $deckLikeManager, EntityManagerInterface $entityManager)
    {
        if ($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }

        try {
            $nbLikes = $deckLikeManager->addLike($deck, $this->getUser());
            $entityManager->flush();
        } catch (\Exception $ex) {
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
    public function deleteAction (Deck $deck, DeckLikeManager $deckLikeManager, EntityManagerInterface $entityManager)
    {
        if ($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }

        try {
            $nbLikes = $deckLikeManager->removeLike($deck, $this->getUser());
            $entityManager->flush();
        } catch (\Exception $ex) {
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
    public function getAction (Deck $deck, DeckLikeManager $deckLikeManager)
    {
        if ($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }

        try {
            $like = $deckLikeManager->getLike($deck, $this->getUser());
        } catch (\Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($like);
    }

    /**
     * @Route("/decks/{deckId}/likes")
     * @Method("GET")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function listAction (Deck $deck, DeckLikeManager $deckLikeManager)
    {
        if ($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }

        try {
            $likes = $deckLikeManager->listLikes($deck);
        } catch (\Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($likes, [
            'User',
            'user' => [
                'Default'
            ]
        ]);
    }
}
