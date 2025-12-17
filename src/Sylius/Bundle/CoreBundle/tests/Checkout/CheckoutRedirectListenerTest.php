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

namespace Tests\Sylius\Bundle\CoreBundle\Checkout;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutRedirectListener;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class CheckoutRedirectListenerTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    private CheckoutStateUrlGeneratorInterface&MockObject $checkoutStateUrlGenerator;

    private MockObject&RequestMatcherInterface $requestMatcher;

    private CheckoutRedirectListener $checkoutRedirectListener;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->checkoutStateUrlGenerator = $this->createMock(CheckoutStateUrlGeneratorInterface::class);
        $this->requestMatcher = $this->createMock(RequestMatcherInterface::class);
        $this->checkoutRedirectListener = new CheckoutRedirectListener($this->requestStack, $this->checkoutStateUrlGenerator, $this->requestMatcher);
    }

    public function testRedirectsToProperRouteBasedOnOrderCheckoutState(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $request = $this->createMock(Request::class);
        $resourceControllerEvent = $this->createMock(GenericEvent::class);

        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $request->attributes = new ParameterBag(['_sylius' => []]);

        $resourceControllerEvent->expects($this->once())->method('getSubject')->willReturn($order);

        $this->checkoutStateUrlGenerator
            ->expects($this->once())
            ->method('generateForOrderCheckoutState')
            ->with($order)
            ->willReturn('https://redirect-path')
        ;

        $resourceControllerEvent
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(RedirectResponse::class))
        ;

        $this->checkoutRedirectListener->handleCheckoutRedirect($resourceControllerEvent);
    }

    public function testDoesNothingIfCurrentRequestIsNotCheckoutRequest(): void
    {
        $request = $this->createMock(Request::class);
        $resourceControllerEvent = $this->createMock(GenericEvent::class);

        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(false)
        ;

        $resourceControllerEvent->expects($this->never())->method('getSubject');

        $this->checkoutRedirectListener->handleCheckoutRedirect($resourceControllerEvent);
    }

    public function testDoesNothingIfCurrentRequestHasRedirectConfigured(): void
    {
        $request = $this->createMock(Request::class);
        $resourceControllerEvent = $this->createMock(GenericEvent::class);

        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $request->attributes = new ParameterBag(['_sylius' => ['redirect' => 'redirect_route']]);

        $resourceControllerEvent->expects($this->never())->method('getSubject');

        $this->checkoutRedirectListener->handleCheckoutRedirect($resourceControllerEvent);
    }

    public function testThrowsExceptionIfEventSubjectIsNotAnOrder(): void
    {
        $request = $this->createMock(Request::class);
        $resourceControllerEvent = $this->createMock(GenericEvent::class);

        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $request->attributes = new ParameterBag(['_sylius' => []]);

        $resourceControllerEvent
            ->expects($this->once())
            ->method('getSubject')
            ->willReturn('bad-object')
        ;

        $this->expectException(InvalidArgumentException::class);

        $this->checkoutRedirectListener->handleCheckoutRedirect($resourceControllerEvent);
    }
}
