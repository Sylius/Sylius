<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Shipping\Model;

interface ShippableInterface
{
    /**
     * @return float|null
     */
    public function getShippingWeight(): ?float;

    /**
     * @return float|null
     */
    public function getShippingVolume(): ?float;

    /**
     * @return float|null
     */
    public function getShippingWidth(): ?float;

    /**
     * @return float|null
     */
    public function getShippingHeight(): ?float;

    /**
     * @return float|null
     */
    public function getShippingDepth(): ?float;

    /**
     * @return ShippingCategoryInterface|null
     */
    public function getShippingCategory(): ?ShippingCategoryInterface;
}
