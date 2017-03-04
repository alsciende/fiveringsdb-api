<?php

namespace Alsciende\DoctrineSerializerBundle;

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
                array(new PropertyNormalizer()), array(new JsonEncoder())
        );
    }

    /**
     * 
     * @param Annotation\Source $source
     * @return array
     */
    public function decode (Annotation\Source $source)
    {
        
        $parts = explode('\\', $source->className);
        $path = $source->path . "/" . array_pop($parts);

        if(isset($source->break)) {
            if(file_exists("$path") and is_dir("$path")) {
                return $this->decodeDirectory("$path");
            } else {
                throw new \Exception("Directory $path not found");
            }
        } else {
            if(file_exists("$path.json") and is_file("$path.json")) {
                return $this->decodeFile("$path.json");
            } else {
                throw new \Exception("File $path.json not found");
            }
        }
    }

    /**
     * 
     * @param type $path
     * @return array
     */
    public function decodeDirectory ($path)
    {
        $filenames = glob("$path/*.json");

        $files = [];
        foreach($filenames as $filename) {
            $files = array_merge($files, $this->decodeFile($filename));
        }
        return $files;
    }

    /**
     * 
     * @param type $path
     * @return array
     */
    public function decodeFile ($path)
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
