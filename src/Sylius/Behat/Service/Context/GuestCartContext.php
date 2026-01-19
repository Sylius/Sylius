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

namespace Sylius\Behat\Service\Context;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;

final readonly class GuestCartContext implements CartContextInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private string $guestCartTokenFilePath,
    ) {
    }

    public function getCart(): OrderInterface
    {
        if (!file_exists($this->guestCartTokenFilePath)) {
            throw new CartNotFoundException(sprintf('The file at "%s" could not be found.', $this->guestCartTokenFilePath));
        }

        $token = file_get_contents($this->guestCartTokenFilePath);
        $cart = $this->orderRepository->findCartByTokenValue($token);

        if (null === $cart) {
            throw new CartNotFoundException();
        }

        return $cart;
    }
}
