<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Authentication;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Authentication\AuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AuthenticationFailureHandlerSpec extends ObjectBehavior
{
    function let(HttpKernelInterface $httpKernel, HttpUtils $httpUtils)
    {
        $this->beConstructedWith($httpKernel, $httpUtils);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AuthenticationFailureHandler::class);
    }

    function it_extends_default_authentication_failure_handler()
    {
        $this->shouldHaveType(DefaultAuthenticationFailureHandler::class);
    }

    function it_is_a_authentication_failure_handler()
    {
        $this->shouldImplement(AuthenticationFailureHandlerInterface::class);
    }

    function it_returns_json_response_if_request_is_xml_based(
        Request $request,
        AuthenticationException $authenticationException
    ) {
        $request->isXmlHttpRequest()->willReturn(true);
        $authenticationException->getMessageKey()->willReturn('Invalid credentials.');

        $this->onAuthenticationFailure($request, $authenticationException)->shouldHaveType(JsonResponse::class);
    }
}
