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

namespace Sylius\Bundle\PaymentBundle\Processor\Offline;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class AfterCaptureProcessor implements AfterCaptureProcessorInterface
{
    public function __construct(
        private StateMachineFactoryInterface|StateMachineInterface $stateMachineFactory,
    ) {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            trigger_deprecation(
                'sylius/payment-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the first argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    StateMachineFactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function process(PaymentRequestInterface $paymentRequest): void
    {
        $payment = $paymentRequest->getPayment();

        $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);
        if ($paymentRequest->getResponseData()['paid']) {
            $stateMachine->apply(PaymentTransitions::TRANSITION_COMPLETE);
        } else {
            $stateMachine->apply(PaymentTransitions::TRANSITION_PROCESS);
        }
    }
}
