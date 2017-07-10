<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Manager\DeckManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of DeckLineageController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckLineageController extends BaseApiController
{
    
    /**
     * Create a minor version
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (private)",
     * )
     * @Route("/private-decks/{deckId}/lineage")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("parent", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function postAction (Request $request, Deck $parent)
    {
        $data = json_decode($request->getContent(), TRUE);
        /** @var Deck $deck */
        $deck = $this->get('jms_serializer')->fromArray($data, Deck::class);

        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $minor = $manager->createNewMinorVersion($deck, $parent);
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($minor);
    }

    /**
     * Get all private versions of a deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (private)",
     * )
     * @Route("/private-decks/{deckId}/lineage")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function listAction (Deck $deck)
    {
        /* @var $repository \AppBundle\Repository\DeckRepository */
        $repository = $this->getDoctrine()->getRepository(Deck::class);
        $decks = $repository->findByLineage($deck->getLineage(), $deck->getUser());
        return $this->success($decks);
    }

    /**
     * Delete a lineage: all private versions of a deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (private)",
     * )
     * @Route("/private-decks/{deckId}/lineage")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("deck", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function deleteAction (Deck $deck)
    {
        if($deck->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $manager->deleteLineage($deck);
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }
        
        return $this->success();
    }
}
