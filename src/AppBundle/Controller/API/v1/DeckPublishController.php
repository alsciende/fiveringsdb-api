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
 * Description of DeckPublishController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckPublishController extends BaseApiController
{
    /**
     * Create a public deck from an existing deck
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Decks (private)",
     * )
     * @Route("/private_decks/{id}/publish")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request, Deck $parent)
    {
        if($parent->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        
        $data = json_decode($request->getContent(), TRUE);
        
        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $deck = $manager->update($data, $manager->createNewMajorVersion($parent));
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($deck);
    }

}
