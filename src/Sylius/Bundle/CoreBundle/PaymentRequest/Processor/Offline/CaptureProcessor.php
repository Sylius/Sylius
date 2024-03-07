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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Processor\Offline;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class CaptureProcessor implements CaptureProcessorInterface
{
    public function __construct(private readonly StateMachineInterface $stateMachine)
    {
    }

    public function process(PaymentRequestInterface $paymentRequest): void
    {
        $payment = $paymentRequest->getPayment();

        if ($this->stateMachine->can($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_PROCESS)) {
            $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_PROCESS);
        }

        $paymentRequest->setState(PaymentRequestInterface::STATE_COMPLETED);
    }
}
