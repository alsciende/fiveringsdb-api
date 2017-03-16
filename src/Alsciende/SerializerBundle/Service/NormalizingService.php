<?php

namespace Alsciende\SerializerBundle\Service;

/**
 * Turns an object into an array
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class NormalizingService implements NormalizingServiceInterface
{

    public function __construct (\Symfony\Component\Serializer\Serializer $serializer, $group)
    {
        $this->serializer = $serializer;
        $this->group = $group;
    }

    /**
     *
     * @var \Symfony\Component\Serializer\Serializer
     */
    private $serializer;

    /**
     *
     * @var string
     */
    private $group;

    public function normalize ($object)
    {
        $context = [];
        if(!empty($this->group)) {
            $context['groups'] = array($this->group);
        }
        $data = $this->serializer->normalize($object, null, $context);

        return $data;
    }

    public function denormalize ($data, $type)
    {
        $context = [];
        if(isset($this->group)) {
            $context['groups'] = array($this->group);
        }
        $object = $this->serializer->denormalize($data, $type, null, $context);

        return $object;
    }

}
