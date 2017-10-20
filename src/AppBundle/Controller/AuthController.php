<?php

namespace AppBundle\Controller;

use AppBundle\Security\AccessToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * @Template()
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
     * @Template()
     */
    public function codeAction (Request $request)
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
        $res = $this->get('metagame')->get(
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

        $accessToken = AccessToken::createFromJson($res->getBody());

        return [
            'message' => [
                'type' => 'access_token',
                'access_token' => $accessToken->getAccessToken()
            ],
            'origin' => $this->getParameter('front_url')
        ];
    }
}