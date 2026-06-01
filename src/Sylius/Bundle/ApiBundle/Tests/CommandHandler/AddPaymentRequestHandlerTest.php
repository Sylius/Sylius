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
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\PaymentNotFoundException;
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\DefaultPayloadProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
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

    private ObjectProphecy|UserContextInterface $userContext;

    private AddPaymentRequestHandler $addPaymentRequestHandler;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = $this->prophesize(PaymentMethodRepositoryInterface::class);
        $this->paymentRepository = $this->prophesize(PaymentRepositoryInterface::class);
        $this->paymentRequestFactory = $this->prophesize(PaymentRequestFactoryInterface::class);
        $this->paymentRequestRepository = $this->prophesize(PaymentRequestRepositoryInterface::class);
        $this->defaultActionProvider = $this->prophesize(DefaultActionProviderInterface::class);
        $this->defaultPayloadProvider = $this->prophesize(DefaultPayloadProviderInterface::class);
        $this->userContext = $this->prophesize(UserContextInterface::class);

        $this->addPaymentRequestHandler = new AddPaymentRequestHandler(
            $this->paymentMethodRepository->reveal(),
            $this->paymentRepository->reveal(),
            $this->paymentRequestFactory->reveal(),
            $this->paymentRequestRepository->reveal(),
            $this->defaultActionProvider->reveal(),
            $this->defaultPayloadProvider->reveal(),
            $this->userContext->reveal(),
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
    public function it_throws_an_exception_if_the_order_is_not_owned_by_the_logged_in_shop_user(): void
    {
        $this->expectException(PaymentNotFoundException::class);

        $payment = $this->prophesize(PaymentInterface::class);
        $order = $this->prophesize(OrderInterface::class);
        $shopUser = $this->prophesize(ShopUserInterface::class);
        $orderCustomer = $this->prophesize(CustomerInterface::class)->reveal();
        $userCustomer = $this->prophesize(CustomerInterface::class)->reveal();

        $this->paymentRepository->findOneByOrderToken(1, 'token')->willReturn($payment->reveal());
        $payment->getOrder()->willReturn($order->reveal());
        $order->getCustomer()->willReturn($orderCustomer);
        $this->userContext->getUser()->willReturn($shopUser->reveal());
        $shopUser->getCustomer()->willReturn($userCustomer);

        $this->paymentMethodRepository->findOneBy(['code' => 'bank_transfer'])->shouldNotBeCalled();

        $this->addPaymentRequestHandler->__invoke(new AddPaymentRequest('token', 1, 'bank_transfer'));
    }

    /** @test */
    public function it_throws_an_exception_if_an_anonymous_user_creates_a_payment_request_for_a_customer_order(): void
    {
        $this->expectException(PaymentNotFoundException::class);

        $payment = $this->prophesize(PaymentInterface::class);
        $order = $this->prophesize(OrderInterface::class);
        $customer = $this->prophesize(CustomerInterface::class);
        $shopUser = $this->prophesize(ShopUserInterface::class);

        $this->paymentRepository->findOneByOrderToken(1, 'token')->willReturn($payment->reveal());
        $payment->getOrder()->willReturn($order->reveal());
        $order->getCustomer()->willReturn($customer->reveal());
        $customer->getUser()->willReturn($shopUser->reveal());
        $order->isCreatedByGuest()->willReturn(false);
        $this->userContext->getUser()->willReturn(null);

        $this->paymentMethodRepository->findOneBy(['code' => 'bank_transfer'])->shouldNotBeCalled();

        $this->addPaymentRequestHandler->__invoke(new AddPaymentRequest('token', 1, 'bank_transfer'));
    }

    /** @test */
    public function it_throws_an_exception_if_there_is_no_payment_method_for_given_code(): void
    {
        $this->expectException(PaymentMethodNotFoundException::class);

        $payment = $this->prophesize(PaymentInterface::class);
        $order = $this->prophesize(OrderInterface::class);

        $this->paymentRepository->findOneByOrderToken(1, 'token')->willReturn($payment->reveal());
        $payment->getOrder()->willReturn($order->reveal());
        $order->getCustomer()->willReturn(null);
        $this->userContext->getUser()->willReturn(null);
        $this->paymentMethodRepository->findOneBy(['code' => 'bank_transfer'])->willReturn(null);

        $this->addPaymentRequestHandler->__invoke(new AddPaymentRequest('token', 1, 'bank_transfer'));
    }

    /** @test */
    public function it_creates_a_payment_request_for_a_guest_order_requested_by_an_anonymous_user(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $order = $this->prophesize(OrderInterface::class);
        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentRequest = $this->prophesize(PaymentRequestInterface::class);

        $this->paymentRepository->findOneByOrderToken(1, 'token')->willReturn($payment->reveal());
        $payment->getOrder()->willReturn($order->reveal());
        $order->getCustomer()->willReturn(null);
        $this->userContext->getUser()->willReturn(null);
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

    /** @test */
    public function it_creates_a_payment_request_for_an_order_owned_by_the_logged_in_shop_user(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $order = $this->prophesize(OrderInterface::class);
        $shopUser = $this->prophesize(ShopUserInterface::class);
        $customer = $this->prophesize(CustomerInterface::class)->reveal();
        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentRequest = $this->prophesize(PaymentRequestInterface::class);

        $this->paymentRepository->findOneByOrderToken(1, 'token')->willReturn($payment->reveal());
        $payment->getOrder()->willReturn($order->reveal());
        $order->getCustomer()->willReturn($customer);
        $this->userContext->getUser()->willReturn($shopUser->reveal());
        $shopUser->getCustomer()->willReturn($customer);
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
