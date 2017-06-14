<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Card;
use AppBundle\Entity\Ruling;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of RulingController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class RulingController extends BaseApiController
{

    /**
     * Create a ruling on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Rulings",
     * )
     * @Route("/cards/{card_code}/rulings")
     * @Method("POST")
     * @Security("has_role('ROLE_GURU')")
     * @ParamConverter("card", class="AppBundle:Card", options={"id" = "card_code"})
     */
    public function postAction (Request $request, Card $card)
    {
        $data = json_decode($request->getContent(), TRUE);

        /* @var $manager \AppBundle\Manager\RulingManager */
        $manager = $this->get('app.ruling_manager');
        $ruling = $manager->create($data, $this->getUser(), $card);
        $this->getDoctrine()->getManager()->flush();
        return $this->success($ruling);
    }

    /**
     * Get all rulings on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Rulings",
     * )
     * @Route("/cards/{card_code}/rulings")
     * @Method("GET")
     * @ParamConverter("card", class="AppBundle:Card", options={"id" = "card_code"})
     */
    public function listAction (Card $card)
    {
        /* @var $manager \AppBundle\Manager\RulingManager */
        $manager = $this->get('app.ruling_manager');
        $rulings = $manager->findByCard($card);
        return $this->success($rulings);
    }

    /**
     * Get a ruling on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Rulings",
     * )
     * @Route("/cards/{card_code}/rulings/{id}")
     * @Method("GET")
     */
    public function getAction (Ruling $ruling)
    {
        return $this->success($ruling);
    }

    /**
     * Edit a ruling on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Rulings",
     * )
     * @Route("/cards/{card_code}/rulings/{id}")
     * @Method("PUT")
     * @Security("has_role('ROLE_GURU')")
     */
    public function putAction (Request $request, Ruling $ruling)
    {
        if ($this->getUser() !== $ruling->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $data = json_decode($request->getContent(), TRUE);

        /* @var $manager \AppBundle\Manager\RulingManager */
        $manager = $this->get('app.ruling_manager');
        try {
            $updated = $manager->update($data, $ruling->getId());
        } catch (Exception $ex) {
            return $this->failure($ex->getMessage());
        }

        return $this->success($updated);
    }

}
