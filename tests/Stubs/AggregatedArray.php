<?php

namespace Rentalhost\VanillaSort\Stubs;

use ArrayIterator;
use IteratorAggregate;

/**
 * Class AggregatedArray
 * @package Rentalhost\VanillaSort\Stubs
 */
class AggregatedArray implements IteratorAggregate
{
    private $storage = [ 3, 5, 1 ];

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->storage);
    }
}
