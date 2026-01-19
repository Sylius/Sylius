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
use Sylius\Bundle\ShopBundle\EventListener\ShopUserLogoutHandler;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class ShopUserLogoutHandlerTest extends TestCase
{
    private ChannelContextInterface&MockObject $channelContext;

    private CartStorageInterface&MockObject $cartStorage;

    private ShopUserLogoutHandler $shopUserLogoutHandler;

    protected function setUp(): void
    {
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->cartStorage = $this->createMock(CartStorageInterface::class);

        $this->shopUserLogoutHandler = new ShopUserLogoutHandler($this->channelContext, $this->cartStorage);
    }

    public function testClearsCartSessionAfterLoggingOut(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var LogoutEvent&MockObject $logoutEvent */
        $logoutEvent = $this->createMock(LogoutEvent::class);

        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $this->cartStorage->expects($this->once())->method('removeForChannel')->with($channel);

        $this->shopUserLogoutHandler->onLogout();
    }
}
