<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Card;
use AppBundle\Entity\Review;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of ReviewController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ReviewController extends BaseApiController
{

    /**
     * Create a review on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Reviews",
     * )
     * @Route("/cards/{card_code}/reviews")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("card", class="AppBundle:Card", options={"id" = "card_code"})
     */
    public function postAction (Request $request, Card $card)
    {
        $data = json_decode($request->getContent(), TRUE);
        
        /* @var $manager \AppBundle\Manager\ReviewManager */
        $manager = $this->get('app.review_manager');
        $review = $manager->create($data, $this->getUser(), $card);
        $this->getDoctrine()->getManager()->flush();
        return $this->success($review);
    }

    /**
     * List all reviews on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Reviews",
     * )
     * @Route("/cards/{card_code}/reviews")
     * @Method("GET")
     * @ParamConverter("card", class="AppBundle:Card", options={"id" = "card_code"})
     */
    public function listAction (Card $card)
    {
        /* @var $manager \AppBundle\Manager\ReviewManager */
        $manager = $this->get('app.review_manager');
        $reviews = $manager->findByCard($card);
        return $this->success($reviews);
    }

    /**
     * Get a review on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Reviews",
     * )
     * @Route("/cards/{card_code}/reviews/{id}")
     * @Method("GET")
     */
    public function getAction (Review $review)
    {
        return $this->success($review);
    }

    /**
     * Edit a review on a card
     * 
     * @ApiDoc(
     *  resource=true,
     *  section="Reviews",
     * )
     * @Route("/cards/{card_code}/reviews/{id}")
     * @Method("PUT")
     * @Security("has_role('ROLE_USER')")
     */
    public function putAction (Request $request, Review $review)
    {
        if ($this->getUser() !== $review->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $data = json_decode($request->getContent(), TRUE);
        
        /* @var $manager \AppBundle\Manager\ReviewManager */
        $manager = $this->get('app.review_manager');
        $updated = $manager->update($data, $review->getId());
        return $this->success($updated);
    }

}
