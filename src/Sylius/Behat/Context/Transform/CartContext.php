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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Transform /^(cart)$/
     */
    public function provideCartToken(): ?string
    {
        if ($this->sharedStorage->has('cart_token')) {
            return $this->sharedStorage->get('cart_token');
        }

        return null;
    }

    /**
     * @Transform /^((?:previous|customer|customer's|visitor's|their) cart)$/
     */
    public function providePreviousCartToken(): ?string
    {
        if ($this->sharedStorage->has('previous_cart_token')) {
            return $this->sharedStorage->get('previous_cart_token');
        }

        return $this->provideCartToken();
    }

    /**
     * @Transform /^(customer's latest cart)$/
     */
    public function provideLatestCart(): OrderInterface
    {
        $carts = $this->orderRepository->findBy(
            ['state' => OrderInterface::STATE_CART],
            ['createdAt' => 'DESC'],
            1,
        );

        Assert::count($carts, 1);

        return $carts[0];
    }
}
