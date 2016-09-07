<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Processor;

use Sylius\Component\Order\Model\OrderInterface;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
    public function addProcessor(OrderProcessorInterface $orderProcessor, $priority = 0)
    {
        $this->orderProcessors->insert($orderProcessor, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
        foreach ($this->orderProcessors as $orderProcessor) {
            $orderProcessor->process($order);
        }
    }
}
