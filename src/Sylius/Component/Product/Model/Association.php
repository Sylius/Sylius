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

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Abstract association class
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
abstract class Association implements TimestampableInterface
{
    /**
     * Product id.
     *
     * @var int
     */
    protected $id;

    /**
     * @var AssociationTypeInterface
     */
    private $type;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Deletion time.
     *
     * @var \DateTime
     */
    protected $deletedAt;

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

    /**
     * @param AssociationTypeInterface $type
     */
    public function setType(AssociationTypeInterface $type)
    {
        $this->type = $type;
    }

    /**
     * @return object
     */
    abstract public function getAssociatedObject();

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted()
    {
        return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
} 