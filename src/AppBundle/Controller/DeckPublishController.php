<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deck;
use AppBundle\Entity\Strain;

use AppBundle\Form\Type\PublicDeckType;
use AppBundle\Service\DeckManager;
use AppBundle\Service\DeckValidator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of DeckPublishController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckPublishController extends AbstractApiController
{
    /**
     * Publish the head of the strain
     *
     * @Route("/strains/{id}/publish", name="strain_publish")
     * @Method("PATCH")
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request, Strain $strain, DeckManager $deckManager, EntityManagerInterface $entityManager)
    {
        if ($strain->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $deck = $strain->getHead();
        if (!$deck instanceof Deck) {
            return $this->failure('invalid_deck', 'This strain is empty');
        }
        if ($deck->isPublished()) {
            throw $this->createNotFoundException();
        }
        if ($deck->getProblem() !== DeckValidator::VALID_DECK) {
            return $this->failure('invalid_deck', 'This deck is invalid');
        }

        $form = $this->createForm(PublicDeckType::class, $deck);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $deckManager->publish($deck);
            $entityManager->flush();

            return $this->success($deck, [
                'Default',
                'User',
            ]);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }
}
