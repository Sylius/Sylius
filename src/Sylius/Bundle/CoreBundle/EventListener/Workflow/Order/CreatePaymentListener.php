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

namespace Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use Sylius\Component\Contracts\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class CreatePaymentListener
{
    public function __construct(private StateMachineInterface $compositeStateMachine)
    {
    }

    public function __invoke(CompletedEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $payments = $order->getPayments();

        foreach ($payments as $payment) {
            $this->compositeStateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CREATE);
        }
    }
}
