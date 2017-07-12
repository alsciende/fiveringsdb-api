<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseApiController;
use AppBundle\Entity\Deck;
use AppBundle\Manager\DeckManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Description of DeckCopyController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckCopyController extends BaseApiController
{
    /**
     * Create a private deck in initial version (0.1) from an existing deck
     * @Route("/private-decks/{deckId}/copy")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("parent", class="AppBundle:Deck", options={"id" = "deckId"})
     */
    public function postAction (Deck $parent)
    {
        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');
        try {
            $copy = $manager->createNewCopy($parent, $this->getUser());
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($copy);
    }

}
