<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Token;
use AppBundle\Form\Type\TokenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of TokenController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class TokenController extends BaseApiController
{
    /**
     * Uses the token to get the user data with Metagame, then saves it
     * @Route("/tokens")
     * @Method("POST")
     */
    public function postAction (Request $request)
    {
        $token = new Token();
        $form = $this->createForm(TokenType::class, $token);
        $form->submit(json_decode($request->getContent(), true), true);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getDoctrine()->getRepository(Token::class)->find($token->getId()) instanceof Token) {
                return $this->success($token);
            }

            $res = $this->get('metagame')->get('api/users/me', [], $token->getId());
            if ($res->getStatusCode() !== 200) {
                return $this->failure('token_error', (string)$res->getBody());
            }

            $manager = $this->get('app.security.user_manager');

            $data = json_decode((string)$res->getBody(), true);
            $user = $manager->findUserByUsername($data['username']);
            if ($user === null) {
                $user = $manager->createUser($data['username']);
                $user->setId($data['id']);
                $manager->updateUser($user);
            }

            $token->setUser($user);
            $this->getDoctrine()->getManager()->persist($token);
            $this->getDoctrine()->getManager()->flush();

            return $this->success(
                $token,
                [
                    'Default',
                    'user_group',
                    'user' => [
                        'Default',
                    ],
                ]
            );
        }

        return $this->failure('validation_error', $this->formatValidationErrors($form->getErrors(true)));
    }
}