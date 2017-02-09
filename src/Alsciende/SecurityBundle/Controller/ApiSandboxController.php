<?php

namespace Alsciende\SecurityBundle\Controller;

use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Connects a user via oauth2 - Sandbox
 *
 * @author Alsciende <alsciende@icloud.com>
 * 
 * @Route("/api_sandbox")
 */
class ApiSandboxController extends Controller
{

    /**
     * Display the API explorer
     * @Route("/explorer", name="api_sandbox_explorer")
     * @Method("GET")
     * @Template
     */
    public function explorerAction (Request $request)
    {
        // we check if we have an access-token in session
        $session = $request->getSession();
        if(!$session->has('api_sandbox_token_response')) {
            // no token, we redirect to a login page
            return $this->redirectToRoute('api_sandbox_initiate');
        }

        $oauthTokenResponse = $session->get('api_sandbox_token_response');

        return [
            'token' => $oauthTokenResponse
        ];
    }

    /**
     * Display a page with "Connect to FiveRingsDB" button
     * @param Request $request
     * @Route("/initiate", name="api_sandbox_initiate")
     * @Method("GET")
     * @Template
     */
    public function initiateAction ()
    {
        return [
            'client_id' => $this->getParameter('oauth_test_client_id'),
            'redirect_uri' => $this->getParameter('oauth_test_redirect_uri')
        ];
    }

    /**
     * Receive the authorization code and request an access token
     * @param Request $request
     * @Route("/callback", name="api_sandbox_callback")
     * @Method("GET")
     */
    public function callbackAction (Request $request)
    {
        // receive the aothorization code
        $code = $request->get('code');

        // request the access-token to the oauth server
        $url = $this->get('router')->generate('fos_oauth_server_token', [
            'client_id' => $this->getParameter('oauth_test_client_id'),
            'client_secret' => $this->getParameter('oauth_test_client_secret'),
            'redirect_uri' => $this->getParameter('oauth_test_redirect_uri'),
            'grant_type' => 'authorization_code',
            'code' => $code
                ], UrlGenerator::ABSOLUTE_URL);

        $client = new Client();
        $res = $client->request('GET', $url);
        if($res->getStatusCode() !== 200) {
            throw new \Exception($res->getReasonPhrase());
        }

        // process the response
        $response = json_decode($res->getBody(), TRUE);
        $now = new \DateTime();
        $response['creation_date'] = $now->format('c');
        $now->add(\DateInterval::createFromDateString($response['expires_in'] . ' seconds'));
        $response['expiration_date'] = $now->format('c');

        // store the response
        $request->getSession()->set('api_sandbox_token_response', $response);

        // redirect to the explorer
        return $this->redirectToRoute('api_sandbox_explorer');
    }

}
