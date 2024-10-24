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

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class ShopUserLogoutHandlerSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext,
        CartStorageInterface $cartStorage,
    ): void {
        $this->beConstructedWith($channelContext, $cartStorage);
    }

    function it_clears_cart_session_after_logging_out(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        CartStorageInterface $cartStorage,
        LogoutEvent $logoutEvent,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $cartStorage->removeForChannel($channel)->shouldBeCalled();

        $this->onLogout($logoutEvent);
    }
}
