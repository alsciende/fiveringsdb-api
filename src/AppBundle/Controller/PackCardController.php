<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PackCard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of PackCardController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackCardController extends BaseApiController
{
    /**
     * Get all pairs Pack-Card
     * @Route("/pack-cards")
     * @Method("GET")
     */
    public function listAction (Request $request)
    {
        $this->setPublic($request);

        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(PackCard::class)
                ->findAll(),
            [
                'Default',
                'Card',
                'card' => [
                    'Id',
                ],
                'Pack',
                'pack' => [
                    'Id',
                ],
            ]
        );
    }

}
