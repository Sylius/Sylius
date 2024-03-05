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

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Action\PlaceholderAction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ChangePaymentMethodEventSubscriberSpec extends ObjectBehavior
{
    function let(PaymentRequestRepositoryInterface $paymentRequestRepository): void
    {
        $this->beConstructedWith($paymentRequestRepository);
    }

    function it_updates_payments_request_for_change_payment_method_command(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        HttpKernelInterface $kernel,
        PaymentRequestInterface $paymentRequest,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_PATCH);

        $changePaymentMethod = new ChangePaymentMethod('paypal');
        $changePaymentMethod->paymentId = 1;
        $paymentRequestRepository->findAllByPaymentId(1)->willReturn([$paymentRequest]);
        $paymentRequest->getState()->willReturn(PaymentRequestInterface::STATE_NEW);
        $paymentRequest->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getCode()->willReturn('stripe');
        $paymentRequest->setState(PaymentRequestInterface::STATE_CANCELLED)->shouldBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $changePaymentMethod,
            ],
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        ));
    }

    function it_updates_payments_request_for_choose_payment_method_command(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        ChoosePaymentMethod $choosePaymentMethod,
        HttpKernelInterface $kernel,
        PaymentRequestInterface $paymentRequest,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_PATCH);

        $choosePaymentMethod->getPaymentMethodCode()->willReturn('paypal');
        $choosePaymentMethod->paymentId = 1;
        $paymentRequestRepository->findAllByPaymentId(1)->willReturn([$paymentRequest]);
        $paymentRequest->getState()->willReturn(PaymentRequestInterface::STATE_NEW);
        $paymentRequest->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getCode()->willReturn('stripe');
        $paymentRequest->setState(PaymentRequestInterface::STATE_CANCELLED)->shouldBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $choosePaymentMethod->getWrappedObject(),
            ],
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        ));
    }

    function it_does_not_update_payment_request_state_if_payment_method_code_is_the_same(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        HttpKernelInterface $kernel,
        PaymentRequestInterface $paymentRequest,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_PATCH);

        $choosePaymentMethod = new ChoosePaymentMethod('stripe');
        $choosePaymentMethod->paymentId = 1;
        $paymentRequestRepository->findAllByPaymentId(1)->willReturn([$paymentRequest]);
        $paymentRequest->getState()->willReturn(PaymentRequestInterface::STATE_NEW);
        $paymentRequest->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getCode()->willReturn('stripe');
        $paymentRequest->setState(PaymentRequestInterface::STATE_CANCELLED)->shouldNotBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $choosePaymentMethod,
            ],
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        ));
    }

    function it_does_not_update_payment_request_state_if_it_is_different_state_than_new(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        ChoosePaymentMethod $choosePaymentMethod,
        HttpKernelInterface $kernel,
        PaymentRequestInterface $paymentRequest,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_PATCH);

        $choosePaymentMethod->getPaymentMethodCode()->willReturn('paypal');
        $choosePaymentMethod->paymentId = 1;
        $paymentRequestRepository->findAllByPaymentId(1)->willReturn([$paymentRequest]);
        $paymentRequest->getState()->willReturn(PaymentRequestInterface::STATE_COMPLETED);
        $paymentRequest->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getCode()->willReturn('stripe');
        $paymentRequest->setState(PaymentRequestInterface::STATE_CANCELLED)->shouldNotBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $choosePaymentMethod->getWrappedObject(),
            ],
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        ));
    }

    function it_does_not_update_payment_request_state_for_non_patch_requests(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        ChoosePaymentMethod $choosePaymentMethod,
        HttpKernelInterface $kernel,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $paymentRequestRepository->findAllByPaymentId(Argument::any())->shouldNotBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $choosePaymentMethod->getWrappedObject(),
            ],
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        ));
    }

    function it_does_not_update_payment_request_state_for_different_commands(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        SendContactRequest $sendContactRequest,
        HttpKernelInterface $kernel,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_PATCH);
        $paymentRequestRepository->findAllByPaymentId(Argument::any())->shouldNotBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $sendContactRequest->getWrappedObject(),
            ],
            $request,
            HttpKernelInterface::MASTER_REQUEST,
        ));
    }
}
