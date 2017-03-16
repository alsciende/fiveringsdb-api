<?php

namespace Alsciende\SerializerBundle\Encoder;

/**
 * Takes a Block and turns it into Fragments, or vice-versa
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Encoder
{
    
    /**
     * 
     * @param \Alsciende\SerializerBundle\Model\Block $block
     * @return \Alsciende\SerializerBundle\Model\Fragment[]
     */
    public function decode(\Alsciende\SerializerBundle\Model\Block $block)
    {
        $list = json_decode($block->getData(), true);
        if(!$list or ! is_array($list)) {
            throw new \UnexpectedValueException("Block data cannot be decoded to an array!");
        }
        $fragments = [];
        foreach($list as $data) {
            $fragment = new \Alsciende\SerializerBundle\Model\Fragment($data);
            $fragment->setBlock($block);
            $fragments[] = $fragment;
        }
        return $fragments;
    }

    /**
     * 
     * @param \Alsciende\SerializerBundle\Model\Fragment[] $fragments
     * @return \Alsciende\SerializerBundle\Model\Block
     */
    public function encode($fragments)
    {
        $list = [];
        foreach($fragments as $fragment) {
            $list[] = $fragment->getData();
        }
        $data = json_encode($list);
        return new \Alsciende\SerializerBundle\Model\Block($data);
    }
}
