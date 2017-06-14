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
 * Description of DeckLikeController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckLikeController extends BaseApiController
{
    /**
     * Create a like from a public deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (public)",
     * )
     * @Route("/public_decks/{id}/like")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Deck $deck)
    {
        if(!$deck->getIsPublished()) {
            throw $this->createNotFoundException();
        }
        
        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $nbLikes = $manager->addLike($deck, $this->getUser());
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($nbLikes);
    }

    /**
     * Delete a like from a public deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (public)",
     * )
     * @Route("/public_decks/{id}/like")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction (Deck $deck)
    {
        if(!$deck->getIsPublished()) {
            throw $this->createNotFoundException();
        }
        
        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $nbLikes = $manager->removeLike($deck, $this->getUser());
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($nbLikes);
    }
}
