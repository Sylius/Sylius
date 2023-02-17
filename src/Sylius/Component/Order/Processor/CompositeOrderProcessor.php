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

final class CompositeOrderProcessor implements OrderProcessorInterface
{
    private iterable $orderProcessors;

    public function __construct(iterable $orderProcessors)
    {
        $this->orderProcessors = $orderProcessors instanceof \Traversable ? iterator_to_array($orderProcessors) : $orderProcessors;
    }

    public function process(OrderInterface $order): void
    {
        foreach ($this->orderProcessors as $orderProcessor) {
            $orderProcessor->process($order);
        }
    }
}
