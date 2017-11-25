<?php

namespace AppBundle\Controller;

use AppBundle\Behavior\Controller\ApiControllerInterface;
use AppBundle\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of ApiController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class AbstractApiController extends Controller implements ApiControllerInterface
{
    /** @var ApiService $apiService */
    private $apiService;

    public function setApiService (ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function success ($data = null, $groups = ['Default'])
    {
        return $this->apiService->buildResponse($data, $groups);
    }

    public function failure ($message = "unknown_error", $description = "An unknown error has occurred.")
    {
        return new JsonResponse(
            [
                "success"     => false,
                "message"     => $message,
                "description" => $description,
            ]
        );
    }

    public function formatValidationErrors (FormErrorIterator $errors)
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = [
                "property_path" => $error->getCause() ? $error->getCause()->getPropertyPath() : null,
                "invalid_value" => $error->getCause() ? $error->getCause()->getInvalidValue() : null,
                "error_message" => $error->getMessage(),
            ];
        }

        return $messages;
    }

    public function setPublic (Request $request, bool $public = true)
    {
        $this->apiService->setPublic($request, $public);
    }
}
