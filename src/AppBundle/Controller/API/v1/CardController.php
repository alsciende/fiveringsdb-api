<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\Card;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of CardsController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardController extends BaseApiController
{
    /**
     * Get all Cards
     * 
     * @ApiDoc(
     *  resource=true,
     *  output="AppBundle\Entity\Card",
     *  section="Cards",
     * )
     * @Route("/cards")
     * @Method("GET")
     */
    public function listAction ()
    {
        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(Card::class)
                ->findAll(),
            [
                'Default',
                'packs_group',
                'packCards' => [
                    'Default',
                    'pack_group',
                    'pack' => [
                        'code_group'
                    ]
                ]
            ]
        );
    }
}
