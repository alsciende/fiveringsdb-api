<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of ApiController
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class BaseApiController extends Controller
{
    public function success ($data = null, $groups = ['Default'])
    {
        return $this->get('app.api')->buildResponse($data, $groups);
    }

    public function failure ($message = "unknown_error", $description = "An unknown error has occured.")
    {
        $this->get('logger')->info($message);

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
                "property_path" => $error->getCause()->getPropertyPath(),
                "invalid_value" => $error->getCause()->getInvalidValue(),
                "error_message" => $error->getMessage(),
            ];
        }

        return $messages;
    }

    public function setPublic(Request $request, bool $public = true)
    {
        $this->get('app.api')->setPublic($request, $public);
    }
}
