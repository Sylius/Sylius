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

use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Webmozart\Assert\Assert;

final class UserImpersonatedListener
{
    public function __construct(
        private CartStorageInterface $cartStorage,
        private ChannelContextInterface $channelContext,
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function onUserImpersonated(UserEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $customer = $user->getCustomer();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $cart = $this->orderRepository->findLatestCartByChannelAndCustomer($channel, $customer);

        if ($cart === null) {
            $this->cartStorage->removeForChannel($channel);

            return;
        }

        $this->cartStorage->setForChannel($channel, $cart);
    }
}
