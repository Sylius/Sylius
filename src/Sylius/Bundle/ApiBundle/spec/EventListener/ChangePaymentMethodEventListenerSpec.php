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

namespace spec\Sylius\Bundle\ApiBundle\EventListener;

use ApiPlatform\Action\PlaceholderAction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Component\Payment\Canceller\PaymentRequestCancellerInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ChangePaymentMethodEventListenerSpec extends ObjectBehavior
{
    function let(PaymentRequestCancellerInterface $paymentRequestCanceller): void
    {
        $this->beConstructedWith($paymentRequestCanceller);
    }

    function it_updates_payments_request_for_change_payment_method_command(
        HttpKernelInterface $kernel,
        PaymentRequestCancellerInterface $paymentRequestCanceller,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_PATCH);

        $changePaymentMethod = new ChangePaymentMethod('token', 1, 'paypal');
        $paymentRequestCanceller->cancelPaymentRequests(1, 'paypal')->shouldBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $changePaymentMethod,
            ],
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        ));
    }

    function it_updates_payments_request_for_choose_payment_method_command(
        PaymentRequestRepositoryInterface $paymentRequestRepository,
        HttpKernelInterface $kernel,
        PaymentRequestCancellerInterface $paymentRequestCanceller,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_PATCH);
        $choosePaymentMethod = new ChoosePaymentMethod('token', 1, 'paypal');

        $paymentRequestCanceller->cancelPaymentRequests(1, 'paypal')->shouldBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $choosePaymentMethod,
            ],
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        ));
    }

    function it_does_not_update_payment_request_state_for_non_patch_requests(
        ChoosePaymentMethod $choosePaymentMethod,
        HttpKernelInterface $kernel,
        PaymentRequestCancellerInterface $paymentRequestCanceller,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $paymentRequestCanceller->cancelPaymentRequests(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $choosePaymentMethod->getWrappedObject(),
            ],
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        ));
    }

    function it_does_not_update_payment_request_state_for_different_commands(
        SendContactRequest $sendContactRequest,
        HttpKernelInterface $kernel,
        PaymentRequestCancellerInterface $paymentRequestCanceller,
    ): void {
        $request = new Request();
        $request->setMethod(Request::METHOD_PATCH);
        $paymentRequestCanceller->cancelPaymentRequests(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->cancelPaymentRequestsWithDifferentPaymentMethod(new ControllerArgumentsEvent(
            $kernel->getWrappedObject(),
            new PlaceholderAction(),
            [
                $sendContactRequest->getWrappedObject(),
            ],
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        ));
    }
}
