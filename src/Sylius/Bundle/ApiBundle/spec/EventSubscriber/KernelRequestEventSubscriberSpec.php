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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class KernelRequestEventSubscriberSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(true, '/api/v2');
    }

    function it_does_nothing_if_api_is_enabled(
        RequestEvent $event,
        Request $request,
        HttpKernelInterface $kernel
    ): void {
        $event->getRequest()->willReturn($request);

        $request->getPathInfo()->willReturn('/api/v2/any-endpoint');

        $this->validateApi(new RequestEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST
        ));
    }

    function it_throws_not_found_exception_if_api_is_disabled(
        RequestEvent $event,
        Request $request,
        HttpKernelInterface $kernel
    ): void {
        $this->beConstructedWith(false, '/api/v2');

        $event->getRequest()->willReturn($request);

        $request->getPathInfo()->willReturn('/api/v2/any-endpoint');

        $this
            ->shouldThrow(NotFoundHttpException::class)
            ->during(
                'validateApi',
                [
                    new RequestEvent(
                        $kernel->getWrappedObject(),
                        $request->getWrappedObject(),
                        HttpKernelInterface::MASTER_REQUEST
                    )
                ]
            );
    }

    function it_does_nothing_for_non_api_endpoints_when_api_is_disabled(
        RequestEvent $event,
        Request $request,
        HttpKernelInterface $kernel
    ): void {
        $this->beConstructedWith(false, '/api/v2');

        $event->getRequest()->willReturn($request);

        $request->getPathInfo()->willReturn('/');

        $this->validateApi(new RequestEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST
        ));
    }
}
