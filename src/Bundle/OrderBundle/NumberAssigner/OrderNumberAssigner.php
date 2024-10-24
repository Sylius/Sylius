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

namespace Sylius\Bundle\OrderBundle\NumberAssigner;

use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;

final class OrderNumberAssigner implements OrderNumberAssignerInterface
{
    public function __construct(private OrderNumberGeneratorInterface $numberGenerator)
    {
    }

    public function assignNumber(OrderInterface $order): void
    {
        if (null !== $order->getNumber()) {
            return;
        }

        $order->setNumber($this->numberGenerator->generate($order));
    }
}
