<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

/**
 * Abstract association class
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
abstract class Association
{
    /**
     * @var mixed $id
     */
    private $id;

    /**
     * @var AssociationType
     */
    private $type;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    public function __construct(AssociationType $type)
    {
        $this->type = $type;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(AssociationType $type)
    {
        $this->type = $type;

        return $this;
    }

    abstract function getAssociatedObject();
} 