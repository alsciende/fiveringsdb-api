<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Card;
use AppBundle\Entity\Ruling;
use AppBundle\Form\Type\RulingType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of RulingController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardRulingController extends AbstractController
{

    /**
     * Create a ruling on a card
     * @Route("/cards/{id}/rulings", name="createCardRuling")
     * @Method("POST")
     * @Security("has_role('ROLE_GURU')")
     */
    public function postAction (Request $request, Card $card, EntityManagerInterface $entityManager)
    {
        $ruling = new Ruling();
        $form = $this->createForm(RulingType::class, $ruling);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $ruling->setUser($this->getUser())->setCard($card);
            $entityManager->persist($ruling);
            $entityManager->flush();

            return $this->success($ruling);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * Get all rulings on a card
     * @Route("/cards/{id}/rulings", name="listCardRulings")
     * @Method("GET")
     */
    public function listAction (Card $card, EntityManagerInterface $entityManager)
    {
        $rulings = $entityManager
            ->getRepository(Ruling::class)
            ->findBy(['card' => $card]);

        return $this->success($rulings);
    }

    /**
     * Get a ruling on a card
     * @Route("/cards/{cardId}/rulings/{id}", name="getCardRuling")
     * @Method("GET")
     */
    public function getAction (Ruling $ruling)
    {
        return $this->success($ruling);
    }

    /**
     * Delete a ruling on a card
     * @Route("/cards/{cardId}/rulings/{id}", name="deleteCardRuling")
     * @Method("DELETE")
     * @Security("has_role('ROLE_GURU')")
     */
    public function deleteAction (Ruling $ruling, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($ruling);
        $entityManager->flush();

        return $this->success();
    }

    /**
     * Edit a ruling on a card
     * @Route("/cards/{cardId}/rulings/{id}", name="patchCardRuling")
     * @Method("PATCH")
     * @Security("has_role('ROLE_GURU')")
     */
    public function patchAction (Request $request, Ruling $ruling, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(RulingType::class, $ruling);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->success($ruling);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

}
