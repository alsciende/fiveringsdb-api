<?php

namespace AppBundle\Service;

use AppBundle\Search\PaginatedSearchInterface;
use AppBundle\Search\SearchInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

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

    public function __construct (RequestStack $requestStack, Serializer $serializer, $httpCacheMaxAge)
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
            $response->setPublic();
            $response->setMaxAge($this->httpCacheMaxAge);
        }

        $content = $this->buildContent($data);
        $content['success'] = true;

        $serialized = $this->serializer->serialize($content, 'json', SerializationContext::create()->setGroups($groups));

        $response->setContent($serialized);

        return $response;
    }

    public function buildContent ($data = null)
    {
        $content = [];
        $content['rrg-version'] = '10';
        if ($data instanceof SearchInterface) {
            $content['records'] = $data->getRecords();
            $content['size'] = $data->getTotal();
            if ($data instanceof PaginatedSearchInterface) {
                $content['page'] = $data->getPage();
                $content['limit'] = $data->getLimit();
            }
        } else if (is_array($data)) {
            $content['records'] = $data;
            $content['size'] = count($content['records']);
        } else {
            $content['record'] = $data;
        }

        return $content;
    }

    public function getEmptyResponse ()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');

        return $response;
    }

    public function isPublic (Request $request = null): bool
    {
        return $request instanceof Request && $request->attributes->get('public') ?? false;
    }

    public function setPublic (Request $request, bool $public = true)
    {
        $request->attributes->set('public', $public);
    }
}
