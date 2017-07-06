<?php

namespace Alsciende\SerializerBundle\Service;

use Alsciende\SerializerBundle\Model\Block;
use Alsciende\SerializerBundle\Model\Source;
use Exception;

/**
 * Turns a string into a file
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class StoringService
{

    /**
     * Retrieve Blocks from a Source configuration
     *
     * @param Source $source
     * @return Block
     */
    public function retrieve (Source $source)
    {

        $parts = explode('\\', $source->getClassName());
        $path = $source->getPath() . "/" . array_pop($parts);

        if ($source->getBreak() === null) {
            if (file_exists("$path.json") and is_file("$path.json")) {
                $blocks = $this->scanFile("$path.json");
            } else {
                return;
            }
        } else {
            if (file_exists("$path") and is_dir("$path")) {
                $blocks = $this->scanDirectory("$path");
            } else {
                throw new Exception("Directory $path not found");
            }
        }

        foreach ($blocks as $block) {
            $block->setSource($source);
        }

        return $blocks;
    }

    /**
     *
     * @param string $path
     * @return Block[]
     */
    public function scanDirectory ($path)
    {
        $filenames = glob("$path/*.json");

        $blocks = [];
        foreach ($filenames as $filename) {
            $blocks = array_merge($blocks, $this->scanFile($filename));
        }
        return $blocks;
    }

    /**
     *
     * @param string $path
     * @return Block
     */
    public function scanFile ($path)
    {
        $data = file_get_contents($path);
        $block = new Block($data, $path);
        return array($block);
    }

}
