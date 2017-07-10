<?php

declare(strict_types=1);

namespace AppBundle\Manager;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Deck;
use AppBundle\Entity\User;

class CommentManager extends BaseManager
{
    public function create (array $data, User $user, Deck $deck): Comment
    {
        /** @var Comment $comment */
        $comment = $this->serializer->denormalize($data, Comment::class);
        $comment->setUser($user);
        $comment->setDeck($deck);
        $this->entityManager->persist($comment);
        return $comment;
    }

    public function update (array $data, Comment $comment): Comment
    {
        if(isset($data['text'])) {
            $comment->setText($data['text']);
        }
        if(isset($data['is_visible'])) {
            $comment->setIsVisible($data['is_visible']);
        }

        return $this->entityManager->merge($comment);
    }

    public function findByDeck (Deck $deck)
    {
        return $this->entityManager->getRepository(Comment::class)->findBy(['deck' => $deck]);
    }
}
