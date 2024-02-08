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

namespace Sylius\Bundle\CoreBundle\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class CustomerAndChannelBasedCartContext implements CartContextInterface
{
    public function __construct(
        private CustomerContextInterface $customerContext,
        private ChannelContextInterface $channelContext,
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function getCart(): OrderInterface
    {
        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException) {
            throw new CartNotFoundException('Sylius was not able to find the cart, as there is no current channel.');
        }

        $customer = $this->customerContext->getCustomer();
        if (null === $customer) {
            throw new CartNotFoundException('Sylius was not able to find the cart, as there is no logged in user.');
        }
        Assert::isInstanceOf($channel, ChannelInterface::class);
        Assert::isInstanceOf($customer, CustomerInterface::class);
        $cart = $this->orderRepository->findLatestNotEmptyCartByChannelAndCustomer($channel, $customer);
        if (null === $cart) {
            throw new CartNotFoundException('Sylius was not able to find the cart for currently logged in user.');
        }

        return $cart;
    }
}
