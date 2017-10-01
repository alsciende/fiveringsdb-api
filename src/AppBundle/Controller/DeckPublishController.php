<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Strain;
use AppBundle\Form\Type\DeckType;
use AppBundle\Form\Type\PublicDeckType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of DeckPublishController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckPublishController extends BaseApiController
{
    /**
     * Publish the head of the strain
     * @Route("/strains/{id}/publish")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction (Request $request, Strain $strain)
    {
        if ($strain->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $deck = $strain->getHead();
        if ($deck->isPublished()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PublicDeckType::class, $deck);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app.deck_manager')->publish($strain->getHead());
            $this->getDoctrine()->getManager()->flush();

            return $this->success($deck, [
                'Default',
                'User'
            ]);
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }
}
