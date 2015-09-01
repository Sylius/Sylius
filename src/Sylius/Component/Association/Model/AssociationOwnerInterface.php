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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface AssociationOwnerInterface
{
    /**
     * @return AssociationInterface
     */
    public function getAssociations();

    /**
     * @param AssociationInterface
     */
    public function setAssociations(AssociationInterface $association);

    /**
     * @param AssociationInterface
     */
    public function addAssociation(AssociationInterface $association);

    /**
     * @param AssociationInterface
     */
    public function removeAssociation(AssociationInterface $association);
}