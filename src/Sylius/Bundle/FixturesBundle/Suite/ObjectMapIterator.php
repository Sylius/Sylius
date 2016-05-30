<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Suite;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ObjectMapIterator implements \Iterator
{
    /**
     * @var array
     */
    private $keys;

    /**
     * @var array
     */
    private $values;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $pointer = 0;

    /**
     * @param array $keys
     * @param array $values
     */
    public function __construct(array $keys, array $values)
    {
        if (count($keys) !== count($values)) {
            throw new \InvalidArgumentException('Amount of keys and values elements must be equal!');
        }

        $this->keys = array_values($keys);
        $this->values = array_values($values);
        $this->count = count($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->values[$this->pointer];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->keys[$this->pointer];
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->pointer < $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->pointer = 0;
    }
}
