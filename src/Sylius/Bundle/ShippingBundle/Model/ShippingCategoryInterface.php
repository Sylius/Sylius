<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Model;

/**
 * Shipping category interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ShippingCategoryInterface
{
    /**
     * Get shipping category id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get category name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set category name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get all methods for this category.
     *
     * @return Collection
     */
    public function getMethods();

    /**
     * Add method.
     *
     * @param ShippingMethodInterface $method
     */
    public function addMethod(ShippingMethodInterface $method);

    /**
     * Remove method.
     *
     * @param ShippingMethodInterface $method
     */
    public function removeMethod(ShippingMethodInterface $method);

    /**
     * Has method?
     *
     * @param ShippingMethodInterface $method
     *
     * @return Boolean
     */
    public function hasMethod(ShippingMethodInterface $method);

    /**
     * Get creation time.
     *
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Set updated at.
     *
     * @return DateTime
     */
    public function getUpdatedAt();
}
