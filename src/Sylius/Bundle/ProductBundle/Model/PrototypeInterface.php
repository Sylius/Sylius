<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Used to generate full product form.
 * It simplifies product creation.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface PrototypeInterface extends TimestampableInterface
{
    /**
     * Get name, in most cases it will be displayed by user only in backend.
     * Can be something like 't-shirt' or 'tv'.
     *
     * @return string
     */
    public function getName();

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Returns all prototype properties.
     *
     * @return PropertyInterface[]
     */
    public function getProperties();

    /**
     * Sets all prototype properties.
     *
     * @param Collection $properties
     */
    public function setProperties(Collection $properties);

    /**
     * Adds property.
     *
     * @param PropertyInterface $property
     */
    public function addProperty(PropertyInterface $property);

    /**
     * Removes property from prototype.
     *
     * @param PropertyInterface $property
     */
    public function removeProperty(PropertyInterface $property);

    /**
     * Checks whether prototype has given property.
     *
     * @param PropertyInterface $property
     *
     * @return Boolean
     */
    public function hasProperty(PropertyInterface $property);
}
