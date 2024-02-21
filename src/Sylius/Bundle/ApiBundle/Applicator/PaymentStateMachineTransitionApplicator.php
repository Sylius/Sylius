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

namespace Sylius\Bundle\ApiBundle\Applicator;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class PaymentStateMachineTransitionApplicator implements PaymentStateMachineTransitionApplicatorInterface
{
    public function __construct(private StateMachineFactoryInterface|StateMachineInterface $stateMachineFactory)
    {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            trigger_deprecation(
                'sylius/api-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the first argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    StateMachineFactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function complete(PaymentInterface $data): PaymentInterface
    {
        $this->applyTransition($data, PaymentTransitions::TRANSITION_COMPLETE);

        return $data;
    }

    private function applyTransition(PaymentInterface $payment, string $transition): void
    {
        $stateMachine = $this->getStateMachine();

        if (false === $stateMachine->can($payment, PaymentTransitions::GRAPH, $transition)) {
            throw new StateMachineTransitionFailedException('Cannot complete the payment.');
        }

        $stateMachine->apply($payment, PaymentTransitions::GRAPH, $transition);
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
