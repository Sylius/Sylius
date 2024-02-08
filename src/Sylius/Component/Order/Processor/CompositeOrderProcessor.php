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

namespace Sylius\Component\Order\Processor;

use Laminas\Stdlib\PriorityQueue;
use Sylius\Component\Order\Model\OrderInterface;

final class CompositeOrderProcessor implements OrderProcessorInterface
{
    /** @var PriorityQueue<OrderProcessorInterface> */
    private PriorityQueue $orderProcessors;

    public function __construct()
    {
        $this->orderProcessors = new PriorityQueue();
    }

    public function addProcessor(OrderProcessorInterface $orderProcessor, int $priority = 0): void
    {
        $this->orderProcessors->insert($orderProcessor, $priority);
    }

    public function process(OrderInterface $order): void
    {
        foreach ($this->orderProcessors as $orderProcessor) {
            $orderProcessor->process($order);
        }
    }
}
