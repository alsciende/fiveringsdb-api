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
                array(new PropertyNormalizer()), array(new JsonEncoder())
        );
    }

    /**
     * 
     * @param type $path
     * @param type $className
     * @param int $outputType
     * @return array
     */
    public function decode ($path, $className, $outputType)
    {
        $parts = explode('\\', $className);
        $file = array_pop($parts);

        $files = [];

        $isSingleFile = $outputType & AlsciendeCerealBundle::SINGLE_FILE;
        $isSingleData = $outputType & AlsciendeCerealBundle::SINGLE_DATA;

        if($isSingleFile) {
            if(file_exists("$path/$file.json") and is_file("$path/$file.json")) {
                if($isSingleData) {
                    $files = $this->decodeExplodedFile("$path/$file.json");
                } else {
                    $files = $this->decodeCombinedFile("$path/$file.json");
                }
            } else {
                throw new \Exception("File $path/$file.json not found");
            }
        } else {
            if(file_exists("$path/$file") and is_dir("$path/$file")) {
                $files = $this->decodeDirectory("$path/$file", $isSingleData);
            } else {
                throw new \Exception("Directory $path/$file not found");
            }
        }

        return $files;
    }

    /**
     * 
     * @param type $path
     * @param boolean $isSingleData
     * @return array
     */
    public function decodeDirectory ($path, $isSingleData)
    {
        $filenames = glob("$path/*.json");

        $files = [];
        foreach($filenames as $filename) {
            if($isSingleData) {
                $files[] = $this->decodeExplodedFile($filename);
            } else {
                $files = array_merge($files, $this->decodeCombinedFile($filename));
            }
        }
        return $files;
    }

    /**
     * 
     * @param type $path
     * @return array
     */
    public function decodeExplodedFile ($path)
    {
        $contents = file_get_contents($path);
        $data = $this->serializer->decode($contents, 'json');

        return array($path, $data);
    }

    /**
     * 
     * @param type $path
     * @return array
     */
    public function decodeCombinedFile ($path)
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
