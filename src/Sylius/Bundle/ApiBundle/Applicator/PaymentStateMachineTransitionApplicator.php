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

namespace Sylius\Bundle\ApiBundle\Applicator;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class PaymentStateMachineTransitionApplicator implements PaymentStateMachineTransitionApplicatorInterface
{
    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    public function __construct(StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function complete(PaymentInterface $data): PaymentInterface
    {
        $this->applyTransition($data, PaymentTransitions::TRANSITION_COMPLETE);

        return $data;
    }

    private function applyTransition(PaymentInterface $payment, string $transition): void
    {
        $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);
        $stateMachine->apply($transition);
    }
}
