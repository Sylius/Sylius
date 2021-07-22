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

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class KernelRequestEventSubscriberSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(true, '/new-api');
    }

    function it_does_nothing_if_api_is_enabled(
        RequestEvent $event,
        Request $request,
        HttpKernelInterface $kernel
    ): void {
        $event->getRequest()->willReturn($request);

        $request->getPathInfo()->willReturn('/new-api/any-endpoint');

        $event->setResponse(new Response('Route not found', 404))->shouldNotBeCalled();

        $this->validateApi($event);
    }

    function it_returns_404_if_api_is_disabled(
        RequestEvent $event,
        Request $request,
        HttpKernelInterface $kernel
    ): void {
        $this->beConstructedWith(false, '/new-api');

        $event->getRequest()->willReturn($request);

        $request->getPathInfo()->willReturn('/new-api/any-endpoint');

        $event->setResponse(new Response('Route not found', 404))->shouldBeCalled();

        $this->validateApi($event);
    }

    function it_does_nothing_for_non_api_endpoints_when_api_is_disabled(
        RequestEvent $event,
        Request $request,
        HttpKernelInterface $kernel
    ): void {
        $this->beConstructedWith(false, '/new-api');

        $event->getRequest()->willReturn($request);

        $request->getPathInfo()->willReturn('/');

        $event->setResponse(new Response('Route not found', 404))->shouldNotBeCalled();

        $this->validateApi($event);
    }
}
