<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Card;
use AppBundle\Entity\Ruling;
use AppBundle\Form\RulingType;
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
class CardRulingController extends BaseApiController
{

    /**
     * Create a ruling on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Rulings",
     * )
     * @Route("/cards/{cardCode}/rulings")
     * @Method("POST")
     * @Security("has_role('ROLE_GURU')")
     * @ParamConverter("card", class="AppBundle:Card", options={"id" = "cardCode"})
     */
    public function postAction (Request $request, Card $card)
    {
        $ruling = new Ruling();
        $form = $this->createForm(RulingType::class, $ruling);
        $form->submit(json_decode($request->getContent(), true), false);

        if($form->isSubmitted() && $form->isValid()) {
            $ruling->setUser($this->getUser())->setCard($card);
            $this->getDoctrine()->getManager()->persist($ruling);
            $this->getDoctrine()->getManager()->flush();
            return $this->success($ruling);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors()));
    }

    /**
     * Get all rulings on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Rulings",
     * )
     * @Route("/cards/{cardCode}/rulings")
     * @Method("GET")
     * @ParamConverter("card", class="AppBundle:Card", options={"id" = "cardCode"})
     */
    public function listAction (Card $card)
    {
        $rulings = $this
            ->get('doctrine')
            ->getRepository(Ruling::class)
            ->findBy(['card' => $card]);
        return $this->success($rulings);
    }

    /**
     * Get a ruling on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Rulings",
     * )
     * @Route("/cards/{cardCode}/rulings/{id}")
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
     * @Route("/cards/{cardCode}/rulings/{id}")
     * @Method("PATCH")
     * @Security("has_role('ROLE_GURU')")
     */
    public function patchAction (Request $request, Ruling $ruling)
    {
        if ($this->getUser() !== $ruling->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(RulingType::class, $ruling);
        $form->submit(json_decode($request->getContent(), true), false);

        if($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->success($ruling);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors()));
    }

}
