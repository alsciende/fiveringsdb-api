<?php

namespace Alsciende\CerealBundle\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * Description of JsonFileEncoder
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class JsonFileEncoder
{
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    private $serializer;
    
    public function __construct ()
    {
        $this->serializer = new \Symfony\Component\Serializer\Serializer(
            array(new PropertyNormalizer()),
            array(new JsonEncoder())
        );
    }
    
    public function decode($path, $className)
    {
        $parts = explode('\\', $className);
        $file = strtolower(array_pop($parts));
        
        $arrays = [];
        
        if(file_exists("${path}/${file}s.json") and is_file("${path}/${file}s.json")) {
            $arrays = $this->decodeCombinedFile("${path}/${file}s.json");
        } else if(file_exists("${path}/${file}s") and is_dir("${path}/${file}s")) {
            $arrays = $this->decodeDirectory("${path}/${file}s");
        }
        
        return $arrays;
    }
    
    public function decodeDirectory($path)
    {
        $files = glob("$path/*.json");
        $entities = [];
        
        foreach($files as $file) {
            $entities[] = $this->decodeExplodedFile($file);
        }
        
        return $entities;
    }
    
    public function decodeExplodedFile($path)
    {
        $data = file_get_contents($path);
        
        return $this->serializer->decode($data, 'json');
    }
    
    public function decodeCombinedFile($path)
    {
        $data = file_get_contents($path);
        
        return $this->serializer->decode($data, 'json');
    }
    
}
