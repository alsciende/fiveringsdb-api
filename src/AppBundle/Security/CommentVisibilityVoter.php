<?php

namespace AppBundle\Security;

use AppBundle\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVisibilityVoter extends Voter
{
    protected function supports($attribute, $object)
    {
      if ($attribute !== 'COMMENT_VISIBILITY') {
            return false;
        }

        if (!$object instanceof Comment) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $object, TokenInterface $token)
    {
      if ($token->getUser() !== $object->getDeck()->getUser()) {
        return false;
      }

      return true;
    }
}
