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

namespace Sylius\Bundle\OrderBundle\Tests\Stub;

use Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

#[AsOrderProcessor(priority: 10)]
final class OrderProcessorWithAttributeStub implements OrderProcessorInterface
{
    public function process(OrderInterface $order): void
    {
    }
}
