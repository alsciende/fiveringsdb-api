<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deck;
use AppBundle\Entity\Strain;
use AppBundle\Form\Type\DeckType;
use AppBundle\Service\DeckManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class StrainDeckController extends AbstractApiController
{
    /**
     * Create a minor version
     * @Route("/strains/{id}/decks")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request, Strain $strain, DeckManager $deckManager, EntityManagerInterface $entityManager)
    {
        if ($strain->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $deck = new Deck();
        $form = $this->createForm(DeckType::class, $deck);
        $form->submit(json_decode($request->getContent(), true), true);

        if ($form->isSubmitted() && $form->isValid()) {
            $deck->setUser($this->getUser())->setStrain($strain);
            $deckManager->persist($deck);
            $entityManager->flush();

            return $this->success($deck, [
                'Default',
                'Cards',
                'Strain',
            ]);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * Get all private versions of a deck
     * @Route("/strains/{id}/decks")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction (Strain $strain, EntityManagerInterface $entityManager)
    {
        if ($strain->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $decks = $entityManager
            ->getRepository(Deck::class)
            ->findBy(['strain' => $strain], ['createdAt' => 'ASC']);

        return $this->success($decks, [
            'Default',
            'Cards'
        ]);
    }

    /**
     * Get a private deck
     * @Route("/strains/{strainId}/decks/{id}")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function getAction (Deck $deck)
    {
        if ($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->success($deck, [
            'Default',
            'Description',
            'Cards',
        ]);
    }

    /**
     * Delete a private deck. Other versions are untouched.
     * @Route("/strains/{strainId}/decks/{id}")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction (Deck $deck, DeckManager $deckManager)
    {
        if ($deck->isPublished()) {
            throw $this->createNotFoundException();
        }
        if ($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        try {
            $deckManager->deleteDeck($deck);
        } catch (\Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success();
    }
}
