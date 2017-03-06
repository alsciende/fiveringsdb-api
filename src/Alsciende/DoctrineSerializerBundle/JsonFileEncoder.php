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
     * @param Model\Source $source
     * @return Model\Fragment[]
     */
    public function decode (Model\Source $source)
    {
        
        $parts = explode('\\', $source->className);
        $path = $source->path . "/" . array_pop($parts);

        if(isset($source->break)) {
            if(file_exists("$path") and is_dir("$path")) {
                return $this->decodeDirectory($source, "$path");
            } else {
                throw new \Exception("Directory $path not found");
            }
        } else {
            if(file_exists("$path.json") and is_file("$path.json")) {
                return $this->decodeFile($source, "$path.json");
            } else {
                throw new \Exception("File $path.json not found");
            }
        }
    }

    /**
     * 
     * @param type $path
     * @return Model\Fragment[]
     */
    public function decodeDirectory (Model\Source $source, string $path)
    {
        $filenames = glob("$path/*.json");

        $fragments = [];
        foreach($filenames as $filename) {
            $fragments = array_merge($fragments, $this->decodeFile($source, $filename));
        }
        return $fragments;
    }

    /**
     * 
     * @param type $path
     * @return Model\Fragment[]
     */
    public function decodeFile (Model\Source $source, string $path)
    {
        $contents = file_get_contents($path);
        $list = $this->serializer->decode($contents, 'json');

        $fragments = [];
        foreach($list as $data) {
            $fragments[] = new Model\Fragment($source, $path, $data);
        }
        
        return $fragments;
    }

}
