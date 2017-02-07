<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Description of DefaultController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DefaultController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{

    /**
     * @Route("/")
     * 
     */
    public function indexAction ()
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/profile")
     * 
     */
    public function profileAction ()
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/api/public/test")
     * 
     */
    public function publicApiTestAction ()
    {
        if($user = $this->getUser()) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(["success" => true, "user" => $user->getUsername()]);
        } else {
            return new \Symfony\Component\HttpFoundation\JsonResponse(["success" => true]);
        }
    }

    /**
     * @Route("/api/private/test")
     * 
     */
    public function privateApiTestAction ()
    {
        $user = $this->getUser();
        return new \Symfony\Component\HttpFoundation\JsonResponse(["success" => true, "user" => $user->getUsername()]);
    }

}
