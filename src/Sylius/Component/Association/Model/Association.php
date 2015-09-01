<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Association\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class Association implements AssociationInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var AssociationTypeInterface
     */
    protected $type;

    /**
     * @var Associatable
     */
    protected $owner;

    /**
     * @var Collection<Associatable>
     */
    protected $associatedObjects;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->associatedObjects = new ArrayCollection();
    }

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(AssociationTypeInterface $type)
    {
        $this->type = $type;
    }

    /**
     * @return Associatable
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Associatable $owner
     */
    public function setOwner(Associatable $owner = null)
    {
        $this->owner = $owner;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociatedObjects()
    {
        return $this->associatedObjects;
    }

    /**
     * @param Collection $associatedObjects
     */
    public function setAssociatedObjects(Collection $associatedObjects)
    {
        $this->associatedObjects = $associatedObjects;
    }

    /**
     * @param Associatable $associatedObject
     */
    public function hasAssociatedObject(Associatable $associatedObject)
    {
        return $this->associatedObjects->contains($associatedObject);
    }

    /**
     * @param Associatable $associatedObject
     */
    public function addAssociatedObject(Associatable $associatedObject)
    {
        if (!$this->hasAssociatedObject($associatedObject)) {
            $this->associatedObjects->add($associatedObject);
        }
    }
} 