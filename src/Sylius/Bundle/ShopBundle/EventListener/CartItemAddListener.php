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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final readonly class CartItemAddListener
{
    public function __construct(private OrderModifierInterface $orderModifier)
    {
    }

    public function addToOrder(GenericEvent $event): void
    {
        $addToCartCommand = $event->getSubject();

        Assert::isInstanceOf($addToCartCommand, AddToCartCommandInterface::class);

        $this->orderModifier->addToOrder($addToCartCommand->getCart(), $addToCartCommand->getCartItem());
    }
}
