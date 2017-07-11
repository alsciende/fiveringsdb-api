<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Form\DeckType;
use AppBundle\Manager\DeckManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of PublicDeckController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PublicDeckController extends BaseApiController
{
    
    /**
     * Get all public decks
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (public)",
     * )
     * @Route("/public-decks")
     * @Method("GET")
     */
    public function listAction ()
    {
        $decks = $this->getDoctrine()->getRepository(Deck::class)->findBy(['published' => TRUE]);
        return $this->success($decks);
    }

    /**
     * Get a public deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (public)",
     * )
     * @Route("/public-decks/{id}")
     * @Method("GET")
     */
    public function getAction (Deck $deck)
    {
        if(!$deck->isPublished()) {
            throw $this->createNotFoundException();
        }
        return $this->success($deck);
    }

    /**
     * Update a public deck - only name and description can be updated
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (public)",
     * )
     * @Route("/public-decks/{id}")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     */
    public function patchAction (Request $request, Deck $deck)
    {
        if($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(DeckType::class, $deck);
        $form->submit(json_decode($request->getContent(), true), false);

        if($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->success($deck);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors()));
    }
    
    /**
     * Delete a public deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (public)",
     * )
     * @Route("/public-decks/{id}")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction (Deck $deck)
    {
        if($deck->isPublished() === false) {
            throw $this->createNotFoundException();
        }
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $manager->deleteDeck($deck);
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }
        return $this->success();
    }
}
