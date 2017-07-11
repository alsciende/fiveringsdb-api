<?php

declare(strict_types=1);

namespace Tests\AppBundle\Controller;

/**
 * Description of BaseControllerTest
 *
 * @author Cédric Bertolini <cedric.bertolini@proximedia.fr>
 */
abstract class BaseControllerTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{

    /**
     * 
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    public function getAuthenticatedClient ($username = 'user', $password = 'user')
    {
        return static::createClient(array(), array(
                    'PHP_AUTH_USER' => $username,
                    'PHP_AUTH_PW' => $password,
        ));
    }

    /**
     * 
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    public function getAnonymousClient ()
    {
        return static::createClient();
    }

}
