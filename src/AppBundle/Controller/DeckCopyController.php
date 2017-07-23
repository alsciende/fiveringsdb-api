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
     * @Route("/decks/{id}/copy")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Deck $deck)
    {
        /* @var $manager DeckManager */
        $manager = $this->get('app.deck_manager');

        $strain = $manager->createNewStrain($this->getUser());

        $copy = new Deck();
        $copy->setUser($this->getUser())->setStrain($strain);

        $manager->copy($copy, $deck)->persist($copy);

        $this->getDoctrine()->getManager()->flush();

        return $this->success($copy);
    }
}
