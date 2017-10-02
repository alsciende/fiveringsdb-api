<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 02/10/17
 * Time: 11:06
 */

namespace AppBundle\Behavior\Entity;


interface Timestampable
{
    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
