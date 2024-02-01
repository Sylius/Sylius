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

namespace Sylius\Bundle\PaymentBundle\Processor;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Webmozart\Assert\Assert;

final class AfterOfflineCaptureProcessor implements AfterOfflineCaptureProcessorInterface
{
    public function __construct(
        private StateMachineFactoryInterface $stateMachineFactory,
    ) {
    }

    public function process(PaymentRequestInterface $paymentRequest): void
    {
        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);
        if ($paymentRequest->getResponseData()['paid']) {
            $stateMachine->apply(PaymentTransitions::TRANSITION_COMPLETE);
        } else {
            $stateMachine->apply(PaymentTransitions::TRANSITION_PROCESS);
        }
    }
}
