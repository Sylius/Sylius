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
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
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
    public function setName($name);
    public function getDescription();
    public function setDescription($description);
    public function getMethods();
    public function addMethod(ShippingMethodInterface $method);
    public function removeMethod(ShippingMethodInterface $method);
    public function hasMethod(ShippingMethodInterface $method);
    public function getCreatedAt();
    public function getUpdatedAt();
}
