<?php

namespace AppBundle\Behavior\Controller;

use AppBundle\Service\ApiService;

interface ApiControllerInterface
{
    public function setApiService(ApiService $apiService);
}
