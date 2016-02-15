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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface AssociationInterface extends TimestampableInterface, ResourceInterface
{
    /**
     * @return AssociationType
     */
    public function getType();

    /**
     * @param AssociationTypeInterface $type
     */
    public function setType(AssociationTypeInterface $type);

    /**
     * @return AssociableInterface
     */
    public function getOwner();

    /**
     * @param AssociableInterface
     */
    public function setOwner(AssociableInterface $owner = null);

    /**
     * @return Collection
     */
    public function getAssociatedObjects();

    /**
     * @param AssociableInterface
     */
    public function addAssociatedObject(AssociableInterface $associatedObject);

    /**
     * @param AssociableInterface
     */
    public function removeAssociatedObject(AssociableInterface $associatedObject);

    /**
     * @param AssociableInterface
     *
     * @return bool
     */
    public function hasAssociatedObject(AssociableInterface $associatedObject);
}
