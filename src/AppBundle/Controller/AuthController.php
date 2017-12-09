<?php

namespace AppBundle\Controller;

use AppBundle\Service\Metagame;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * This controller is not part of the API. Responses are HTML pages.
 *
 * @author Alsciende <alsciende@icloud.com>
 * @Route("/auth")
 */
class AuthController extends Controller
{
    /**
     * @Route("/init")
     * @Method("GET")
     * @Template("AppBundle:Auth:init.html.twig")
     */
    public function initAction (Request $request)
    {
        if (!$request->getSession() instanceof SessionInterface) {
            throw $this->createAccessDeniedException();
        }

        $request->getSession()->start();

        $metagameBaseUri = $this->getParameter('metagame_base_uri');

        return [
            'uri'   => $metagameBaseUri . 'oauth/v2/auth',
            'query' => http_build_query(
                [
                    'client_id'     => $this->getParameter('metagame_client_id'),
                    'redirect_uri'  => $this->getParameter('metagame_redirect_uri'),
                    'response_type' => 'code',
                    'state'         => $request->getSession()->getId(),
                ]
            ),
        ];
    }

    /**
     * @Route("/code")
     * @Method("GET")
     * @Template("AppBundle:Auth:code.html.twig")
     */
    public function codeAction (Request $request, Metagame $metagame)
    {
        if (!$request->getSession() instanceof SessionInterface) {
            throw $this->createAccessDeniedException();
        }

        $request->getSession()->start();

        // check the state
        if ($request->get('state') !== $request->getSession()->getId()) {
            throw new \Exception("State does not match.");
        }

        // receive the authorization code
        $code = $request->get('code');

        // request the access-token to the oauth server
        $res = $metagame->get(
            'oauth/v2/token', [
                'client_id'     => $this->getParameter('metagame_client_id'),
                'client_secret' => $this->getParameter('metagame_client_secret'),
                'redirect_uri'  => $this->getParameter('metagame_redirect_uri'),
                'grant_type'    => 'authorization_code',
                'code'          => $code,
            ]
        );
        if ($res->getStatusCode() !== 200) {
            throw new \Exception($res->getReasonPhrase());
        }

        return [
            'message' => json_decode($res->getBody(), true),
            'origin'  => $this->getParameter('front_url'),
        ];
    }

    /**
     * @Route("/refresh")
     * @Method("POST")
     */
    public function refreshAction (Request $request, Metagame $metagame)
    {
        $form = $this->createFormBuilder([])->add('refresh_token', TextType::class)->getForm();
        $form->submit(json_decode($request->getContent(), true), true);

        if ($form->isSubmitted() && $form->isValid()) {
            $refreshToken = $form->getData()['refresh_token'];

            // request the access-token to the oauth server
            $res = $metagame->get(
                'oauth/v2/token', [
                    'client_id'     => $this->getParameter('metagame_client_id'),
                    'client_secret' => $this->getParameter('metagame_client_secret'),
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ]
            );
            if ($res->getStatusCode() !== 200) {
                throw new \Exception($res->getReasonPhrase());
            }

            return new JsonResponse(json_decode($res->getBody(), true));
        }
    }
}