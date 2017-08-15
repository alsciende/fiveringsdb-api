<?php

namespace AppBundle\Security;

use AppBundle\Entity\Deck;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReadDeckVoter extends Voter
{
    /**
     * @param string $attribute
     * @param mixed $object
     * @return bool
     */
    protected function supports($attribute, $object)
    {
        if ($attribute !== 'READ_DECK') {
            return false;
        }

        if (!$object instanceof Deck) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param Deck $object
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $object, TokenInterface $token)
    {
        if ($token->getUser() === $object->getUser()) {
            return true;
        }

        if ($object->isPublished()) {
            return true;
        }

        return false;
    }
}
