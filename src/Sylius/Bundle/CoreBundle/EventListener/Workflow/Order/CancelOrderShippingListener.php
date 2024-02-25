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

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class CancelOrderShippingListener
{
    public function __construct(private StateMachineInterface $compositeOrderStateMachine)
    {
    }

    public function __invoke(CompletedEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        if ($this->compositeOrderStateMachine->can($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_CANCEL)) {
            $this->compositeOrderStateMachine->apply($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_CANCEL);
        }
    }
}
