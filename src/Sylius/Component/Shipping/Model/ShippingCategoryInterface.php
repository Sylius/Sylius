<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Shipping category interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShippingCategoryInterface extends TimestampableInterface
{
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
}
