<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deck;
use AppBundle\Entity\Strain;
use AppBundle\Form\Type\StrainType;
use AppBundle\Service\DeckManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/strains", name="strains_")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class StrainController extends AbstractApiController
{
    /**
     * Create a strain
     * If 'origin' is set in the request body,
     * find the deck whose id 'origin' is
     * and create a first deck in the strain as a copy of that deck
     *
     * @Route("", name="create")
     * @Method("POST")
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request, DeckManager $deckManager, EntityManagerInterface $entityManager)
    {
        $count = $deckManager->countStrains($this->getUser());
        if ($count >= 100) {
            return $this->failure('quota_error', "Quota reached");
        }

        $strain = new Strain($this->getUser());
        $form = $this->createForm(StrainType::class, $strain);
        $form->submit(json_decode($request->getContent(), true), true);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($strain);
            if ($strain->getOrigin() !== null) {
                $origin = $entityManager->getRepository(Deck::class)->find($strain->getOrigin());
                if ($origin instanceof Deck) {
                    $copy = new Deck();
                    $copy->setUser($this->getUser())->setStrain($strain);
                    $deckManager->copy($copy, $origin)->persist($copy);
                }
            }
            $entityManager->flush();

            return $this->success(
                $strain, [
                    'Default',
                    'Head',
                    'head' => [
                        'Default',
                        'Cards',
                    ],
                ]
            );
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * Get all strains
     *
     * @Route("", name="list")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction (EntityManagerInterface $entityManager)
    {
        $strains = $entityManager
            ->getRepository(Strain::class)
            ->findBy(['user' => $this->getUser()], ['updatedAt' => 'DESC']);

        return $this->success(
            $strains, [
                'Default',
                'Head',
                'head' => [
                    'Default',
                    'Cards',
                ],
            ]
        );
    }

    /**
     * Get a strain.
     *
     * @Route("/{id}", name="get")
     * @Method("GET")
     */
    public function getAction (Strain $strain)
    {
        return $this->success(
            $strain, [
                'Default',
                'Head',
                'head' => [
                    'Default',
                    'Description',
                    'Cards',
                ],
            ]
        );
    }

    /**
     * Delete a strain.
     * All its decks are deleted as well.
     * Published (major) decks won't have a strain.
     *
     * @Route("/{id}", name="delete")
     * @Method("DELETE")
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction (Strain $strain, EntityManagerInterface $entityManager)
    {
        if ($strain->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        foreach ($strain->getDecks() as $deck) {
            if ($deck->isPublished()) {
                $deck->setStrain(null);
            }
        }

        $strain->clearHead();
        $entityManager->flush();
        $entityManager->remove($strain);
        $entityManager->flush();

        return $this->success();
    }
}
