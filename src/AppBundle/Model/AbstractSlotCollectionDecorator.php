<?php

namespace AppBundle\Model;

/**
 * Description of AbstractSlotCollectionDecorator
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class AbstractSlotCollectionDecorator extends \Doctrine\Common\Collections\ArrayCollection
{
    public function __construct (array $elements = array())
    {
        parent::__construct($elements);
    }
    
    /**
     * @return SlotInterface[]
     */
    public function toArray ()
    {
        return parent::toArray();
    }
    
    /**
     * Iterates over elements of the collection, returning the first element $p returns thruthly for.
     * The predicate is invoked with three arguments: ($value, $index|$key, $collection).
     * 
     * @param \Closure $p
     * @return SlotInterface
     */
    public function find(\Closure $p)
    {
        foreach($this as $key => $element) {
            if(call_user_func($p, $element, $key, $this)) {
                return $element;
            }
        }
    }

    public function countElements ()
    {
        $count = 0;
        foreach($this->toArray() as $slot) {
            $count += $slot->getQuantity();
        }
        return $count;
    }

    public function getContent ()
    {
        $content = [];
        foreach($this->toArray() as $slot) {
            $content[$slot->getElement()->getCode()] = $slot->getQuantity();
        }
        ksort($content);
        return $content;
    }
}
