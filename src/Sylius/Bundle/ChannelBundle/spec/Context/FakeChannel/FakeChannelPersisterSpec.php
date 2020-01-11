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
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class FakeChannelPersisterSpec extends ObjectBehavior
{
    function let(FakeChannelCodeProviderInterface $fakeHostnameProvider): void
    {
        $this->beConstructedWith($fakeHostnameProvider);
    }

    function it_applies_only_to_master_requests(ResponseEvent $responseEvent): void
    {
        $responseEvent->getRequestType()->willReturn(HttpKernelInterface::SUB_REQUEST);

        $responseEvent->getRequest()->shouldNotBeCalled();
        $responseEvent->getResponse()->shouldNotBeCalled();

        $this->onKernelResponse($responseEvent);
    }

    function it_applies_only_for_request_with_fake_channel_code(
        FakeChannelCodeProviderInterface $fakeHostnameProvider,
        ResponseEvent $responseEvent,
        Request $request
    ): void {
        $responseEvent->getRequestType()->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $responseEvent->getRequest()->willReturn($request);

        $fakeHostnameProvider->getCode($request)->willReturn(null);

        $responseEvent->getResponse()->shouldNotBeCalled();

        $this->onKernelResponse($responseEvent);
    }

    function it_persists_fake_channel_codes_in_a_cookie(
        FakeChannelCodeProviderInterface $fakeHostnameProvider,
        ResponseEvent $responseEvent,
        Request $request,
        Response $response,
        ResponseHeaderBag $responseHeaderBag
    ): void {
        $responseEvent->getRequestType()->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $responseEvent->getRequest()->willReturn($request);

        $fakeHostnameProvider->getCode($request)->willReturn('fake_channel_code');

        $responseEvent->getResponse()->willReturn($response);

        $response->headers = $responseHeaderBag;
        $responseHeaderBag
            ->setCookie(Argument::that(function (Cookie $cookie) {
                return $cookie->getName() === '_channel_code' && $cookie->getValue() === 'fake_channel_code';
            }))
            ->shouldBeCalled()
        ;

        $this->onKernelResponse($responseEvent);
    }
}
