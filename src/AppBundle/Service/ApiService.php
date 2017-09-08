<?php

namespace AppBundle\Service;

use JMS\Serializer\SerializationContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Description of ApiService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ApiService
{

    /**
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     *
     * @var \JMS\Serializer\Serializer
     */
    private $serializer;

    /**
     *
     * @var integer
     */
    private $httpCacheMaxAge;

    public function __construct (RequestStack $requestStack, \JMS\Serializer\Serializer $serializer, $httpCacheMaxAge)
    {
        $this->requestStack = $requestStack;
        $this->serializer = $serializer;
        $this->httpCacheMaxAge = $httpCacheMaxAge;
    }

    public function buildResponse ($data = null, $groups = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        $response = $this->getEmptyResponse();

        if ($this->isPublic($request)) {
            // make response public and cacheable
            $response->setPublic();
            $response->setMaxAge($this->httpCacheMaxAge);
            // find last update of data
            $dateUpdate = $this->getDateUpdate($data);
            $response->setLastModified($dateUpdate);
            // compare to request header
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        $content = $this->buildContent($data, $groups);
        $content['success'] = true;
        $content['last_updated'] = isset($dateUpdate) ? $dateUpdate->format('c') : null;

        $serialized = $this->serializer->serialize($content, 'json', SerializationContext::create()->setGroups($groups));

        $response->setContent($serialized);

        return $response;
    }

    public function buildContent ($data = null, $groups = [])
    {
        $content = [];
        if (is_array($data)) {
            $content['records'] = $data;
            $content['size'] = count($content['records']);
        } else {
            $content['record'] = $data;
        }

        return $content;
    }

    public function getDateUpdate ($data)
    {
        if (is_array($data) === false) {
            $data = [$data];
        }

        return array_reduce(
            $data, function ($carry, $item) {
            if ($carry && $item->getUpdatedAt() < $carry) {
                return $carry;
            } else {
                return $item->getUpdatedAt();
            }
        }
        );
    }

    public function getEmptyResponse ()
    {
        $response = new Response();
//        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');

        return $response;
    }

    public function isPublic(Request $request): bool
    {
        return $request->attributes->get('public') ?? false;
    }

    public function setPublic(Request $request, bool $public = true)
    {
        $request->attributes->set('public', $public);
    }
}
