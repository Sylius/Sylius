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
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class Association implements AssociationInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var AssociationTypeInterface
     */
    protected $type;

    /**
     * @var AssociableInterface
     */
    protected $owner;

    /**
     * @var Collection<AssociableInterface>
     */
    protected $associatedObjects;

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
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(AssociableInterface $owner = null)
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
     * {@inheritdoc}
     */
    public function hasAssociatedObject(AssociableInterface $associatedObject)
    {
        return $this->associatedObjects->contains($associatedObject);
    }

    /**
     * {@inheritdoc}
     */
    public function addAssociatedObject(AssociableInterface $associatedObject)
    {
        if (!$this->hasAssociatedObject($associatedObject)) {
            $this->associatedObjects->add($associatedObject);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssociatedObject(AssociableInterface $associatedObject)
    {
        if ($this->hasAssociatedObject($associatedObject)) {
            $this->associatedObjects->removeElement($associatedObject);
        }
    }
}
