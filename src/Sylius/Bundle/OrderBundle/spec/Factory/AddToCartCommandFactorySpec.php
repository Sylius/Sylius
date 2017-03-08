<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommand;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactory;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AddToCartCommandFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddToCartCommandFactory::class);
    }

    function it_is_add_to_cart_command_factory()
    {
        $this->shouldImplement(AddToCartCommandFactoryInterface::class);
    }

    function it_creates_add_to_cart_command_with_cart_and_cart_item(
        OrderInterface $cart,
        OrderItemInterface $cartItem
    ) {
        $this->createWithCartAndCartItem($cart, $cartItem)->shouldBeLike(new AddToCartCommand($cart->getWrappedObject(), $cartItem->getWrappedObject()));
    }
}
