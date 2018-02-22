<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 21/02/18
 * Time: 17:36
 */

namespace AppBundle\Behavior\Service;

interface OauthServiceInterface
{
    public function getUserData(string $credentials): ?array;
}
