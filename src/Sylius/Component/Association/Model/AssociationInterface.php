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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface AssociationInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return AssociationType
     */
    public function getType();

    /**
     * @param AssociationTypeInterface $type
     */
    public function setType(AssociationTypeInterface $type);

    /**
     * @return Associatable
     */
    public function getOwner();

    /**
     * @param Associatable
     */
    public function setOwner(Associatable $associatedObject = null);

    /**
     * @return Associatable
     */
    public function getAssociatedObjects();

    /**
     * @param Collection
     */
    public function setAssociatedObjects(Collection $associatedObjects);

    /**
     * @param Associatable
     */
    public function addAssociatedObject(Associatable $associatedObject);

    /**
     * @param Associatable
     *
     * @return bool
     */
    public function hasAssociatedObject(Associatable $associatedObject);
}