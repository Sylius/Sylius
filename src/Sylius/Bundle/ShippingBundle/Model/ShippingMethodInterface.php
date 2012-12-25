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
 * Shipping rate interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface ShippingMethodInterface
{
    public function getId();
    public function getCategory();
    public function setCategory(ShippingCategoryInterface $category = null);
    public function getName();
    public function setName($name);
    public function getCalculator();
    public function setCalculator($calculator);
    public function getConfiguration();
    public function setConfiguration(array $configuration);
    public function getCreatedAt();
    public function getUpdatedAt();
}
