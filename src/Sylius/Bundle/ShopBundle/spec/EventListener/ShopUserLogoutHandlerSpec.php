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

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\HttpUtils;

final class ShopUserLogoutHandlerSpec extends ObjectBehavior
{
    function let(
        HttpUtils $httpUtils,
        ChannelContextInterface $channelContext,
        CartStorageInterface $cartStorage,
        TokenStorageInterface $tokenStorage,
    ): void {
        $this->beConstructedWith($httpUtils, '/', $channelContext, $cartStorage, $tokenStorage);
    }

    function it_clears_cart_session_after_logging_out_and_return_default_handler_response(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        HttpUtils $httpUtils,
        Request $request,
        RedirectResponse $response,
        CartStorageInterface $cartStorage,
        LogoutEvent $logoutEvent,
        SessionInterface $session,
        TokenStorageInterface $tokenStorage,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $logoutEvent->getRequest()->willReturn($request);
        $logoutEvent->getResponse()->willReturn(null);
        $request->getSession()->willReturn($session);

        $httpUtils->createRedirectResponse($request, '/')->willReturn($response);

        $tokenStorage->setToken(null)->shouldBeCalled();
        $cartStorage->removeForChannel($channel)->shouldBeCalled();
        $logoutEvent->setResponse($response)->shouldBeCalled();

        $this->onLogout($logoutEvent);
    }
}
