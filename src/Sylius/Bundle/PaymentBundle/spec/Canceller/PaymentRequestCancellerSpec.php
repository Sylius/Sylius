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

namespace spec\Sylius\Bundle\PaymentBundle\Canceller;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class PaymentRequestCancellerSpec extends ObjectBehavior
{
    function let(PaymentRequestRepositoryInterface $paymentRequestRepository, StateMachineInterface $stateMachine, ObjectManager $objectManager): void
    {
        $this->beConstructedWith($paymentRequestRepository, $stateMachine, $objectManager, [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING]);
    }

    function it_cancels_payment_requests_if_the_payment_method_code_is_different(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        PaymentRequestInterface $paymentRequest1,
        PaymentRequestInterface $paymentRequest2,
        PaymentMethodInterface $paymentMethod1,
        PaymentMethodInterface $paymentMethod2,
        StateMachineInterface $stateMachine,
        ObjectManager $objectManager,
    ): void {
        $paymentRequestRepository
            ->findByPaymentIdAndStates(1, [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING])
            ->willReturn([$paymentRequest1, $paymentRequest2])
        ;

        $paymentRequest1->getMethod()->willReturn($paymentMethod1);
        $paymentMethod1->getCode()->willReturn('payment_method_with_different_code');
        $paymentRequest2->getMethod()->willReturn($paymentMethod2);
        $paymentMethod2->getCode()->willReturn('payment_method_code');

        $stateMachine->apply($paymentRequest1, 'sylius_payment_request', 'cancel')->shouldBeCalled();
        $stateMachine->apply($paymentRequest2, 'sylius_payment_request', 'cancel')->shouldNotBeCalled();
        $objectManager->persist($paymentRequest1)->shouldBeCalled();
        $objectManager->persist($paymentRequest2)->shouldNotBeCalled();
        $objectManager->flush()->shouldBeCalledOnce();

        $this->cancelPaymentRequests(1, 'payment_method_code');
    }

    function it_does_not_cancel_payment_requests_if_none_found(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        StateMachineInterface $stateMachine,
    ): void {
        $paymentRequestRepository->findByPaymentIdAndStates(1, [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING])
            ->willReturn([]);

        $stateMachine->apply(Argument::cetera())->shouldNotBeCalled();

        $this->cancelPaymentRequests(1, 'payment_method_code');
    }
}
