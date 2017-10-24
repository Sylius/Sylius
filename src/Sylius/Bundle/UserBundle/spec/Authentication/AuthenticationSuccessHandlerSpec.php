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

namespace spec\Sylius\Bundle\UserBundle\Authentication;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

final class AuthenticationSuccessHandlerSpec extends ObjectBehavior
{
    function let(HttpUtils $httpUtils): void
    {
        $this->beConstructedWith($httpUtils);
    }

    function it_extends_default_authentication_success_handler(): void
    {
        $this->shouldHaveType(DefaultAuthenticationSuccessHandler::class);
    }

    function it_is_a_authentication_success_handler(): void
    {
        $this->shouldImplement(AuthenticationSuccessHandlerInterface::class);
    }

    function it_returns_json_response_if_request_is_xml_based(Request $request, TokenInterface $token): void
    {
        $request->isXmlHttpRequest()->willReturn(true);

        $this->onAuthenticationSuccess($request, $token);
    }
}
