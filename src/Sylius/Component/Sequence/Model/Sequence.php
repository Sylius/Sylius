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
    protected $index;

    /**
     * Sequence type
     * @var string
     */
    protected $type;

    public function __construct($type, $index = 1)
    {
        $this->type = $type;
        $this->index = $index;
    }

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
    public function getIndex()
    {
        return $this->index;
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
