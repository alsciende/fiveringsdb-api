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
     * @Route("/public_decks")
     * @Method("GET")
     */
    public function listAction ()
    {
        $decks = $this->getDoctrine()->getRepository(Deck::class)->findBy(['isPublished' => TRUE]);
        return $this->success($decks);
    }

    /**
     * Get a public deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (public)",
     * )
     * @Route("/public_decks/{id}")
     * @Method("GET")
     */
    public function getAction (Deck $deck)
    {
        if(!$deck->getIsPublished()) {
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
     * @Route("/public_decks/{id}")
     * @Method("PUT")
     * @Security("has_role('ROLE_USER')")
     */
    public function putAction (Request $request, Deck $deck)
    {
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $data = json_decode($request->getContent(), TRUE);
        
        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $updated = $manager->update($data, $deck);
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($updated);
    }
    
    /**
     * Delete a public deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (public)",
     * )
     * @Route("/public_decks/{id}")
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
