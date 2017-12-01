<?php

namespace AppBundle\Controller;

use AppBundle\Service\FeatureManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of FeatureController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class FeatureController extends AbstractApiController
{
    /**
     * @Route("/features", name="features_list")
     * @Method("GET")
     */
    public function listAction (Request $request, FeatureManager $featureManager)
    {
        $this->setPublic($request);

        $features = $featureManager->getCurrentFeatures();

        return $this->success($features, [
            'Default',
            'Deck',
            'deck' => [
                'Default',
                'Description',
                'Cards',
                'User',
                'Comments',
            ],
        ]);
    }
}