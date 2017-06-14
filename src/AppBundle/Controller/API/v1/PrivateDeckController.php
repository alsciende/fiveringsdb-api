<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Manager\DeckManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Private decks
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PrivateDeckController extends BaseApiController
{

    /**
     * Create a deck in initial version (0.1)
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (private)",
     * )
     * @Route("/private_decks")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request)
    {
        $data = json_decode($request->getContent(), TRUE);
        
        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $deck = $manager->createNewInitialDeck($data, $this->getUser());
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }
        return $this->success($deck);
    }

    /**
     * Get all private decks
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (private)",
     * )
     * @Route("/private_decks")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction ()
    {
        $decks = $this->getDoctrine()->getRepository(Deck::class)->findBy(['user' => $this->getUser()]);
        return $this->success($decks);
    }

    /**
     * Get a private deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (private)",
     * )
     * @Route("/private_decks/{id}")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function getAction (Deck $deck)
    {
        if($deck->getIsPublished()) {
            throw $this->createNotFoundException();
        }
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        return $this->success($deck);
    }

    /**
     * Delete a private deck. Other versions are untouched.
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (private)",
     * )
     * @Route("/private_decks/{id}")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction (Deck $deck)
    {
        if($deck->getIsPublished()) {
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
