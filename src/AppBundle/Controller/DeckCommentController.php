<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Deck;
use AppBundle\Event\CommentAddedEvent;
use AppBundle\Form\Type\CommentType;
use AppBundle\Form\Type\CommentVisibilityType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/decks/{deckId}/comments", name="deck_comments_")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckCommentController extends AbstractApiController
{
    /**
     * Create a comment on a deck
     *
     * @Route("", name="create")
     * @Method("POST")
     * @ParamConverter("deck", options={"id" = "deckId"})
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request, Deck $deck, EventDispatcher $eventDispatcher, EntityManagerInterface $entityManager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->submit(json_decode($request->getContent(), true), true);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser())->setDeck($deck);
            $entityManager->persist($comment);
            $entityManager->flush();

            $eventDispatcher->dispatch(CommentAddedEvent::NAME, new CommentAddedEvent($comment));

            return $this->success($comment);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * List all comments on a deck
     * @Route("", name="list")
     * @Method("GET")
     * @ParamConverter("deck", options={"id" = "deckId"})
     */
    public function listAction (Deck $deck, EntityManagerInterface $entityManager)
    {
        $comments = $entityManager
            ->getRepository(Comment::class)
            ->findBy(['deck' => $deck]);

        return $this->success($comments, [
            'Default',
            'User',
        ]);
    }

    /**
     * Get a comment on a deck
     * @Route("", name="get")
     * @Method("GET")
     */
    public function getAction (Comment $comment)
    {
        return $this->success($comment, [
            'Default',
            'User',
        ]);
    }

    /**
     * Edit a comment on a deck
     *
     * @Route("/{id}", name="patch")
     * @Method("PATCH")
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function patchAction (Request $request, Comment $comment, EntityManagerInterface $entityManager)
    {
        if ($this->getUser() !== $comment->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->success($comment);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * Show/Hide a comment on a deck
     *
     * @Route("/{id}/visibility", name="toggle")
     * @Method("PATCH")
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function visibilityPatchAction (Request $request, Comment $comment, EntityManagerInterface $entityManager)
    {
        if ($this->isGranted('COMMENT_VISIBILITY', $comment) === false) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CommentVisibilityType::class, $comment);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->success($comment);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }
}
