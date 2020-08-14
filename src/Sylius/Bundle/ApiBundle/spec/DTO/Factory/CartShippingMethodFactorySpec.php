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

namespace spec\Sylius\Bundle\ApiBundle\DTO\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\DTO\Factory\CartShippingMethodFactoryInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

class CartShippingMethodFactorySpec extends ObjectBehavior
{
    function it_is_a_cart_shipping_method_factory(): void
    {
        $this->shouldImplement(CartShippingMethodFactoryInterface::class);
    }

    function it_creates_a_cart_shipping_method(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethod->getCode()->willReturn('inpost');

        $cartShippingMethod = $this->create('inpost', $shippingMethod->getWrappedObject(), 10);

        $cartShippingMethod->getCode()->shouldReturn('inpost');
        $cartShippingMethod->getShippingMethod()->shouldReturn($shippingMethod);
        $cartShippingMethod->getCost()->shouldReturn(10);
    }
}
