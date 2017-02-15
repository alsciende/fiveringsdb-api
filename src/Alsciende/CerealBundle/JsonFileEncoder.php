<?php

namespace Alsciende\CerealBundle;

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
     * @return array
     */
    public function decode($path, $className)
    {
        $parts = explode('\\', $className);
        $file = strtolower(array_pop($parts));
        
        $files = [];
        
        if(file_exists("$path/$file.json") and is_file("$path/$file.json")) {
            $files = $this->decodeCombinedFile("$path/$file.json");
        } else if(file_exists("$path/$file") and is_dir("$path/$file")) {
            $files = $this->decodeDirectory("$path/$file");
        }
        
        return $files;
    }
    
    /**
     * 
     * @param type $path
     * @param type $className
     * @return array
     */
    public function decodeDirectory($path)
    {
        $filenames = glob("$path/*.json");
        
        $files = [];
        foreach($filenames as $filename) {
            $files[] = $this->decodeExplodedFile($filename);
        }        
        return $files;
    }
    
    /**
     * 
     * @param type $path
     * @param type $className
     * @return array
     */
    public function decodeExplodedFile($path)
    {
        $contents = file_get_contents($path);
        $data = $this->serializer->decode($contents, 'json');
        
        return array($path, $data);
    }
    
    /**
     * 
     * @param type $path
     * @param type $className
     * @return array
     */
    public function decodeCombinedFile($path)
    {
        $contents = file_get_contents($path);
        $list = $this->serializer->decode($contents, 'json');
        
        $files = [];
        foreach($list as $data) {
            $files[] = array($path, $data);
        }
        return $files;
    }
    
}
