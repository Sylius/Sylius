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

namespace spec\Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ChannelBundle\Context\FakeChannel\FakeChannelCodeProviderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class FakeChannelPersisterSpec extends ObjectBehavior
{
    function let(FakeChannelCodeProviderInterface $fakeHostnameProvider): void
    {
        $this->beConstructedWith($fakeHostnameProvider);
    }

    function it_applies_only_to_master_requests(FilterResponseEvent $filterResponseEvent): void
    {
        $filterResponseEvent->getRequestType()->willReturn(HttpKernelInterface::SUB_REQUEST);

        $filterResponseEvent->getRequest()->shouldNotBeCalled();
        $filterResponseEvent->getResponse()->shouldNotBeCalled();

        $this->onKernelResponse($filterResponseEvent);
    }

    function it_applies_only_for_request_with_fake_channel_code(
        FakeChannelCodeProviderInterface $fakeHostnameProvider,
        FilterResponseEvent $filterResponseEvent,
        Request $request
    ): void {
        $filterResponseEvent->getRequestType()->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $filterResponseEvent->getRequest()->willReturn($request);

        $fakeHostnameProvider->getCode($request)->willReturn(null);

        $filterResponseEvent->getResponse()->shouldNotBeCalled();

        $this->onKernelResponse($filterResponseEvent);
    }

    function it_persists_fake_channel_codes_in_a_cookie(
        FakeChannelCodeProviderInterface $fakeHostnameProvider,
        FilterResponseEvent $filterResponseEvent,
        Request $request,
        Response $response,
        ResponseHeaderBag $responseHeaderBag
    ): void {
        $filterResponseEvent->getRequestType()->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $filterResponseEvent->getRequest()->willReturn($request);

        $fakeHostnameProvider->getCode($request)->willReturn('fake_channel_code');

        $filterResponseEvent->getResponse()->willReturn($response);

        $response->headers = $responseHeaderBag;
        $responseHeaderBag
            ->setCookie(Argument::that(function (Cookie $cookie) {
                if ($cookie->getName() !== '_channel_code') {
                    return false;
                }

                if ($cookie->getValue() !== 'fake_channel_code') {
                    return false;
                }

                return true;
            }))
            ->shouldBeCalled()
        ;

        $this->onKernelResponse($filterResponseEvent);
    }
}
