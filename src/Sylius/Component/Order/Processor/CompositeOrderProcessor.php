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

namespace Sylius\Component\Order\Processor;

use Sylius\Component\Order\Model\OrderInterface;
use Zend\Stdlib\PriorityQueue;

final class CompositeOrderProcessor implements OrderProcessorInterface
{
    /**
     * @var PriorityQueue|OrderProcessorInterface[]
     */
    private $orderProcessors;

    public function __construct()
    {
        $this->orderProcessors = new PriorityQueue();
    }

    /**
     * @param OrderProcessorInterface $orderProcessor
     * @param int $priority
     */
    public function addProcessor(OrderProcessorInterface $orderProcessor, int $priority = 0): void
    {
        $this->orderProcessors->insert($orderProcessor, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order): void
    {
        foreach ($this->orderProcessors as $orderProcessor) {
            $orderProcessor->process($order);
        }
    }
}
