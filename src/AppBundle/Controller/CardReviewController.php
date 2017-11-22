<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Card;
use AppBundle\Entity\Review;
use AppBundle\Form\Type\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of ReviewController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardReviewController extends AbstractController
{
    /**
     * Create a review on a card
     * @Route("/cards/{id}/reviews", name="createCardReview")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request, Card $card, EntityManagerInterface $entityManager)
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->submit(json_decode($request->getContent(), true), true);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setUser($this->getUser())->setCard($card);
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->success($review);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * List all reviews on a card
     * @Route("/cards/{id}/reviews", name="listCardReviews")
     * @Method("GET")
     */
    public function listAction (Card $card, EntityManagerInterface $entityManager)
    {
        $reviews = $entityManager
            ->getRepository(Review::class)
            ->findBy(['card' => $card]);

        return $this->success($reviews);
    }

    /**
     * Get a review on a card
     * @Route("/cards/{cardId}/reviews/{id}", name="getCardReview")
     * @Method("GET")
     */
    public function getAction (Review $review)
    {
        return $this->success($review);
    }

    /**
     * Edit a review on a card
     * @Route("/cards/{cardId}/reviews/{id}", name="patchCardReview")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     */
    public function patchAction (Request $request, Review $review, EntityManagerInterface $entityManager)
    {
        if ($this->getUser() !== $review->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ReviewType::class, $review);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->success($review);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }
}
