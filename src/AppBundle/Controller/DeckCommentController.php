<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Deck;
use AppBundle\Form\Type\CommentType;
use AppBundle\Form\Type\CommentVisibilityType;
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
     * @Route("/decks/{deckId}/comments")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function postAction (Request $request, Deck $deck)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->submit(json_decode($request->getContent(), true), false);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser())->setDeck($deck);
            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();
            return $this->success($comment);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors()));
    }

    /**
     * List all comments on a deck
     * @Route("/decks/{deckId}/comments")
     * @Method("GET")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function listAction (Deck $deck)
    {
        $comments = $this
            ->get('doctrine')
            ->getRepository(Comment::class)
            ->findBy(['deck' => $deck]);
        return $this->success($comments);
    }

    /**
     * Get a comment on a deck
     * @Route("/decks/{deckId}/comments/{id}")
     * @Method("GET")
     */
    public function getAction (Comment $comment)
    {
        return $this->success($comment);
    }

    /**
     * Edit a comment on a deck
     * @Route("/decks/{deckId}/comments/{id}")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     */
    public function patchAction (Request $request, Comment $comment)
    {
        if ($this->getUser() !== $comment->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->submit(json_decode($request->getContent(), true), false);

        if($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->success($comment);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors()));
    }

    /**
     * Show/Hide a comment on a deck
     * @Route("/decks/{deckId}/comments/{id}/visibility")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     */
    public function visibilityPatchAction (Request $request, Comment $comment)
    {
      if($this->isGranted('COMMENT_VISIBILITY', $comment) === false) {
        throw $this->createAccessDeniedException();
      }
        

        $form = $this->createForm(CommentVisibilityType::class, $comment);
        $form->submit(json_decode($request->getContent(), true), false);

        if($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->success($comment);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors()));
    }
}
