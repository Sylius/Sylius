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

namespace Tests\Sylius\Bundle\ShopBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\EventListener\UserImpersonatedListener;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\User\Model\UserInterface;

final class UserImpersonatedListenerTest extends TestCase
{
    private CartStorageInterface&MockObject $cartStorage;

    private ChannelContextInterface&MockObject $channelContext;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private UserImpersonatedListener $userImpersonatedListener;

    protected function setUp(): void
    {
        $this->cartStorage = $this->createMock(CartStorageInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);

        $this->userImpersonatedListener = new UserImpersonatedListener(
            $this->cartStorage,
            $this->channelContext,
            $this->orderRepository,
        );
    }

    public function testSetsCartIdOfAnImpersonatedCustomerInSession(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);

        $event->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('getCustomer')->willReturn($customer);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $this->orderRepository->expects($this->once())->method('findLatestCartByChannelAndCustomer')->with($channel, $customer)->willReturn($cart);
        $this->cartStorage->expects($this->once())->method('setForChannel')->with($channel, $cart);

        $this->userImpersonatedListener->onUserImpersonated($event);
    }

    public function testRemovesTheCurrentCartIdIfAnImpersonatedCustomerHasNoCart(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $event->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('getCustomer')->willReturn($customer);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $this->orderRepository->expects($this->once())->method('findLatestCartByChannelAndCustomer')->with($channel, $customer)->willReturn(null);
        $this->cartStorage->expects($this->once())->method('removeForChannel')->with($channel);

        $this->userImpersonatedListener->onUserImpersonated($event);
    }

    public function testDoesNothingWhenTheUserIsNotAShopUserType(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);

        $event->expects($this->once())->method('getUser')->willReturn($user);

        $this->userImpersonatedListener->onUserImpersonated($event);
    }
}
