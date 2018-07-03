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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Webmozart\Assert\Assert;

final class UserImpersonatedListener
{
    /**
     * @var CartStorageInterface
     */
    private $cartStorage;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param CartStorageInterface $cartStorage
     * @param ChannelContextInterface $channelContext
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        CartStorageInterface $cartStorage,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->cartStorage = $cartStorage;
        $this->channelContext = $channelContext;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param UserEvent $event
     */
    public function onUserImpersonated(UserEvent $event): void
    {
        /** @var ShopUserInterface $user */
        $user = $event->getUser();
        Assert::isInstanceOf($user, ShopUserInterface::class);

        $customer = $user->getCustomer();

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findLatestCartByChannelAndCustomer($channel, $customer);

        if ($cart === null) {
            $this->cartStorage->removeForChannel($channel);

            return;
        }

        $this->cartStorage->setForChannel($channel, $cart);
    }
}
