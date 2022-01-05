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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\CartItemRemoveEventListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

class CartItemRemoveEventListenerSpec extends ObjectBehavior
{
    function let(CartContextInterface $cartContext, OrderProcessorInterface $orderProcessor, EntityManagerInterface $entityManager): void
    {
        $this->beConstructedWith($cartContext, $orderProcessor, $entityManager);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CartItemRemoveEventListener::class);
    }

    function it_processes_cart(
        CartContextInterface $cartContext,
        OrderProcessorInterface $orderProcessor,
        EntityManagerInterface $entityManager,
        OrderInterface $cart
    ): void {
        $cartContext->getCart()->willReturn($cart);

        $orderProcessor->process($cart)->shouldBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $this->recalculateCart();
    }
}
