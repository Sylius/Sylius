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

namespace spec\Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class FakeChannelPersisterSpec extends ObjectBehavior
{
    function let(FakeChannelCodeProviderInterface $fakeHostnameProvider): void
    {
        $this->beConstructedWith($fakeHostnameProvider);
    }

    function it_applies_only_to_master_requests(HttpKernelInterface $kernel, Request $request, Response $response): void
    {
        $this->onKernelResponse(new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::SUB_REQUEST,
            $response->getWrappedObject(),
        ));
    }

    function it_applies_only_for_request_with_fake_channel_code(
        FakeChannelCodeProviderInterface $fakeHostnameProvider,
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
    ): void {
        $fakeHostnameProvider->getCode($request)->willReturn(null);

        $this->onKernelResponse(new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $response->getWrappedObject(),
        ));
    }

    function it_persists_fake_channel_codes_in_a_cookie(
        FakeChannelCodeProviderInterface $fakeHostnameProvider,
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseHeaderBag $responseHeaderBag,
    ): void {
        $fakeHostnameProvider->getCode($request)->willReturn('fake_channel_code');

        $response->headers = $responseHeaderBag;
        $responseHeaderBag
            ->setCookie(Argument::that(static fn (Cookie $cookie): bool => $cookie->getName() === '_channel_code' && $cookie->getValue() === 'fake_channel_code'))
            ->shouldBeCalled()
        ;

        $this->onKernelResponse(new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $response->getWrappedObject(),
        ));
    }
}
