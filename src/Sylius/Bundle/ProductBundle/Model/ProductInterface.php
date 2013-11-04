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
use Sylius\Bundle\ResourceBundle\Model\SoftDeletableInterface;
use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Basic product interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ProductInterface extends SoftDeletableInterface, TimestampableInterface
{
    /**
     * Get product name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set product name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get permalink/slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Set the permalink.
     *
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * Get product name.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set product description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Check whether the product is available.
     *
     * @return boolean
     */
    public function isAvailable();

    /**
     * Return available on.
     *
     * @return \DateTime
     */
    public function getAvailableOn();

    /**
     * Set available on.
     *
     * @param \DateTime $availableOn
     */
    public function setAvailableOn(\DateTime $availableOn);

    /**
     * Get meta keywords.
     *
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Set meta keywords for the product.
     *
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Get meta description.
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Set meta description for the product.
     *
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription);

    /**
     * Returns all product product properties.
     *
     * @return ProductPropertyInterface[]
     */
    public function getProperties();

    /**
     * Sets all product product properties.
     *
     * @param Collection $properties Array of ProductPropertyInterface
     */
    public function setProperties(Collection $properties);

    /**
     * Adds product property.
     *
     * @param ProductPropertyInterface $property
     */
    public function addProperty(ProductPropertyInterface $property);

    /**
     * Removes product property from product.
     *
     * @param ProductPropertyInterface $property
     */
    public function removeProperty(ProductPropertyInterface $property);

    /**
     * Checks whether product has given product property.
     *
     * @param ProductPropertyInterface $property
     *
     * @return Boolean
     */
    public function hasProperty(ProductPropertyInterface $property);

    /**
     * Checks whether product has given product property, access by name.
     *
     * @param string $propertyName
     *
     * @return Boolean
     */
    public function hasPropertyByName($propertyName);

    /**
     * Returns a property by its name.
     *
     * @param string $propertyName
     *
     * @return ProductPropertyInterface
     */
    public function getPropertyByName($propertyName);
}
