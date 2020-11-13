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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Changer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\AbstractPaymentMethod;use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class CommandPaymentMethodChangerSpec extends ObjectBehavior
{
    function let(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository
    ): void {
        $this->beConstructedWith($paymentRepository, $paymentMethodRepository);
    }

    function it_throws_an_exception_if_payment_method_with_given_code_has_not_been_found(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        OrderInterface $order
    ): void {
        $abstractPaymentMethod = new class extends AbstractPaymentMethod {
            public function __construct()
            {
                parent::__construct('CASH_ON_DELIVERY_METHOD');
            }
        };
        $abstractPaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $abstractPaymentMethod->setSubresourceId('123');

        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);

        $paymentRepository->findOneByOrderId(Argument::type(AbstractPaymentMethod::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('changePaymentMethod', [$abstractPaymentMethod, $order])
        ;
    }

    function it_throws_an_exception_if_payment_with_given_id_has_not_been_found(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        OrderInterface $order
    ): void {
        $abstractPaymentMethod = new class extends AbstractPaymentMethod {
            public function __construct()
            {
                parent::__construct('CASH_ON_DELIVERY_METHOD');
            }
        };
        $abstractPaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $abstractPaymentMethod->setSubresourceId('123');

        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $order->getId()->willReturn('444');

        $paymentRepository->findOneByOrderId('123', '444')->willReturn(null);

        $order->getState()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('changePaymentMethod', [$abstractPaymentMethod, $order])
        ;
    }

    function it_throws_an_exception_if_payment_is_in_different_state_than_new(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
        OrderInterface $order
    ): void {
        $abstractPaymentMethod = new class extends AbstractPaymentMethod {
            public function __construct()
            {
                parent::__construct('CASH_ON_DELIVERY_METHOD');
            }
        };
        $abstractPaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $abstractPaymentMethod->setSubresourceId('123');

        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $order->getId()->willReturn('444');

        $paymentRepository->findOneByOrderId('123', '444')->willReturn(null);

        $order->getState()->willReturn(OrderInterface::STATE_FULFILLED);

        $payment->setMethod($paymentMethod)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('changePaymentMethod', [$abstractPaymentMethod, $order])
        ;
    }

    function it_changes_payment_method_to_specified_payment(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
        OrderInterface $order
    ): void {
        $abstractPaymentMethod = new class extends AbstractPaymentMethod {
            public function __construct()
            {
                parent::__construct('CASH_ON_DELIVERY_METHOD');
            }
        };
        $abstractPaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $abstractPaymentMethod->setSubresourceId('123');

        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $order->getId()->willReturn('444');

        $paymentRepository->findOneByOrderId('123', '444')->willReturn($payment);

        $order->getState()->willReturn(OrderInterface::STATE_NEW);

        $payment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $payment->setMethod($paymentMethod)->shouldBeCalled();

        $this->changePaymentMethod($abstractPaymentMethod, $order)->shouldReturn($order);
    }
}
