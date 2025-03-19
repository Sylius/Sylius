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

namespace Sylius\Bundle\ApiBundle\Tests\CommandHandler;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
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
    use ProphecyTrait;

    private ObjectProphecy|PaymentMethodRepositoryInterface $paymentMethodRepository;

    private ObjectProphecy|PaymentRepositoryInterface $paymentRepository;

    private ObjectProphecy|PaymentRequestFactoryInterface $paymentRequestFactory;

    private ObjectProphecy|PaymentRequestRepositoryInterface $paymentRequestRepository;

    private DefaultActionProviderInterface|ObjectProphecy $defaultActionProvider;

    private DefaultPayloadProviderInterface|ObjectProphecy $defaultPayloadProvider;

    private AddPaymentRequestHandler $addPaymentRequestHandler;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = $this->prophesize(PaymentMethodRepositoryInterface::class);
        $this->paymentRepository = $this->prophesize(PaymentRepositoryInterface::class);
        $this->paymentRequestFactory = $this->prophesize(PaymentRequestFactoryInterface::class);
        $this->paymentRequestRepository = $this->prophesize(PaymentRequestRepositoryInterface::class);
        $this->defaultActionProvider = $this->prophesize(DefaultActionProviderInterface::class);
        $this->defaultPayloadProvider = $this->prophesize(DefaultPayloadProviderInterface::class);

        $this->addPaymentRequestHandler = new AddPaymentRequestHandler(
            $this->paymentMethodRepository->reveal(),
            $this->paymentRepository->reveal(),
            $this->paymentRequestFactory->reveal(),
            $this->paymentRequestRepository->reveal(),
            $this->defaultActionProvider->reveal(),
            $this->defaultPayloadProvider->reveal(),
        );
    }

    /** @test */
    public function it_throws_an_exception_if_there_is_no_payment_for_given_id_and_order_token_value(): void
    {
        $this->expectException(PaymentNotFoundException::class);

        $this->paymentRepository->findOneByOrderToken(1, 'token')->willReturn(null);

        $this->addPaymentRequestHandler->__invoke(new AddPaymentRequest('token', 1, 'bank_transfer'));
    }

    /** @test */
    public function it_throws_an_exception_if_there_is_no_payment_method_for_given_code(): void
    {
        $this->expectException(PaymentMethodNotFoundException::class);

        $payment = $this->prophesize(PaymentInterface::class);

        $this->paymentRepository->findOneByOrderToken(1, 'token')->willReturn($payment->reveal());
        $this->paymentMethodRepository->findOneBy(['code' => 'bank_transfer'])->willReturn(null);

        $this->addPaymentRequestHandler->__invoke(new AddPaymentRequest('token', 1, 'bank_transfer'));
    }

    /** @test */
    public function it_creates_a_payment_request(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentRequest = $this->prophesize(PaymentRequestInterface::class);

        $this->paymentRepository->findOneByOrderToken(1, 'token')->willReturn($payment->reveal());
        $this->paymentMethodRepository->findOneBy(['code' => 'bank_transfer'])->willReturn($paymentMethod->reveal());
        $this->defaultActionProvider->getAction($paymentRequest)->willReturn('authorize');
        $this->defaultPayloadProvider->getPayload($paymentRequest)->willReturn(['foo' => 'bar']);

        $this->paymentRequestFactory->create($payment->reveal(), $paymentMethod->reveal())->willReturn($paymentRequest->reveal());
        $paymentRequest->setAction('authorize')->shouldBeCalled();
        $paymentRequest->setPayload(['foo' => 'bar'])->shouldBeCalled();

        self::assertSame(
            $paymentRequest->reveal(),
            $this->addPaymentRequestHandler->__invoke(
                new AddPaymentRequest('token', 1, 'bank_transfer'),
            ),
        );
    }
}
