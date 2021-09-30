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

namespace Sylius\Bundle\ApiBundle\View;

use Sylius\Component\Core\Model\ShippingMethodInterface;

/** @experimental */
class CartShippingMethod implements CartShippingMethodInterface
{
    /** @var ShippingMethodInterface */
    private $shippingMethod;

    /** @var int */
    private $price;

    public function __construct(ShippingMethodInterface $shippingMethod, int $price)
    {
        $this->shippingMethod = $shippingMethod;
        $this->price = $price;
    }

    public function getShippingMethod(): ShippingMethodInterface
    {
        return $this->shippingMethod;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
