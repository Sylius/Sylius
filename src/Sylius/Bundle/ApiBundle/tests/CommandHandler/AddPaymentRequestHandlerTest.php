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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Bundle\ApiBundle\CommandHandler\Payment\AddPaymentRequestHandler;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\PaymentNotFoundException;
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\DefaultPayloadProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class AddPaymentRequestHandlerTest extends TestCase
{
    private MockObject&PaymentMethodRepositoryInterface $paymentMethodRepository;

    private MockObject&PaymentRepositoryInterface $paymentRepository;

    private MockObject&PaymentRequestFactoryInterface $paymentRequestFactory;

    private MockObject&PaymentRequestRepositoryInterface $paymentRequestRepository;

    private DefaultActionProviderInterface&MockObject $defaultActionProvider;

    private DefaultPayloadProviderInterface&MockObject $defaultPayloadProvider;

    private AddPaymentRequestHandler $addPaymentRequestHandler;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->paymentRequestFactory = $this->createMock(PaymentRequestFactoryInterface::class);
        $this->paymentRequestRepository = $this->createMock(PaymentRequestRepositoryInterface::class);
        $this->defaultActionProvider = $this->createMock(DefaultActionProviderInterface::class);
        $this->defaultPayloadProvider = $this->createMock(DefaultPayloadProviderInterface::class);

        $this->addPaymentRequestHandler = new AddPaymentRequestHandler(
            $this->paymentMethodRepository,
            $this->paymentRepository,
            $this->paymentRequestFactory,
            $this->paymentRequestRepository,
            $this->defaultActionProvider,
            $this->defaultPayloadProvider,
        );
    }

    #[Test]
    public function it_throws_an_exception_if_there_is_no_payment_for_given_id_and_order_token_value(): void
    {
        self::expectException(PaymentNotFoundException::class);

        $this->paymentRepository->method('findOneByOrderToken')->with(1, 'token')->willReturn(null);

        $this->addPaymentRequestHandler->__invoke(new AddPaymentRequest('token', 1, 'bank_transfer'));
    }

    #[Test]
    public function it_throws_an_exception_if_there_is_no_payment_method_for_given_code(): void
    {
        self::expectException(PaymentMethodNotFoundException::class);

        $payment = $this->createMock(PaymentInterface::class);

        $this->paymentRepository->method('findOneByOrderToken')->with(1, 'token')->willReturn($payment);
        $this->paymentMethodRepository->method('findOneBy')->with(['code' => 'bank_transfer'])->willReturn(null);

        $this->addPaymentRequestHandler->__invoke(new AddPaymentRequest('token', 1, 'bank_transfer'));
    }

    #[Test]
    public function it_creates_a_payment_request(): void
    {
        $payment = $this->createMock(PaymentInterface::class);
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $paymentRequest = $this->createMock(PaymentRequestInterface::class);

        $this->paymentRepository->method('findOneByOrderToken')->with(1, 'token')->willReturn($payment);
        $this->paymentMethodRepository->method('findOneBy')->with(['code' => 'bank_transfer'])->willReturn($paymentMethod);
        $this->defaultActionProvider->method('getAction')->with($paymentRequest)->willReturn('authorize');
        $this->defaultPayloadProvider->method('getPayload')->with($paymentRequest)->willReturn(['foo' => 'bar']);

        $this->paymentRequestFactory->method('create')->with($payment, $paymentMethod)->willReturn($paymentRequest);
        $paymentRequest->expects($this->once())->method('setAction')->with('authorize');
        $paymentRequest->expects($this->once())->method('setPayload')->with(['foo' => 'bar']);

        self::assertSame(
            $paymentRequest,
            $this->addPaymentRequestHandler->__invoke(
                new AddPaymentRequest('token', 1, 'bank_transfer'),
            ),
        );
    }
}
