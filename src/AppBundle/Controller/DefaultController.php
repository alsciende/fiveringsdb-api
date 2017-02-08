<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of DefaultController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DefaultController extends Controller
{

    /**
     * @Route("/test")
     * 
     */
    public function indexAction ()
    {
        return new Response("<h1>Hello World</h1>");
    }

    /**
     * @Route("/profile")
     * 
     */
    public function profileAction ()
    {
        return new Response("<h1>Profile</h1>");
    }

    /**
     * @Route("/deckbuilder")
     * 
     */
    public function deckbuilderAction ()
    {
        return new Response("<h1>Deckbuilder</h1>");
    }

    /**
     * @Route("/api/public/test")
     * 
     */
    public function publicApiTestAction ()
    {
        if($user = $this->getUser()) {
            return new JsonResponse(["success" => true, "user" => $user->getUsername()]);
        } else {
            return new JsonResponse(["success" => true]);
        }
    }

    /**
     * @Route("/api/private/test")
     * 
     */
    public function privateApiTestAction ()
    {
        $user = $this->getUser();
        return new JsonResponse(["success" => true, "user" => $user->getUsername()]);
    }

}
