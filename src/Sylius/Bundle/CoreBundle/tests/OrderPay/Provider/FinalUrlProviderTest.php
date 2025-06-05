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

namespace Tests\Sylius\Bundle\CoreBundle\OrderPay\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\OrderPay\Processor\RouteParametersProcessorInterface;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\FinalUrlProvider;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class FinalUrlProviderTest extends TestCase
{
    private MockObject&RouteParametersProcessorInterface $routeParametersProcessor;

    private FinalUrlProvider $provider;

    protected function setUp(): void
    {
        $this->routeParametersProcessor = $this->createMock(RouteParametersProcessorInterface::class);

        $this->provider = new FinalUrlProvider(
            $this->routeParametersProcessor,
            'final_route',
            [],
            'retry_route',
            [],
        );
    }

    public function testProvidesAFinalUrlFromNullPayment(): void
    {
        $this->routeParametersProcessor
            ->expects($this->once())
            ->method('process')
            ->with(
                'final_route',
                [],
                UrlGeneratorInterface::ABSOLUTE_PATH,
                ['payment' => null, 'order' => null],
            )
            ->willReturn('/final_route')
        ;

        $result = $this->provider->getUrl(null);
        $this->assertSame('/final_route', $result);
    }

    public function testProvidesAFinalUrlFromPaymentWithStateComplete(): void
    {
        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getOrder')->willReturn(null);
        $payment->method('getState')->willReturn(BasePaymentInterface::STATE_COMPLETED);

        $this->routeParametersProcessor
            ->expects($this->once())
            ->method('process')
            ->with(
                'final_route',
                [],
                UrlGeneratorInterface::ABSOLUTE_PATH,
                ['payment' => $payment, 'order' => null],
            )
            ->willReturn('/final_route')
        ;

        $result = $this->provider->getUrl($payment);
        $this->assertSame('/final_route', $result);
    }

    public function testProvidesAFinalUrlFromPaymentWithStateAuthorized(): void
    {
        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getOrder')->willReturn(null);
        $payment->method('getState')->willReturn(BasePaymentInterface::STATE_AUTHORIZED);

        $this->routeParametersProcessor
            ->expects($this->once())
            ->method('process')
            ->with(
                'final_route',
                [],
                UrlGeneratorInterface::ABSOLUTE_PATH,
                ['payment' => $payment, 'order' => null],
            )
            ->willReturn('/final_route')
        ;

        $result = $this->provider->getUrl($payment);
        $this->assertSame('/final_route', $result);
    }

    public function testProvidesARetryUrlFromPaymentWithStateCancelled(): void
    {
        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getOrder')->willReturn(null);
        $payment->method('getState')->willReturn(BasePaymentInterface::STATE_CANCELLED);

        $this->routeParametersProcessor
            ->expects($this->once())
            ->method('process')
            ->with(
                'retry_route',
                [],
                UrlGeneratorInterface::ABSOLUTE_PATH,
                ['payment' => $payment, 'order' => null],
            )
            ->willReturn('/retry_route')
        ;

        $result = $this->provider->getUrl($payment);
        $this->assertSame('/retry_route', $result);
    }
}
