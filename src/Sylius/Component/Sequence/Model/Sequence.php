<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Model;

class Sequence implements SequenceInterface
{
    /**
     * Identifier
     * @var integer
     */
    protected $id;

    /**
     * Sequence index
     * @var integer
     */
    protected $index = 1;

    /**
     * Sequence type
     * @var string
     */
    protected $type;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementIndex()
    {
        $this->index++;

        return $this;
    }
}
