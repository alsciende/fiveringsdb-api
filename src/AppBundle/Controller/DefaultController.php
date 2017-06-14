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
     * @Route("/")
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
        $user = $this->getUser();
        return new Response("<h1>Profile of " . $user->getUsername() . "</h1>");
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
     * @Route("/api/public")
     * 
     */
    public function publicApiTestAction ()
    {
        if ($user = $this->getUser()) {
            return new JsonResponse(["success" => true, "user" => $user->getUsername()]);
        } else {
            return new JsonResponse(["success" => true]);
        }
    }

    /**
     * @Route("/api/private")
     * @Security("has_role('ROLE_USER')")
     * 
     */
    public function privateApiTestAction ()
    {
        $user = $this->getUser();
        return new JsonResponse(["success" => true, "user" => $user->getUsername()]);
    }

}
