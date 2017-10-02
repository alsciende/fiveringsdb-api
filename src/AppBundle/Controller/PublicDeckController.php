<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deck;
use AppBundle\Form\Type\DeckSearchType;
use AppBundle\Form\Type\PublicDeckType;
use AppBundle\Search\DeckSearch;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of PublicDeckController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PublicDeckController extends BaseApiController
{

    /**
     * Get all public decks
     * @Route("/decks")
     * @Method("GET")
     */
    public function listAction (Request $request)
    {
        $this->setPublic($request);

        $search = new DeckSearch();
        $form = $this->createForm(DeckSearchType::class, $search);
        $form->submit($request->query->all(), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app.deck_search')->search($search);

            return $this->success($search, [
                'Default',
                'User',
            ]);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * Get a public deck
     * @Route("/decks/{id}")
     * @Method("GET")
     */
    public function getAction (Request $request, Deck $deck)
    {
        $this->setPublic($request);

        if (!$deck->isPublished()) {
            throw $this->createNotFoundException();
        }

        return $this->success($deck, [
            'Default',
            'Description',
            'Cards',
        ]);
    }

    /**
     * Update a public deck - only name and description can be updated
     * @Route("/decks/{id}")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     */
    public function patchAction (Request $request, Deck $deck)
    {
        if ($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }
        if ($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(PublicDeckType::class, $deck);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->success($deck, [
                'Default',
                'Description',
                'Cards',
            ]);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }

    /**
     * Delete a public deck
     * @Route("/decks/{id}")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction (Deck $deck)
    {
        if ($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }
        if ($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        try {
            $this->get('app.deck_manager')->deleteDeck($deck);
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success();
    }
}
