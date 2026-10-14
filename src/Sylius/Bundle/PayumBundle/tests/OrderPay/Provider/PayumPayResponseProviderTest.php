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

namespace Tests\Sylius\Bundle\PayumBundle\OrderPay\Provider;

use Payum\Core\Payum;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\OrderPay\Resolver\PaymentToPayResolverInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfig;
use Sylius\Bundle\PayumBundle\OrderPay\Provider\PayumPayResponseProvider;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class PayumPayResponseProviderTest extends TestCase
{
    private MockObject&Payum $payum;

    private MockObject&PaymentToPayResolverInterface $paymentToPayResolver;

    private GenericTokenFactoryInterface&MockObject $tokenFactory;

    private PayumPayResponseProvider $provider;

    protected function setUp(): void
    {
        $this->payum = $this->createMock(Payum::class);
        $this->paymentToPayResolver = $this->createMock(PaymentToPayResolverInterface::class);
        $this->tokenFactory = $this->createMock(GenericTokenFactoryInterface::class);

        $this->provider = new PayumPayResponseProvider(
            $this->payum,
            $this->paymentToPayResolver,
            'app_shop_order_after_pay',
            ['tokenValue' => 'fixed-token-value'],
        );
    }

    public function testItCreatesCaptureTokenWithConfiguredAfterPayRouteAndParameters(): void
    {
        $order = $this->createOrder();
        $payment = $this->createPaymentWithGatewayConfig(['use_authorize' => false]);
        $token = $this->createMock(TokenInterface::class);

        $this->paymentToPayResolver
            ->expects(self::once())
            ->method('getPayment')
            ->with($order)
            ->willReturn($payment)
        ;
        $this->payum->expects(self::once())->method('getTokenFactory')->willReturn($this->tokenFactory);
        $this->tokenFactory
            ->expects(self::once())
            ->method('createCaptureToken')
            ->with(
                'offline',
                $payment,
                'app_shop_order_after_pay',
                ['tokenValue' => 'fixed-token-value'],
            )
            ->willReturn($token)
        ;
        $this->tokenFactory->expects(self::never())->method('createAuthorizeToken');
        $token->expects(self::once())->method('getTargetUrl')->willReturn('/payum/capture');

        $response = $this->provider->getResponse(new Request(), $order);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/payum/capture', $response->getTargetUrl());
    }

    public function testItCreatesAuthorizeTokenWithConfiguredAfterPayRouteAndParameters(): void
    {
        $order = $this->createOrder();
        $payment = $this->createPaymentWithGatewayConfig(['use_authorize' => true]);
        $token = $this->createMock(TokenInterface::class);

        $this->paymentToPayResolver
            ->expects(self::once())
            ->method('getPayment')
            ->with($order)
            ->willReturn($payment)
        ;
        $this->payum->expects(self::once())->method('getTokenFactory')->willReturn($this->tokenFactory);
        $this->tokenFactory
            ->expects(self::once())
            ->method('createAuthorizeToken')
            ->with(
                'offline',
                $payment,
                'app_shop_order_after_pay',
                ['tokenValue' => 'fixed-token-value'],
            )
            ->willReturn($token)
        ;
        $this->tokenFactory->expects(self::never())->method('createCaptureToken');
        $token->expects(self::once())->method('getTargetUrl')->willReturn('/payum/authorize');

        $response = $this->provider->getResponse(new Request(), $order);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/payum/authorize', $response->getTargetUrl());
    }

    public function testItSupportsOrdersWithPayumEnabledPayment(): void
    {
        $order = $this->createOrder();
        $payment = $this->createPaymentWithGatewayConfig([]);

        $this->paymentToPayResolver
            ->expects(self::once())
            ->method('getPayment')
            ->with($order)
            ->willReturn($payment)
        ;

        self::assertTrue($this->provider->supports(new Request(), $order));
    }

    public function testItDoesNotSupportOrdersWithPayumDisabledPayment(): void
    {
        $order = $this->createOrder();
        $gatewayConfig = new GatewayConfig();
        $gatewayConfig->setUsePayum(false);
        $payment = $this->createPayment($gatewayConfig);

        $this->paymentToPayResolver
            ->expects(self::once())
            ->method('getPayment')
            ->with($order)
            ->willReturn($payment)
        ;

        self::assertFalse($this->provider->supports(new Request(), $order));
    }

    private function createOrder(): MockObject&OrderInterface
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getId')->willReturn(1);

        return $order;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createPaymentWithGatewayConfig(array $config): MockObject&PaymentInterface
    {
        $gatewayConfig = new GatewayConfig();
        $gatewayConfig->setGatewayName('offline');
        $gatewayConfig->setConfig($config);

        return $this->createPayment($gatewayConfig);
    }

    private function createPayment(GatewayConfig $gatewayConfig): MockObject&PaymentInterface
    {
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $paymentMethod->method('getGatewayConfig')->willReturn($gatewayConfig);

        $payment = $this->createMock(PaymentInterface::class);
        $payment->method('getMethod')->willReturn($paymentMethod);

        return $payment;
    }
}
