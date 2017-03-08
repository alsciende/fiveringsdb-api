<?php

namespace Alsciende\DoctrineSerializerBundle\Encoder;

/**
 * Takes a Block and turns it into Fragments, or vice-versa
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class Encoder
{
    
    /**
     * 
     * @param \Alsciende\DoctrineSerializerBundle\Model\Block $block
     * @return \Alsciende\DoctrineSerializerBundle\Model\Fragment[]
     */
    public function decode(\Alsciende\DoctrineSerializerBundle\Model\Block $block)
    {
        $list = json_decode($block->getData(), true);
        if(!$list or ! is_array($list)) {
            throw new \UnexpectedValueException("Block data cannot be decoded to an array!");
        }
        $fragments = [];
        foreach($list as $data) {
            $fragment = new \Alsciende\DoctrineSerializerBundle\Model\Fragment($data);
            $fragment->setBlock($block);
            $fragments[] = $fragment;
        }
        return $fragments;
    }

    /**
     * 
     * @param \Alsciende\DoctrineSerializerBundle\Model\Fragment[] $fragments
     * @return \Alsciende\DoctrineSerializerBundle\Model\Block
     */
    public function encode($fragments)
    {
        $list = [];
        foreach($fragments as $fragment) {
            $list[] = $fragment->getData();
        }
        $data = json_encode($list);
        return new \Alsciende\DoctrineSerializerBundle\Model\Block($data);
    }
}
