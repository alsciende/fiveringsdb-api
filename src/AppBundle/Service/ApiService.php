<?php

namespace AppBundle\Service;

use JMS\Serializer\SerializationContext;
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

    /**
     *
     * @var string
     */
    private $kernelEnvironment;
    
    function __construct (RequestStack $requestStack, \JMS\Serializer\Serializer $serializer, $httpCacheMaxAge)
    {
        $this->requestStack = $requestStack;
        $this->serializer = $serializer;
        $this->httpCacheMaxAge = $httpCacheMaxAge;
    }

    function buildResponse ($data = null, $groups = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        $isPublic = $request->getMethod() === 'GET';
        $response = $this->getEmptyResponse();

        if($isPublic) {
            // make response public and cacheable
            $response->setPublic();
            $response->setMaxAge($this->httpCacheMaxAge);
            // find last update of data
            $dateUpdate = $this->getDateUpdate($data);
            $response->setLastModified($dateUpdate);
            // compare to request header
            if($response->isNotModified($request)) {
                return $response;
            }
        }

        $content = $this->buildContent($data, $groups);
        $content['success'] = TRUE;
        $content['last_updated'] = isset($dateUpdate) ? $dateUpdate->format('c') : null;

        $serialized = $this->serializer->serialize($content,'json', SerializationContext::create()->setGroups($groups));

        $response->setContent($serialized);

        return $response;
    }

    function buildContent ($data = null, $groups = [])
    {
        $content = [];
        if(is_array($data)) {
            $content['records'] = $data;
            $content['size'] = count($content['records']);
        } else {
            $content['record'] = $data;
        }

        return $content;
    }

    function getDateUpdate ($data)
    {
        if(is_array($data) === false) {
            $data = array($data);
        }
        return array_reduce($data, function($carry, $item) {
            if($carry && $item->getUpdatedAt() < $carry) {
                return $carry;
            } else {
                return $item->getUpdatedAt();
            }
        });
    }

    function getEmptyResponse ()
    {
        $response = new Response();
//        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        return $response;
    }
}
