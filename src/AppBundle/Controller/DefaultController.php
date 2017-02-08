<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Security("has_role('ROLE_USER')")
     * 
     */
    public function profileAction ()
    {
        return new Response("<h1>Profile</h1>");
    }

    /**
     * @Route("/deckbuilder")
     * @Security("has_role('ROLE_USER')")
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
     * @Security("has_role('ROLE_USER')")
     * 
     */
    public function privateApiTestAction ()
    {
        $user = $this->getUser();
        return new JsonResponse(["success" => true, "user" => $user->getUsername()]);
    }

}
