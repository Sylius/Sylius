<?php

// This stub is created to support Psalm generics.

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Laminas\Stdlib;

use Countable;
use IteratorAggregate;
use Serializable;

/**
 * @psalm-template T
 * @template-extends IteratorAggregate<int, T>
 * @template-extends ArrayAccess<int|null, T>
 */
class PriorityQueue implements Countable, IteratorAggregate, Serializable
{
    const EXTR_DATA = 0x00000001;
    const EXTR_PRIORITY = 0x00000002;
    const EXTR_BOTH = 0x00000003;

    /**
     * @param mixed $data
     * @param int $priority
     *
     * @return PriorityQueue
     *
     * @psalm-param T $data
     * @psalm-return PriorityQueue<T>
     */
    public function insert($data, $priority = 1) { }

    /**
     * @param mixed $datum
     *
     * @return bool False if the item was not found, true otherwise.
     *
     * @psalm-param T $datum
     */
    public function remove($datum) { }

    /**
     * @return bool
     */
    public function isEmpty() { }

    /**
     * @return int
     */
    public function count() { }

    /**
     * @return mixed
     */
    public function top() { }

    /**
     * @return mixed
     */
    public function extract() { }

    /**
     * @return SplPriorityQueue
     *
     * @psalm-return SplPriorityQueue<T>
     */
    public function getIterator() { }

    /**
     * @return string
     */
    public function serialize() { }

    /**
     * @param string $data
     *
     * @return void
     */
    public function unserialize($data) { }

    /**
     * @param int $flag
     *
     * @return array
     */
    public function toArray($flag = self::EXTR_DATA) { }

    /**
     * @param string $class
     *
     * @return PriorityQueue
     *
     * @psalm-return PriorityQueue<T>
     */
    public function setInternalQueueClass($class) { }

    /**
     * @param mixed $datum
     *
     * @return bool
     *
     * @psalm-param T $datum
     */
    public function contains($datum) { }

    /**
     * @param int $priority
     *
     * @return bool
     */
    public function hasPriority($priority) { }

    /**
     * @return void
     */
    public function __clone() { }
}
