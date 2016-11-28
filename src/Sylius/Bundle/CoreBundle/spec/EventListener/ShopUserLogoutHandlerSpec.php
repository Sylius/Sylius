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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ShopUserLogoutHandlerSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ShopUserLogoutHandler::class);
    }

    function it_implements_logout_success_handler_interface()
    {
        $this->shouldImplement(LogoutSuccessHandlerInterface::class);
    }

    function it_clears_cart_session_after_logging_out(Request $request, SessionInterface $session)
    {
        $session->clear()->shouldBeCalled();

        $this->onLogoutSuccess($request);
    }
}
