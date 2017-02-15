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
    
    /**
     * 
     * @param type $path
     * @param type $className
     * @return \Alsciende\CerealBundle\Model\DecodedJsonFile[]
     */
    public function decode($path, $className)
    {
        $parts = explode('\\', $className);
        $file = strtolower(array_pop($parts));
        
        $jobs = [];
        
        if(file_exists("${path}/${file}s.json") and is_file("${path}/${file}s.json")) {
            $jobs = $this->decodeCombinedFile("${path}/${file}s.json");
        } else if(file_exists("${path}/${file}s") and is_dir("${path}/${file}s")) {
            $jobs = $this->decodeDirectory("${path}/${file}s");
        }
        
        return $jobs;
    }
    
    /**
     * 
     * @param type $path
     * @param type $className
     * @return \Alsciende\CerealBundle\Model\DecodedJsonFile[]
     */
    public function decodeDirectory($path)
    {
        $files = glob("$path/*.json");
        
        $jobs = [];
        foreach($files as $file) {
            $jobs[] = $this->decodeExplodedFile($file);
        }        
        return $jobs;
    }
    
    /**
     * 
     * @param type $path
     * @param type $className
     * @return \Alsciende\CerealBundle\Model\DecodedJsonFile
     */
    public function decodeExplodedFile($path)
    {
        $contents = file_get_contents($path);
        $data = $this->serializer->decode($contents, 'json');
        
        return new \Alsciende\CerealBundle\Model\DecodedJsonFile($path, $data);
    }
    
    /**
     * 
     * @param type $path
     * @param type $className
     * @return \Alsciende\CerealBundle\Model\DecodedJsonFile[]
     */
    public function decodeCombinedFile($path)
    {
        $contents = file_get_contents($path);
        $list = $this->serializer->decode($contents, 'json');
        
        $jobs = [];
        foreach($list as $data) {
            $jobs[] = new \Alsciende\CerealBundle\Model\DecodedJsonFile($path, $data);
        }
        return $jobs;
    }
    
}
