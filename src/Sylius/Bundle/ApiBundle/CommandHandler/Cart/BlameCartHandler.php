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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Sylius\Bundle\ApiBundle\Command\Cart\BlameCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BlameCartHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $shopUserRepository,
        private OrderRepositoryInterface $orderRepository,
        private OrderProcessorInterface $orderProcessor,
    ) {
    }

    public function __invoke(BlameCart $blameCart): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->shopUserRepository->findOneByEmail($blameCart->shopUserEmail);

        if ($user === null) {
            throw new \InvalidArgumentException('There is currently no customer with given email');
        }

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($blameCart->orderTokenValue);

        if ($cart === null) {
            throw new \InvalidArgumentException('Cart with given token value could not be found');
        }

        if (null !== $cart->getCustomer()) {
            throw new \InvalidArgumentException('There is an assigned customer to this cart');
        }

        $cart->setCustomerWithAuthorization($user->getCustomer());

        $this->orderProcessor->process($cart);
    }
}
