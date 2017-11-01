<?php

namespace AppBundle\Event;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Deck;
use Symfony\Component\EventDispatcher\Event;

/**
 * Description of ActivityEvent
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CommentAddedEvent extends Event
{
    const NAME = 'comment.added';

    /** @var Comment */
    protected $comment;

    public function __construct (Comment $comment)
    {
        $this->comment = $comment;
    }

    public function getComment (): Comment
    {
        return $this->comment;
    }
}