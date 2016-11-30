<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ShippingMethodRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     * @param string $locale
     *
     * @return ShippingMethodInterface[]
     */
    public function findByName($name, $locale);
}
