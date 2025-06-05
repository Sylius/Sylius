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

namespace Tests\Sylius\Bundle\PayumBundle\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Authorize;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PayumBundle\Action\AuthorizePaymentAction;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class AuthorizePaymentActionTest extends TestCase
{
    private MockObject&PaymentDescriptionProviderInterface $paymentDescriptionProvider;

    private AuthorizePaymentAction $authorizePaymentAction;

    private Authorize&MockObject $authorize;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentDescriptionProvider = $this->createMock(PaymentDescriptionProviderInterface::class);
        $this->authorizePaymentAction = new AuthorizePaymentAction($this->paymentDescriptionProvider);
        $this->authorize = $this->createMock(Authorize::class);
    }

    public function testThrowExceptionWhenUnsupportedRequest(): void
    {
        $this->authorize->method('getModel')->willReturn(new \stdClass());

        self::expectException(RequestNotSupportedException::class);

        $this->authorizePaymentAction->execute($this->authorize);
    }

    public function testPerformBasicAuthorize(): void
    {
        /** @var GatewayInterface&MockObject $gateway */
        $gateway = $this->createMock(GatewayInterface::class);
        /** @var PaymentInterface&MockObject $payment */
        $payment = $this->createMock(PaymentInterface::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);

        $this->authorizePaymentAction->setGateway($gateway);

        $payment->expects(self::once())->method('getOrder')->willReturn($order);

        $payment->expects(self::once())->method('getDetails')->willReturn([]);

        $this->authorize->expects(self::any())->method('getModel')->willReturn($payment);

        $payment->expects(self::once())->method('setDetails')->with([]);

        $this->authorize->expects(self::once())->method('setModel')->with(new ArrayObject());

        $this->authorizePaymentAction->execute($this->authorize);
    }
}
