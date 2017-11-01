<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of FeatureController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class FeatureController extends AbstractController
{
    /**
     * @Route("/features")
     * @Method("GET")
     */
    public function listAction (Request $request)
    {
        $this->setPublic($request);

        $features = $this->get('app.feature_manager')->getCurrentFeatures();

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