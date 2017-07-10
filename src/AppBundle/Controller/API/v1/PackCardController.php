<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Controller\API\BaseApiController;
use AppBundle\Entity\PackCard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Description of PackCardController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackCardController extends BaseApiController
{
    /**
     * Get all pairs Pack-Card
     * 
     * @ApiDoc(
     *  resource=true,
     *  output="AppBundle\Entity\PackCard",
     *  section="Cards",
     * )
     * @Route("/pack-cards")
     * @Method("GET")
     */
    public function listAction ()
    {
        return $this->success(
            $this
                ->getDoctrine()
                ->getRepository(PackCard::class)
                ->findAll(),
            [
                'Default',
                'card_group',
                'pack_group',
                'card' => [
                    'code_group'
                ],
                'pack' => [
                    'code_group'
                ],
            ]
        );
    }

}
