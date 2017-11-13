<?php

namespace Alsciende\SerializerBundle\Service;

use Alsciende\SerializerBundle\Model\Block;
use Alsciende\SerializerBundle\Model\Fragment;
use UnexpectedValueException;

/**
 * Turns an array into a string
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class EncodingService
{

    /**
     *
     * @param Block $block
     * @return Fragment[]
     */
    public function decode (Block $block)
    {
        $list = json_decode($block->getData(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new UnexpectedValueException("Block data cannot be decoded (".json_last_error_msg().") " . $block->getData());
        }
        $valid = is_array($list) && (count($list) === 0 || array_key_exists(0, $list));
        if ($valid === false) {
            throw new UnexpectedValueException("Block data cannot be decoded to a numeric array: " . $block->getData());
        }
        $fragments = [];
        foreach ($list as $data) {
            if ($block->getSource()->getBreak()) {
                $this->applyBreak($block, $data);
            }
            $fragment = new Fragment($data);
            $fragment->setBlock($block);
            $fragments[] = $fragment;
        }
        return $fragments;
    }

    private function applyBreak (Block $block, &$data)
    {
        $break = $block->getSource()->getBreak();
        if (!isset($data[$break])) {
            $data[$break] = $block->getName();
        } elseif ($data[$break] !== $block->getName()) {
            throw new \Exception("Discrepancy in " . $block->getPath() . ": value from '" . $break . "': " . $data[$break] . " is different from block name: " . $block->getName());
        }
    }

    /**
     *
     * @param Fragment[] $fragments
     * @return Block
     */
    public function encode ($fragments)
    {
        $list = [];
        foreach ($fragments as $fragment) {
            $list[] = $fragment->getData();
        }
        $data = json_encode($list);
        return new Block($data);
    }

}
