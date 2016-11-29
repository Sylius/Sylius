<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class OrderSequence implements OrderSequenceInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    public function incrementIndex()
    {
        ++$this->index;
    }
}
