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

namespace spec\Sylius\Bundle\ApiBundle\View\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\View\CartShippingMethod;
use Sylius\Bundle\ApiBundle\View\Factory\CartShippingMethodFactoryInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class CartShippingMethodFactorySpec extends ObjectBehavior
{
    function it_is_a_cart_shipping_method_factory(): void
    {
        $this->shouldImplement(CartShippingMethodFactoryInterface::class);
    }

    function it_creates_a_cart_shipping_method(ShippingMethodInterface $shippingMethod): void
    {
        $this->create($shippingMethod->getWrappedObject(), 10)->shouldBeLike(new CartShippingMethod(
            $shippingMethod->getWrappedObject(),
            10
        ));
    }
}
