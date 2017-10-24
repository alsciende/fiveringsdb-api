<?php

namespace AppBundle\Entity;
use AppBundle\Behavior\Entity\SequentialIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Notification
 *
 * @ORM\Entity()
 * @ORM\Table(name="notifications")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Notification
{
    use SequentialIdTrait;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var Activity
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="notifications")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id", nullable=false)
     */
    protected $activity;

    public function __construct (User $user, Activity $activity)
    {
        $this->user = $user;
        $this->activity = $activity;
    }

    public function getActivity(): Activity
    {
        return $this->activity;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}