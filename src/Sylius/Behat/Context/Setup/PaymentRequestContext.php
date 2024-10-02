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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class PaymentRequestContext implements Context
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private StateMachineInterface $stateMachine,
    ) {
    }

    /**
     * @Given the payment request action :action has been executed for order :order with the payment method :paymentMethod
     */
    public function thePaymentRequestActionHasBeenExecutedForOrderWithThePaymentMethod(
        string $action,
        OrderInterface $order,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $addPaymentRequest = new AddPaymentRequest(
            paymentId: $order->getLastPayment()->getId(),
            paymentMethodCode: $paymentMethod->getCode(),
            action: $action,
        );

        $this->commandBus->dispatch($addPaymentRequest);
    }

    /**
     * @Given there is (also) a payment request action :action executed for order :order with the payment method :paymentMethod and state :state
     */
    public function thePaymentRequestActionHasBeenExecutedForOrderWithThePaymentMethodAndState(
        string $action,
        OrderInterface $order,
        PaymentMethodInterface $paymentMethod,
        string $state,
    ): void {
        $paymentRequest = $this->paymentRequestFactory->create($order->getLastPayment(), $paymentMethod);

        if ($state !== PaymentRequestInterface::STATE_NEW) {
            $this->stateMachine->apply(
                $paymentRequest,
                PaymentRequestTransitions::GRAPH,
                $this->getTransitionForState($state),
            );
        }

        $paymentRequest->setAction($action);

        $this->paymentRequestRepository->add($paymentRequest);
    }

    private function getTransitionForState(string $state): string
    {
        return match ($state) {
            'completed' => PaymentRequestTransitions::TRANSITION_COMPLETE,
            'processing' => PaymentRequestTransitions::TRANSITION_PROCESS,
            default => throw new \InvalidArgumentException(sprintf('Invalid state "%s" provided.', $state)),
        };
    }
}
