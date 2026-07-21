<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\OrderBundle\Adder;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final readonly class CartItemAdder implements CartItemAdderInterface
{
    public function __construct(
        private AddToCartCommandFactoryInterface $addToCartCommandFactory,
        private EventDispatcherInterface $eventDispatcher,
        private ObjectManager $orderManager,
    ) {
    }

    public function add(OrderInterface $cart, OrderItemInterface $cartItem): void
    {
        $addToCartCommand = $this->addToCartCommandFactory->createWithCartAndCartItem($cart, $cartItem);

        $this->eventDispatcher->dispatch(new GenericEvent($addToCartCommand), SyliusCartEvents::CART_ITEM_ADD);

        $this->orderManager->persist($cart);
        $this->orderManager->flush();
    }
}
