<?php

namespace AppBundle\Security;

use AppBundle\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVisibilityVoter extends Voter
{
    /**
     * @param string $attribute
     * @param mixed  $object
     * @return bool
     */
    protected function supports ($attribute, $object)
    {
        if ($attribute !== 'COMMENT_VISIBILITY') {
            return false;
        }

        if (!$object instanceof Comment) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param Comment        $object
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute ($attribute, $object, TokenInterface $token)
    {
        if ($token->getUser() === $object->getDeck()->getUser()) {
            return true;
        }

        if ($token->getUser()->hasRole('ROLE_MODERATOR')) {
            return true;
        }

        return false;
    }
}
