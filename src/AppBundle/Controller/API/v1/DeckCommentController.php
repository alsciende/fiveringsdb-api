<?php

declare(strict_types=1);

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Deck;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckCommentController extends BaseApiController
{
    /**
     * Create a comment on a deck
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Comments",
     * )
     * @Route("/public-decks/{deckId}/comments")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function postAction (Request $request, Deck $deck)
    {
        $data = json_decode($request->getContent(), TRUE);

        /* @var $manager \AppBundle\Manager\CommentManager */
        $manager = $this->get('app.comment_manager');
        $comment = $manager->create($data, $this->getUser(), $deck);
        $this->getDoctrine()->getManager()->flush();
        return $this->success($comment);
    }

    /**
     * List all comments on a deck
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Comments",
     * )
     * @Route("/public-decks/{deckId}/comments")
     * @Method("GET")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function listAction (Deck $deck)
    {
        /* @var $manager \AppBundle\Manager\CommentManager */
        $manager = $this->get('app.comment_manager');
        $comments = $manager->findByDeck($deck);
        return $this->success($comments);
    }

    /**
     * Get a comment on a deck
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Comments",
     * )
     * @Route("/public-decks/{deckId}/comments/{id}")
     * @Method("GET")
     */
    public function getAction (Comment $comment)
    {
        return $this->success($comment);
    }

    /**
     * Edit a comment on a deck
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Comments",
     * )
     * @Route("/public-decks/{deckId}/comments/{id}")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     */
    public function patchAction (Request $request, Comment $comment)
    {
        if ($this->getUser() !== $comment->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $data = json_decode($request->getContent(), TRUE);

        $updated = $this->get('app.comment_manager')->update($data, $comment);
        $this->getDoctrine()->getManager()->flush();
        return $this->success($updated);
    }
}
