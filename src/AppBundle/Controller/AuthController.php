<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Token;
use AppBundle\Service\Metagame;
use AppBundle\Service\TokenManager;
use AppBundle\Service\UserManager;
use JMS\Serializer\ArrayTransformerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @Template("Auth/init.html.twig")
     */
    public function initAction(Request $request)
    {
        $parameters = $this->getParameter('metagame');

        return [
            'uri'   => $parameters['base_uri'] . 'oauth/v2/auth',
            'query' => http_build_query(
                [
                    'client_id'     => $parameters['client_id'],
                    'redirect_uri'  => $parameters['redirect_uri'],
                    'response_type' => 'code',
                    'state'         => $request->cookies->get('PHPSESSID'),
                ]
            ),
        ];
    }

    /**
     * @Route("/code")
     * @Method("GET")
     * @Template("Auth/code.html.twig")
     */
    public function codeAction(
        Request $request,
        Metagame $metagame,
        TokenManager $tokenManager,
        UserManager $userManager,
        ArrayTransformerInterface $arrayTransformer
    ) {
        // check the state
        if ($request->get('state') !== $request->cookies->get('PHPSESSID')) {
            throw new \Exception("State does not match.");
        }

        $tokenData = $metagame->exchangeAuthorizationCode($request->get('code'));
        $token = $tokenManager->createToken($tokenData);

        if ($token instanceof Token) {
            $token->setUser($userManager->findTokenUser($token));
            $tokenManager->updateToken($token);

            return [
                'message' => [
                    'token' => $arrayTransformer->toArray($token),
                    'user'  => $arrayTransformer->toArray($token->getUser()),
                ],
                'origin'  => $this->getParameter('front_url'),
            ];
        }

        throw $this->createAccessDeniedException();
    }

    /**
     * @Route("/refresh")
     * @Method("POST")
     */
    public function refreshAction(Request $request, Metagame $metagame)
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