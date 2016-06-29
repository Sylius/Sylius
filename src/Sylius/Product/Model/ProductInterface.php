<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Product\Model;

use Sylius\Archetype\Model\ArchetypeSubjectInterface;
use Sylius\Association\Model\AssociableInterface;
use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\SlugAwareInterface;
use Sylius\Resource\Model\TimestampableInterface;
use Sylius\Resource\Model\ToggleableInterface;
use Sylius\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ProductInterface extends
    ArchetypeSubjectInterface,
    SlugAwareInterface,
    TimestampableInterface,
    ToggleableInterface,
    ProductTranslationInterface,
    AssociableInterface,
    CodeAwareInterface,
    TranslatableInterface
{
    /**
     * @return bool
     */
    public function isAvailable();

    /**
     * @return \DateTime
     */
    public function getAvailableOn();

    /**
     * @param null|\DateTime $availableOn
     */
    public function setAvailableOn(\DateTime $availableOn = null);

    /**
     * @return \DateTime
     */
    public function getAvailableUntil();

    /**
     * @param null|\DateTime $availableUntil
     */
    public function setAvailableUntil(\DateTime $availableUntil = null);

    /**
     * @param ProductAssociationInterface $association
     */
    public function addAssociation(ProductAssociationInterface $association);

    /**
     * @param ProductAssociationInterface[] $association
     */
    public function getAssociations();

    /**
     * @param ProductAssociationInterface $association
     */
    public function removeAssociation(ProductAssociationInterface $association);
    
    /**
     * @return bool
     */
    public function isSimple();
}
