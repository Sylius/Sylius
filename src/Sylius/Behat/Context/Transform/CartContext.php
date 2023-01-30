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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

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
     * @Transform /^(the customer's cart)$/
     */
    public function provideCart(): ?OrderInterface
    {
        $cartToken = $this->provideCartToken();
        if (null === $cartToken) {
            return null;
        }

        return $this->orderRepository->findOneBy(['tokenValue' => $cartToken]);
    }
}
