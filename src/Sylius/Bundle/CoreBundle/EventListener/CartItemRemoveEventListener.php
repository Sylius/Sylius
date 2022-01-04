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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class CartItemRemoveEventListener
{
    private CartContextInterface $cartContext;

    private OrderProcessorInterface $orderProcessor;

    private EntityManagerInterface $entityManager;

    public function __construct(CartContextInterface $cartContext, OrderProcessorInterface $orderProcessor, EntityManagerInterface $entityManager)
    {
        $this->cartContext = $cartContext;
        $this->orderProcessor = $orderProcessor;
        $this->entityManager = $entityManager;
    }

    public function recalculateCart(): void
    {
        $cart = $this->cartContext->getCart();

        $this->orderProcessor->process($cart);

        $this->entityManager->flush();
    }
}
