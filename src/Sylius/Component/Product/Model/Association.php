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
     * @var AssociationTypeInterface
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

    public function __construct(AssociationTypeInterface $type)
    {
        $this->type = $type;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AssociationType
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType(AssociationTypeInterface $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return (boolean) $this->deletedAt;
    }

    abstract public function getAssociatedObject();
} 