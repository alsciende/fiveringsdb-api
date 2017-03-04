<?php

namespace Alsciende\DoctrineSerializerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('AlsciendeDoctrineSerializerBundle:Default:index.html.twig');
    }
}
