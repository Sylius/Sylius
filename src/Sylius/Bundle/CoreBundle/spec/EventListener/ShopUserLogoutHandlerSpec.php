<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\ShopUserLogoutHandler;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ShopUserLogoutHandlerSpec extends ObjectBehavior
{
    function let(HttpUtils $httpUtils, SessionInterface $session, ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($httpUtils, '/', $session, $channelContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ShopUserLogoutHandler::class);
    }

    function it_is_default_logout_success_handler()
    {
        $this->shouldHaveType(DefaultLogoutSuccessHandler::class);
    }

    function it_implements_logout_success_handler_interface()
    {
        $this->shouldImplement(LogoutSuccessHandlerInterface::class);
    }

    function it_clears_cart_session_after_logging_out_and_return_default_handler_response(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        HttpUtils $httpUtils,
        Request $request,
        Response $response,
        SessionInterface $session
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $session->remove('_sylius.cart.WEB_US')->shouldBeCalled();

        $httpUtils->createRedirectResponse($request, '/')->willReturn($response);

        $this->onLogoutSuccess($request)->shouldReturn($response);
    }
}
